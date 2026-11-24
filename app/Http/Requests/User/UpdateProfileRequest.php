<?php

// [Anggota 3] Validasi edit data pribadi

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Format nomor telepon tidak valid (contoh: 081234567890).'];
    }
}
