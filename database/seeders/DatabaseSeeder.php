<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Usage:
     * - Default seeding: php artisan db:seed
     * - Full database restore: php artisan migrate:fresh --seed --seeder=MasterSeeder
     */
    public function run(): void
    {
        // استخدم MasterSeeder لاستعادة كل البيانات
        // Use MasterSeeder to restore all data
        $this->call(MasterSeeder::class);
    }
}
