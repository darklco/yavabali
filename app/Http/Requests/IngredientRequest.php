<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
{
    /**
     * Tentukan apakah user bisa membuat request ini.
     */
    public function authorize(): bool
    {
        return true; // set true agar semua user bisa akses (bisa disesuaikan dengan auth)
    }

    /**
     * Aturan validasi untuk request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|json',
            'image' => 'nullable|json',
            'origin' => 'nullable|json',
        ];
    }

    /**
     * Pesan custom (opsional).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan wajib diisi dalam format JSON.',
            'name.json' => 'Format nama harus JSON (misalnya multilingual).',
            'image.json' => 'Format gambar harus JSON.',
            'origin.json' => 'Format asal bahan harus JSON.',
        ];
    }
}
