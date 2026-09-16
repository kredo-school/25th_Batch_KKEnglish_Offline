<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherShiftPatternAssignment;
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
                'shift_pattern_id'    => ['required', 'integer', 'exists:shift_patterns,id'],
                'teacher_ids'         => ['required', 'array', 'min:1'],
                'teacher_ids.*'       => ['integer', 'exists:teachers,id'],
                'weekdays'            => ['required', 'array', 'min:1'],
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
                DB::transaction(function () use ($data, $teacherIds, $weekdays, $newStart, $newEnd, $priority, $createdBy, &$insertedIds) {
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
                'shift_pattern_id'    => ['required', 'integer', 'exists:shift_patterns,id'],
                'teacher_ids'         => ['required', 'array', 'min:1'],
                'teacher_ids.*'       => ['integer', 'exists:teachers,id'],
                'weekdays'            => ['required', 'array', 'min:1'],
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

    public function destroyByTeacher(Teacher $teacher): RedirectResponse
    {
        // 必要なら authorize を追加
        // $this->authorize('delete', TeacherShiftPatternAssignment::class);

        $deleted = TeacherShiftPatternAssignment::where('teacher_id', $teacher->id)->delete();

        return redirect()
            ->route('admin.shift-pattern-assignments.index', ['menu' => 'schedule'])
            ->with('status', "Teacher #{$teacher->id} の割り当てを {$deleted} 件削除しました。");
    }

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
