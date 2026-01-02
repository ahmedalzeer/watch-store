<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        $watchCategories = [
            [
                'ar' => 'ساعات رجالية',
                'en' => 'Men\'s Watches',
                'sub' => [
                    ['ar' => 'ساعات كلاسيكية', 'en' => 'Classic Watches'],
                    ['ar' => 'ساعات رياضية', 'en' => 'Sports Watches'],
                    ['ar' => 'ساعات رسمية', 'en' => 'Luxury Watches'],
                ]
            ],
            [
                'ar' => 'ساعات نسائية',
                'en' => 'Women\'s Watches',
                'sub' => [
                    ['ar' => 'ساعات مرصعة بالماس', 'en' => 'Diamond Watches'],
                    ['ar' => 'ساعات سوار', 'en' => 'Bracelet Watches'],
                ]
            ],
            [
                'ar' => 'ساعات ذكية',
                'en' => 'Smart Watches',
                'sub' => [
                    ['ar' => 'أجهزة تتبع اللياقة', 'en' => 'Fitness Trackers'],
                    ['ar' => 'ساعات ذكية هجينة', 'en' => 'Hybrid Smartwatches'],
                ]
            ],
            [
                'ar' => 'إكسسوارات الساعات',
                'en' => 'Watch Accessories',
                'sub' => [
                    ['ar' => 'أحزمة ساعات (Strap)', 'en' => 'Watch Straps'],
                    ['ar' => 'صناديق عرض', 'en' => 'Watch Boxes'],
                ]
            ],
        ];

        foreach ($stores as $store) {
            foreach ($watchCategories as $catData) {

                $parent = Category::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'slug'     => Str::slug($catData['en']) . "-{$store->id}"
                    ],
                    [
                        'name'      => ['ar' => $catData['ar'], 'en' => $catData['en']],
                        'parent_id' => null,
                        'is_active' => true,
                    ]
                );


                foreach ($catData['sub'] as $subData) {
                    Category::updateOrCreate(
                        [
                            'store_id' => $store->id,
                            'slug'     => Str::slug($subData['en']) . "-{$store->id}"
                        ],
                        [
                            'name'      => ['ar' => $subData['ar'], 'en' => $subData['en']],
                            'parent_id' => $parent->id,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
