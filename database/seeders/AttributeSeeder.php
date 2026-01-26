<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Store;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();

        $attributesData = [
            [
                'name' => ['ar' => 'نوع الحركة', 'en' => 'Movement'],
                'values' => [
                    ['ar' => 'أوتوماتيك', 'en' => 'Automatic'],
                    ['ar' => 'كوارتز', 'en' => 'Quartz'],
                    ['ar' => 'يدوي', 'en' => 'Manual'],
                    ['ar' => 'توليد حركي', 'en' => 'Kinetic'],
                    ['ar' => 'شمسي', 'en' => 'Solar'],
                ]
            ],
            [
                'name' => ['ar' => 'مادة الهيكل', 'en' => 'Case Material'],
                'values' => [
                    ['ar' => 'فولاذ مقاوم للصدأ', 'en' => 'Stainless Steel'],
                    ['ar' => 'تيتانيوم', 'en' => 'Titanium'],
                    ['ar' => 'ذهب وردي', 'en' => 'Rose Gold'],
                    ['ar' => 'ذهب أصفر', 'en' => 'Yellow Gold'],
                    ['ar' => 'سيراميك', 'en' => 'Ceramic'],
                    ['ar' => 'ألياف الكربون', 'en' => 'Carbon Fiber'],
                ]
            ],
            [
                'name' => ['ar' => 'نوع الزجاج', 'en' => 'Glass Type'],
                'values' => [
                    ['ar' => 'ياقوت كريستال', 'en' => 'Sapphire Crystal'],
                    ['ar' => 'زجاج معدني', 'en' => 'Mineral Glass'],
                    ['ar' => 'هيزاليت', 'en' => 'Hesalite'],
                    ['ar' => 'أكريليك', 'en' => 'Acrylic'],
                ]
            ],
            [
                'name' => ['ar' => 'مقاومة الماء', 'en' => 'Water Resistance'],
                'values' => [
                    ['ar' => '٣ ضغط جوي', 'en' => '3 ATM'],
                    ['ar' => '٥ ضغط جوي', 'en' => '5 ATM'],
                    ['ar' => '١٠ ضغط جوي', 'en' => '10 ATM'],
                    ['ar' => '٢٠ ضغط جوي', 'en' => '20 ATM'],
                    ['ar' => '٣٠ ضغط جوي', 'en' => '30 ATM'],
                ]
            ],
            [
                'name' => ['ar' => 'مادة السوار', 'en' => 'Strap Material'],
                'values' => [
                    ['ar' => 'جلد طبيعي', 'en' => 'Genuine Leather'],
                    ['ar' => 'فولاذ مقاوم للصدأ', 'en' => 'Stainless Steel'],
                    ['ar' => 'مطاط', 'en' => 'Rubber'],
                    ['ar' => 'سيليكون', 'en' => 'Silicone'],
                    ['ar' => 'ناتو', 'en' => 'NATO'],
                ]
            ],
            [
                'name' => ['ar' => 'لون المينا', 'en' => 'Dial Color'],
                'values' => [
                    ['ar' => 'أسود', 'en' => 'Black'],
                    ['ar' => 'فضي', 'en' => 'Silver'],
                    ['ar' => 'أزرق', 'en' => 'Blue'],
                    ['ar' => 'أبيض', 'en' => 'White'],
                    ['ar' => 'أخضر', 'en' => 'Green'],
                    ['ar' => 'رمادي', 'en' => 'Grey'],
                ]
            ],
            [
                'name' => ['ar' => 'قطر الهيكل', 'en' => 'Case Diameter'],
                'values' => [
                    ['ar' => '٣٨ مم', 'en' => '38mm'],
                    ['ar' => '٤٠ مم', 'en' => '40mm'],
                    ['ar' => '٤٢ مم', 'en' => '42mm'],
                    ['ar' => '٤٤ مم', 'en' => '44mm'],
                ]
            ],
        ];

        foreach ($stores as $store) {
            foreach ($attributesData as $attr) {
                $attribute = Attribute::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name->en' => $attr['name']['en']
                    ],
                    ['name' => $attr['name']]
                );

                foreach ($attr['values'] as $val) {
                    AttributeValue::updateOrCreate(
                        [
                            'attribute_id' => $attribute->id,
                            'value->en' => $val['en']
                        ],
                        ['value' => $val]
                    );
                }
            }
        }
    }
}
