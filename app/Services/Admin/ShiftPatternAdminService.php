<?php

namespace App\Services\Admin;

use App\Models\ShiftPattern;
use App\Models\TeacherShiftPatternAssignment;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DomainException;

class ShiftPatternAdminService
{
    /**
     * @param array{
     *  pattern_code:string, pattern_name:string, start_time:string, end_time:string,
     *  end_day_offset:int, slot_minutes:int, is_active:bool, display_order?:int|null,
     *  rules:array<int,array{weekday:int,start_time:string,end_time:string,lesson_type:string}>,
     *  breaks?:array<int,array{weekday:int,start_time:string,end_time:string,reason?:string|null}>
     * } $data
     */
    public function upsert(array $data, ?ShiftPattern $pattern, int $userId): ShiftPattern
    {
        return DB::transaction(function () use ($data, $pattern, $userId) {
            $pattern ??= new ShiftPattern();

            if (!$pattern->exists) {
                $pattern->created_by = $userId;
            }

            $pattern->fill([
                'pattern_code' => $data['pattern_code'],
                'pattern_name' => $data['pattern_name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'end_day_offset' => $data['end_day_offset'],
                'slot_minutes' => $data['slot_minutes'],
                'is_active' => $data['is_active'],
                'display_order' => $data['display_order'] ?? 0,
            ]);
            $pattern->save();

            // 全置換（シンプル・安全）
            // $pattern->rules()->delete();
            // foreach (($data['rules'] ?? []) as $row) {
            //     $this->assertRuleConsistency($row, (int)$data['slot_minutes']);
            //     $pattern->rules()->create($row);
            // }

            $pattern->breaks()->delete();
            foreach (($data['breaks'] ?? []) as $row) {
                if (empty($row['start_time']) || empty($row['end_time'])) {
                    continue;
                }
                $this->assertBreakConsistency($row);
                $pattern->breaks()->create($row);
            }

            // 休憩が勤務帯内か、ざっくり検証（同一曜日）
            // $this->assertBreaksInsideRules($pattern);

            return $pattern->fresh(['breaks']);
        });
    }

    // private function assertRuleConsistency(array $rule, int $slotMinutes): void
    // {
    //     // 要件: in_person は 60分かつ xx:00 開始
    //     if ($rule['lesson_type'] === 'in_person' || $rule['lesson_type'] === 'both') {
    //         if (substr($rule['start_time'], 3, 2) !== '00') {
    //             throw new DomainException('対面授業を含むルールの開始は xx:00 のみです。');
    //         }
    //     }

    //     // パターンのslot_minutesとの整合（運用ポリシー）
    //     // ※ online30 / in_person60を厳密分離するなら将来ここを拡張
    //     if (!in_array($slotMinutes, [30, 60], true)) {
    //         throw new DomainException('slot_minutes は 30 または 60 である必要があります。');
    //     }
    // }

    private function assertBreakConsistency(array $break): void
    {
        if ($break['start_time'] >= $break['end_time']) {
            throw new DomainException('休憩時間が不正です。');
        }
    }

    // private function assertBreaksInsideRules(ShiftPattern $pattern): void
    // {
    //     $rulesByWeekday = $pattern->rules->groupBy('weekday');

    //     foreach ($pattern->breaks as $break) {
    //         $rules = $rulesByWeekday->get($break->weekday, collect());

    //         $covered = $rules->contains(function ($rule) use ($break) {
    //             return $rule->start_time <= $break->start_time
    //                 && $rule->end_time >= $break->end_time;
    //         });

    //         if (!$covered) {
    //             throw new DomainException("休憩({$break->weekday} {$break->start_time}-{$break->end_time})が勤務時間外です。");
    //         }
    //     }
    // }

    /**
     * シフトパターンを物理削除
     *
     * 先生が割り当てられているShift Patternは削除不可。
     *
     * 削除できる条件
     * - 先生の割り当てが0人
     *
     * 削除時の処理
     * 1. 未来の未予約Scheduleを削除
     * 2. ScheduleExceptionの参照を解除
     * 3. Shift Pattern Assignmentを削除
     * 4. Shift Patternを物理削除
     */
    public function delete(ShiftPattern $shiftPattern): bool
    {
        return DB::transaction(function () use ($shiftPattern) {

            /*
            * 1. このShift Patternを使用している先生の人数を確認
            */
            $teacherCount = TeacherShiftPatternAssignment::query()
                ->where('shift_pattern_id', $shiftPattern->id)
                ->distinct('teacher_id')
                ->count('teacher_id');

            /*
            * 先生が1人でも割り当てられている場合は削除不可
            */
            if ($teacherCount > 0) {
                throw new DomainException(
                    "This Shift Pattern currently has {$teacherCount} teachers assigned."
                    . " If you want to delete this Shift Pattern, please reassign the teachers to other Shift Patterns first."
                );
            }

            /*
            * 2. 未来の未予約Scheduleを取得
            */
            $schedules = TeacherSchedule::query()
                ->where('shift_pattern_id', $shiftPattern->id)
                ->whereDate('available_date', '>=', Carbon::today())
                ->whereDoesntHave('reservations')
                ->get();

            /*
            * 3. ScheduleExceptionの参照を解除してから
            *    Scheduleを削除
            */
            if ($schedules->isNotEmpty()) {
                $scheduleIds = $schedules->pluck('schedule_id');

                ScheduleException::query()
                    ->whereIn('schedule_id', $scheduleIds)
                    ->update([
                        'schedule_id' => null,
                    ]);

                TeacherSchedule::query()
                    ->whereIn('schedule_id', $scheduleIds)
                    ->delete();
            }

            /*
            * 4. 未来の未予約Scheduleを削除した後、
            *    まだScheduleが残っているか確認
            *
            * 過去のScheduleや予約済みScheduleが残っている場合、
            * Shift Patternは物理削除できない。
            */
            $remainingSchedules = TeacherSchedule::query()
                ->where('shift_pattern_id', $shiftPattern->id)
                ->exists();

            if ($remainingSchedules) {
                throw new DomainException(
                    'This Shift Pattern cannot be deleted because there are past or reserved Schedules remaining.'
                );
            }

            /*
            * 5. Shift Patternを物理削除
            */
            return $shiftPattern->delete();
        });
    }
}
