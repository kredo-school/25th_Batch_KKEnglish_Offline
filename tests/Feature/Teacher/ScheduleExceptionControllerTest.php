<?php

namespace Tests\Feature\Teacher;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleExceptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();
    $this->withoutMiddleware(\App\Http\Middleware\RoleMiddleware::class);
}

    public function test_teacher_can_get_confirmed_schedules(): void
    {
        $teacherRole = \App\Models\Role::firstOrCreate(
    ['role_code' => 'teacher'], // ← ここも期待値に合わせる
    ['role_name' => 'teacher']
);
        $user = \App\Models\User::factory()->create([
    'role_id' => $teacherRole->id,
]);
$teacher = \App\Models\Teacher::factory()->create(['user_id' => $user->id]);

$this->actingAs($user);

        $response = $this
            ->actingAs($teacher->user)
            ->getJson(route('teachers.schedule-exceptions.index'));

        $response->dump();

        $response->assertOk();

        $response->assertJsonStructure([
            'schedules',
            'exception_types',
        ]);
    }
}
