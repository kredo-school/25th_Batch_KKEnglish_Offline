<?php

/**
 * ================================================================
 * 【このファイルの役割】
 * TeacherScheduleGenerationService.php
 * ================================================================
 *
 * Shift Pattern と Teacher Shift Pattern Assignment をもとに、
 * teacher_schedules に「実際に勤務可能な時間帯」を登録するサービスです。
 *
 * 重要な考え方
 * 1. shift_patterns       = 勤務時間の基本設定
 * 2. shift_pattern_breaks = 休憩時間
 * 3. assignments          = その勤務パターンを誰に、何曜日、何日から何日まで適用するか
 * 4. teacher_schedules    = 最終的に画面表示や予約計算で使う実際の勤務可能時間
 *
 * 例：09:00～18:00、休憩13:00～14:00 の場合
 * teacher_schedules には
 *   09:00～13:00
 *   14:00～18:00
 * の2行を作ります。
 *
 * 【重複時間について】
 * 同じ先生に複数のシフトが登録され、時間が重なっている場合、
 * regenerateWindow() では一度候補を集めて mergePeriods() で重複をまとめます。
 * そのため、同じ時間帯をダブルで生成しないようにしています。
 *
 * 【注意】
 * このファイルは「予約枠そのもの」を作るものではありません。
 * slot_minutes は teacher_schedules の行を30分単位などの予約枠へ変換するときに使います。
 * ================================================================
 */

namespace App\Services\Admin;

