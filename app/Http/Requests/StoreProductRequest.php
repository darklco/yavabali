<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|array',
            'image' => 'nullable|array',
            'description' => 'nullable|array',
            'ingredient_id' => 'nullable|exists:ingredients,id',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
