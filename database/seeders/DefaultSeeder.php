<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء الأدوار أولاً في جدول roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // 2. إنشاء المستخدمين وتعيين الأدوار لهم

        // مسؤول النظام (Admin)
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'System Admin',
                'phone'    => '01000000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // بائع (Vendor)
        $vendor = User::updateOrCreate(
            ['email' => 'vendor@admin.com'],
            [
                'name'     => 'Sample Vendor',
                'phone'    => '01000000002',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $vendor->assignRole($vendorRole);

        // عميل (Customer)
        $customer = User::updateOrCreate(
            ['email' => 'customer@admin.com'],
            [
                'name'     => 'Standard Customer',
                'phone'    => '01000000003',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole($customerRole);
    }
}
