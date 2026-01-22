<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have a vendor user and store
        $vendor = User::firstOrCreate(
            ['email' => 'vendor1@admin.com'],
            [
                'name' => 'Fast Vendor',
                'password' => bcrypt('password'),
                'phone' => '01099999999',
            ]
        );
        $vendor->assignRole('vendor');

        $store = Store::firstOrCreate(
            ['user_id' => $vendor->id],
            [
                'name' => 'Fast Test Store',
                'slug' => 'fast-test-store',
                'currency_id' => 1, // Assumptions
            ]
        );

        $this->command->info("Seeding data for store: {$store->name} (ID: {$store->id})");

        // 2. Create Categories
        $categories = Category::factory()
            ->count(5)
            ->create(['store_id' => $store->id]);
        
        $this->command->info("Created 5 Categories.");

        // 3. Create Brands
        $brands = Brand::factory()
            ->count(5)
            ->create(['store_id' => $store->id]);
            
        $this->command->info("Created 5 Brands.");

        // 4. Create Products
        Product::factory()
            ->count(20)
            ->make([
                'store_id' => $store->id,
            ])
            ->each(function ($product) use ($categories, $brands) {
                $product->category_id = $categories->random()->id;
                $product->brand_id = $brands->random()->id;
                $product->save();
            });

        $this->command->info("Created 20 Products.");
    }
}
