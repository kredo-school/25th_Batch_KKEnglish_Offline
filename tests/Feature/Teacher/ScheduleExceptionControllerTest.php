<?php

namespace Tests\Feature\Teacher;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleExceptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_get_confirmed_schedules(): void
    {
        $teacher = Teacher::query()
            ->with('user')
            ->firstOrFail();

        $response = $this
            ->actingAs($teacher->user)
            ->getJson('/teacher/schedule-exceptions');

        $response->dump();

        $response->assertOk();

        $response->assertJsonStructure([
            'schedules',
            'exception_types',
        ]);
    }
}
