<?php

namespace Tests\Unit\Services\Admin;

use App\Models\ShiftPattern;
use App\Services\Admin\ShiftPatternAdminService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftPatternAdminServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function breaksがrules外なら例外(): void
    {
        $service = app(ShiftPatternAdminService::class);

        $this->expectException(DomainException::class);

        $service->upsert([
            'pattern_code' => 'P001',
            'pattern_name' => 'Test',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'end_day_offset' => 0,
            'slot_minutes' => 30,
            'is_active' => true,
            'display_order' => 0,
            'rules' => [
                ['weekday' => 1, 'start_time' => '09:00', 'end_time' => '10:00', 'lesson_type' => 'online'],
            ],
            'breaks' => [
                ['weekday' => 1, 'start_time' => '10:00', 'end_time' => '10:30', 'reason' => 'out'], // 勤務外
            ],
        ], null, 1);
    }

    /** @test */
    public function in_person開始が00分以外なら例外(): void
    {
        $service = app(ShiftPatternAdminService::class);

        $this->expectException(DomainException::class);

        $service->upsert([
            'pattern_code' => 'P002',
            'pattern_name' => 'Test2',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'end_day_offset' => 0,
            'slot_minutes' => 60,
            'is_active' => true,
            'display_order' => 0,
            'rules' => [
                ['weekday' => 1, 'start_time' => '09:30', 'end_time' => '10:30', 'lesson_type' => 'in_person'],
            ],
            'breaks' => [],
        ], null, 1);
    }
}
