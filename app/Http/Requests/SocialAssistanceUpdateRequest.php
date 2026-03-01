<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceUpdateRequest extends FormRequest
{
     public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
