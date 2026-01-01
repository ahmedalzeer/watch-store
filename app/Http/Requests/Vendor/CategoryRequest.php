<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('store_id', $this->user()->store->id)
            ],
            'image_path' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
