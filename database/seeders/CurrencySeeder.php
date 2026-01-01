<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [

            ['ar' => 'ريال سعودي', 'en' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => '﷼'],
            ['ar' => 'درهم إماراتي', 'en' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['ar' => 'دينار كويتي', 'en' => 'Kuwaiti Dinar', 'code' => 'KWD', 'symbol' => 'د.ك'],
            ['ar' => 'ريال قطري', 'en' => 'Qatari Riyal', 'code' => 'QAR', 'symbol' => 'ر.ق'],
            ['ar' => 'دينار بحريني', 'en' => 'Bahraini Dinar', 'code' => 'BHD', 'symbol' => 'د.ب'],
            ['ar' => 'ريال عماني', 'en' => 'Omani Rial', 'code' => 'OMR', 'symbol' => 'ر.ع'],


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
                    'name' => [
                        'ar' => $currency['ar'],
                        'en' => $currency['en']
                    ],
                    'symbol' => $currency['symbol'],
                    'exchange_rate' => 1.00,
                    'is_active' => true,
                ]
            );
        }
    }
}
