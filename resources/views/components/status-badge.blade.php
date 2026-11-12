{{-- [Anggota 1] Badge status booking / pembatalan. Pemakaian: <x-status-badge :status="$booking->status" /> --}}
@props(['status'])
@php
    $classes = [
        'pending'   => 'bg-amber-100 text-amber-800 ring-amber-200',
        'confirmed' => 'bg-paddy-50 text-paddy-700 ring-paddy-200',
        'completed' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        'approved'  => 'bg-paddy-50 text-paddy-700 ring-paddy-200',
        'rejected'  => 'bg-red-50 text-red-700 ring-red-200',
    ][$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset $classes"]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
    {{ status_label($status) }}
</span>
