{{-- [Anggota 1] Notifikasi flash (success / error / validasi) --}}
@if (session('success') || session('error') || $errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 7000)"
         class="mx-auto mb-6 max-w-7xl px-4 sm:px-6 lg:px-8 {{ $inner ?? false ? '!px-0' : '' }}">
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-xl border border-paddy-200 bg-paddy-50 px-4 py-3 text-sm text-paddy-800">
                <span class="mt-0.5">✔</span><span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-paddy-700/60 hover:text-paddy-800">✕</button>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <span class="mt-0.5">!</span><span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-700/60 hover:text-red-800">✕</button>
            </div>
        @endif
        @if ($errors->any() && ! session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold">Periksa kembali isian Anda:</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
