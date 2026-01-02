<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        // التحقق من أن المستخدم يملك المتجر المرسل في الطلب
        return auth()->user()->stores()->where('id', $this->store_id)->exists();
    }

    public function rules(): bool|array
    {
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;

        return [
            'store_id' => ['required', 'exists:stores,id'],
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'website' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['boolean'],
            'image_path' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'main_menu' => ['boolean'],
            'main_store' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required' => __('messages.name_ar_required'),
            'name.en.required' => __('messages.name_en_required'),
            'slug.unique' => __('messages.slug_already_exists'),
            'website.url' => __('messages.invalid_url_format'),
        ];
    }
}
