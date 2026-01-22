<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::first();
        if (!$product) return;

        // 1. Create Attribute (Color)
        $colorAttribute = Attribute::create([
            'store_id' => $product->store_id,
            'name' => [
                'en' => 'Color',
                'ar' => 'اللون'
            ]
        ]);

        // 2. Create Attribute Values
        $gold = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => [
                'en' => 'Gold',
                'ar' => 'ذهبي'
            ]
        ]);

        $silver = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => [
                'en' => 'Silver',
                'ar' => 'فضي'
            ]
        ]);

        $black = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => [
                'en' => 'Black',
                'ar' => 'أسود'
            ]
        ]);

        // 3. Create Variants
        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $product->sku . '-GOLD',
            'price' => 10000,
            'stock' => 10,
            'is_primary' => true
        ]);
        $variant1->attributeValues()->attach($gold->id);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $product->sku . '-SILVER',
            'price' => 8000,
            'stock' => 5,
            'is_primary' => false
        ]);
        $variant2->attributeValues()->attach($silver->id);

        $variant3 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $product->sku . '-BLACK',
            'price' => 9000,
            'stock' => 15,
            'is_primary' => false
        ]);
        $variant3->attributeValues()->attach($black->id);
    }
}
