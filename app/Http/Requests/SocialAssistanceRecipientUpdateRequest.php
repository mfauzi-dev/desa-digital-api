<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceRecipientUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'social_assistance_id' => ['required', 'exists:social_assistances,id'],
            'head_of_family_id' => ['required', 'exists:head_of_families,id'],
            'amount' => ['required', 'decimal'],
            'reason' => ['required', 'string'],
            'bank' => ['required', 'string', 'in:bri,bni,bca,mandiri'],
            'account_number' => ['required'],
            'proof' => ['nullable', 'image'],
            'status' => ['nullable', 'string', 'in:pending,approved,rejected']

        ];
    }
}
