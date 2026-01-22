<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        if ($stores->isEmpty()) {
            $this->command->error('The stores table is empty. Please run StoreSeeder first.');
            return;
        }

        $watchData = [
            ['en' => 'Rolex Submariner', 'ar' => 'رولكس صب مارينر'],
            ['en' => 'Omega Speedmaster', 'ar' => 'أوميغا سبيد ماستر'],
            ['en' => 'Patek Philippe Nautilus', 'ar' => 'باتيك فيليب نوتيلوس'],
            ['en' => 'Audemars Piguet Royal Oak', 'ar' => 'أوديمار بيغيه رويال أوك'],
            ['en' => 'Hublot Big Bang', 'ar' => 'هوبلو بيج بانج'],
            ['en' => 'Tag Heuer Carrera', 'ar' => 'تاغ هوير كاريرا'],
            ['en' => 'Cartier Santos', 'ar' => 'كارتييه سانتوس'],
            ['en' => 'Tudor Black Bay', 'ar' => 'تودور بلاك باي'],
        ];

        foreach ($stores as $store) {
            $categories = Category::where('store_id', $store->id)->get();
            $brands = Brand::where('store_id', $store->id)->get();

            if ($categories->isEmpty() || $brands->isEmpty()) {
                $this->command->warn("Store ID: {$store->id} has no categories or brands. Skipping.");
                continue;
            }

            for ($i = 0; $i < 5; $i++) {
                $watch = $watchData[array_rand($watchData)];
                $price = rand(5000, 50000);
                $nameEn = $watch['en'] . ' ' . Str::random(3);

                $product = Product::create([
                    'store_id' => $store->id,
                    'category_id' => $categories->random()->id,
                    'brand_id' => $brands->random()->id,
                    'name' => [
                        'ar' => $watch['ar'] . ' ' . ($i + 1),
                        'en' => $nameEn,
                    ],
                    'description' => [
                        'ar' => 'هذا الوصف تجريبي لساعة فاخرة متوفرة في متجرنا بأسعار تنافسية.',
                        'en' => 'This is a luxury watch description available in our store with competitive prices.',
                    ],
                    'slug' => Str::slug($nameEn) . '-' . uniqid(),
                    'sku' => 'SKU-' . $store->id . '-' . strtoupper(Str::random(5)),
                    'price' => $price,
                    'discount_price' => rand(0, 1) ? ($price * 0.85) : null,
                    'stock' => rand(0, 50),

                    'specifications' => [
                        [
                            'key' => ['en' => 'Material', 'ar' => 'المادة'],
                            'value' => ['en' => 'Gold and Stainless Steel', 'ar' => 'ذهب وفولاذ مقاوم للصدأ']
                        ],
                        [
                            'key' => ['en' => 'Glass', 'ar' => 'الزجاج'],
                            'value' => ['en' => 'Scratch-resistant Sapphire', 'ar' => 'ياقوت مقاوم للخدش']
                        ],
                        [
                            'key' => ['en' => 'Water Resistance', 'ar' => 'مقاومة الماء'],
                            'value' => ['en' => '100 Meters', 'ar' => '100 متر']
                        ],
                        [
                            'key' => ['en' => 'Movement', 'ar' => 'نوع الحركة'],
                            'value' => ['en' => 'Automatic', 'ar' => 'أوتوماتيك']
                        ]
                    ],

                    'is_active' => true,
                    'main_menu' => (bool)rand(0, 1),
                    'main_store' => (bool)rand(0, 1),
                    'condition' => ['new', 'used', 'refurbished'][rand(0, 2)],
                ]);

                try {
                    for ($j = 0; $j < 3; $j++) {
                        $product->addMediaFromUrl("https://picsum.photos/seed/" . Str::random(5) . "/800/800")
                            ->withCustomProperties([
                                'is_main' => ($j === 0)
                            ])
                            ->toMediaCollection('product_gallery');
                    }
                } catch (\Exception $e) {
                    $this->command->warn("Could not upload images for product: " . $product->slug . ". Check your internet connection.");
                }
            }
        }

        $this->command->info('products table seeded successfully with translated specifications.');
    }
}
