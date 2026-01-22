<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translatable_fields_return_strings_in_to_array()
    {
        $product = Product::factory()->create([
            'name' => [
                'en' => 'English Name',
                'ar' => 'Arabic Name'
            ]
        ]);

        // Test English
        app()->setLocale('en');
        $arrayEn = $product->fresh()->toArray();
        $this->assertEquals('English Name', $arrayEn['name']);
        $this->assertIsString($arrayEn['name']);

        // Test Arabic
        app()->setLocale('ar');
        $arrayAr = $product->fresh()->toArray();
        $this->assertEquals('Arabic Name', $arrayAr['name']);
        $this->assertIsString($arrayAr['name']);
    }
}
