<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Tidak Diizinkan | {{ $programInfo->nama_program ?? 'Jemaat Bali' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f6f0] font-sans text-stone-900 antialiased flex items-center justify-center p-6 selection:bg-[#b55322]/20 selection:text-[#b55322]">
    <div class="w-full max-w-md rounded-3xl bg-white border border-[#e8e4dc] p-8 shadow-sm text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-200/80 mb-5 shadow-2xs">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#b55322]">KODE 403 · AKSES DITOLAK</p>
        <h1 class="mt-2 font-serif text-2xl font-bold text-stone-900">Wewenang Tidak Mencukupi</h1>
        
        <p class="mt-3 text-xs leading-relaxed text-stone-600">
            {{ $exception->getMessage() ?: 'Akun Anda tidak memiliki izin untuk membuka halaman atau melakukan tindakan ini.' }}
        </p>

        <div class="mt-4 rounded-2xl bg-stone-50 border border-[#ede8df] p-3 text-left">
            <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Status Akun Anda:</p>
            <p class="text-xs font-semibold text-stone-800 mt-0.5">
                {{ Auth::user()->name ?? 'Tamu' }} 
                <span class="font-normal text-stone-500">({{ Auth::user()->role_display_name ?? 'Tanpa Peran' }})</span>
            </p>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-2.5 justify-center">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-5 py-2.5 text-xs font-semibold text-white shadow-2xs transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
            <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">
                <span>Halaman Sebelumnya</span>
            </a>
        </div>
    </div>
</body>
</html>
