<?php


if (! function_exists('rupiah')) {
    /** Format angka menjadi Rupiah. Contoh: rupiah(500000) => "Rp 500.000" */
    function rupiah(int|float|null $value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('tanggal')) {
    /** Format tanggal Indonesia. Contoh: tanggal($date) => "12 Nov 2026" */
    function tanggal($date, string $format = 'd M Y'): string
    {
        if (! $date) {
            return '-';
        }

        return \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat($format);
    }
}

if (! function_exists('status_label')) {
    /** Label Bahasa Indonesia untuk status booking / pembatalan. */
    function status_label(string $status): string
    {
        return [
            'pending' => 'Menunggu',
            'confirmed' => 'Terkonfirmasi',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ][$status] ?? ucfirst($status);
    }
}
