<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo permissions cho Users
        $userPermissions = [
            'view_any_users',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'verify_user_email',
            'reset_user_password',
            'toggle_user_status',
            'view_user_downloads',
            'view_user_favorites',
            'view_user_ratings',
            'view_user_comments',
        ];

        // Tạo permissions nếu chưa tồn tại
        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        $this->command->info('Đã tạo ' . count($userPermissions) . ' permissions cho Users');

        // Gán permissions cho các roles hiện có
        $this->assignPermissionsToRoles($userPermissions);
    }

    private function assignPermissionsToRoles(array $userPermissions): void
    {
        // Super Admin - có tất cả permissions
        $superAdmin = Role::where('name', 'Super Admin')->where('guard_name', 'admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($userPermissions);
            $this->command->info('Đã gán tất cả user permissions cho Super Admin');
        }

        // Admin - có hầu hết permissions trừ delete
        $admin = Role::where('name', 'Admin')->where('guard_name', 'admin')->first();
        if ($admin) {
            $adminPermissions = [
                'view_any_users',
                'view_users',
                'create_users',
                'update_users',
                'verify_user_email',
                'reset_user_password',
                'toggle_user_status',
                'view_user_downloads',
                'view_user_favorites',
                'view_user_ratings',
                'view_user_comments',
            ];
            $admin->givePermissionTo($adminPermissions);
            $this->command->info('Đã gán user permissions cho Admin (trừ delete)');
        }

        // Editor - chỉ có permissions xem
        $editor = Role::where('name', 'Editor')->where('guard_name', 'admin')->first();
        if ($editor) {
            $editorPermissions = [
                'view_any_users',
                'view_users',
                'view_user_downloads',
                'view_user_favorites',
                'view_user_ratings',
                'view_user_comments',
            ];
            $editor->givePermissionTo($editorPermissions);
            $this->command->info('Đã gán user permissions cho Editor (chỉ xem)');
        }

        // Moderator (nếu có) - permissions trung bình
        $moderator = Role::where('name', 'Moderator')->where('guard_name', 'admin')->first();
        if ($moderator) {
            $moderatorPermissions = [
                'view_any_users',
                'view_users',
                'update_users',
                'toggle_user_status',
                'view_user_downloads',
                'view_user_favorites',
                'view_user_ratings',
                'view_user_comments',
            ];
            $moderator->givePermissionTo($moderatorPermissions);
            $this->command->info('Đã gán user permissions cho Moderator');
        }

        // Nếu không có role nào, tạo thông báo
        if (!$superAdmin && !$admin && !$editor) {
            $this->command->warn('Không tìm thấy roles nào. Vui lòng chạy RolePermissionSeeder trước.');
            $this->command->info('Hoặc gán permissions thủ công cho các admin users.');
        }
    }
}