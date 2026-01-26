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
        $products = Product::all();
        
        foreach ($products as $product) {
            // 1. Create Attribute (Case Material)
            $caseAttribute = Attribute::updateOrCreate(
                ['store_id' => $product->store_id, 'name->en' => 'Case Material'],
                ['name' => ['en' => 'Case Material', 'ar' => 'مادة الهيكل']]
            );

            // 2. Create Attribute Values
            $gold = AttributeValue::updateOrCreate(
                ['attribute_id' => $caseAttribute->id, 'value->en' => 'Gold'],
                ['value' => ['en' => 'Gold', 'ar' => 'ذهبي']]
            );
            $silver = AttributeValue::updateOrCreate(
                ['attribute_id' => $caseAttribute->id, 'value->en' => 'Silver'],
                ['value' => ['en' => 'Silver', 'ar' => 'فضي']]
            );

            // 3. Create Variants if they don't exist
            if ($product->variants()->count() === 0) {
                $v1 = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku . '-GLD',
                    'price' => $product->price * 1.2,
                    'stock' => rand(5, 20),
                    'is_primary' => true
                ]);
                $v1->attributeValues()->attach($gold->id);

                $v2 = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku . '-SLV',
                    'price' => $product->price,
                    'stock' => rand(5, 20),
                    'is_primary' => false
                ]);
                $v2->attributeValues()->attach($silver->id);
            }
        }
    }
}
