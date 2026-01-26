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
            ['ar' => 'رولكس', 'en' => 'Rolex', 'site' => 'https://www.rolex.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/04/Rolex-Logo.png'],
            ['ar' => 'أوميغا', 'en' => 'Omega', 'site' => 'https://www.omegawatches.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2021/08/Omega-Logo.png'],
            ['ar' => 'كاسيو', 'en' => 'Casio', 'site' => 'https://www.casio.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/11/Casio-Logo.png'],
            ['ar' => 'تيسو', 'en' => 'Tissot', 'site' => 'https://www.tissotwatches.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/12/Tissot-Logo.png'],
            ['ar' => 'سيكو', 'en' => 'Seiko', 'site' => 'https://www.seikowatches.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/11/Seiko-Logo.png'],
            ['ar' => 'باتيك فيليب', 'en' => 'Patek Philippe', 'site' => 'https://www.patek.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2021/03/Patek-Philippe-Logo.png'],
            ['ar' => 'أوديمار بيغيه', 'en' => 'Audemars Piguet', 'site' => 'https://www.audemarspiguet.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2021/08/Audemars-Piguet-Logo.png'],
            ['ar' => 'كارتييه', 'en' => 'Cartier', 'site' => 'https://www.cartier.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/06/Cartier-Logo.png'],
            ['ar' => 'هوبلو', 'en' => 'Hublot', 'site' => 'https://www.hublot.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2021/08/Hublot-Logo.png'],
            ['ar' => 'تاغ هوير', 'en' => 'TAG Heuer', 'site' => 'https://www.tagheuer.com', 'logo' => 'https://logos-world.net/wp-content/uploads/2020/12/TAG-Heuer-Logo.png'],
        ];

        foreach ($stores as $store) {
            foreach ($watchBrands as $brandData) {
                $brand = Brand::updateOrCreate(
                    ['slug' => Str::slug($brandData['en']) . "-{$store->id}"],
                    [
                        'name' => ['ar' => $brandData['ar'], 'en' => $brandData['en']],
                        'website' => $brandData['site'],
                        'store_id' => $store->id,
                        'is_featured' => true,
                        'is_active' => true,
                    ]
                );

                if ($brand->getMedia('brand_logos')->isEmpty()) {
                    try {
                        $brand->addMediaFromUrl($brandData['logo'])->toMediaCollection('brand_logos');
                    } catch (\Exception $e) {
                        // Skip if logo fails
                    }
                }
            }
        }
    }
}
