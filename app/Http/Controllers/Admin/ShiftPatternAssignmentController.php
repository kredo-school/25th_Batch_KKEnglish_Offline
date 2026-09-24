<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\TeacherShiftPatternAssignment;
use App\Models\ScheduleException; // スケジュール例外モデル
use App\Services\Admin\TeacherScheduleGenerationService; // スケジュール生成サービス
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ShiftPatternAssignmentController extends Controller
{
    // スケジュール生成サービスを注入
    public function __construct(
        private readonly TeacherScheduleGenerationService $scheduleGenerator
    ) {}

    public function index(Request $request): View
    {
        // 割り当てが存在する先生を1人1行で取得（全先生を表示対象とする場合は whereHas を外してください）
        $teachers = Teacher::query()
            ->whereHas('shiftPatternAssignments')
            ->with([
                'user:id,first_name,last_name',
                'shiftPatternAssignments.shiftPattern'
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);
        return view('admin.shift-pattern-assignments.index', [
            'teachers' => $teachers,
        ]);
    }

    public function create(Request $request): View
    {
        $patterns = ShiftPattern::query()
            ->orderBy('id', 'desc')
            ->get(['id', 'pattern_name', 'pattern_code']);

        $teachers = Teacher::query()
            ->with(['user:id,first_name,last_name'])
            ->orderBy('id', 'desc')
            ->get(['id', 'user_id']);

            // コレクションを取得した後に、Bladeの表示名ロジックに合わせてソートする
        $teachers = $teachers->sortBy(function ($teacher) {
            // Bladeにある表示ロジックと同じものを再現してソートキーとする
            return trim(
                ($teacher->user->first_name ?? '') . ' ' . ($teacher->user->last_name ?? '')
            ) ?: ('Teacher #' . $teacher->id); // 名前がない場合のフォールバックも合わせる
        });

        return view('admin.shift-pattern-assignments.create', [
            'patterns' => $patterns,
            'teachers' => $teachers,
            'defaultPatternId' => $request->integer('pattern_id') ?: null,
        ]);
    }

        public function store(Request $request): RedirectResponse
        {
            $data = $request->validate([
                'assignment_type'     => ['required', 'string', 'in:weekly,specific_date'],
                'shift_pattern_id'    => ['required', 'integer', 'exists:shift_patterns,id'],
                'teacher_ids'         => ['required', 'array', 'min:1'],
                'teacher_ids.*'       => ['integer', 'exists:teachers,id'],
                'weekdays'            => ['nullable', 'array', 'min:1'],
                'weekdays.*'          => ['integer', 'in:0,1,2,3,4,5,6'],
                'start_date'          => ['required', 'date'],
                'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
                'priority'            => ['nullable', 'integer', 'min:0'],
                // 'replace_overlapping' => ['nullable', 'boolean'],
            ]);

            $teacherIds = collect($data['teacher_ids'])->map(fn($v) => (int)$v)->unique()->values();
            if ($data['assignment_type'] === 'specific_date') {
                $dateObj = Carbon::parse($data['start_date']);
                $weekdays = collect([$dateObj->dayOfWeek]);
                $newStart = $data['start_date'];
                $newEnd = $data['start_date'];
                $priority = 1; // 臨時シフトは優先度を上げて上書きする
            } else {
            $weekdays   = collect($data['weekdays'])->map(fn($v) => (int)$v)->unique()->values();
            if ($weekdays->isEmpty()) {
                return back()->withInput()->withErrors(['weekdays' => 'Select the weekdays you want to modify.']);
            }
            // $endDate    = !empty($data['end_date']) ? $data['end_date'] : null;
            // $now        = now();
            // $replaceOverlapping = (bool)($data['replace_overlapping'] ?? false);

            $newStart = $data['start_date'];
            $newEnd = $data['end_date'] ?? null;
            $priority = (int) ($data['priority'] ?? 0);
            }

            $createdBy = (int) auth()->id();

            $insertedIds = [];

            try {
                // 1) assignment保存だけをトランザクションで確定
                DB::transaction(function () use ($data, $teacherIds, $weekdays, $newStart, $newEnd, $priority, $createdBy, &$insertedIds) {

                // 【重要】設定日以降の「未予約の古いスケジュール」を事前に削除する
                $query = TeacherSchedule::whereIn('teacher_id', $teacherIds)
                    ->where('available_date', '>=', $newStart)
                    ->whereIn('status', ['confirmed', 'draft']); // ★未予約の confirmed と draft の両方を対象にする
                if ($newEnd) {
                    $query->where('available_date', '<=', $newEnd);
                }

                // MySQLの場合 DAYOFWEEK は 1(Sun)~7(Sat) なので -1 して 0~6 に合わせる
                $query->whereIn(DB::raw('DAYOFWEEK(available_date) - 1'), $weekdays);
                // 削除対象のID配列を取得
                $scheduleIdsToDelete = $query->pluck('schedule_id');

                if ($scheduleIdsToDelete->isNotEmpty()) {
                    // 【修正】例外(休講)データは消さず、紐づけ(schedule_id)のみを外して残す
                    \App\Models\ScheduleException::whereIn('schedule_id', $scheduleIdsToDelete)
                        ->update(['schedule_id' => null]);

                    // その後、古いスケジュール枠のみを削除
                    TeacherSchedule::whereIn('schedule_id', $scheduleIdsToDelete)->delete();
                }

                // Assignmentsの保存と更新
                foreach ($teacherIds as $teacherId) {
                    foreach ($weekdays as $weekday) {

                        /*
                        * ------------------------------------------------------
                        * ① 同じTeacher・同じ曜日の既存Assignmentを取得
                        * ------------------------------------------------------
                        */
                        $oldAssignments = TeacherShiftPatternAssignment::query()
                            ->where('teacher_id', $teacherId)
                            ->where('weekday', $weekday)
                            ->where(function ($query) use ($newStart, $newEnd) {

                                $query->where('start_date', '<=', $newEnd ?? '9999-12-31')
                                    ->where(function ($query) use ($newStart) {
                                        $query->whereNull('end_date')
                                            ->orWhere('end_date', '>=', $newStart);
                                    });
                            })
                            ->lockForUpdate()
                            ->get();

                        /*
                        * ------------------------------------------------------
                        * ② 新しいAssignmentのstart_date以降を
                        *    古いAssignmentから切り離す
                        * ------------------------------------------------------
                        */
                        foreach ($oldAssignments as $oldAssignment) {

                            /*
                            * 古いAssignmentが新しいstart_dateより前から始まっている
                            * 場合は、前日まで残す。
                            */
                            if (
                                $oldAssignment->start_date < $newStart
                            ) {
                                $oldAssignment->end_date = Carbon::parse($newStart)
                                    ->subDay()
                                    ->toDateString();

                                $oldAssignment->save();

                            } else {
                                /*
                                * 古いAssignmentの開始日が
                                * 新しいAssignmentの開始日以降なら、
                                * 今回は完全に未来側なので取り消す。
                                */
                                $oldAssignment->delete();
                            }
                        }

                        /*
                        * ------------------------------------------------------
                        * ③ 新しいAssignmentを登録
                        * ------------------------------------------------------
                        */
                        $assignment = TeacherShiftPatternAssignment::create([
                            'shift_pattern_id' => (int) $data['shift_pattern_id'],
                            'teacher_id' => $teacherId,
                            'weekday' => $weekday,
                            'start_date' => $newStart,
                            'end_date' => $newEnd,
                            'priority' => $priority,
                        ]);

                        $insertedIds[] = $assignment->id;
                    }
                }
            });

            /*
            * ----------------------------------------------------------
            * ④ 新しいAssignmentに合わせてScheduleを生成
            * ----------------------------------------------------------
            */
            $generatedSchedules = 0;

            try {
                $assignments = TeacherShiftPatternAssignment::query()
                    ->with('shiftPattern.breaks')
                    ->whereIn('id', $insertedIds)
                    ->get();

                foreach ($assignments as $assignment) {
                    $result = $this->scheduleGenerator
                        ->generateFromAssignment(
                            $assignment,
                            $createdBy
                        );

                    $generatedSchedules += $result['generated'];
                }

            } catch (\Throwable $e) {
                Log::error(
                    'Teacher schedule generation failed.',
                    [
                        // 'assignment_ids' => $insertedIds,
                        'error' => $e->getMessage(),
                    ]
                );
            }

            return redirect()
                ->route(
                    'admin.shift-pattern-assignments.index',
                    ['menu' => 'schedule']
                )
                ->with(
                    'status',
                    "Shift assignmentを登録しました。設定日以降の古い枠を整理して{$generatedSchedules}件のスケジュールを生成しました。"
                );

        } catch (\Throwable $e) {

            Log::error(
                'ShiftPatternAssignment Store Error.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'error' => '保存中にエラーが発生しました: ' . $e->getMessage(),
                ]);
        }
    }

    public function destroyByTeacher(Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($teacher) {
            // アサインメントの削除
            TeacherShiftPatternAssignment::where('teacher_id', $teacher->id)->delete();

            // 削除対象の「未来（今日以降）の未予約スケジュール」のIDを取得
            $scheduleIdsToDelete = TeacherSchedule::where('teacher_id', $teacher->id)
                ->where('available_date', '>=', now()->toDateString()) // 過去は残す
                ->where('status', 'confirmed') // 未予約のみ
                ->pluck('schedule_id'); // 主キーを取得

            if ($scheduleIdsToDelete->isNotEmpty()) {
                // 例外(休講)データは消さず、紐づけ(schedule_id)のみを外して残す
                \App\Models\ScheduleException::whereIn('schedule_id', $scheduleIdsToDelete)->update(['schedule_id' => null]);

                // その後、スケジュール本体を削除
                TeacherSchedule::whereIn('schedule_id', $scheduleIdsToDelete)->delete();
            }
        });
        return redirect()
            ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
            ->with('status', "Teacher #{$teacher->id} の割り当てと、未来の未予約シフトを一括削除しました。（休講記録は保持されます）");
    }

