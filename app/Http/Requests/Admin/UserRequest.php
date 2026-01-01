<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'role'     => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'avatar_path' => ['nullable', 'string'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => __('messages.name'),
            'email' => __('messages.email'),
            'role'  => __('messages.role'),
            'phone' => __('messages.phone'),
            'password' => __('messages.password'),
        ];
    }
}
