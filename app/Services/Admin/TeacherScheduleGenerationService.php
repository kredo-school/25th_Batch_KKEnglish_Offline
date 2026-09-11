<?php

namespace App\Services\Admin;

// use App\Models\ScheduleException;
use App\Models\ShiftPattern;
use App\Models\TeacherSchedule;
use App\Models\TeacherShiftPatternAssignment;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
// use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherScheduleGenerationService
{
    /** 今日を含めて何日先まで生成するか */
    private const WINDOW_DAYS = 28;

    /**
     * 割当て1件から teacher_schedules を生成する（割当て作成直後に呼ぶ）
     */
    public function generateFromAssignment(TeacherShiftPatternAssignment $assignment, int $createdBy): array
    {
        $this->debugAssignment($assignment);
        $pattern = $assignment->shiftPattern ?? ShiftPattern::with('breaks')->find($assignment->shift_pattern_id);

        if (!$pattern || !$pattern->is_active) {
            return ['generated' => 0, 'skipped' => 0];
        }
        if (!$pattern->relationLoaded('breaks')) {
            $pattern->load('breaks');
        }

        $today     = Carbon::today();
        /*
         * 今日を1日目として28日分
         *
         * 例：
         * 9/11 ～ 10/8
         */
        $windowEnd = $today->copy()->addDays(self::WINDOW_DAYS - 1);

        $assignStart = Carbon::parse($assignment->start_date)->startOfDay();
        $assignEnd   = $assignment->end_date ? Carbon::parse($assignment->end_date)->startOfDay() : null;

        /*
         * 実際に生成する開始日
         */
        $periodStart = $assignStart->greaterThan($today) ? $assignStart : $today;

        /*
         * 実際に生成する終了日
         */
        $periodEnd   = ($assignEnd && $assignEnd->lessThan($windowEnd)) ? $assignEnd : $windowEnd;

        if ($periodStart->greaterThan($periodEnd)) {
            return ['generated' => 0, 'skipped' => 0];
        }

        return $this->insertSlots(
            (int) $assignment->teacher_id,
            (int) $assignment->weekday,
            $pattern,
            $periodStart,
            $periodEnd,
            $createdBy
        );
    }

    /**
     * 有効な全割当てを対象に「今日〜27日後」を補完生成する（日次バッチ用）
     */
    public function regenerateWindow(int $createdBy, ?array $teacherIds = null): array
    {
        $today     = Carbon::today();
        /*
         * 今日を含めて28日分
         */
        $windowEnd = $today->copy()->addDays(self::WINDOW_DAYS - 1);

        $query = TeacherShiftPatternAssignment::with('shiftPattern.breaks')
            ->whereHas('shiftPattern', fn ($q) => $q->where('is_active', true))
            ->where('start_date', '<=', $windowEnd->toDateString())
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today->toDateString());
            })
            ->orderByDesc('priority');

        if (!empty($teacherIds)) {
            $query->whereIn('teacher_id', $teacherIds);
        }

        $assignments = $query->get();
        $totals = ['assignments' => $assignments->count(), 'generated' => 0, 'skipped' => 0];

        foreach ($assignments as $assignment) {
            try {
                $result = $this->generateFromAssignment($assignment, $createdBy);
                $totals['generated'] += $result['generated'];
                $totals['skipped']   += $result['skipped'];
            } catch (\Throwable $e) {
                Log::error("TeacherScheduleGenerationService: assignment_id={$assignment->id} failed: {$e->getMessage()}");
            }
        }

        return $totals;
    }

    /**
     * Teacher Scheduleを生成する
     *
     * 今回は30分などのSlotではなく、
     * 「勤務可能時間帯」を登録する。
     *
     * 例：
     *
     * 09:00 ～ 18:00
     * Break 12:00 ～ 13:00
     *
     * ↓
     *
     * 09:00 ～ 12:00
     * 13:00 ～ 18:00
     */
    private function insertSlots(int $teacherId, int $weekday, ShiftPattern $pattern, Carbon $from, Carbon $to, int $createdBy): array
    {
        /*
         * 既に存在するScheduleを取得
         *
         * 同じ
         * teacher_id
         * available_date
         * start_time
         * end_time
         *
         * のScheduleがあれば重複登録しない
         */
        $existingKeys = TeacherSchedule::where('teacher_id', $teacherId)
            ->whereBetween('available_date', [$from->toDateString(), $to->toDateString()])
            ->whereNotIn('status', ['cancelled'])
            ->get(['available_date', 'start_time', 'end_time'])
            ->map(fn ($row) => $row->available_date->format('Y-m-d') . '_' . $row->start_time. '_' . $row->end_time)
            ->flip();

        /*
         * Schedule Exception取得
         *
         * blockされている時間帯は
         * Scheduleとして生成しない
         */
        // $exceptions = ScheduleException::where('teacher_id', $teacherId)
        //     ->where('type', 'block')
        //     ->where('exception_start', '<', $to->copy()->endOfDay())
        //     ->where('exception_end', '>', $from->copy()->startOfDay())
        //     ->get(['exception_start', 'exception_end']);

        $rows      = [];
        $generated = 0;
        $skipped   = 0;

        /*
         * 今日～28日分を1日ずつ確認
         */
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {

            /*
             * Assignmentの曜日と一致しない場合は
             * Scheduleを作らない
             */
            if ($date->dayOfWeek !== $weekday) {
                continue;
            }

            /*
             * 勤務時間をBreakで分割
             */
            $periods = $this->buildSlots($date, $pattern
            );

            foreach ($periods as $period) {

                $startTime = $period['start']->format('H:i:s');
                $endTime = $period['end']->format('H:i:s');

                /*
                 * ScheduleExceptionによるBlockを考慮
                 *
                 * ここで必要ならさらに分割する
                 */
                // $availablePeriods = $this->subtractExceptions($periodStart, $periodEnd, $exceptions
                //     );

                // foreach ($availablePeriods as $availablePeriod) {

                //     $startTime = $availablePeriod['start']
                //             ->format('H:i:s');

                //     $endTime = $availablePeriod['end']
                //             ->format('H:i:s');

                    /*
                     * 同じ日付・開始・終了時間なら
                     * 既存Scheduleとして扱う
                     */
                    $key = $date->toDateString() . '_' . $startTime . '_' . $endTime;

                    if (isset($existingKeys[$key])) {
                        $skipped++;
                        continue;
                    }

                    $rows[] = [
                        'teacher_id' => $teacherId,
                        'shift_pattern_id' => $pattern->id,
                        'available_date' => $date->toDateString(),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status' => 'draft',
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $generated++;

                    /*
                     * 同一処理内での重複生成防止
                     */
                    $existingKeys[$key] = true;
                }
            }

            /*
            * 登録するデータがなければ終了
            */
            if (empty($rows)) {

                return [
                    'generated' => $generated,
                    'skipped' => $skipped,
                ];
            }
            
        /*
         * 500件ずつInsert
         */
        foreach (array_chunk($rows, 500) as $chunk) {

            try {

                DB::transaction(
                    function () use ($chunk) {

                        TeacherSchedule::insert(
                            $chunk
                        );
                    }
                );

            } catch (QueryException $e) {

                Log::warning(
                    'TeacherSchedule insert skipped duplicate chunk: '
                    . $e->getMessage()
                );
            }
        }

            return [
            'generated' => $generated,
            'skipped' => $skipped,
        ];
    }

    //         foreach ($this->buildSlots($date, $pattern) as $slot) {
    //             $key       = $date->toDateString() . '_' . $slot['start_time'];
    //             $slotStart = Carbon::parse($date->toDateString() . ' ' . $slot['start_time']);
    //             $slotEnd   = Carbon::parse($date->toDateString() . ' ' . $slot['end_time']);

    //             if (isset($existingKeys[$key]) || $this->overlapsException($slotStart, $slotEnd, $exceptions)) {
    //                 $skipped++;
    //                 continue;
    //             }

    //             $rows[] = [
    //                 'teacher_id'       => $teacherId,
    //                 'shift_pattern_id' => $pattern->id,
    //                 'available_date'   => $date->toDateString(),
    //                 'start_time'       => $slot['start_time'],
    //                 'end_time'         => $slot['end_time'],
    //                 'status'           => 'draft',
    //                 'created_by'       => $createdBy,
    //                 'created_at'       => now(),
    //                 'updated_at'       => now(),
    //             ];
    //             $generated++;
    //             $existingKeys[$key] = true;
    //         }
    //     }

    //     foreach (array_chunk($rows, 500) as $chunk) {
    //         try {
    //             DB::transaction(function () use ($chunk) {
    //                 TeacherSchedule::insert($chunk);
    //             });
    //         } catch (QueryException $e) {
    //             Log::warning('TeacherSchedule insert skipped duplicate chunk: ' . $e->getMessage());
    //         }
    //     }

    //     return ['generated' => $generated, 'skipped' => $skipped];
    // }

    /**
     * Shift Patternの勤務時間を
     * Breakによって分割する
     *
     * 例：
     *
     * Shift
     * 09:00 ～ 18:00
     *
     * Break
     * 12:00 ～ 13:00
     *
     * ↓
     *
     * [
     *     09:00 ～ 12:00,
     *     13:00 ～ 18:00
     * ]
     */
    private function buildSlots(Carbon $date, ShiftPattern $pattern): array
    {
        $slots = [];
        /*
         * Shift開始
         */
        $shiftStart = $date->copy()->setTimeFromTimeString($pattern->start_time);
        /*
         * Shift終了
         */
        $shiftEnd   = $date->copy()
            ->addDays($pattern->end_day_offset)
            ->setTimeFromTimeString($pattern->end_time);

        /*
         * この曜日に適用されるBreak
         */
        $weekday      = $date->dayOfWeek;
        $breaksForDay = $pattern->breaks->filter(
            fn ($b) => is_null($b->weekday) || (int) $b->weekday === $weekday
        )
        ->sortBy('start_time')
        ->values();;

        /*
         * 現在の勤務開始位置
         */
        $cursor = $shiftStart->copy();

foreach ($breaksForDay as $break) {

            /*
             * Break開始
             */
            $breakStart = $date->copy()->setTimeFromTimeString($break->start_time
                );

            /*
             * Break終了
             */
            $breakEnd = $date->copy()->setTimeFromTimeString($break->end_time
                );

            /*
             * Shift時間外のBreakは無視
             */
            if (
                $breakEnd->lte($shiftStart)||$breakStart->gte($shiftEnd)
            ) {
                continue;
            }

            /*
             * Break開始がShift開始より前の場合
             */
            if ($breakStart->lt($shiftStart)) {$breakStart = $shiftStart->copy();
            }

            /*
             * Break終了がShift終了より後の場合
             */
            if ($breakEnd->gt($shiftEnd)) {$breakEnd = $shiftEnd->copy();
            }

            /*
             * Break開始までの勤務時間
             *
             * 例：
             * 09:00 ～ 12:00
             */
            if ($cursor->lt($breakStart)) {

                $slots[] = ['start' => $cursor->copy(),'end' => $breakStart->copy(),
                ];
            }

            /*
             * Break終了後から勤務再開
             *
             * 例：
             * 13:00
             */
            if ($cursor->lt($breakEnd)) {$cursor = $breakEnd->copy();
            }
        }

        /*
         * 最後のBreak終了から
         * Shift終了まで
         *
         * 例：
         * 13:00 ～ 18:00
         */
        if ($cursor->lt($shiftEnd)) {

            $slots[] = ['start' => $cursor->copy(),'end' => $shiftEnd->copy(),];
        }
        // while ($cursor->copy()->addMinutes($pattern->slot_minutes)->lte($shiftEnd)) {
        //     $slotStart = $cursor->copy();
        //     $slotEnd   = $cursor->copy()->addMinutes($pattern->slot_minutes);

        //     if (!$this->overlapsBreak($slotStart, $slotEnd, $breaksForDay)) {
        //         $slots[] = [
        //             'start_time' => $slotStart->format('H:i:s'),
        //             'end_time'   => $slotEnd->format('H:i:s'),
        //         ];
        //     }

        //     $cursor->addMinutes($pattern->slot_minutes);
        // }

        return $slots;
    }

    /**
     * Schedule ExceptionによるBlockを除外する
     *
     * 例：
     *
     * 元：
     * 09:00 ～ 18:00
     *
     * Exception：
     * 14:00 ～ 15:00
     *
     * ↓
     *
     * 09:00 ～ 14:00
     * 15:00 ～ 18:00
     */
    // private function subtractExceptions(Carbon $periodStart, Carbon $periodEnd, Collection $exceptions): array {

    //     $periods = [];

    //     /*
    //      * 現在の開始位置
    //      */
    //     $cursor = $periodStart->copy();

    //     foreach ($exceptions as $exception) {

    //         $exceptionStart = Carbon::parse($exception->exception_start
    //         );

    //         $exceptionEnd = Carbon::parse($exception->exception_end
    //         );

    //         /*
    //          * この勤務時間と関係ないExceptionは無視
    //          */
    //         if (
    //             $exceptionEnd->lte($periodStart)||$exceptionStart->gte($periodEnd)
    //         ) {
    //             continue;
    //         }

    //         /*
    //          * Exception開始が勤務開始より前なら
    //          * 勤務開始時間に合わせる
    //          */
    //         if ($exceptionStart->lt($periodStart)) {
    //             $exceptionStart = $periodStart->copy();
    //         }

    //         /*
    //          * Exception終了が勤務終了より後なら
    //          * 勤務終了時間に合わせる
    //          */
    //         if ($exceptionEnd->gt($periodEnd)) {
    //             $exceptionEnd = $periodEnd->copy();
    //         }

    //         /*
    //          * Exception開始まで勤務可能
    //          */
    //         if ($cursor->lt($exceptionStart)) {

    //             $periods[] = [
    //                 'start' => $cursor->copy(),
    //                 'end' => $exceptionStart->copy(),
    //             ];
    //         }

    //         /*
    //          * Exception終了後から再開
    //          */
    //         if ($cursor->lt($exceptionEnd)) {
    //             $cursor = $exceptionEnd->copy();
    //         }
    //     }

    //     /*
    //      * 最後のExceptionから
    //      * 勤務終了まで
    //      */
    //     if ($cursor->lt($periodEnd)) {

    //         $periods[] = [
    //             'start' => $cursor->copy(),
    //             'end' => $periodEnd->copy(),
    //         ];
    //     }

    //     return $periods;
    // }

    // private function overlapsBreak(Carbon $slotStart, Carbon $slotEnd, Collection $breaksForDay): bool
    // {
    //     foreach ($breaksForDay as $break) {
    //         $breakStart = $slotStart->copy()->setTimeFromTimeString($break->start_time);
    //         $breakEnd   = $slotStart->copy()->setTimeFromTimeString($break->end_time);

    //         if ($slotStart->lt($breakEnd) && $slotEnd->gt($breakStart)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    // private function overlapsException(Carbon $slotStart, Carbon $slotEnd, Collection $exceptions): bool
    // {
    //     foreach ($exceptions as $ex) {
    //         $exStart = Carbon::parse($ex->exception_start);
    //         $exEnd   = Carbon::parse($ex->exception_end);

    //         if ($slotStart->lt($exEnd) && $slotEnd->gt($exStart)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
    private function debugAssignment(
    TeacherShiftPatternAssignment $assignment
): void {

    $pattern = ShiftPattern::with('breaks')
        ->find($assignment->shift_pattern_id);

    Log::info('===== Teacher Schedule Debug =====');

    Log::info([
        'assignment_id' => $assignment->id,
        'teacher_id' => $assignment->teacher_id,
        'shift_pattern_id' => $assignment->shift_pattern_id,
        'weekday' => $assignment->weekday,
        'start_date' => $assignment->start_date,
        'end_date' => $assignment->end_date,
        'priority' => $assignment->priority,
    ]);

    Log::info([
        'pattern_exists' => $pattern ? true : false,
        'pattern_id' => $pattern?->id,
        'pattern_name' => $pattern?->pattern_name,
        'start_time' => $pattern?->start_time,
        'end_time' => $pattern?->end_time,
        'is_active' => $pattern?->is_active,
        'break_count' => $pattern?->breaks?->count(),
    ]);

    if ($pattern) {

        $today = Carbon::today();

        $windowEnd = $today->copy()
            ->addDays(self::WINDOW_DAYS - 1);

        $assignStart = Carbon::parse(
            $assignment->start_date
        )->startOfDay();

        $assignEnd = $assignment->end_date
            ? Carbon::parse($assignment->end_date)->startOfDay()
            : null;

        $periodStart = $assignStart->greaterThan($today)
            ? $assignStart
            : $today;

        $periodEnd = (
            $assignEnd &&
            $assignEnd->lessThan($windowEnd)
        )
            ? $assignEnd
            : $windowEnd;

        Log::info([
            'today' => $today->toDateString(),
            'window_end' => $windowEnd->toDateString(),
            'assign_start' => $assignStart->toDateString(),
            'assign_end' => $assignEnd?->toDateString(),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'period_invalid' => $periodStart->greaterThan($periodEnd),
        ]);

        if ($periodStart->lte($periodEnd)) {

            foreach ($periodStart->copy()->daysUntil(
                $periodEnd->copy()->addDay()
            ) as $date) {

                if ($date->dayOfWeek === (int) $assignment->weekday) {

                    $slots = $this->buildSlots(
                        $date,
                        $pattern
                    );

                    Log::info([
                        'matched_date' => $date->toDateString(),
                        'date_weekday' => $date->dayOfWeek,
                        'assignment_weekday' => (int) $assignment->weekday,
                        'slots' => $slots,
                    ]);
                }
            }
        }
    }
}
}
