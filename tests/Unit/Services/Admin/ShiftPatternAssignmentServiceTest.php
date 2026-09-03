<?php

namespace Tests\Unit\Services\Admin;

use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherShiftAssignment;
use App\Services\Admin\GenerateTeacherSchedulesService;
use App\Services\Admin\ShiftPatternAssignmentService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Tests\TestCase;

class ShiftPatternAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 重複時replace_falseで例外(): void
    {
        $teacher = Teacher::factory()->create();
        $patternA = ShiftPattern::factory()->create();
        $patternB = ShiftPattern::factory()->create();

        TeacherShiftAssignment::query()->create([
            'teacher_id' => $teacher->id,
            'shift_pattern_id' => $patternA->id,
            'effective_from' => '2026-09-01',
            'effective_to' => '2026-09-30',
            'priority' => 0,
        ]);

        $generator = Mockery::mock(GenerateTeacherSchedulesService::class);
        $service = new ShiftPatternAssignmentService($generator);

        $this->expectException(DomainException::class);

        $service->assign([
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $patternB->id,
            'effective_from' => '2026-09-10',
            'effective_to' => '2026-10-10',
            'replace_overlapping' => false,
            'priority' => 0,
        ], 99);
    }

    #[Test]
    public function 正常時は割当作成と生成件数集計(): void
    {
        $teacher = Teacher::factory()->create();
        $pattern = ShiftPattern::factory()->create();

        $generator = Mockery::mock(GenerateTeacherSchedulesService::class);
        $generator->shouldReceive('generate')->once()->andReturn(5);

        $service = new ShiftPatternAssignmentService($generator);

        $result = $service->assign([
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $pattern->id,
            'effective_from' => '2026-09-10',
            'effective_to' => '2026-09-12',
            'replace_overlapping' => false,
            'priority' => 0,
        ], 99);

        $this->assertSame(1, $result['created_assignments']);
        $this->assertSame(5, $result['generated_slots']);
    }
}
