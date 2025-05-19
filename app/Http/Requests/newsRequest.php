<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|array',
            'excerpt' => 'required|array',
            'content' => 'required|array',
            'author' => 'required|array',
            'media_url' => 'nullable|string',
            'is_highlight' => 'boolean',
        ];

        // Untuk update, jadikan semua optional
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules = array_map(function ($rule) {
                return str_replace('required', 'sometimes', $rule);
            }, $rules);
        }

        return $rules;
    }
}
