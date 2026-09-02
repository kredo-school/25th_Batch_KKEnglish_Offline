<?php

namespace Tests\Feature\Admin;

use App\Models\ShiftPattern;
use App\Models\ShiftPatternBreak;
use App\Models\ShiftPatternRelated;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\TeacherShiftAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftPatternAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 重複割当でreplace_overlapping_falseなら失敗する(): void
    {
        $adminRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'admin',
    'role_code' => 'admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

$studentRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'student',
    'role_code' => 'student',
    'created_at' => now(),
    'updated_at' => now(),
]);

        $adminRole = Role::query()->firstOrCreate(
    ['role_code' => 'admin'],
    ['role_name' => 'admin']
);

$studentRole = Role::query()->firstOrCreate(
    ['role_code' => 'student'],
    ['role_name' => 'student']
);

$admin = User::factory()->for($adminRole, 'role')->create();
$teacherUser = User::factory()->for($studentRole, 'role')->create();
$teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $patternA = ShiftPattern::factory()->create(['is_active' => true]);
        $patternB = ShiftPattern::factory()->create(['is_active' => true]);

        TeacherShiftAssignment::query()->create([
            'teacher_id' => $teacher->id,
            'shift_pattern_id' => $patternA->id,
            'effective_from' => '2026-09-01',
            'effective_to' => '2026-09-30',
            'priority' => 0,
        ]);

        $payload = [
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $patternB->id,
            'effective_from' => '2026-09-15',
            'effective_to' => '2026-10-15',
            'priority' => 0,
            'replace_overlapping' => 0,
        ];

        $res = $this->actingAs($admin)->post(route('admin.shift-pattern-assignments.store'), $payload);

        $res->assertStatus(302);
        $this->assertDatabaseCount('teacher_shift_assignments', 1);
    }

    /** @test */
    public function 正常割当でスロットが生成される(): void
    {
        $adminRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'admin',
    'role_code' => 'admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

$studentRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'student',
    'role_code' => 'student',
    'created_at' => now(),
    'updated_at' => now(),
]);

        $adminRole = Role::query()->firstOrCreate(
    ['role_code' => 'admin'],
    ['role_name' => 'admin']
);

$studentRole = Role::query()->firstOrCreate(
    ['role_code' => 'student'],
    ['role_name' => 'student']
);

$admin = User::factory()->for($adminRole, 'role')->create();
$teacherUser = User::factory()->for($studentRole, 'role')->create();
$teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $pattern = ShiftPattern::factory()->create([
            'is_active' => true,
            'slot_minutes' => 30,
        ]);

        ShiftPatternRelated::query()->create([
            'shift_pattern_id' => $pattern->id,
            'weekday' => 1, // Monday
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'lesson_type' => 'online',
        ]);

        $payload = [
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $pattern->id,
            'effective_from' => '2026-09-07', // Monday
            'effective_to' => '2026-09-07',
            'priority' => 0,
            'replace_overlapping' => 0,
        ];

        $res = $this->actingAs($admin)->post(route('admin.shift-pattern-assignments.store'), $payload);
        $res->assertStatus(302);

        $this->assertDatabaseHas('teacher_shift_assignments', [
            'teacher_id' => $teacher->id,
            'shift_pattern_id' => $pattern->id,
        ]);

        $this->assertSame(2, TeacherSchedule::query()->where('teacher_id', $teacher->id)->count());
    }

    /** @test */
    public function break時間帯のスロットは生成されない(): void
    {
        $adminRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'admin',
    'role_code' => 'admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

$studentRoleId = \DB::table('roles')->insertGetId([
    'role_name' => 'student',
    'role_code' => 'student',
    'created_at' => now(),
    'updated_at' => now(),
]);

        $adminRole = Role::firstOrCreate(
    ['role_code' => 'admin'],
    ['role_name' => 'admin']
);

$studentRole = Role::query()->firstOrCreate(
    ['role_code' => 'student'],
    ['role_name' => 'student']
);

$admin = User::factory()->for($adminRole, 'role')->create();
$teacherUser = User::factory()->for($studentRole, 'role')->create();
$teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $pattern = ShiftPattern::factory()->create([
            'is_active' => true,
            'slot_minutes' => 30,
        ]);

        ShiftPatternRelated::query()->create([
            'shift_pattern_id' => $pattern->id,
            'weekday' => 2, // Tuesday
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'lesson_type' => 'online',
        ]);

        ShiftPatternBreak::query()->create([
            'shift_pattern_id' => $pattern->id,
            'weekday' => 2,
            'start_time' => '09:30:00',
            'end_time' => '10:30:00',
            'reason' => 'break',
        ]);

        $payload = [
            'teacher_ids' => [$teacher->id],
            'shift_pattern_id' => $pattern->id,
            'effective_from' => '2026-09-08', // Tuesday
            'effective_to' => '2026-09-08',
            'priority' => 0,
            'replace_overlapping' => 0,
        ];

        $res = $this->actingAs($admin)->post(route('admin.shift-pattern-assignments.store'), $payload);
        $res->assertStatus(302);

        $slots = TeacherSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->orderBy('start_time')
            ->get(['start_time', 'end_time'])
            ->map(fn ($r) => [$r->start_time, $r->end_time])
            ->toArray();

        $this->assertCount(2, $slots);
        $this->assertSame(['09:00:00', '09:30:00'], $slots[0]);
        $this->assertSame(['10:30:00', '11:00:00'], $slots[1]);
    }
}