use App\Models\ShiftPattern;
use App\Models\TeacherSchedule;
use App\Models\TeacherShiftPatternAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TeacherScheduleGenerationService
{
    // 今日を含めて28日分を自動生成します。
    // 例：今日が1日なら、1日～28日までです。
    private const WINDOW_DAYS = 28;

    /**
     * 1件の Assignment だけを対象に勤務時間を生成します。
     *
     * 主に「特定の割当だけ生成したい」ときに使う処理です。
     * 複数の割当をまとめて処理するときは regenerateWindow() を使います。
     */
    public function generateFromAssignment(
        TeacherShiftPatternAssignment $assignment,
        int $createdBy
    ): array {
        // Assignment に紐づく Shift Pattern を取得します。
        // breaks も必要なので、未ロードなら後で読み込みます。
        $pattern = $assignment->shiftPattern ?? ShiftPattern::with('breaks')->find($assignment->shift_pattern_id);

        if (!$pattern || !$pattern->is_active) {
            return ['generated' => 0, 'skipped' => 0];
        }

        if (!$pattern->relationLoaded('breaks')) {
            $pattern->load('breaks');
        }

        // 「今日から28日間」が今回の生成対象です。
        $today = Carbon::today();
        $windowEnd = $today->copy()->addDays(self::WINDOW_DAYS - 1);
        $from = Carbon::parse($assignment->start_date)->greaterThan($today)
            ? Carbon::parse($assignment->start_date)
            : $today;
        $assignmentEnd = $assignment->end_date ? Carbon::parse($assignment->end_date) : null;
        $to = $assignmentEnd && $assignmentEnd->lessThan($windowEnd) ? $assignmentEnd : $windowEnd;

        if ($from->greaterThan($to)) {
            return ['generated' => 0, 'skipped' => 0];
        }

        $generated = 0;
        $skipped = 0;

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            if ($date->dayOfWeek !== (int) $assignment->weekday) {
                continue;
            }

            // 休憩を除いた「勤務可能な時間帯」を作ります。
            // 09-18 + 13-14休憩なら 09-13 / 14-18 になります。
            foreach ($this->buildPeriods($date, $pattern) as $period) {
                // まったく同じ teacher / date / start / end が既にあれば二重登録しません。
                $exists = TeacherSchedule::query()
                    ->where('teacher_id', $assignment->teacher_id)
                    ->where('available_date', $date->toDateString())
                    ->where('start_time', $period['start']->format('H:i:s'))
                    ->where('end_time', $period['end']->format('H:i:s'))
                    ->whereNotIn('status', ['cancelled'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                TeacherSchedule::create([
                    'teacher_id' => $assignment->teacher_id,
                    'shift_pattern_id' => $pattern->id,
                    'available_date' => $date->toDateString(),
                    'start_time' => $period['start']->format('H:i:s'),
                    'end_time' => $period['end']->format('H:i:s'),
                    'status' => 'confirmed',
                    'created_by' => $createdBy,
                    'confirmed_by' => $createdBy,
                    'confirmed_at' => now(),
                ]);

                $generated++;
            }
        }

        return compact('generated', 'skipped');
    }

    /**
     * 現在有効な Assignment をまとめて取得し、28日分を再生成します。
     *
     * このメソッドでは、まず全候補を $candidates に集めます。
     * その後、先生＋日付ごとに mergePeriods() を実行して、
     * 重なった時間帯を1つにまとめてから teacher_schedules に保存します。
     */
    public function regenerateWindow(int $createdBy, ?array $teacherIds = null): array
    {
        $today = Carbon::today();
        $windowEnd = $today->copy()->addDays(self::WINDOW_DAYS - 1);

        $query = TeacherShiftPatternAssignment::with('shiftPattern.breaks')
            ->whereHas('shiftPattern', fn ($q) => $q->where('is_active', true))
            ->where('start_date', '<=', $windowEnd->toDateString())
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today->toDateString());
            })
            ->orderByDesc('priority')
            ->orderBy('id');

        if (!empty($teacherIds)) {
            $query->whereIn('teacher_id', $teacherIds);
        }

        // ここで対象となる Assignment を一括取得します。
        $assignments = $query->get();
        $candidates = [];

        foreach ($assignments as $assignment) {
            $pattern = $assignment->shiftPattern;
            if (!$pattern || !$pattern->is_active) {
                continue;
            }

            if (!$pattern->relationLoaded('breaks')) {
                $pattern->load('breaks');
            }

            $from = Carbon::parse($assignment->start_date)->greaterThan($today)
                ? Carbon::parse($assignment->start_date)
                : $today;
            $assignmentEnd = $assignment->end_date ? Carbon::parse($assignment->end_date) : null;
            $to = $assignmentEnd && $assignmentEnd->lessThan($windowEnd) ? $assignmentEnd : $windowEnd;

            for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
                if ($date->dayOfWeek !== (int) $assignment->weekday) {
                    continue;
                }

                $dayKey = $assignment->teacher_id . '_' . $date->toDateString();

                // 休憩を除いた勤務可能時間を候補として保存します。
                foreach ($this->buildPeriods($date, $pattern) as $period) {
                    $candidates[$dayKey][] = [
                        'teacher_id' => (int) $assignment->teacher_id,
                        'available_date' => $date->toDateString(),
                        'start' => $period['start']->copy(),
                        'end' => $period['end']->copy(),
                        'shift_pattern_id' => (int) $pattern->id,
                        'priority' => (int) ($assignment->priority ?? 0),
                    ];
                }
            }
        }

        // 既に登録済みの teacher_schedules を取得して、同じ行を作らないようにします。
        $existing = TeacherSchedule::query()
            ->whereBetween('available_date', [$today->toDateString(), $windowEnd->toDateString()])
            ->whereNotIn('status', ['cancelled'])
            ->get(['teacher_id', 'available_date', 'start_time', 'end_time']);

        $generated = 0;
        $skipped = 0;

        // 先生＋日付ごとに重複を解消してから登録します。
        foreach ($candidates as $dayCandidates) {
            foreach ($this->mergePeriods($dayCandidates) as $period) {
                $start = $period['start']->format('H:i:s');
                $end = $period['end']->format('H:i:s');

                $exists = $existing->contains(function ($row) use ($period, $start, $end) {
                    return (int) $row->teacher_id === $period['teacher_id']
                        && Carbon::parse($row->available_date)->format('Y-m-d') === $period['available_date']
                        && $row->start_time === $start
                        && $row->end_time === $end;
                });

                if ($exists) {
                    $skipped++;
                    continue;
                }

                try {
                    TeacherSchedule::create([
                        'teacher_id' => $period['teacher_id'],
                        'shift_pattern_id' => $period['shift_pattern_id'],
                        'available_date' => $period['available_date'],
                        'start_time' => $start,
                        'end_time' => $end,
                        'status' => 'confirmed',
                        'created_by' => $createdBy,
                        'confirmed_by' => $createdBy,
                        'confirmed_at' => now(),
                    ]);
                    $generated++;
                    $existing->push((object) [
                        'teacher_id' => $period['teacher_id'],
                        'available_date' => $period['available_date'],
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('TeacherSchedule insert skipped: ' . $e->getMessage());
                    $skipped++;
                }
            }
        }

        return [
            'assignments' => $assignments->count(),
            'generated' => $generated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Shift Pattern と Break から「勤務可能な区間」を作ります。
     *
     * ここでは slot_minutes は使いません。
     * teacher_schedules は「09:00～13:00」のような時間帯を保存する場所だからです。
     */
    private function buildPeriods(Carbon $date, ShiftPattern $pattern): array
    {
        $periods = [];
        $shiftStart = $date->copy()->setTimeFromTimeString($pattern->start_time);
        $shiftEnd = $date->copy()->addDays((int) $pattern->end_day_offset)->setTimeFromTimeString($pattern->end_time);
        $weekday = $date->dayOfWeek;
        $breaks = $pattern->breaks
            ->filter(fn ($break) => is_null($break->weekday) || (int) $break->weekday === $weekday)
            ->sortBy('start_time')
            ->values();

        $cursor = $shiftStart->copy();

        // 休憩時間のところを勤務可能時間から切り取ります。
        foreach ($breaks as $break) {
            $breakStart = $date->copy()->setTimeFromTimeString($break->start_time);
            $breakEnd = $date->copy()->setTimeFromTimeString($break->end_time);

            if ($breakEnd->lte($shiftStart) || $breakStart->gte($shiftEnd)) {
                continue;
            }

            $breakStart = $breakStart->lt($shiftStart) ? $shiftStart->copy() : $breakStart;
            $breakEnd = $breakEnd->gt($shiftEnd) ? $shiftEnd->copy() : $breakEnd;

            if ($cursor->lt($breakStart)) {
                $periods[] = ['start' => $cursor->copy(), 'end' => $breakStart->copy()];
            }

            if ($cursor->lt($breakEnd)) {
                $cursor = $breakEnd->copy();
            }
        }

        if ($cursor->lt($shiftEnd)) {
            $periods[] = ['start' => $cursor->copy(), 'end' => $shiftEnd->copy()];
        }

        return $periods;
    }

    /**
     * 同じ先生・同じ日付の勤務時間が重なった場合に1つへまとめます。
     *
     * 例：
     *   09:00～18:00
     *   13:00～17:00
     *
     * この2つを単純に足すと「26スロット」のような二重計算になります。
     * mergePeriods() では 09:00～18:00 の1区間にまとめます。
     *
     * priority が高い Assignment を優先して pattern_id を残します。
     * ただし異なる slot_minutes のパターンが重なる場合は、
     * システム側で「どの slot_minutes を採用するか」という業務ルールを
     * 別途決める必要があります。
     */
    private function mergePeriods(array $periods): array
    {
        usort($periods, function ($a, $b) {
            $cmp = $a['start']->timestamp <=> $b['start']->timestamp;
            return $cmp !== 0 ? $cmp : $b['priority'] <=> $a['priority'];
        });

        $merged = [];

        // 開始時刻の順番で比較しながら、前の区間と重なるか確認します。
        foreach ($periods as $period) {
            if (empty($merged)) {
                $merged[] = $period;
                continue;
            }

            $i = count($merged) - 1;
            if ($period['start']->lte($merged[$i]['end'])) {
                if ($period['end']->gt($merged[$i]['end'])) {
                    $merged[$i]['end'] = $period['end']->copy();
                }

                if ($period['start']->equalTo($merged[$i]['start']) && $period['priority'] > $merged[$i]['priority']) {
                    $merged[$i]['shift_pattern_id'] = $period['shift_pattern_id'];
                    $merged[$i]['priority'] = $period['priority'];
                }
            } else {
                $merged[] = $period;
            }
        }

        return $merged;
    }
}
