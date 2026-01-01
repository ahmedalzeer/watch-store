<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCurrency = Currency::where('code', 'EGP')->first() ?? Currency::first();

        $vendors = User::role('vendor')->get();

        foreach ($vendors as $vendor) {

            preg_match('/\d+/', $vendor->email, $matches);
            $vendorNumber = isset($matches[0]) ? (int)$matches[0] : 0;

            $storesCount = 1;
            if ($vendorNumber === 4) {
                $storesCount = 2;
            } elseif ($vendorNumber === 6) {
                $storesCount = 3;
            }

            for ($i = 1; $i <= $storesCount; $i++) {
                $suffix = $storesCount > 1 ? "-{$i}" : "";

                Store::updateOrCreate(

                    ['subdomain' => "store-v{$vendorNumber}{$suffix}"],
                    [
                        'user_id'     => $vendor->id,
                        'currency_id' => $defaultCurrency->id,
                        'name' => [
                            'ar' => "متجر {$vendor->name} {$suffix}",
                            'en' => "{$vendor->name} Store {$suffix}"
                        ],
                        'description' => [
                            'ar' => "وصف متجر {$vendor->name}",
                            'en' => "Description for {$vendor->name}"
                        ],
                        'theme_color'   => '#7e3af2',
                        'contact_email' => $vendor->email,
                        'is_active'     => true,
                    ]
                );
            }
        }
    }
}
