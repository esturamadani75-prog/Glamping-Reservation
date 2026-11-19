<?php

// [Anggota 2] Validasi form reservasi

namespace App\Http\Requests\Booking;

use App\Models\GlampingUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // tamu tanpa akun juga boleh reservasi
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['has_insurance' => $this->boolean('has_insurance')]);
    }

    public function rules(): array
    {
        $unit = GlampingUnit::find($this->input('glamping_unit_id'));

        return [
            'glamping_unit_id' => ['required', Rule::exists('glamping_units', 'id')->where('is_active', true)],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'unit_qty' => ['required', 'integer', 'min:1', 'max:'.($unit?->stock_total ?? 1)],
            'guest_name' => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:150'],
            'guest_phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'has_insurance' => ['boolean'],
        ];
    }

    /** Validasi tambahan: lama menginap maksimal sesuai config. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['check_in_date', 'check_out_date'])) {
                    return;
                }
                $nights = Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date));
                $max = config('glamping.max_nights', 14);
                if ($nights > $max) {
                    $validator->errors()->add('check_out_date', "Maksimal lama menginap adalah {$max} malam per reservasi.");
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'glamping_unit_id' => 'unit glamping',
            'check_in_date' => 'tanggal check-in',
            'check_out_date' => 'tanggal check-out',
            'unit_qty' => 'jumlah unit',
            'guest_name' => 'nama pemesan',
            'guest_email' => 'email',
            'guest_phone' => 'nomor telepon',
        ];
    }

    public function messages(): array
    {
        return [
            'check_in_date.after_or_equal' => 'Tanggal check-in tidak boleh sebelum hari ini.',
            'check_out_date.after' => 'Tanggal check-out harus setelah tanggal check-in.',
            'guest_phone.regex' => 'Format nomor telepon tidak valid (contoh: 081234567890).',
        ];
    }
}
