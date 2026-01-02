<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WatchBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $watchBrands = [
            ['ar' => 'رولكس', 'en' => 'Rolex', 'site' => 'https://www.rolex.com'],
            ['ar' => 'أوميغا', 'en' => 'Omega', 'site' => 'https://www.omegawatches.com'],
            ['ar' => 'كاسيو', 'en' => 'Casio', 'site' => 'https://www.casio.com'],
            ['ar' => 'تيسو', 'en' => 'Tissot', 'site' => 'https://www.tissotwatches.com'],
            ['ar' => 'سيكو', 'en' => 'Seiko', 'site' => 'https://www.seikowatches.com'],
        ];

        foreach ($stores as $store) {
            foreach ($watchBrands as $brandData) {
                Brand::updateOrCreate(
                    ['slug' => Str::slug($brandData['en']) . "-{$store->id}"],
                    [
                        'name' => ['ar' => $brandData['ar'], 'en' => $brandData['en']],
                        'website' => $brandData['site'],
                        'store_id' => $store->id,
                        'is_featured' => true,
                    ]
                );
            }
        }
    }
}
