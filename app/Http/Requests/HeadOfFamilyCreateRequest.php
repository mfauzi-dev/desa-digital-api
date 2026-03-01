<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeadOfFamilyCreateRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'identity_number' => ['required', 'string'],
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
