<?php

// [Anggota 2] Validasi form cek status booking

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CheckBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'booking_code' => strtoupper(trim((string) $this->booking_code)),
            'contact' => trim((string) $this->contact),
        ]);
    }

    public function rules(): array
    {
        return [
            'booking_code' => ['required', 'string', 'max:20'],
            'contact' => ['required', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return ['booking_code' => 'kode booking', 'contact' => 'email / no. telepon'];
    }
}
