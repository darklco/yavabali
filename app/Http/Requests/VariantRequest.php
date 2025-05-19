<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'size' => 'nullable|array',
            'image_front' => 'nullable|array',
            'image_back' => 'nullable|array',
        ];

        // Hanya tambah validasi `product_id` saat store
        if ($this->isMethod('post')) {
            $rules['product_id'] = 'required|uuid|exists:products,id';
        }

        return $rules;
    }
}
