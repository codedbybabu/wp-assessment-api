<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'id' => 'sometimes|integer|exists:wp_posts,ID',
            'slug' => 'sometimes|string|exists:wp_posts,post_name'
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => 'Product not found',
            'slug.exists' => 'Product not found'
        ];
    }
}
