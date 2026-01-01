<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = $this->route('store');

        return [
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'exists:users,id'
            ],
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'subdomain' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('stores', 'subdomain')->ignore($storeId),
            ],
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'currency_id' => 'required|exists:currencies,id',
            'theme_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.facebook' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'logo_path' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => __('messages.vendor'),
            'name.ar' => __('messages.name_ar'),
            'name.en' => __('messages.name_en'),
            'subdomain' => __('messages.subdomain'),
            'contact_email' => __('messages.email'),
            'phone' => __('messages.phone'),
            'currency' => __('messages.currency'),
            'social_links.facebook' => 'Facebook URL',
            'social_links.instagram' => 'Instagram URL',
            'social_links.twitter' => 'Twitter URL',
            'logo_path' => __('messages.store_logo'),
        ];
    }
}
