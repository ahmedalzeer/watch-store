<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller via authorizeStore
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:main,promo,footer,promotion,sale,seasonal,featured,announcement',
            'title.ar' => 'required|string|max:255',
            'title.en' => 'required|string|max:255',
            'description.ar' => 'nullable|string|min:3',
            'description.en' => 'nullable|string|min:3',
            'link' => 'nullable|url',
            'active' => 'boolean',
            'order' => 'integer',
            'image_path' => 'nullable|string',
        ];
    }
}
