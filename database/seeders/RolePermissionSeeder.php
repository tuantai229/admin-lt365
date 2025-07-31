<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\AdminUser;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo permissions
        $permissions = [
            // Content Management
            'view_any_documents', 'view_documents', 'create_documents', 'update_documents', 'delete_documents',
            'view_any_levels', 'view_levels', 'create_levels', 'update_levels', 'delete_levels',
            'view_any_subjects', 'view_subjects', 'create_subjects', 'update_subjects', 'delete_subjects',
            'view_any_document_types', 'view_document_types', 'create_document_types', 'update_document_types', 'delete_document_types',
            'view_any_difficulty_levels', 'view_difficulty_levels', 'create_difficulty_levels', 'update_difficulty_levels', 'delete_difficulty_levels',
            
            // School Management
            'view_any_schools', 'view_schools', 'create_schools', 'update_schools', 'delete_schools',
            'view_any_school_types', 'view_school_types', 'create_school_types', 'update_school_types', 'delete_school_types',
            
            // News Management
            'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news',
            'view_any_news_categories', 'view_news_categories', 'create_news_categories', 'update_news_categories', 'delete_news_categories',
            'view_any_pages', 'view_pages', 'create_pages', 'update_pages', 'delete_pages',
            
            // Teachers & Centers
            'view_any_teachers', 'view_teachers', 'create_teachers', 'update_teachers', 'delete_teachers',
            'view_any_centers', 'view_centers', 'create_centers', 'update_centers', 'delete_centers',
            
            // E-commerce
            'view_any_orders', 'view_orders', 'create_orders', 'update_orders', 'delete_orders',
            'view_any_user_downloads', 'view_user_downloads',
            
            // Interactions
            'view_any_comments', 'view_comments', 'update_comments', 'delete_comments',
            'view_any_ratings', 'view_ratings', 'update_ratings', 'delete_ratings',
            'view_any_contacts', 'view_contacts', 'update_contacts', 'delete_contacts',
            'view_any_newsletters', 'view_newsletters', 'delete_newsletters',
            
            // Others
            'view_any_tags', 'view_tags', 'create_tags', 'update_tags', 'delete_tags',
            'view_any_provinces', 'view_provinces', 'create_provinces', 'update_provinces', 'delete_provinces',
            'view_any_communes', 'view_communes', 'create_communes', 'update_communes', 'delete_communes',
            'view_any_menus', 'view_menus', 'create_menus', 'update_menus', 'delete_menus',
            
            // Admin Management (chỉ Super Admin)
            'view_any_admin_users', 'view_admin_users', 'create_admin_users', 'update_admin_users', 'delete_admin_users',
            'view_any_roles', 'view_roles', 'create_roles', 'update_roles', 'delete_roles',
            'view_any_permissions', 'view_permissions',
            'view_any_settings', 'view_settings', 'update_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Tạo roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);
        $moderator = Role::firstOrCreate(['name' => 'Moderator', 'guard_name' => 'admin']);

        // Gán quyền cho Super Admin (toàn quyền)
        $superAdmin->givePermissionTo(Permission::all());

        // Gán quyền cho Admin (trừ quản lý admin users và settings)
        $adminPermissions = Permission::whereNotIn('name', [
            'view_any_admin_users', 'view_admin_users', 'create_admin_users', 'update_admin_users', 'delete_admin_users',
            'view_any_roles', 'view_roles', 'create_roles', 'update_roles', 'delete_roles',
            'view_any_permissions', 'view_permissions',
            'view_any_settings', 'view_settings', 'update_settings',
        ])->pluck('name');
        $admin->givePermissionTo($adminPermissions);

        // Gán quyền cho Moderator (chỉ news, comments, contacts, newsletters)
        $moderatorPermissions = [
            'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news',
            'view_any_news_categories', 'view_news_categories', 'create_news_categories', 'update_news_categories', 'delete_news_categories',
            'view_any_pages', 'view_pages', 'create_pages', 'update_pages', 'delete_pages',
            'view_any_comments', 'view_comments', 'update_comments', 'delete_comments',
            'view_any_ratings', 'view_ratings', 'update_ratings', 'delete_ratings',
            'view_any_contacts', 'view_contacts', 'update_contacts', 'delete_contacts',
            'view_any_newsletters', 'view_newsletters', 'delete_newsletters',
            'view_any_tags', 'view_tags', 'create_tags', 'update_tags', 'delete_tags',
        ];
        $moderator->givePermissionTo($moderatorPermissions);

        // Gán role cho user id = 1
        $existingUser = AdminUser::find(1); // Lấy user có ID = 1
        if ($existingUser) {
            $existingUser->assignRole('Super Admin');
        }
    }
}
