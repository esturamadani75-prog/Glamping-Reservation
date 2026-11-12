{{-- [Anggota 1] <head> bersama: Tailwind CDN + palet warna, Alpine.js, font --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@hasSection('title')@yield('title') · @endif Merapi Glamping Tengah Sawah</title>
<meta name="description" content="Bangun Pagi Disapa Merapi, Malam Tidur di Tengah Sawah — reservasi glamping di Cangkringan, Sleman.">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><path d='M2 26 L13 8 L18 15 L21 11 L30 26 Z' fill='%232D6A4F'/><circle cx='24' cy='7' r='3' fill='%23D97706'/></svg>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    paddy:   { DEFAULT: '#2D6A4F', 50: '#EEF6F1', 100: '#D5EADF', 200: '#A9D3BD', 500: '#40916C', 600: '#2D6A4F', 700: '#24563F', 800: '#1B4332', 900: '#133024' },
                    harvest: { DEFAULT: '#D97706', 50: '#FFF8EB', 100: '#FDEBC8', 200: '#FAD38A', 500: '#F59E0B', 600: '#D97706', 700: '#B45309' },
                    canvas:  { DEFAULT: '#FAF9F5', dark: '#F1EFE7' },
                    merapi:  { DEFAULT: '#1E293B', light: '#334155' },
                },
                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
                },
            },
        },
    };
</script>
<style>[x-cloak] { display: none !important; }</style>
{{-- Kelas komponen reusable (diproses oleh Tailwind CDN) --}}
<style type="text/tailwindcss">
    @layer components {
        .btn { @apply inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50; }
        .btn-primary { @apply btn bg-paddy text-white hover:bg-paddy-700 focus:ring-paddy; }
        .btn-accent { @apply btn bg-harvest text-white hover:bg-harvest-700 focus:ring-harvest; }
        .btn-outline { @apply btn border border-slate-300 bg-white text-merapi hover:bg-canvas-dark focus:ring-slate-400; }
        .btn-danger { @apply btn bg-red-600 text-white hover:bg-red-700 focus:ring-red-500; }
        .btn-sm { @apply px-3 py-1.5 text-xs; }
        .card { @apply rounded-2xl border border-slate-200/70 bg-white shadow-sm; }
        .label { @apply mb-1.5 block text-sm font-medium text-merapi; }
        .input { @apply block w-full rounded-xl border-slate-300 bg-white text-sm text-merapi shadow-sm focus:border-paddy focus:ring-paddy; }
        .input-error { @apply border-red-400 focus:border-red-500 focus:ring-red-500; }
        .table-th { @apply whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500; }
        .table-td { @apply whitespace-nowrap px-4 py-3 text-sm text-slate-700; }
        .section-title { @apply font-display text-3xl font-semibold text-merapi sm:text-4xl; }
        .eyebrow { @apply text-xs font-bold uppercase tracking-[0.2em] text-harvest; }
    }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
