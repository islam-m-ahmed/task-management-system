<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permission definitions for each role.
     */
    private const ROLE_PERMISSIONS = [
        'manager' => [
            'task:view-all',
            'task:create',
            'task:update',
            'task:update-status',
            'task:assign',
        ],
        'user' => [
            'task:view-assigned',
            'task:update-status',
        ],
    ];

    /**
     * User-to-role mapping by email.
     */
    private const USER_ROLES = [
        'manager@example.com' => 'manager',
        'jane.manager@example.com' => 'manager',
        'user@example.com' => 'user',
        'alice.user@example.com' => 'user',
        'charlie.user@example.com' => 'user',
    ];

    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();
        $this->assignPermissionsToRoles();
        $this->assignRolesToUsers();
    }

    /**
     * Create all permissions.
     */
    private function createPermissions(): void
    {
        $allPermissions = collect(self::ROLE_PERMISSIONS)->flatten()->unique();

        foreach ($allPermissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Create all roles.
     */
    private function createRoles(): void
    {
        foreach (array_keys(self::ROLE_PERMISSIONS) as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            if (!$role)
                continue;

            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $role->id,
                ]);
            }
        }
    }

    /**
     * Assign roles to users.
     */
    private function assignRolesToUsers(): void
    {
        foreach (self::USER_ROLES as $email => $roleName) {
            $user = DB::table('users')->where('email', $email)->first();
            $role = DB::table('roles')->where('name', $roleName)->first();

            if ($user && $role) {
                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $role->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ]);
            }
        }
    }
}