/**
 * Bulk Editで選択した曜日の未来の未予約Shiftを削除
 *
 * - 選択した曜日だけ対象
 * - 今日以降だけ対象
 * - confirmed / draft の両方を対象
 * - 予約があるScheduleは削除しない
 * - 過去のAssignmentは履歴として残す
 * - 未来のAssignmentは終了させる
 */
public function bulkDestroy(
    Teacher $teacher,
    Request $request
): RedirectResponse {
    $data = $request->validate([
        'weekdays' => ['required', 'array', 'min:1'],
        'weekdays.*' => ['integer', 'between:0,6'],
    ]);

    $weekdays = collect($data['weekdays'])
        ->map(fn ($value) => (int) $value)
        ->unique()
        ->values();

    $today = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();

    $deletedScheduleCount = 0;
    $deletedAssignmentCount = 0;
    $updatedAssignmentCount = 0;

    try {
        DB::transaction(function () use (
            $teacher,
            $weekdays,
            $today,
            $yesterday,
            &$deletedScheduleCount,
            &$deletedAssignmentCount,
            &$updatedAssignmentCount
        ) {

            /*
             * ----------------------------------------------------------
             * 1. 今日以降の未予約Scheduleを取得
             * ----------------------------------------------------------
             *
             * confirmed と draft の両方を対象にする。
             */
            $schedules = TeacherSchedule::query()
                ->where('teacher_id', $teacher->id)
                ->whereDate('available_date', '>=', $today)
                ->whereIn('status', ['confirmed', 'draft'])
                ->whereIn(
                    DB::raw('DAYOFWEEK(available_date) - 1'),
                    $weekdays->all()
                )
                ->whereDoesntHave('reservations')
                ->get();

            /*
             * ----------------------------------------------------------
             * 2. Scheduleを削除
             * ----------------------------------------------------------
             */
            if ($schedules->isNotEmpty()) {

                $scheduleIds = $schedules->pluck('schedule_id');

                /*
                 * ScheduleExceptionは履歴として残す。
                 * ただし削除するScheduleとの紐付けだけ外す。
                 */
                ScheduleException::whereIn('schedule_id', $scheduleIds)
                    ->update([
                        'schedule_id' => null,
                    ]);

                $deletedScheduleCount = TeacherSchedule::query()
                    ->whereIn('schedule_id', $scheduleIds)
                    ->delete();
            }

            /*
             * ----------------------------------------------------------
             * 3. 選択された曜日のAssignmentを停止
             * ----------------------------------------------------------
             *
             * 未来から始まるAssignment
             * → Assignment自体を削除
             *
             * 過去から継続しているAssignment
             * → 昨日までに終了させる
             *
             * こうすることで過去の履歴は残しつつ、
             * 今後の自動Schedule生成を止める。
             */
            $assignments = TeacherShiftPatternAssignment::query()
                ->where('teacher_id', $teacher->id)
                ->whereIn('weekday', $weekdays->all())
                ->where(function ($query) use ($today) {
                    $query
                        ->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $today);
                })
                ->lockForUpdate()
                ->get();

            foreach ($assignments as $assignment) {

                /*
                 * Assignmentが今日より前から始まっている場合
                 * → 昨日で終了させる
                 */
                if ($assignment->start_date < $today) {

                    $assignment->end_date = $yesterday;
                    $assignment->save();

                    $updatedAssignmentCount++;

                } else {

                    /*
                     * 今日以降から始まるAssignmentなら
                     * 完全に未来側なので削除する。
                     */
                    $assignment->delete();

                    $deletedAssignmentCount++;
                }
            }
        });

        return redirect()
            ->route(
                'admin.shift-pattern-assignments.index',
                $teacher
            )
            ->with(
                'status',
                "Successfully deleted the day shifts selected."
                . " Schedule {$deletedScheduleCount} deleted, "
                . " Assignment {$deletedAssignmentCount} deleted, "
                . " {$updatedAssignmentCount} Assignments ended."
            );

    } catch (\Throwable $e) {

        Log::error(
            'ShiftPatternAssignment Bulk Destroy Error.',
            [
                'teacher_id' => $teacher->id,
                'weekdays' => $weekdays->all(),
                'error' => $e->getMessage(),
            ]
        );

        return back()
            ->withInput()
            ->withErrors([
                'error' => 'An error occurred while deleting the shifts: '
                    . $e->getMessage(),
            ]);
    }
}
    public function edit(TeacherShiftPatternAssignment $assignment): View
    {
        $assignment->load(['teacher.user', 'shiftPattern',
        ]);

        $patterns = ShiftPattern::query()
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get(['id', 'pattern_name', 'pattern_code',
            ]);

        return view('admin.shift-pattern-assignments.edit',compact('assignment', 'patterns'
            )
        );
    }

    public function update(TeacherShiftPatternAssignment $assignment, Request $request): RedirectResponse
    {
        $data = $request->validate([
                'assignment_type'     => ['required', 'string', 'in:weekly,specific_date'],
                'shift_pattern_id'    => ['required', 'integer', 'exists:shift_patterns,id'],
                'teacher_ids'         => ['required', 'array', 'min:1'],
                'teacher_ids.*'       => ['integer', 'exists:teachers,id'],
                'weekdays'            => ['nullable', 'array', 'min:1'],
                'weekdays.*'          => ['integer', 'in:0,1,2,3,4,5,6'],
                'start_date'          => ['required', 'date'],
                'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
                'priority'            => ['nullable', 'integer', 'min:0'],
                // 'replace_overlapping' => ['nullable', 'boolean'],
            ]);

            $teacherIds = collect($data['teacher_ids'])->map(fn($v) => (int)$v)->unique()->values();
            $weekdays   = collect($data['weekdays'])->map(fn($v) => (int)$v)->unique()->values();
            // $endDate    = !empty($data['end_date']) ? $data['end_date'] : null;
            // $now        = now();
            // $replaceOverlapping = (bool)($data['replace_overlapping'] ?? false);

            $newStart = $data['start_date'];
            $newEnd = $data['end_date'] ?? null;
            $priority = (int) ($data['priority'] ?? 0);
            $createdBy = (int) auth()->id();

            $insertedIds = [];

            try {
                // 1) assignment保存だけをトランザクションで確定
                DB::transaction(function () use ($data, $teacherIds, $weekdays, $newStart, $newEnd, $priority, $createdBy, $insertedIds) {
                foreach ($teacherIds as $teacherId) {
                    foreach ($weekdays as $weekday) {

                        /*
                        * ------------------------------------------------------
                        * ① 同じTeacher・同じ曜日の既存Assignmentを取得
                        * ------------------------------------------------------
                        */
                        $oldAssignments = TeacherShiftPatternAssignment::query()
                            ->where('teacher_id', $teacherId)
                            ->where('weekday', $weekday)
                            ->where(function ($query) use ($newStart, $newEnd) {

                                $query->where('start_date', '<=', $newEnd ?? '9999-12-31')
                                    ->where(function ($query) use ($newStart) {
                                        $query->whereNull('end_date')
                                            ->orWhere('end_date', '>=', $newStart);
                                    });
                            })
                            ->lockForUpdate()
                            ->get();

                        /*
                        * ------------------------------------------------------
                        * ② 新しいAssignmentのstart_date以降を
                        *    古いAssignmentから切り離す
                        * ------------------------------------------------------
                        */
                        foreach ($oldAssignments as $oldAssignment) {

                            /*
                            * 古いAssignmentが新しいstart_dateより前から始まっている
                            * 場合は、前日まで残す。
                            */
                            if (
                                $oldAssignment->start_date < $newStart
                            ) {
                                $oldAssignment->end_date = Carbon::parse($newStart)
                                    ->subDay()
                                    ->toDateString();

                                $oldAssignment->save();

                            } else {
                                /*
                                * 古いAssignmentの開始日が
                                * 新しいAssignmentの開始日以降なら、
                                * 今回は完全に未来側なので取り消す。
                                */
                                $oldAssignment->delete();
                            }
                        }

                        /*
                        * ------------------------------------------------------
                        * ③ 新しいAssignmentを登録
                        * ------------------------------------------------------
                        */
                        $assignment = TeacherShiftPatternAssignment::create([
                            'shift_pattern_id' => (int) $data['shift_pattern_id'],
                            'teacher_id' => $teacherId,
                            'weekday' => $weekday,
                            'start_date' => $newStart,
                            'end_date' => $newEnd,
                            'priority' => $priority,
                        ]);

                        $insertedIds[] = $assignment->id;
                    }
                }
            });

            /*
            * ----------------------------------------------------------
            * ④ 新しいAssignmentに合わせてScheduleを生成
            * ----------------------------------------------------------
            */
            $generatedSchedules = 0;

            try {
                $assignments = TeacherShiftPatternAssignment::query()
                    ->with('shiftPattern.breaks')
                    ->whereIn('id', $insertedIds)
                    ->get();

                foreach ($assignments as $assignment) {
                    $result = $this->scheduleGenerator
                        ->generateFromAssignment(
                            $assignment,
                            $createdBy
                        );

                    $generatedSchedules += $result['generated'];
                }

            } catch (\Throwable $e) {
                Log::error(
                    'Teacher schedule generation failed.',
                    [
                        'assignment_ids' => $insertedIds,
                        'error' => $e->getMessage(),
                    ]
                );
            }

            return redirect()
                ->route(
                    'admin.shift-pattern-assignments.index',
                    ['menu' => 'schedule']
                )
                ->with(
                    'status',
                    "Shift assignmentを登録しました。{$generatedSchedules}件のスケジュールを生成しました。"
                );

        } catch (\Throwable $e) {

            Log::error(
                'ShiftPatternAssignment Store Error.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'error' => '保存中にエラーが発生しました: ' . $e->getMessage(),
                ]);
        }
    }

    public function destroy(TeacherShiftPatternAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return redirect()
            ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
            ->with('status', 'Teacher assignmentを削除しました。');
    }

    // public function destroyByTeacher(Teacher $teacher): RedirectResponse
    // {
    //     // 必要なら authorize を追加
    //     // $this->authorize('delete', TeacherShiftPatternAssignment::class);

    //     $deleted = TeacherShiftPatternAssignment::where('teacher_id', $teacher->id)->delete();

    //     return redirect()
    //         ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
    //         ->with('status', "Teacher #{$teacher->id} の割り当てを {$deleted} 件削除しました。");
    // }

    public function bulkEdit(Teacher $teacher): View
    {
        $teacher->load([
            'user:id,first_name,last_name',
            'shiftPatternAssignments.shiftPattern',
        ]);

        $patterns = ShiftPattern::query()
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get(['id', 'pattern_name', 'pattern_code']);

        $assignments = $teacher->shiftPatternAssignments;

        $selectedWeekdays = $assignments
            ->pluck('weekday')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $firstAssignment = $assignments->sortBy('start_date')->first();

        return view('admin.shift-pattern-assignments.bulk-edit', [
            'teacher' => $teacher,
            'patterns' => $patterns,
            'selectedWeekdays' => $selectedWeekdays,
            'defaultPatternId' => $firstAssignment?->shift_pattern_id,
            'defaultStartDate' => $firstAssignment?->start_date?->format('Y-m-d')
                ?? $firstAssignment?->start_date,
            'defaultEndDate' => $firstAssignment?->end_date?->format('Y-m-d')
                ?? $firstAssignment?->end_date,
            'defaultPriority' => $firstAssignment?->priority ?? 0,
            'weekdayNames' => [
                0 => 'Sun',
                1 => 'Mon',
                2 => 'Tue',
                3 => 'Wed',
                4 => 'Thu',
                5 => 'Fri',
                6 => 'Sat',
            ],
        ]);
    }

    /**
     * Teacher単位の一括更新
     *
     * 選択された曜日について、
     * 新しいstart_date以降の既存Assignmentを終了・削除し、
     * 新しいAssignmentを作成します。
     */
