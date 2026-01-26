<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->stores()->where('id', $this->store_id)->exists();
    }

    public function rules(): array
    {

        if ($this->has('update_specs_only')) {
            return [
                'specifications' => 'nullable|array',
                'store_id' => 'required',
            ];
        }

        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'store_id' => ['required', 'exists:stores,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'slug' => ['required', 'string', Rule::unique('products')->ignore($productId)],
            'sku' => [
                'required',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('store_id', $this->store_id);
                })->ignore($productId)
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(['new', 'used', 'refurbished'])],
            'is_active' => ['boolean'],
            'main_menu' => ['boolean'],
            'main_store' => ['boolean'],
            'specifications' => ['nullable', 'array'],
            'image_paths' => ['nullable', 'array'],
            'main_image_path' => ['nullable', 'string'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.attribute_value_ids' => ['required_with:variants', 'array'],
            'variants.*.attribute_value_ids.*' => ['exists:attribute_values,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('variants') && is_array($this->variants) && count($this->variants) > 0) {
                $totalVariantStock = array_sum(array_column($this->variants, 'stock'));
                $mainStock = (int) $this->stock;

                if ($totalVariantStock > $mainStock) {
                    $validator->errors()->add('variants', __('The sum of variant stocks (:total) cannot exceed the total product stock (:main).', [
                        'total' => $totalVariantStock,
                        'main' => $mainStock
                    ]));
                }
            }
        });
    }
}
