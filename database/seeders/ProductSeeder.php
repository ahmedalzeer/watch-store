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

        $watchModels = [
            'Rolex' => [
                ['en' => 'Submariner Date', 'ar' => 'صب مارينر ديت', 'price' => 15000, 'img' => 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=800'],
                ['en' => 'Daytona Cosmograph', 'ar' => 'دويتونا كوزموغراف', 'price' => 35000, 'img' => 'https://images.unsplash.com/photo-1524592091214-8c9fc4854593?q=80&w=800'],
                ['en' => 'Datejust 41', 'ar' => 'ديت جست ٤١', 'price' => 12000, 'img' => 'https://images.unsplash.com/photo-1614164185128-e4ec99c436d7?q=80&w=800'],
            ],
            'Omega' => [
                ['en' => 'Speedmaster Professional', 'ar' => 'سبيد ماستر بروفيشينال', 'price' => 7000, 'img' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?q=80&w=800'],
                ['en' => 'Seamaster Diver 300M', 'ar' => 'سي ماستر دايفر ٣٠٠ م', 'price' => 5500, 'img' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=800'],
            ],
            'Seiko' => [
                ['en' => 'Prospex Turtle', 'ar' => 'بروسبكس تيرتل', 'price' => 500, 'img' => 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?q=80&w=800'],
                ['en' => 'Presage Cocktail Time', 'ar' => 'بريسادج كوكتيل تايم', 'price' => 450, 'img' => 'https://images.unsplash.com/photo-1619134704035-9e190d01c720?q=80&w=800'],
            ],
            'Tissot' => [
                ['en' => 'PRX Powermatic 80', 'ar' => 'بي آر إكس باورماتيك ٨٠', 'price' => 650, 'img' => 'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?q=80&w=800'],
                ['en' => 'Le Locle Automatic', 'ar' => 'لي لوكل أوتوماتيك', 'price' => 600, 'img' => 'https://images.unsplash.com/photo-1508685096489-7a68fb03a5b9?q=80&w=800'],
            ],
            'Casio' => [
                ['en' => 'G-Shock GA-2100', 'ar' => 'جي شوك GA-2100', 'price' => 110, 'img' => 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?q=80&w=800'],
                ['en' => 'Classic Digital A168W', 'ar' => 'كلاسيك ديجيتال A168W', 'price' => 50, 'img' => 'https://images.unsplash.com/photo-1614704372431-76619a9a5f45?q=80&w=800'],
            ],
            'TAG Heuer' => [
                ['en' => 'Carrera Chronograph', 'ar' => 'كاريرا كرونوغراف', 'price' => 6000, 'img' => 'https://images.unsplash.com/photo-1585123334904-845d60e97b29?q=80&w=800'],
                ['en' => 'Monaco Gulf Edition', 'ar' => 'موناكو غلف إيديشن', 'price' => 7500, 'img' => 'https://images.unsplash.com/photo-1622434641406-a158123450f9?q=80&w=800'],
            ],
        ];

        foreach ($stores as $store) {
            $categories = Category::where('store_id', $store->id)->get();
            $brands = Brand::where('store_id', $store->id)->get();

            if ($categories->isEmpty() || $brands->isEmpty()) {
                continue;
            }

            foreach ($brands as $brand) {
                $brandNameEn = $brand->getTranslation('name', 'en');
                if (!isset($watchModels[$brandNameEn])) continue;

                foreach ($watchModels[$brandNameEn] as $modelData) {
                    $nameEn = $brandNameEn . ' ' . $modelData['en'];
                    $nameAr = $brand->getTranslation('name', 'ar') . ' ' . $modelData['ar'];

                    $product = Product::create([
                        'store_id' => $store->id,
                        'category_id' => $categories->random()->id,
                        'brand_id' => $brand->id,
                        'name' => [
                            'ar' => $nameAr,
                            'en' => $nameEn,
                        ],
                        'description' => [
                            'ar' => "استمتع بالأناقة المطلقة مع ساعة {$nameAr}. تتميز بتصميم كلاسيكي خالد مع حرفية عالية الجودة وأداء استثنائي.",
                            'en' => "Experience ultimate elegance with the {$nameEn}. Featuring a timeless classic design with high-quality craftsmanship and exceptional performance.",
                        ],
                        'slug' => Str::slug($nameEn) . '-' . $store->id,
                        'sku' => strtoupper($brandNameEn) . '-' . rand(1000, 9999),
                        'price' => $modelData['price'],
                        'discount_price' => rand(0, 1) ? ($modelData['price'] * 0.9) : null,
                        'stock' => rand(5, 50),
                        'specifications' => [
                            ['key' => ['en' => 'Case Material', 'ar' => 'مادة الهيكل'], 'value' => ['en' => 'Stainless Steel / Gold', 'ar' => 'فولاذ مقاوم للصدأ / ذهب']],
                            ['key' => ['en' => 'Movement', 'ar' => 'نوع الحركة'], 'value' => ['en' => 'Swiss Automatic', 'ar' => 'أوتوماتيك سويسري']],
                            ['key' => ['en' => 'Water Resistance', 'ar' => 'مقاومة الماء'], 'value' => ['en' => '100m / 330ft', 'ar' => '١٠٠ متر']],
                            ['key' => ['en' => 'Crystal', 'ar' => 'الزجاج'], 'value' => ['en' => 'Scratch-Resistant Sapphire', 'ar' => 'ياقوت مقاوم للخدش']],
                        ],
                        'is_active' => true,
                    ]);

                    try {
                        $product->addMediaFromUrl($modelData['img'])
                            ->withCustomProperties(['is_main' => true])
                            ->toMediaCollection('product_gallery');
                            
                        // Add some extra random watch images
                        $product->addMediaFromUrl($modelData['img'])
                            ->toMediaCollection('product_gallery');

                        // ADD REVIEWS
                        $customers = \App\Models\User::role('customer')->get();
                        foreach ($customers->random(rand(2, 5)) as $customer) {
                            \App\Models\ProductReview::create([
                                'product_id' => $product->id,
                                'user_id' => $customer->id,
                                'rating' => rand(4, 5),
                                'review' => [
                                    'en' => 'Amazing watch! The quality is top-notch and it looks even better in person.',
                                    'ar' => 'ساعة مذهلة! الجودة ممتازة وتبدو أفضل بكثير في الحقيقة.'
                                ][rand(0, 1) ? 'en' : 'ar'], // Simple random toggle for now
                                'is_approved' => true,
                            ]);
                        }

                    } catch (\Exception $e) {
                        $this->command->warn("Media upload failed for product: " . $product->slug);
                    }
                }
            }
        }

        $this->command->info('Products seeded with realistic watch data.');
    }
}
