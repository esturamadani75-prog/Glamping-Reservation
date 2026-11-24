{{-- [Anggota 3] Tombol "Ajukan Pembatalan" di halaman detail booking.
     Disisipkan ke halaman Anggota 2 lewat @includeIf, jadi aman walau file ini belum ada. --}}
@auth
    @if ($booking->isOwnedBy(auth()->user()) && \App\Services\RefundCalculator::canCancel($booking))
        <a href="{{ route('user.cancel.create', $booking) }}" class="btn-outline mt-6 text-red-600">Ajukan Pembatalan</a>
    @endif
@endauth
