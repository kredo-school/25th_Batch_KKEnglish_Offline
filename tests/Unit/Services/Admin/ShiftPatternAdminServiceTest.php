<?php

namespace Tests\Unit\Services\Admin;

use App\Models\Role;
use App\Models\User;
use App\Services\Admin\ShiftPatternAdminService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShiftPatternAdminServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShiftPatternAdminService $service;

    protected function setUp(): void
{
    parent::setUp();

    \Illuminate\Support\Facades\DB::listen(function ($query) {
        if (str_contains($query->sql, 'insert into "roles"')) {
            dump('ROLES INSERT SQL: '.$query->sql, $query->bindings);
            dump((new \Exception('trace'))->getTraceAsString());
        }
    });

    $this->service = app(\App\Services\Admin\ShiftPatternAdminService::class);
}

    #[Test]
    public function breaksがrules外なら例外(): void
    {
        $adminRole = \App\Models\Role::query()->firstOrCreate(
    ['role_code' => 'admin'],
    ['role_name' => 'admin']
);

$actor = \App\Models\User::query()->create([
    'first_name' => 'Test',
    'last_name' => 'Admin',
    'email' => 'tester'.uniqid().'@example.com',
    'password' => bcrypt('password'),
    'role_id' => $adminRole->id,
    // 必要なら以下も（スキーマ次第）
    'nationality' => 'JP',
    'gender' => 'other',
    'status' => 'active',
    'email_verified_at' => now(),
]);

        $payload = [
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
                ['weekday' => 1, 'start_time' => '10:00', 'end_time' => '10:30', 'reason' => 'out'],
            ],
        ];

        $this->expectException(\DomainException::class);
        $this->service->upsert($payload, null, $actor->id); // ← fixed
    }

    #[Test]
    public function in_person開始が00分以外なら例外(): void
    {
        $adminRole = \App\Models\Role::query()->firstOrCreate(
    ['role_code' => 'admin'],
    ['role_name' => 'admin']
);

$actor = \App\Models\User::query()->create([
    'first_name' => 'Test',
    'last_name' => 'Admin',
    'email' => 'tester'.uniqid().'@example.com',
    'password' => bcrypt('password'),
    'role_id' => $adminRole->id,
    // 必要なら以下も（スキーマ次第）
    'nationality' => 'JP',
    'gender' => 'other',
    'status' => 'active',
    'email_verified_at' => now(),
]);
        $payload = [
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
        ];

        $this->expectException(\DomainException::class);
        $this->service->upsert($payload, null, $actor->id); // ← fixed
    }
}
