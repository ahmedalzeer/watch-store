<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        $bannerData = [
            [
                'title' => ['en' => 'The New Era of Luxury', 'ar' => 'عصر جديد من الفخامة'],
                'description' => ['en' => 'Discover the finest collection of Swiss timepieces.', 'ar' => 'اكتشف أرقى مجموعة من الساعات السويسرية.'],
                'type' => 'hero',
                'img' => 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=1920'
            ],
            [
                'title' => ['en' => 'Timeless Elegance', 'ar' => 'أناقة خالدة'],
                'description' => ['en' => 'Exquisite designs crafted for perfection.', 'ar' => 'تصاميم رائعة صنعت من أجل الكمال.'],
                'type' => 'slider',
                'img' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=1920'
            ],
            [
                'title' => ['en' => 'Precision and Performance', 'ar' => 'الدقة والأداء'],
                'description' => ['en' => 'Engineered for the modern explorer.', 'ar' => 'مصممة خصيصاً للمكتشف العصري.'],
                'type' => 'promo',
                'img' => 'https://images.unsplash.com/photo-1614164185128-e4ec99c436d7?q=80&w=1920'
            ],
        ];

        foreach ($stores as $store) {
            foreach ($bannerData as $index => $data) {
                $banner = Banner::create([
                    'store_id' => $store->id,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'link' => '#',
                    'active' => true,
                    'order' => $index
                ]);

                try {
                    $banner->addMediaFromUrl($data['img'])->toMediaCollection('banner_images');
                } catch (\Exception $e) {
                    // Skip if image fails
                }
            }
        }
    }
}
