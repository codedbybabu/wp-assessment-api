<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:' . \App\Helpers\Constants::TABLE_USERS . ',' . \App\Models\User::COL_USER_EMAIL,
            'password' => 'required|string|min:6'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.exists' => 'These credentials do not match our records',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters'
        ];
    }
}
