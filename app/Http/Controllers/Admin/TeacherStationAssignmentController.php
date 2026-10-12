<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Station;
use App\Models\Teacher;
use App\Models\TeacherStationAssignment;
use App\Models\LessonStationOverride;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class TeacherStationAssignmentController extends Controller
{
    /**
     * Station Assignment 一覧
     */
    public function index()
    {
        $assignments = TeacherStationAssignment::with([
            'teacher.user',
            'station',
        ])
            ->orderBy('start_date')
            ->orderBy('teacher_id')
            ->get();

        return view(
            'admin.teacher-station-assignments.index',
            compact('assignments')
        );
    }

    /**
     * 新規登録画面
     */
    public function create()
    {
        $teachers = Teacher::with('user')
            ->orderBy('id')
            ->get();

        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.teacher-station-assignments.create',
            compact('teachers', 'stations')
        );
    }

    /**
     * 新規登録
     */
    public function store(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'station_id' => ['required', 'integer', 'exists:stations,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])
            : null;

        // 同じStationで、同じ日の勤務時間が重なるかを確認
        $conflicts = $this->findShiftConflicts(
            teacherId: (int) $validated['teacher_id'],
            stationId: (int) $validated['station_id'],
            startDate: $startDate,
            endDate: $endDate,
        );

        // 重複があり、まだ確認されていない場合は確認画面へ
        if ($conflicts && !$request->boolean('confirmed')) {
            return view('admin.teacher-station-assignments.confirm', [
                'conflicts' => $conflicts,
                'assignmentData' => $validated,
                'mode' => 'create',
                'teacherStationAssignment' => null,
            ]);
        }

        // 確認後、または重複がない場合に登録
        DB::transaction(function () use ($validated, $startDate, $endDate) {
            $this->applyNewAssignment(
                teacherId: (int) $validated['teacher_id'],
                stationId: (int) $validated['station_id'],
                startDate: $startDate,
                endDate: $endDate,
            );
        });

        return redirect()
            ->route('admin.teacher-station-assignments.index', [
                'menu' => 'station-assignment',
            ])
            ->with('success', 'Station assignment created successfully.');
    }

    /**
     * 編集画面
     */
    public function edit(TeacherStationAssignment $teacherStationAssignment)
    {
        $teacherStationAssignment->load([
            'teacher.user',
            'station',
        ]);

        $teachers = Teacher::with('user')
            ->orderBy('id')
            ->get();

        $stations = Station::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.teacher-station-assignments.edit',
            compact(
                'teacherStationAssignment',
                'teachers',
                'stations'
            )
        );
    }

    /**
     * 更新
     */
    public function update(
        Request $request,
        TeacherStationAssignment $teacherStationAssignment
    ): View|RedirectResponse {
        $validated = $request->validate([
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'station_id' => ['required', 'integer', 'exists:stations,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])
            : null;

        // 現在編集中の割り当てを除外して重複チェック
        $conflicts = $this->findShiftConflicts(
            teacherId: (int) $validated['teacher_id'],
            stationId: (int) $validated['station_id'],
            startDate: $startDate,
            endDate: $endDate,
            ignoreAssignmentId: $teacherStationAssignment->id,
        );

        // 重複があり、まだ確認されていない場合は確認画面へ
        if ($conflicts && !$request->boolean('confirmed')) {
            return view('admin.teacher-station-assignments.confirm', [
                'conflicts' => $conflicts,
                'assignmentData' => $validated,
                'mode' => 'update',
                'teacherStationAssignment' => $teacherStationAssignment,
            ]);
        }

        DB::transaction(function () use (
            $validated,
            $startDate,
            $endDate,
            $teacherStationAssignment
        ) {
            $teacherStationAssignment->delete();

            $this->applyNewAssignment(
                teacherId: (int) $validated['teacher_id'],
                stationId: (int) $validated['station_id'],
                startDate: $startDate,
                endDate: $endDate,
            );
        });

        return redirect()
            ->route('admin.teacher-station-assignments.index', [
                'menu' => 'station-assignment',
            ])
            ->with('success', 'Station assignment updated successfully.');
    }

    /**
     * レッスン単位のStation変更
     */
    public function override(
        Request $request,
        Reservation $reservation
    ) {
        $validated = $request->validate([
            'station_id' => [
                'required',
                'integer',
                'exists:stations,id',
            ],
        ]);

        LessonStationOverride::updateOrCreate(
            [
                'reservation_id' => $reservation->id,
            ],
            [
                'station_id' => $validated['station_id'],
            ]
        );

        return back()->with(
            'success',
            'Lesson station updated successfully.'
        );
    }

    /**
     * 新しい期間を優先して既存期間を調整する
     */
    private function applyNewAssignment(
        int $teacherId,
        int $stationId,
        Carbon $startDate,
        ?Carbon $endDate
    ): void {
        $existingAssignments = TeacherStationAssignment::where(
            'teacher_id',
            $teacherId
        )
            ->where(function ($query) use ($startDate, $endDate) {
                if ($endDate === null) {
                    // 新しい期間が終了日なしの場合
                    $query->where(function ($q) use ($startDate) {
                        $q->whereNull('end_date')
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                $startDate->toDateString()
                            );
                    });
                } else {
                    // 通常の期間重複判定
                    $query->whereDate(
                        'start_date',
                        '<=',
                        $endDate->toDateString()
                    )
                        ->where(function ($q) use ($startDate) {
                            $q->whereNull('end_date')
                                ->orWhereDate(
                                    'end_date',
                                    '>=',
                                    $startDate->toDateString()
                                );
                        });
                }
            })
            ->get();

        foreach ($existingAssignments as $existing) {
            $oldStart = Carbon::parse($existing->start_date);
            $oldEnd = $existing->end_date
                ? Carbon::parse($existing->end_date)
                : null;

            /*
             * 新しい期間より前の部分を残す
             *
             * 例
             * 10/01 ～ 10/31
             * 新規 10/15 ～ 11/15
             *
             * ↓
             * 10/01 ～ 10/14
             */
            if ($oldStart->lt($startDate)) {
                $existing->update([
                    'end_date' => $startDate
                        ->copy()
                        ->subDay()
                        ->toDateString(),
                ]);

                /*
                 * 古い期間の後半も存在する場合
                 *
                 * 例
                 * 10/01 ～ 12/31
                 * 新規 10/15 ～ 11/15
                 *
                 * ↓
                 * 10/01 ～ 10/14
                 * 11/16 ～ 12/31
                 */
                if (
                    $oldEnd !== null
                    && $oldEnd->gt($endDate ?? $oldEnd)
                ) {
                    TeacherStationAssignment::create([
                        'teacher_id' => $teacherId,
                        'station_id' => $existing->station_id,
                        'start_date' => $endDate
                            ->copy()
                            ->addDay()
                            ->toDateString(),
                        'end_date' => $oldEnd->toDateString(),
                    ]);
                }

                continue;
            }

            /*
             * 古い期間の開始日が新しい期間内の場合
             * → 新しい期間に完全に吸収されるので削除
             */
            $existing->delete();
        }

        TeacherStationAssignment::create([
            'teacher_id' => $teacherId,
            'station_id' => $stationId,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate?->toDateString(),
        ]);
    }

        /**
         * 同じStationで、同じ日の勤務時間が重なる予定を取得する。
         */
        private function findShiftConflicts(
            int $teacherId,
            int $stationId,
            Carbon $startDate,
            ?Carbon $endDate,
            ?int $ignoreAssignmentId = null
        ): array {
            // 新しく割り当てるTeacherの勤務予定
            $newSchedulesQuery = TeacherSchedule::where('teacher_id', $teacherId)
                ->whereDate('available_date', '>=', $startDate->toDateString());

            if ($endDate !== null) {
                $newSchedulesQuery->whereDate(
                    'available_date',
                    '<=',
                    $endDate->toDateString()
                );
            }

            $newSchedules = $newSchedulesQuery
                ->orderBy('available_date')
                ->orderBy('start_time')
                ->get();

            // 新しいTeacherに対象期間内の勤務予定がなければ重複なし
            if ($newSchedules->isEmpty()) {
                return [];
            }

            // 同じStationを使用している、別のTeacherの割り当てを取得
            $assignmentsQuery = TeacherStationAssignment::with([
                'teacher.user',
                'station',
            ])
                ->where('station_id', $stationId)
                ->where('teacher_id', '!=', $teacherId);

            if ($ignoreAssignmentId !== null) {
                $assignmentsQuery->where(
                    'id',
                    '!=',
                    $ignoreAssignmentId
                );
            }

            // 新しい割り当て期間と既存の割り当て期間が
            // 重なる可能性があるものだけを検索する
            if ($endDate !== null) {
                $assignmentsQuery->whereDate(
                    'start_date',
                    '<=',
                    $endDate->toDateString()
                );
            }

            $assignmentsQuery->where(function ($query) use ($startDate) {
                $query->whereNull('end_date')
                    ->orWhereDate(
                        'end_date',
                        '>=',
                        $startDate->toDateString()
                    );
            });

            $existingAssignments = $assignmentsQuery->get();

            $conflicts = [];

            foreach ($existingAssignments as $assignment) {
                // 既存割り当てと新しい割り当ての期間が重なる範囲
                $existingStartDate = Carbon::parse($assignment->start_date);

                $rangeStart = $existingStartDate->gt($startDate)
                    ? $existingStartDate->copy()
                    : $startDate->copy();

                $existingEndDate = $assignment->end_date
                    ? Carbon::parse($assignment->end_date)
                    : null;

                $endCandidates = array_filter(
                    [$endDate, $existingEndDate]
                );

                $rangeEnd = null;

                foreach ($endCandidates as $candidate) {
                    if ($rangeEnd === null || $candidate->lt($rangeEnd)) {
                        $rangeEnd = $candidate->copy();
                    }
                }

                if ($rangeEnd !== null && $rangeStart->gt($rangeEnd)) {
                    continue;
                }

                // 既存Teacherの勤務予定を取得
                $existingSchedulesQuery = TeacherSchedule::where(
                    'teacher_id',
                    $assignment->teacher_id
                )
                    ->whereDate(
                        'available_date',
                        '>=',
                        $rangeStart->toDateString()
                    );

                if ($rangeEnd !== null) {
                    $existingSchedulesQuery->whereDate(
                        'available_date',
                        '<=',
                        $rangeEnd->toDateString()
                    );
                }

                $existingSchedules = $existingSchedulesQuery
                    ->orderBy('available_date')
                    ->orderBy('start_time')
                    ->get();

                foreach ($newSchedules as $newSchedule) {
                    $newDate = Carbon::parse($newSchedule->available_date)
                        ->toDateString();

                    // 既存Teacherと同じ日でなければ対象外
                    foreach ($existingSchedules as $existingSchedule) {
                        $existingDate = Carbon::parse(
                            $existingSchedule->available_date
                        )->toDateString();

                        if ($newDate !== $existingDate) {
                            continue;
                        }

                        $newStart = substr(
                            (string) $newSchedule->start_time,
                            0,
                            8
                        );

                        $newEnd = substr(
                            (string) $newSchedule->end_time,
                            0,
                            8
                        );

                        $existingStart = substr(
                            (string) $existingSchedule->start_time,
                            0,
                            8
                        );

                        $existingEnd = substr(
                            (string) $existingSchedule->end_time,
                            0,
                            8
                        );

                        // 時間が重ならなければ対象外
                        if (
                            $existingStart >= $newEnd ||
                            $existingEnd <= $newStart
                        ) {
                            continue;
                        }

                        // Teacher名
                        $existingTeacherName = trim(
                            ($assignment->teacher->user->first_name ?? '')
                            . ' '
                            . ($assignment->teacher->user->last_name ?? '')
                        );

                        $newTeacher = Teacher::with('user')->find($teacherId);

                        $newTeacherName = trim(
                            ($newTeacher->user->first_name ?? '')
                            . ' '
                            . ($newTeacher->user->last_name ?? '')
                        );

                        // 重複している時間帯
                        $overlapStart = max($newStart, $existingStart);
                        $overlapEnd = min($newEnd, $existingEnd);

                        $conflicts[] = [
                            'station_name' => $assignment->station->name
                                ?? 'Unknown Station',

                            'available_date' => $newDate,

                            'existing_teacher_name' => $existingTeacherName,
                            'new_teacher_name' => $newTeacherName,

                            'existing_start_time' => $existingStart,
                            'existing_end_time' => $existingEnd,

                            'new_start_time' => $newStart,
                            'new_end_time' => $newEnd,

                            'overlap_start_time' => $overlapStart,
                            'overlap_end_time' => $overlapEnd,
                        ];
                    }
                }
            }

            return $conflicts;
        }
}
