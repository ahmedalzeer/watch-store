<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('vendor') &&
            $this->user()->stores()->where('id', $this->store_id)->exists();
    }

    public function rules(): array
    {

        $categoryId = $this->route('category');

        return [

            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],

            'store_id' => ['required', 'exists:stores,id'],

            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('store_id', $this->store_id);
                })
            ],

            'icon' => ['nullable', 'string'],

            'is_active' => ['boolean'],
            'main_menu' => ['boolean'],
            'main_store' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name.ar' => __('messages.name_ar'),
            'name.en' => __('messages.name_en'),
            'store_id' => __('messages.store'),
            'parent_id' => __('messages.parent_category'),
        ];
    }
}
