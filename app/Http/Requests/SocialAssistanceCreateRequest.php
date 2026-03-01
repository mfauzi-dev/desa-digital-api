<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceCreateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'thumbnail' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'category' => ['required', 'string', 'in:staple,cash,subsidized fuel'],
            'amount' => ['required'],
            'provider' => ['required', 'string'],
            'description' => ['required'],
            'is_available' => ['boolean']
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail' => 'Thumbnail',
            'name' => 'Nama',
            'category' => 'Kategori',
            'amount' => 'Jumlah Bantuan',
            'provider' => 'Penyedia',
            'description' => 'Deskripsi',
            'is_available' => 'Ketersediaan'
        ];
    }
}
