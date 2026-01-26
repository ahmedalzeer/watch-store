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
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        $admin = User::updateOrCreate(
            ['email' => 'ahmedalzeer@admin.com'],
            [
                'name'     => 'System Admin',
                'phone'    => '01015258850',
                'password' => Hash::make('01015258850'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        for ($i = 1; $i <= 10; $i++) {
            $vendor = User::updateOrCreate(
                ['email' => "vendor{$i}@admin.com"],
                [
                    'name'     => "Sample Vendor {$i}",
                    'phone'    => '010000000' . (10 + $i),
                    'password' => Hash::make('01015258850'),
                    'email_verified_at' => now(),
                ]
            );
            $vendor->assignRole($vendorRole);
        }

        for ($i = 1; $i <= 10; $i++) {
            $customer = User::updateOrCreate(
                ['email' => "customer{$i}@admin.com"],
                [
                    'name'     => "Sample Customer {$i}",
                    'phone'    => '010000000' . (20 + $i),
                    'password' => Hash::make('01015258850'),
                    'email_verified_at' => now(),
                ]
            );
            $customer->assignRole($customerRole);
        }

        $this->call([
            CurrencySeeder::class,
            StoreSeeder::class,
            CategorySeeder::class,
            WatchBrandSeeder::class,
            BannerSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            AttributeSeeder::class,
        ]);
    }
}
