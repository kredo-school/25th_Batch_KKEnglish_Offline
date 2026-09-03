<?php

use App\Models\Role;
use App\Models\User;

if (! function_exists('actingAsAdminUser')) {
    /**
     * role_code=admin のユーザーを作成して actingAs した状態を返す。
     *
     * @param \Tests\TestCase $testCase
     * @return \App\Models\User
     */
    function actingAsAdminUser(\Tests\TestCase $testCase): User
    {
        $adminRole = Role::query()->firstOrCreate(
            ['role_code' => 'admin'],
            ['role_name' => 'admin']
        );

        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $testCase->actingAs($user);

        return $user;
    }
}

if (! function_exists('createUserWithRoleCode')) {
    /**
     * 任意の role_code を持つユーザーを作成して返す（actingAs はしない）
     *
     * @param string $roleCode
     * @param string|null $roleName
     * @return \App\Models\User
     */
    function createUserWithRoleCode(string $roleCode, ?string $roleName = null): User
    {
        $normalizedRoleName = $roleName ?? strtolower($roleCode);

        $role = Role::query()->firstOrCreate(
            ['role_code' => $roleCode],
            ['role_name' => $normalizedRoleName]
        );

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
