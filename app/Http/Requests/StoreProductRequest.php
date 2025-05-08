<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can implement authorization logic here if needed
        // For example, check if the user has permission to create products
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'types' => 'nullable|json',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:products,slug',
            'description' => 'nullable|string',
            'nutrient' => 'nullable|json',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'The product title is required',
            'title.max' => 'The product title must not exceed 255 characters',
            'slug.unique' => 'This slug is already in use',
            'slug.max' => 'The slug must not exceed 100 characters',
            'types.json' => 'The types field must be a valid JSON string',
            'nutrient.json' => 'The nutrient field must be a valid JSON string',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // If types or nutrient is provided as array, convert to JSON
        if ($this->has('types') && is_array($this->types)) {
            $this->merge([
                'types' => json_encode($this->types)
            ]);
        }

        if ($this->has('nutrient') && is_array($this->nutrient)) {
            $this->merge([
                'nutrient' => json_encode($this->nutrient)
            ]);
        }
    }
}