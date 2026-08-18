<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define and Create Permissions
        $permissions = [
            'manage users',
            'manage posts',
            'create posts',
            'edit own posts',
            'manage comments',
            'manage media',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create Roles and Assign Corresponding Permissions

        // Role: Admin (Super Administrator with full permissions)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Role: Editor (Content management, moderation, media)
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->syncPermissions([
            'manage posts',
            'create posts',
            'edit own posts',
            'manage comments',
            'manage media',
        ]);

        // Role: Author (Create/edit own posts, upload media)
        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $authorRole->syncPermissions([
            'create posts',
            'edit own posts',
            'manage media',
        ]);

        // Role: User (Standard visitor / commenter)
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([]);
    }
}
