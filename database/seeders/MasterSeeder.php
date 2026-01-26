<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MasterSeeder extends Seeder
{
    /**
     * Seeder شامل لكل بيانات المشروع
     * يمكن استخدامه لاستعادة قاعدة البيانات بالكامل
     *
     * الاستخدام:
     * php artisan migrate:fresh --seed --seeder=MasterSeeder
     * أو
     * php artisan db:seed --class=MasterSeeder
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Master Database Seeder...');
        $this->command->newLine();

        // 1. Seed Roles & Users
        $this->seedRolesAndUsers();

        // 2. Seed Currencies
        $this->seedCurrencies();

        // 3. Seed Payment Gateways
        $this->seedPaymentGateways();

        // 4. Seed Stores
        $this->seedStores();

        // 5. Seed Categories
        $this->seedCategories();

        // 6. Seed Brands
        $this->seedBrands();

        // 7. Seed Banners
        $this->seedBanners();

        // 8. Seed Attributes
        $this->seedAttributes();

        // 9. Seed Products (with reviews)
        $this->seedProducts();

        // 10. Seed Product Variants
        $this->seedProductVariants();

        // 11. Seed Wishlists
        $this->seedWishlists();

        // 12. Seed Sample Orders
        $this->seedOrders();

        $this->command->newLine();
        $this->command->info('✅ Master Database Seeding Completed Successfully!');
    }

    /**
     * Seed Roles and Users (Admin, Vendors, Customers)
     */
    private function seedRolesAndUsers(): void
    {
        $this->command->info('👤 Seeding Roles & Users...');

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'ahmedalzeer@admin.com'],
            [
                'name' => 'System Admin',
                'phone' => '01015258850',
                'password' => Hash::make('01015258850'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Vendors (10)
        for ($i = 1; $i <= 10; $i++) {
            $vendor = User::updateOrCreate(
                ['email' => "vendor{$i}@admin.com"],
                [
                    'name' => "Sample Vendor {$i}",
                    'phone' => '010000000' . (10 + $i),
                    'password' => Hash::make('01015258850'),
                    'email_verified_at' => now(),
                ]
            );
            $vendor->assignRole($vendorRole);
        }

        // Create Customers (10)
        for ($i = 1; $i <= 10; $i++) {
            $customer = User::updateOrCreate(
                ['email' => "customer{$i}@admin.com"],
                [
                    'name' => "Sample Customer {$i}",
                    'phone' => '010000000' . (20 + $i),
                    'password' => Hash::make('01015258850'),
                    'email_verified_at' => now(),
                ]
            );
            $customer->assignRole($customerRole);
        }

        $this->command->info('   ✓ Created 1 Admin, 10 Vendors, 10 Customers');
    }

    /**
     * Seed Currencies
     */
    private function seedCurrencies(): void
    {
        $this->command->info('💰 Seeding Currencies...');

        $currencies = [
            // Gulf
            ['ar' => 'ريال سعودي', 'en' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => '﷼'],
            ['ar' => 'درهم إماراتي', 'en' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['ar' => 'دينار كويتي', 'en' => 'Kuwaiti Dinar', 'code' => 'KWD', 'symbol' => 'د.ك'],
            ['ar' => 'ريال قطري', 'en' => 'Qatari Riyal', 'code' => 'QAR', 'symbol' => 'ر.ق'],
            ['ar' => 'دينار بحريني', 'en' => 'Bahraini Dinar', 'code' => 'BHD', 'symbol' => 'د.ب'],
            ['ar' => 'ريال عماني', 'en' => 'Omani Rial', 'code' => 'OMR', 'symbol' => 'ر.ع'],
            // Arab
            ['ar' => 'جنيه مصري', 'en' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => 'ج.م'],
            ['ar' => 'دينار أردني', 'en' => 'Jordanian Dinar', 'code' => 'JOD', 'symbol' => 'د.أ'],
            ['ar' => 'دينار عراقي', 'en' => 'Iraqi Dinar', 'code' => 'IQD', 'symbol' => 'د.ع'],
            ['ar' => 'ليرة لبناني', 'en' => 'Lebanese Pound', 'code' => 'LBP', 'symbol' => 'ل.ل'],
            ['ar' => 'درهم مغربي', 'en' => 'Moroccan Dirham', 'code' => 'MAD', 'symbol' => 'د.م.'],
            ['ar' => 'دينار جزائري', 'en' => 'Algerian Dinar', 'code' => 'DZD', 'symbol' => 'د.ج'],
            ['ar' => 'دينار تونسي', 'en' => 'Tunisian Dinar', 'code' => 'TND', 'symbol' => 'د.ت'],
            ['ar' => 'دينار ليبي', 'en' => 'Libyan Dinar', 'code' => 'LYD', 'symbol' => 'د.ل'],
            ['ar' => 'جنيه سوداني', 'en' => 'Sudanese Pound', 'code' => 'SDG', 'symbol' => 'ج.س'],
            ['ar' => 'ريال يمني', 'en' => 'Yemeni Rial', 'code' => 'YER', 'symbol' => 'ر.ي'],
            // International
            ['ar' => 'دولار أمريكي', 'en' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['ar' => 'يورو', 'en' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
            ['ar' => 'جنيه إسترليني', 'en' => 'British Pound', 'code' => 'GBP', 'symbol' => '£'],
            ['ar' => 'ين ياباني', 'en' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥'],
            ['ar' => 'فرنك سويسري', 'en' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHf'],
            ['ar' => 'دولار كندي', 'en' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'C$'],
            ['ar' => 'دولار أسترالي', 'en' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => 'A$'],
            ['ar' => 'ليرة تركية', 'en' => 'Turkish Lira', 'code' => 'TRY', 'symbol' => '₺'],
            ['ar' => 'يوان صيني', 'en' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                [
                    'name' => ['ar' => $currency['ar'], 'en' => $currency['en']],
                    'symbol' => $currency['symbol'],
                    'exchange_rate' => 1.00,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('   ✓ Created ' . count($currencies) . ' Currencies');
    }

    /**
     * Seed Payment Gateways
     */
    private function seedPaymentGateways(): void
    {
        $this->command->info('💳 Seeding Payment Gateways...');

        $gateways = [
            [
                'name' => 'Cash on Delivery',
                'code' => 'cod',
                'is_active' => true,
                'configs' => null,
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'is_active' => true,
                'configs' => [
                    'public_key' => 'pk_test_xxxxx',
                    'secret_key' => 'sk_test_xxxxx',
                ],
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'is_active' => true,
                'configs' => [
                    'client_id' => 'paypal_client_id',
                    'client_secret' => 'paypal_secret',
                    'mode' => 'sandbox',
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['code' => $gateway['code']],
                $gateway
            );
        }

        $this->command->info('   ✓ Created ' . count($gateways) . ' Payment Gateways');
    }

    /**
     * Seed Stores for Vendors
     */
    private function seedStores(): void
    {
        $this->command->info('🏪 Seeding Stores...');

        $defaultCurrency = Currency::where('code', 'EGP')->first() ?? Currency::first();
        $vendors = User::role('vendor')->get();
        $storeCount = 0;

        foreach ($vendors as $vendor) {
            preg_match('/\d+/', $vendor->email, $matches);
            $vendorNumber = isset($matches[0]) ? (int) $matches[0] : 0;

            // Some vendors have multiple stores
            $storesCount = match ($vendorNumber) {
                4 => 2,
                6 => 3,
                default => 1,
            };

            for ($i = 1; $i <= $storesCount; $i++) {
                $suffix = $storesCount > 1 ? "-{$i}" : "";

                Store::updateOrCreate(
                    ['subdomain' => "store-v{$vendorNumber}{$suffix}"],
                    [
                        'user_id' => $vendor->id,
                        'currency_id' => $defaultCurrency->id,
                        'name' => [
                            'ar' => "متجر {$vendor->name} {$suffix}",
                            'en' => "{$vendor->name} Store {$suffix}",
                        ],
                        'description' => [
                            'ar' => "وصف متجر {$vendor->name}",
                            'en' => "Description for {$vendor->name}",
                        ],
                        'theme_color' => '#7e3af2',
                        'contact_email' => $vendor->email,
                        'is_active' => true,
                    ]
                );
                $storeCount++;
            }
        }

        $this->command->info("   ✓ Created {$storeCount} Stores");
    }

    /**
     * Seed Categories for each Store
     */
    private function seedCategories(): void
    {
        $this->command->info('📁 Seeding Categories...');

        $stores = Store::all();
        $watchCategories = [
            [
                'ar' => 'ساعات رجالية',
                'en' => 'Men\'s Watches',
                'sub' => [
                    ['ar' => 'ساعات كلاسيكية', 'en' => 'Classic Watches'],
                    ['ar' => 'ساعات رياضية', 'en' => 'Sports Watches'],
                    ['ar' => 'ساعات رسمية', 'en' => 'Luxury Watches'],
                ],
            ],
            [
                'ar' => 'ساعات نسائية',
                'en' => 'Women\'s Watches',
                'sub' => [
                    ['ar' => 'ساعات مرصعة بالماس', 'en' => 'Diamond Watches'],
                    ['ar' => 'ساعات سوار', 'en' => 'Bracelet Watches'],
                ],
            ],
            [
                'ar' => 'ساعات ذكية',
                'en' => 'Smart Watches',
                'sub' => [
                    ['ar' => 'أجهزة تتبع اللياقة', 'en' => 'Fitness Trackers'],
                    ['ar' => 'ساعات ذكية هجينة', 'en' => 'Hybrid Smartwatches'],
                ],
            ],
            [
                'ar' => 'إكسسوارات الساعات',
                'en' => 'Watch Accessories',
                'sub' => [
                    ['ar' => 'أحزمة ساعات (Strap)', 'en' => 'Watch Straps'],
                    ['ar' => 'صناديق عرض', 'en' => 'Watch Boxes'],
                ],
            ],
        ];

        $categoryCount = 0;

        foreach ($stores as $store) {
            foreach ($watchCategories as $catData) {
                $parent = Category::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'slug' => Str::slug($catData['en']) . "-{$store->id}",
                    ],
                    [
                        'name' => ['ar' => $catData['ar'], 'en' => $catData['en']],
                        'parent_id' => null,
                        'is_active' => true,
                    ]
                );
                $categoryCount++;

                foreach ($catData['sub'] as $subData) {
                    Category::updateOrCreate(
                        [
                            'store_id' => $store->id,
                            'slug' => Str::slug($subData['en']) . "-{$store->id}",
                        ],
                        [
                            'name' => ['ar' => $subData['ar'], 'en' => $subData['en']],
                            'parent_id' => $parent->id,
                            'is_active' => true,
                        ]
                    );
                    $categoryCount++;
                }
            }
        }

        $this->command->info("   ✓ Created {$categoryCount} Categories");
    }

    /**
     * Seed Brands for each Store
     */
    private function seedBrands(): void
    {
        $this->command->info('🏷️ Seeding Brands...');

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

        $brandCount = 0;

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

                // Add logo if not exists
                if ($brand->getMedia('brand_logos')->isEmpty()) {
                    try {
                        $brand->addMediaFromUrl($brandData['logo'])->toMediaCollection('brand_logos');
                    } catch (\Exception $e) {
                        // Skip if logo fails
                    }
                }
                $brandCount++;
            }
        }

        $this->command->info("   ✓ Created {$brandCount} Brands");
    }

    /**
     * Seed Banners for each Store
     */
    private function seedBanners(): void
    {
        $this->command->info('🖼️ Seeding Banners...');

        $stores = Store::all();
        $bannerData = [
            [
                'title' => ['en' => 'The New Era of Luxury', 'ar' => 'عصر جديد من الفخامة'],
                'description' => ['en' => 'Discover the finest collection of Swiss timepieces.', 'ar' => 'اكتشف أرقى مجموعة من الساعات السويسرية.'],
                'type' => 'hero',
                'img' => 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?q=80&w=1920',
            ],
            [
                'title' => ['en' => 'Timeless Elegance', 'ar' => 'أناقة خالدة'],
                'description' => ['en' => 'Exquisite designs crafted for perfection.', 'ar' => 'تصاميم رائعة صنعت من أجل الكمال.'],
                'type' => 'slider',
                'img' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=1920',
            ],
            [
                'title' => ['en' => 'Precision and Performance', 'ar' => 'الدقة والأداء'],
                'description' => ['en' => 'Engineered for the modern explorer.', 'ar' => 'مصممة خصيصاً للمكتشف العصري.'],
                'type' => 'promo',
                'img' => 'https://images.unsplash.com/photo-1614164185128-e4ec99c436d7?q=80&w=1920',
            ],
        ];

        $bannerCount = 0;

        foreach ($stores as $store) {
            foreach ($bannerData as $index => $data) {
                $banner = Banner::create([
                    'store_id' => $store->id,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'link' => '#',
                    'active' => true,
                    'order' => $index,
                ]);

                try {
                    $banner->addMediaFromUrl($data['img'])->toMediaCollection('banner_images');
                } catch (\Exception $e) {
                    // Skip if image fails
                }
                $bannerCount++;
            }
        }

        $this->command->info("   ✓ Created {$bannerCount} Banners");
    }

    /**
     * Seed Attributes and Values for each Store
     */
    private function seedAttributes(): void
    {
        $this->command->info('🎨 Seeding Attributes & Values...');

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
                ],
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
                    ['ar' => 'ذهبي', 'en' => 'Gold'],
                    ['ar' => 'فضي', 'en' => 'Silver'],
                ],
            ],
            [
                'name' => ['ar' => 'نوع الزجاج', 'en' => 'Glass Type'],
                'values' => [
                    ['ar' => 'ياقوت كريستال', 'en' => 'Sapphire Crystal'],
                    ['ar' => 'زجاج معدني', 'en' => 'Mineral Glass'],
                    ['ar' => 'هيزاليت', 'en' => 'Hesalite'],
                    ['ar' => 'أكريليك', 'en' => 'Acrylic'],
                ],
            ],
            [
                'name' => ['ar' => 'مقاومة الماء', 'en' => 'Water Resistance'],
                'values' => [
                    ['ar' => '٣ ضغط جوي', 'en' => '3 ATM'],
                    ['ar' => '٥ ضغط جوي', 'en' => '5 ATM'],
                    ['ar' => '١٠ ضغط جوي', 'en' => '10 ATM'],
                    ['ar' => '٢٠ ضغط جوي', 'en' => '20 ATM'],
                    ['ar' => '٣٠ ضغط جوي', 'en' => '30 ATM'],
                ],
            ],
            [
                'name' => ['ar' => 'مادة السوار', 'en' => 'Strap Material'],
                'values' => [
                    ['ar' => 'جلد طبيعي', 'en' => 'Genuine Leather'],
                    ['ar' => 'فولاذ مقاوم للصدأ', 'en' => 'Stainless Steel'],
                    ['ar' => 'مطاط', 'en' => 'Rubber'],
                    ['ar' => 'سيليكون', 'en' => 'Silicone'],
                    ['ar' => 'ناتو', 'en' => 'NATO'],
                ],
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
                ],
            ],
            [
                'name' => ['ar' => 'قطر الهيكل', 'en' => 'Case Diameter'],
                'values' => [
                    ['ar' => '٣٨ مم', 'en' => '38mm'],
                    ['ar' => '٤٠ مم', 'en' => '40mm'],
                    ['ar' => '٤٢ مم', 'en' => '42mm'],
                    ['ar' => '٤٤ مم', 'en' => '44mm'],
                ],
            ],
        ];

        $attrCount = 0;
        $valCount = 0;

        foreach ($stores as $store) {
            foreach ($attributesData as $attr) {
                $attribute = Attribute::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name->en' => $attr['name']['en'],
                    ],
                    ['name' => $attr['name']]
                );
                $attrCount++;

                foreach ($attr['values'] as $val) {
                    AttributeValue::updateOrCreate(
                        [
                            'attribute_id' => $attribute->id,
                            'value->en' => $val['en'],
                        ],
                        ['value' => $val]
                    );
                    $valCount++;
                }
            }
        }

        $this->command->info("   ✓ Created {$attrCount} Attributes with {$valCount} Values");
    }

    /**
     * Seed Products with Reviews
     */
    private function seedProducts(): void
    {
        $this->command->info('📦 Seeding Products & Reviews...');

        $stores = Store::all();
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

        $productCount = 0;
        $reviewCount = 0;
        $customers = User::role('customer')->get();

        foreach ($stores as $store) {
            $categories = Category::where('store_id', $store->id)->get();
            $brands = Brand::where('store_id', $store->id)->get();

            if ($categories->isEmpty() || $brands->isEmpty()) {
                continue;
            }

            foreach ($brands as $brand) {
                $brandNameEn = $brand->getTranslation('name', 'en');
                if (!isset($watchModels[$brandNameEn])) {
                    continue;
                }

                foreach ($watchModels[$brandNameEn] as $modelData) {
                    $nameEn = $brandNameEn . ' ' . $modelData['en'];
                    $nameAr = $brand->getTranslation('name', 'ar') . ' ' . $modelData['ar'];

                    $product = Product::create([
                        'store_id' => $store->id,
                        'category_id' => $categories->random()->id,
                        'brand_id' => $brand->id,
                        'name' => ['ar' => $nameAr, 'en' => $nameEn],
                        'description' => [
                            'ar' => "استمتع بالأناقة المطلقة مع ساعة {$nameAr}. تتميز بتصميم كلاسيكي خالد مع حرفية عالية الجودة وأداء استثنائي.",
                            'en' => "Experience ultimate elegance with the {$nameEn}. Featuring a timeless classic design with high-quality craftsmanship and exceptional performance.",
                        ],
                        'slug' => Str::slug($nameEn) . '-' . $store->id . '-' . rand(1000, 9999),
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
                    $productCount++;

                    // Add media
                    try {
                        $product->addMediaFromUrl($modelData['img'])
                            ->withCustomProperties(['is_main' => true])
                            ->toMediaCollection('product_gallery');

                        $product->addMediaFromUrl($modelData['img'])
                            ->toMediaCollection('product_gallery');
                    } catch (\Exception $e) {
                        // Skip if media fails
                    }

                    // Add reviews
                    if ($customers->count() > 0) {
                        $reviewsToAdd = min(rand(2, 5), $customers->count());
                        foreach ($customers->random($reviewsToAdd) as $customer) {
                            ProductReview::create([
                                'product_id' => $product->id,
                                'user_id' => $customer->id,
                                'rating' => rand(4, 5),
                                'review' => [
                                    'en' => 'Amazing watch! The quality is top-notch and it looks even better in person.',
                                    'ar' => 'ساعة مذهلة! الجودة ممتازة وتبدو أفضل بكثير في الحقيقة.',
                                ][rand(0, 1) ? 'en' : 'ar'],
                                'is_approved' => true,
                            ]);
                            $reviewCount++;
                        }
                    }
                }
            }
        }

        $this->command->info("   ✓ Created {$productCount} Products with {$reviewCount} Reviews");
    }

    /**
     * Seed Product Variants with multiple attributes
     * هذا يوضح كيفية إنشاء variants متعددة لكل منتج بناءً على تركيبات الخصائص
     */
    private function seedProductVariants(): void
    {
        $this->command->info('🔄 Seeding Product Variants...');

        $products = Product::all();
        $variantCount = 0;

        foreach ($products as $product) {
            // Skip if product already has variants
            if ($product->variants()->count() > 0) {
                continue;
            }

            // Get attributes for this store
            $caseMaterialAttr = Attribute::where('store_id', $product->store_id)
                ->where('name->en', 'Case Material')
                ->first();

            $dialColorAttr = Attribute::where('store_id', $product->store_id)
                ->where('name->en', 'Dial Color')
                ->first();

            if (!$caseMaterialAttr || !$dialColorAttr) {
                continue;
            }

            // Get some values for each attribute
            $caseMaterials = $caseMaterialAttr->values()->take(3)->get(); // Gold, Silver, Steel
            $dialColors = $dialColorAttr->values()->take(2)->get(); // Black, Blue

            if ($caseMaterials->isEmpty() || $dialColors->isEmpty()) {
                continue;
            }

            $basePrice = $product->price;
            $baseStock = $product->stock ?: rand(20, 50);
            $totalVariantStock = 0;
            $variantIndex = 0;

            // Create variants for each combination
            foreach ($caseMaterials as $caseMaterial) {
                foreach ($dialColors as $dialColor) {
                    $variantIndex++;
                    
                    // Price adjustment based on material
                    $priceMultiplier = match($caseMaterial->getTranslation('value', 'en')) {
                        'Gold', 'Yellow Gold', 'Rose Gold' => 1.3,
                        'Titanium' => 1.15,
                        default => 1.0,
                    };

                    $variantPrice = $basePrice * $priceMultiplier;
                    $variantStock = rand(2, 15);
                    $totalVariantStock += $variantStock;

                    // Create SKU from attribute values
                    $materialCode = strtoupper(substr($caseMaterial->getTranslation('value', 'en'), 0, 3));
                    $colorCode = strtoupper(substr($dialColor->getTranslation('value', 'en'), 0, 3));
                    $sku = "{$product->sku}-{$materialCode}-{$colorCode}";

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'price' => round($variantPrice, 2),
                        'discount_price' => rand(0, 1) ? round($variantPrice * 0.9, 2) : null,
                        'stock' => $variantStock,
                        'is_primary' => $variantIndex === 1,
                        'is_active' => true,
                    ]);

                    // Attach both attribute values to the variant
                    $variant->attributeValues()->attach([
                        $caseMaterial->id,
                        $dialColor->id,
                    ]);

                    $variantCount++;
                }
            }

            // Update product stock to total of all variants
            $product->update(['stock' => $totalVariantStock]);
        }

        $this->command->info("   ✓ Created {$variantCount} Product Variants (multi-attribute combinations)");
    }

    /**
     * Seed Wishlists for Customers
     */
    private function seedWishlists(): void
    {
        $this->command->info('❤️ Seeding Wishlists...');

        $customers = User::role('customer')->get();
        $products = Product::all();
        $wishlistCount = 0;

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->info('   ⚠️ No products or customers found, skipping wishlists');
            return;
        }

        foreach ($customers as $customer) {
            // Each customer gets 2-5 random products in wishlist
            $randomProducts = $products->random(min(rand(2, 5), $products->count()));

            foreach ($randomProducts as $product) {
                Wishlist::firstOrCreate([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                ]);
                $wishlistCount++;
            }
        }

        $this->command->info("   ✓ Created {$wishlistCount} Wishlist Items");
    }

    /**
     * Seed Sample Orders
     */
    private function seedOrders(): void
    {
        $this->command->info('📋 Seeding Sample Orders...');

        $customers = User::role('customer')->get();
        $stores = Store::all();
        $orderCount = 0;
        $itemCount = 0;

        if ($customers->isEmpty() || $stores->isEmpty()) {
            $this->command->info('   ⚠️ No customers or stores found, skipping orders');
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'stripe', 'paypal'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $cities = ['Cairo', 'Alexandria', 'Giza', 'Mansoura', 'Tanta'];

        foreach ($customers as $customer) {
            // Each customer gets 1-3 orders
            $ordersToCreate = rand(1, 3);

            for ($i = 0; $i < $ordersToCreate; $i++) {
                $store = $stores->random();
                $products = Product::where('store_id', $store->id)->get();

                if ($products->isEmpty()) {
                    continue;
                }

                $orderProducts = $products->random(min(rand(1, 3), $products->count()));
                $total = 0;

                // Calculate total first
                foreach ($orderProducts as $product) {
                    $price = $product->discount_price ?? $product->price;
                    $total += $price * rand(1, 2);
                }

                $order = Order::create([
                    'user_id' => $customer->id,
                    'store_id' => $store->id,
                    'status' => $statuses[array_rand($statuses)],
                    'total' => $total,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                    'shipping_details' => [
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'address' => 'شارع التحرير، مبنى ' . rand(1, 100),
                        'city' => $cities[array_rand($cities)],
                        'country' => 'Egypt',
                        'postal_code' => rand(10000, 99999),
                    ],
                ]);
                $orderCount++;

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 2);
                    $price = $product->discount_price ?? $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $product->variants->first()?->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                    $itemCount++;
                }
            }
        }

        $this->command->info("   ✓ Created {$orderCount} Orders with {$itemCount} Items");
    }
}
