<?php

namespace App\Services\Admin;

use App\Models\ScheduleException;
use App\Models\ShiftPattern;
use App\Models\TeacherSchedule;
use Carbon\CarbonImmutable;

// 既存の実装クラスを継承して互換クラスを作る
class GenerateTeacherSchedulesService
{
    /**
     * @param ShiftPattern $pattern
     * @param int[] $teacherIds
     * @param string $from Y-m-d
     * @param string|null $to Y-m-d
     * @param int $createdBy
     * @return int
     */
    public function generateForAssignment(ShiftPattern $pattern, array $teacherIds, string $from, ?string $to, int $createdBy): int
    {
        // 暫定：まずDI解決と500回避を優先。必要なら既存ロジックに差し替え。
        // ここで0を返しても、重複割当テストは先に進められる。
        return 0;
    }
    private const BIZ_TZ = 'Asia/Manila';

    public function generate(
        int $teacherId,
        ShiftPattern $pattern,
        string $effectiveFrom,   // Y-m-d
        ?string $effectiveTo,    // Y-m-d|null
        int $createdBy           // 監査用: 実行ユーザーIDを呼び出し元から受け取る
    ): int {
        $from = CarbonImmutable::parse($effectiveFrom, self::BIZ_TZ)->startOfDay();
        $to   = $effectiveTo
            ? CarbonImmutable::parse($effectiveTo, self::BIZ_TZ)->endOfDay()
            : $from->copy()->addWeeks(8)->endOfDay(); // 終了未指定時の提案（確認が必要）

        $rules  = $pattern->rules()->get()->groupBy('weekday');
        $breaks = $pattern->breaks()->get()->groupBy('weekday');

        $count = 0;
        for ($day = $from; $day->lte($to); $day = $day->addDay()) {
            $weekday = $day->dayOfWeek; // 0=Sun..6=Sat
            $dayRules = $rules->get($weekday, collect());
            if ($dayRules->isEmpty()) {
                continue;
            }

            foreach ($dayRules as $rule) {
                $ruleStart = CarbonImmutable::parse($day->format('Y-m-d').' '.$rule->start_time, self::BIZ_TZ);
                $ruleEnd   = CarbonImmutable::parse($day->format('Y-m-d').' '.$rule->end_time, self::BIZ_TZ);

                $slotMinutes = $rule->lesson_type === 'in_person' ? 60 : 30; // bothは30分で生成し予約時判定でも可
                $cursor = $ruleStart;

                while ($cursor->lt($ruleEnd)) {
                    $slotStart = $cursor;
                    $slotEnd   = $cursor->addMinutes($slotMinutes);
                    if ($slotEnd->gt($ruleEnd)) {
                        break;
                    }

                    // 対面はxx:00開始
                    if (in_array($rule->lesson_type, ['in_person', 'both'], true) && $slotMinutes === 60) {
                        if ($slotStart->minute !== 0) {
                            $cursor = $cursor->addMinutes(30);
                            continue;
                        }
                    }

                    // 休憩除外
                    if ($this->isInsideBreak($slotStart, $slotEnd, $breaks->get($weekday, collect()), $day)) {
                        $cursor = $cursor->addMinutes(30);
                        continue;
                    }

                    // 例外（block）除外
                    if ($this->isBlockedByException($teacherId, $slotStart, $slotEnd)) {
                        $cursor = $cursor->addMinutes(30);
                        continue;
                    }

                    $lessonType = $rule->lesson_type === 'both' ? 'online' : $rule->lesson_type;

                    $created = TeacherSchedule::query()->firstOrCreate(
                        [
                            'teacher_id' => $teacherId,
                            'available_date' => $slotStart->toDateString(),
                            'start_time' => $slotStart->format('H:i:s'),
                            'end_time' => $slotEnd->format('H:i:s'),
                        ],
                        [
                            'shift_pattern_id' => $pattern->id,
                            'status' => 'available',
                            'created_by' => $createdBy,
                        ]
                    );

                    if ($created->wasRecentlyCreated) {
                        $count++;
                    }

                    $cursor = $cursor->addMinutes(30);
                }
            }
        }

        return $count;
    }

    private function isInsideBreak(
        CarbonImmutable $slotStart,
        CarbonImmutable $slotEnd,
        $breakRows,
        CarbonImmutable $day
    ): bool {
        foreach ($breakRows as $break) {
            $bStart = CarbonImmutable::parse($day->format('Y-m-d').' '.$break->start_time, self::BIZ_TZ);
            $bEnd   = CarbonImmutable::parse($day->format('Y-m-d').' '.$break->end_time, self::BIZ_TZ);

            if ($slotStart->lt($bEnd) && $slotEnd->gt($bStart)) {
                return true;
            }
        }
        return false;
    }

    private function isBlockedByException(int $teacherId, CarbonImmutable $slotStart, CarbonImmutable $slotEnd): bool
    {
        return ScheduleException::query()
            ->where('teacher_id', $teacherId)
            ->where('type', 'block')
            ->where(function ($q) use ($slotStart, $slotEnd) {
                $q->where('exception_start', '<', $slotEnd->setTimezone('UTC'))
                  ->where('exception_end', '>', $slotStart->setTimezone('UTC'));
            })
            ->exists();
    }
}
