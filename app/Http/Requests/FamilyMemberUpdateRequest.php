<?php

namespace App\Http\Requests;

use App\Models\FamilyMember;
use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['nullable','string','email','unique:users,email,'. FamilyMember::find($this->route('family_member'))->user_id], 
            'password' => ['required', 'string', 'min:8'],
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'identity_number' => ['required', 'integer'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['required', 'string'],
            'occupation' => ['required', 'string'],
            'martial_status' => ['required', 'string', 'in:married,single'],
            'relation' => ['required', 'string', 'in:wife,child,husband']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Password',
            'head_of_family_id' => 'Kepala Keluarga',
            'profile_picture' => 'Foto Profil',
            'identity_number' => 'Nomor Identitas',
            'gender' => 'Jenis Kelamin',
            'phone_number' => 'Nomor Telepon',
            'occupation' => 'Pekerjaan',
            'martial_status' => 'Status Perkawinan',
            'relation' => 'Hubungan'
        ];
    }
}
