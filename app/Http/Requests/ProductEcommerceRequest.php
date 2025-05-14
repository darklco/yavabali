<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductEcommerceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Add authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tittle' => 'required|json',
            'icon' => 'nullable|json',
            'link' => 'nullable|json',
            'type' => ['nullable', Rule::in(['local', 'international'])],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $attributes = ['tittle', 'icon', 'link'];
        $updates = [];

        foreach ($attributes as $attribute) {
            if (is_array($this->$attribute)) {
                $updates[$attribute] = json_encode($this->$attribute);
            }
        }

        if (!empty($updates)) {
            $this->merge($updates);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tittle.required' => 'The title field is required.',
            'tittle.json' => 'The title must be a valid JSON string.',
            'icon.json' => 'The icon must be a valid JSON string.',
            'link.json' => 'The link must be a valid JSON string.',
            'type.in' => 'The type must be either local or international.',
        ];
    }
}