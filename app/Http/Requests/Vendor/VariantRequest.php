<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->stores()->where('id', $this->store_id)->exists();
    }

    public function rules(): array
    {
        $variantId = $this->route('variant') ? $this->route('variant')->id : null;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_variants')->where(function ($query) {
                    return $query->whereHas('product', function ($q) {
                        $q->where('store_id', $this->store_id);
                    });
                })->ignore($variantId)
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_primary' => ['boolean'],
            'attribute_value_ids' => ['nullable', 'array'],
            'attribute_value_ids.*' => ['exists:attribute_values,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('product_id')) {
                $product = \App\Models\Product::find($this->product_id);
                
                // Check if product belongs to the store
                if ($product && $product->store_id !== $this->store_id) {
                    $validator->errors()->add('product_id', __('messages.product_does_not_belong_to_store'));
                }

                // Check if variant stock exceeds product stock
                if ($product && $this->has('stock')) {
                    $totalVariantStock = \App\Models\ProductVariant::where('product_id', $this->product_id)
                        ->where('id', '!=', $this->route('variant')?->id)
                        ->sum('stock') + $this->stock;

                    if ($totalVariantStock > $product->stock) {
                        $validator->errors()->add('stock', __('messages.variant_stock_exceeds_product_stock'));
                    }
                }
            }
        });
    }
}
