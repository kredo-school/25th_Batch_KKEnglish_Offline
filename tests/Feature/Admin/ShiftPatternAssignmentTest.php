<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\ShiftPattern;
use App\Models\ShiftPatternBreak;
use App\Models\ShiftPatternRelated;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\TeacherShiftPatternAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShiftPatternAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\RoleMiddleware::class);
    }

    #[Test]
    public function 先生割当一覧を表示できる(): void
    {
        $adminRole = Role::firstOrCreate(
            ['role_code' => 'admin'],
            ['role_name' => 'admin']
        );

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.shift-pattern-assignments.index'));

        $res->assertStatus(200);
        $res->assertViewIs('admin.shift-pattern-assignments.index');
    }

    #[Test]
    public function 重複割当でreplace_overlapping_falseなら失敗する(): void
    {
        $adminRole = Role::firstOrCreate(
            ['role_code' => 'admin'],
            ['role_name' => 'admin']
        );

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $teacherUser = User::factory()->student()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $patternA = ShiftPattern::factory()->create(['is_active' => true]);
        $patternB = ShiftPattern::factory()->create(['is_active' => true]);

        TeacherShiftPatternAssignment::query()->create([
            'teacher_id' => $teacher->id,
            'shift_pattern_id' => $patternA->id,
            'weekday' => 1,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'priority' => 0,
        ]);

        $payload = [
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $patternB->id,
            'weekdays' => [1],
            'start_date' => '2026-09-15',
            'end_date' => '2026-10-15',
            'priority' => 0,
            'replace_overlapping' => 0,
        ];

        $res = $this->actingAs($admin)->post(route('admin.shift-pattern-assignments.store'), $payload);

        $res->assertStatus(302);
        $this->assertDatabaseCount('teacher_shift_pattern_assignments', 1);
    }

    #[Test]
    public function 正常割当で先生割当レコードが生成される(): void
    {
        $adminRole = Role::firstOrCreate(
            ['role_code' => 'admin'],
            ['role_name' => 'admin']
        );

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $teacherUser = User::factory()->student()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $pattern = ShiftPattern::factory()->create([
            'is_active' => true,
            'slot_minutes' => 30,
        ]);

        // ShiftPatternRelated::query()->create([
        //     'shift_pattern_id' => $pattern->id,
        //     'weekday' => 1, // Monday
        //     'start_time' => '09:00:00',
        //     'end_time' => '10:00:00',
        //     'lesson_type' => 'online',
        // ]);

        $payload = [
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $pattern->id,
            'weekdays' => [1],
            'start_date' => '2026-09-07', // Monday
            'end_date' => '2026-09-07',
            'priority' => 0,
            'replace_overlapping' => 0,
        ];

        $res = $this->actingAs($admin)->post(route('admin.shift-pattern-assignments.store'), $payload);
        $res->assertStatus(302);

        $this->assertDatabaseHas('teacher_shift_pattern_assignments', [
            'teacher_id' => $teacher->id,
            'shift_pattern_id' => $pattern->id,
            'weekday' => 1,
        ]);
    }
}
