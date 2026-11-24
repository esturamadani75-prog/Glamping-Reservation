<?php

// [Anggota 3] Validasi form pengajuan pembatalan

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreCancellationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // hanya pemilik booking yang boleh mengajukan
        return $this->route('booking')->isOwnedBy($this->user());
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'agree' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return ['reason' => 'alasan pembatalan', 'agree' => 'persetujuan ketentuan refund'];
    }
}
