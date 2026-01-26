<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Permissions
        $this->createPermissions();

        // Create Roles and assign permissions
        $this->createRoles();

        // Create Admin User
        $this->createAdminUsers();

        \Log::info('✅ Database seeding completed successfully - Roles, Permissions and Users are ready!');
    }

    /**
     * Undo the seeder changes - used for rollback
     */
    public static function rollback(): void
    {
        \Log::info('⚠️ Starting RolesAndPermissionsSeeder rollback...');

        // Delete test users
        \App\Models\User::whereIn('email', [
            'superadmin@watchstore.com',
            'admin@watchstore.com',
            'moderator@watchstore.com',
        ])->delete();

        // Delete custom roles (but keep system defaults safe)
        Role::whereNotIn('name', ['super_admin', 'admin', 'moderator', 'vendor', 'customer'])
            ->delete();

        // Clear cache
        app()['cache']->forget('spatie.permission.cache');

        \Log::info('✅ Rollback completed - Database cleaned');
    }

    private function createPermissions(): void
    {
        // User Management Permissions
        $userPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_user_roles',
        ];

        // Store Management Permissions
        $storePermissions = [
            'view_stores',
            'create_stores',
            'edit_stores',
            'delete_stores',
            'manage_store_settings',
            'view_store_analytics',
        ];

        // Product Management Permissions
        $productPermissions = [
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_product_categories',
            'manage_product_variants',
            'manage_product_reviews',
        ];

        // Order Management Permissions
        $orderPermissions = [
            'view_orders',
            'edit_order_status',
            'refund_orders',
            'view_order_analytics',
        ];

        // Payment Management Permissions
        $paymentPermissions = [
            'view_payments',
            'manage_payment_gateways',
            'process_refunds',
        ];

        // Content Management Permissions
        $contentPermissions = [
            'manage_banners',
            'manage_brands',
            'manage_categories',
            'manage_attributes',
        ];

        // System Permissions
        $systemPermissions = [
            'access_admin_panel',
            'access_vendor_panel',
            'access_customer_panel',
            'manage_system_settings',
            'view_system_logs',
            'manage_permissions',
            'impersonate_users',
            'view_dashboard_analytics',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $storePermissions,
            $productPermissions,
            $orderPermissions,
            $paymentPermissions,
            $contentPermissions,
            $systemPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    private function createRoles(): void
    {
        // Create Super Admin Role with all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminPermissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_user_roles',
            // Store Management
            'view_stores',
            'create_stores',
            'edit_stores',
            'delete_stores',
            'manage_store_settings',
            'view_store_analytics',
            // Product Management
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_product_categories',
            'manage_product_variants',
            // Order Management
            'view_orders',
            'edit_order_status',
            'refund_orders',
            'view_order_analytics',
            // Content Management
            'manage_banners',
            'manage_brands',
            'manage_categories',
            'manage_attributes',
            // System
            'access_admin_panel',
            'view_system_logs',
            'view_dashboard_analytics',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Create Moderator Role
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $moderatorPermissions = [
            'view_users',
            'view_stores',
            'view_products',
            'view_orders',
            'edit_order_status',
            'manage_product_reviews',
            'access_admin_panel',
            'view_dashboard_analytics',
        ];
        $moderatorRole->syncPermissions($moderatorPermissions);

        // Create Vendor Role
        $vendorRole = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        $vendorPermissions = [
            'view_products',
            'create_products',
            'edit_products',
            'manage_product_categories',
            'manage_product_variants',
            'view_orders',
            'view_store_analytics',
            'manage_store_settings',
            'manage_banners',
            'access_vendor_panel',
        ];
        $vendorRole->syncPermissions($vendorPermissions);

        // Create Customer Role
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $customerPermissions = [
            'view_products',
            'access_customer_panel',
        ];
        $customerRole->syncPermissions($customerPermissions);
    }

    private function createAdminUsers(): void
    {
        $superAdminUser = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@watchstore.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@watchstore.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        $moderatorUser = \App\Models\User::firstOrCreate(
            ['email' => 'moderator@watchstore.com'],
            [
                'name' => 'Moderator',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $moderatorUser->assignRole('moderator');

        \Log::info('✅ Roles, Permissions and Admin Users created successfully!');
    }
}