public function bulkUpdate(Teacher $teacher, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'assignment_type' => [
                'required',
                'string',
                'in:weekly,specific_date'
            ],
            'shift_pattern_id' => [
                'required',
                'integer',
                'exists:shift_patterns,id',
            ],
            'weekdays' => [
                'required',
                'array',
                'min:1',
            ],
            'weekdays.*' => [
                'integer',
                'in:0,1,2,3,4,5,6',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'priority' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $weekdays = collect($data['weekdays'])
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $newStart = $data['start_date'];
        $newEnd = $data['end_date'] ?? null;
        $priority = (int) ($data['priority'] ?? 0);
        $createdBy = (int) auth()->id();

        $insertedIds = [];

        try {
            DB::transaction(function () use (
                $teacher,
                $data,
                $weekdays,
                $newStart,
                $newEnd,
                $priority,
                &$insertedIds
            ) {
                foreach ($weekdays as $weekday) {

                    $oldAssignments = TeacherShiftPatternAssignment::query()
                        ->where('teacher_id', $teacher->id)
                        ->where('weekday', $weekday)
                        ->where(function ($query) use ($newStart, $newEnd) {
                            $query
                                ->where('start_date', '<=', $newEnd ?? '9999-12-31')
                                ->where(function ($query) use ($newStart) {
                                    $query
                                        ->whereNull('end_date')
                                        ->orWhere('end_date', '>=', $newStart);
                                });
                        })
                        ->lockForUpdate()
                        ->get();

                    foreach ($oldAssignments as $oldAssignment) {
                        if ($oldAssignment->start_date < $newStart) {
                            $oldAssignment->end_date = Carbon::parse($newStart)
                                ->subDay()
                                ->toDateString();

                            $oldAssignment->save();
                        } else {
                            $oldAssignment->delete();
                        }
                    }

                    $assignment = TeacherShiftPatternAssignment::create([
                        'shift_pattern_id' => (int) $data['shift_pattern_id'],
                        'teacher_id' => $teacher->id,
                        'weekday' => $weekday,
                        'start_date' => $newStart,
                        'end_date' => $newEnd,
                        'priority' => $priority,
                    ]);

                    $insertedIds[] = $assignment->id;
                }
            });

            $generatedSchedules = 0;

            try {
                $assignments = TeacherShiftPatternAssignment::query()
                    ->with('shiftPattern.breaks')
                    ->whereIn('id', $insertedIds)
                    ->get();

                foreach ($assignments as $assignment) {
                    $result = $this->scheduleGenerator
                        ->generateFromAssignment($assignment, $createdBy);

                    $generatedSchedules += $result['generated'];
                }
            } catch (\Throwable $e) {
                Log::error(
                    'Teacher schedule bulk generation failed.',
                    [
                        'teacher_id' => $teacher->id,
                        'assignment_ids' => $insertedIds,
                        'error' => $e->getMessage(),
                    ]
                );
            }

            return redirect()
                ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
                ->with(
                    'status',
                    "Teacher #{$teacher->id} のShift Assignmentを一括変更しました。{$generatedSchedules}件のスケジュールを生成しました。"
                );

        } catch (\Throwable $e) {
            Log::error(
                'ShiftPatternAssignment Bulk Update Error.',
                [
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage(),
                ]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'error' => '一括変更中にエラーが発生しました: ' . $e->getMessage(),
                ]);
        }
    }

}
