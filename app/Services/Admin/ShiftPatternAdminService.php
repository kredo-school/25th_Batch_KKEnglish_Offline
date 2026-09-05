<?php

namespace App\Services\Admin;

use App\Models\ShiftPattern;
use App\Models\TeacherShiftAssignment;
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
            $pattern->rules()->delete();
            foreach (($data['rules'] ?? []) as $row) {
                $this->assertRuleConsistency($row, (int)$data['slot_minutes']);
                $pattern->rules()->create($row);
            }

            $pattern->breaks()->delete();
            foreach (($data['breaks'] ?? []) as $row) {
                $this->assertBreakConsistency($row);
                $pattern->breaks()->create($row);
            }

            // 休憩が勤務帯内か、ざっくり検証（同一曜日）
            $this->assertBreaksInsideRules($pattern);

            return $pattern->fresh(['rules', 'breaks']);
        });
    }

    private function assertRuleConsistency(array $rule, int $slotMinutes): void
    {
        // 要件: in_person は 60分かつ xx:00 開始
        if ($rule['lesson_type'] === 'in_person' || $rule['lesson_type'] === 'both') {
            if (substr($rule['start_time'], 3, 2) !== '00') {
                throw new DomainException('対面授業を含むルールの開始は xx:00 のみです。');
            }
        }

        // パターンのslot_minutesとの整合（運用ポリシー）
        // ※ online30 / in_person60を厳密分離するなら将来ここを拡張
        if (!in_array($slotMinutes, [30, 60], true)) {
            throw new DomainException('slot_minutes は 30 または 60 である必要があります。');
        }
    }

    private function assertBreakConsistency(array $break): void
    {
        if ($break['start_time'] >= $break['end_time']) {
            throw new DomainException('休憩時間が不正です。');
        }
    }

    private function assertBreaksInsideRules(ShiftPattern $pattern): void
    {
        $rulesByWeekday = $pattern->rules->groupBy('weekday');

        foreach ($pattern->breaks as $break) {
            $rules = $rulesByWeekday->get($break->weekday, collect());

            $covered = $rules->contains(function ($rule) use ($break) {
                return $rule->start_time <= $break->start_time
                    && $rule->end_time >= $break->end_time;
            });

            if (!$covered) {
                throw new DomainException("休憩({$break->weekday} {$break->start_time}-{$break->end_time})が勤務時間外です。");
            }
        }
    }

    /**
     * シフトパターンを削除
     */
    public function delete(ShiftPattern $shiftPattern): bool
    {
        // 関連データ（割り当て等）の整合性チェックや外部キーのケアが必要な場合はここに記述
        return $shiftPattern->delete();
    }
}
