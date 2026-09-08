<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeacherScheduleGenerationService
{
    /**
     * 今日から28日後までの teacher_schedules を再生成
     * - 対象: teacher_shift_pattern_assignments
     * - 反映: shift_patterns, shift_pattern_breaks
     */
    public function regenerateNext28Days(int $createdBy, ?array $teacherIds = null): void
    {
        $from = now()->startOfDay()->toDateString();
        $to   = now()->addDays(28)->endOfDay()->toDateString();

        $assignments = DB::table('teacher_shift_pattern_assignments as a')
            ->join('shift_patterns as p', 'p.id', '=', 'a.shift_pattern_id')
            ->select([
                'a.id as assignment_id',
                'a.teacher_id',
                'a.shift_pattern_id',
                'a.weekday',
                'a.start_date',
                'a.end_date',
                'a.priority',
                'p.start_time as pattern_start',
                'p.end_time as pattern_end',
                'p.end_day_offset',
                'p.is_active',
            ])
            ->when($teacherIds && count($teacherIds) > 0, fn($q) => $q->whereIn('a.teacher_id', $teacherIds))
            ->whereDate('a.start_date', '<=', $to)
            ->where(function ($q) use ($from) {
                $q->whereNull('a.end_date')->orWhereDate('a.end_date', '>=', $from);
            })
            ->where('p.is_active', 1)
            ->orderByDesc('a.priority')
            ->get();

        if ($assignments->isEmpty()) {
            return;
        }

        $patternIds = $assignments->pluck('shift_pattern_id')->unique()->values();

        $breaksByPattern = DB::table('shift_pattern_breaks')
            ->whereIn('shift_pattern_id', $patternIds)
            ->orderBy('start_time')
            ->get()
            ->groupBy('shift_pattern_id');

        DB::transaction(function () use ($assignments, $breaksByPattern, $from, $to) {
            $teacherIds = $assignments->pluck('teacher_id')->unique()->values();

            DB::table('teacher_schedules')
                ->whereIn('teacher_id', $teacherIds)
                ->whereBetween('available_date', [$from, $to])
                ->delete();

            $rows = [];
            $periodStart = Carbon::parse($from);
            $periodEnd   = Carbon::parse($to);

            foreach ($assignments as $a) {
                $start = Carbon::parse(max($from, $a->start_date))->startOfDay();
                $end   = $a->end_date ? Carbon::parse(min($to, $a->end_date))->endOfDay() : $periodEnd->copy()->endOfDay();

                for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                    if ((int)$d->dayOfWeek !== (int)$a->weekday) {
                        continue;
                    }

                    $segments = $this->splitByBreaks(
                        $a->pattern_start,
                        $a->pattern_end,
                        (int)$a->end_day_offset,
                        $breaksByPattern->get($a->shift_pattern_id, collect())
                    );

                    foreach ($segments as $seg) {
                        $rows[] = [
                            'teacher_id'        => $a->teacher_id,
                            'shift_pattern_id'  => $a->shift_pattern_id,
                            'available_date'    => $d->toDateString(),
                            'start_time'        => $seg['start_time'],
                            'end_time'          => $seg['end_time'],
                            'status'            => 'draft',       // NOT NULL
                            'created_by'        => $createdBy,    // NOT NULL
                            'updated_at'        => now(),
                            'created_at'        => now(),
                        ];
                    }
                }
            }

            if (!empty($rows)) {
                DB::table('teacher_schedules')->insert($rows);
            }
        });
    }

    /**
     * パターン時間を休憩で分割して返す
     */
    private function splitByBreaks(string $workStart, string $workEnd, int $endDayOffset, Collection $breaks): array
    {
        $base = '2000-01-01 ';
        $ws = strtotime($base . $workStart);
        $we = strtotime($base . $workEnd) + ($endDayOffset ? 86400 : 0);

        $result = [['s' => $ws, 'e' => $we]];

        foreach ($breaks as $b) {
            if (empty($b->start_time) || empty($b->end_time)) {
                continue;
            }

            $bs = strtotime($base . $b->start_time);
            $be = strtotime($base . $b->end_time);

            $next = [];
            foreach ($result as $seg) {
                // 重なりなし
                if ($be <= $seg['s'] || $bs >= $seg['e']) {
                    $next[] = $seg;
                    continue;
                }
                // 前半
                if ($bs > $seg['s']) {
                    $next[] = ['s' => $seg['s'], 'e' => $bs];
                }
                // 後半
                if ($be < $seg['e']) {
                    $next[] = ['s' => $be, 'e' => $seg['e']];
                }
            }
            $result = $next;
        }

        return collect($result)
            ->filter(fn($x) => $x['e'] > $x['s'])
            ->map(fn($x) => [
                'start_time' => date('H:i:s', $x['s']),
                'end_time'   => date('H:i:s', $x['e']),
            ])
            ->values()
            ->all();
    }
}
