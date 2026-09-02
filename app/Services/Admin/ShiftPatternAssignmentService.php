<?php

namespace App\Services\Admin;

use App\Models\ShiftPattern;
use App\Models\TeacherShiftAssignment;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class ShiftPatternAssignmentService
{
    public function __construct(
        private readonly GenerateTeacherSchedulesService $generator
    ) {}

    /**
     * @param array{
     *   teacher_ids: array<int>,
     *   shift_pattern_id: int,
     *   effective_from: string,
     *   effective_to?: string|null,
     *   priority?: int|null,
     *   replace_overlapping?: bool|null
     * } $data
     * @return array{created_assignments:int, generated_slots:int}
     */
    public function assign(array $data, int $actorUserId): array
    {
        $teacherIds = $data['teacher_ids'];
        $patternId = (int) $data['shift_pattern_id'];
        $from = Carbon::parse($data['effective_from'])->toDateString();
        $to = isset($data['effective_to']) ? Carbon::parse($data['effective_to'])->toDateString() : null;
        $priority = (int)($data['priority'] ?? 0);
        $replace = (bool)($data['replace_overlapping'] ?? false);

        $pattern = ShiftPattern::query()->findOrFail($patternId);

        // ここでトランザクション実行（割当とスロット生成を一体化）
        return DB::transaction(function () use ($actorUserId, $teacherIds, $patternId, $pattern, $from, $to, $priority, $replace) {
            $createdAssignments = 0;
            $generatedSlots = 0;

            foreach ($teacherIds as $teacherId) {
                $overlapQuery = TeacherShiftAssignment::query()
                    ->where('teacher_id', $teacherId)
                    ->where(function ($q) use ($from, $to) {
                        // 期間重複判定
                        $q->where(function ($qq) use ($from, $to) {
                            $qq->where('effective_from', '<=', $to ?? '9999-12-31')
                               ->where(function ($qqq) use ($from) {
                                   $qqq->whereNull('effective_to')
                                       ->orWhere('effective_to', '>=', $from);
                               });
                        });
                    });

                $hasOverlap = $overlapQuery->exists();

                // 重複あり + 置換しない場合はエラー
                if ($hasOverlap && !$replace) {
                    throw new DomainException("teacher_id={$teacherId} に期間重複する既存割当があります。");
                }

                if ($hasOverlap && $replace) {
                    // シンプルに重複分を削除して再登録
                    $overlapQuery->delete();
                }

                TeacherShiftAssignment::query()->create([
                    'teacher_id' => $teacherId,
                    'shift_pattern_id' => $patternId,
                    'effective_from' => $from,
                    'effective_to' => $to,
                    'priority' => $priority,
                ]);

                $createdAssignments++;

                // ここで実スロットへ反映
                $generatedSlots += $this->generator->generate(
                    teacherId: (int)$teacherId,
                    pattern: $pattern,
                    effectiveFrom: $from,
                    effectiveTo: $to,
                    createdBy: $actorUserId // ここを渡す
                );
            }

            return [
                'created_assignments' => $createdAssignments,
                'generated_slots' => $generatedSlots,
            ];
        });
    }
}
