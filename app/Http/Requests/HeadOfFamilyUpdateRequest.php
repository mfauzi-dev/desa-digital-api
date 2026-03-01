<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeadOfFamilyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'identity_number' => ['required', 'integer'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['required', 'string'],
            'occupation' => ['required', 'string'],
            'martial_status' => ['required', 'string', 'in:married,single']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Password',
            'profile_picture' => 'Foto Profil',
            'identity_number' => 'Nomor Identitas',
            'gender' => 'Jenis Kelamin',
            'phone_number' => 'Nomor Telepon',
            'occupation' => 'Pekerjaan',
            'martial_status' => 'Status Perkawinan'
        ];
    }
}
