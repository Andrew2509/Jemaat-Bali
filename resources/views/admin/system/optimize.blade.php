@extends('layouts.admin')

@section('title', 'Optimasi Cache & Performa')
@section('breadcrumb', 'Optimasi Cache')

@section('content')
    <!-- Notifikasi -->
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-rose-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="mb-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI FUNDRAISER</p>
        <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Optimasi Cache &amp; Performa</h1>
        <p class="mt-1 text-sm text-stone-500">
            Jalankan <code class="rounded bg-stone-100 px-1.5 py-0.5 text-[11px] font-semibold text-stone-700">php artisan optimize</code>
            langsung dari browser &mdash; tanpa perlu akses terminal/SSH server.
        </p>
    </div>

    <!-- Status Cache -->
    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($status as $item)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-2xs">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-bold uppercase tracking-wider text-stone-400">{{ $item['label'] }}</p>
                        <p class="mt-1 truncate text-xs font-semibold text-stone-700">{{ $item['command'] }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $item['active'] ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-stone-100 text-stone-500 ring-1 ring-stone-200' }}">
                        {{ $item['active'] ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
                <p class="mt-2 text-[11px] leading-relaxed text-stone-400">{{ $item['note'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <!-- Aksi -->
        <div class="xl:col-span-1 space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
                <h2 class="font-serif text-lg font-bold text-stone-900">Aksi Optimasi</h2>
                <p class="mt-1 text-xs leading-relaxed text-stone-500">
                    Jalankan perintah memoisasi Laravel. Aman dilakukan kapan saja, tidak mengunci data.
                </p>

                <form method="POST" action="{{ route('admin.system.optimize.run') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-3 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>Jalankan Optimize Sekarang</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.system.clear-cache.run') }}" class="mt-2.5">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white hover:bg-stone-50 px-4 py-2.5 text-xs font-bold text-stone-700 shadow-2xs transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.5 21 3m0 0-3.75 18M21 3h-6.75M3.75 7.5 10.5 3m-6.75 0 3.75 18m0 0h6.75" />
                        </svg>
                        <span>Bersihkan Semua Cache</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.system.setup-database.run') }}" class="mt-2.5">
                    @csrf
                    <button type="submit" onclick="return confirm('Jalankan migrasi database & seeding hak akses di server?')" class="flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 px-4 py-2.5 text-xs font-bold text-amber-900 shadow-2xs transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-amber-600">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2.5 2.5 0 0 0-2.5 2.5v6a2.5 2.5 0 0 0 2.5 2.5h10a2.5 2.5 0 0 0 2.5-2.5v-6A2.5 2.5 0 0 0 17.5 9H15V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                        </svg>
                        <span>Sinkronkan Database &amp; Hak Akses</span>
                    </button>
                </form>
            </div>

            <!-- Peringatan -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-bold text-amber-900">Penting setelah Configuration Cache aktif</p>
                <p class="mt-1.5 text-[11px] leading-relaxed text-amber-800">
                    Nilai <code class="rounded bg-amber-100 px-1 py-0.5 font-semibold">.env</code> (APP_DEBUG, CACHE_STORE, SESSION_DRIVER, dll.) dibaca
                    satu kali lalu dikunci ke <code class="rounded bg-amber-100 px-1 py-0.5 font-semibold">bootstrap/cache/config.php</code>.
                    Setelah mengubah <code class="rounded bg-amber-100 px-1 py-0.5 font-semibold">.env</code> di server, klik
                    <strong>Bangun Ulang Cache</strong> agar perubahan terbaca.
                </p>
            </div>

            <!-- Environment -->
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
                <h2 class="font-serif text-lg font-bold text-stone-900">Environment Server</h2>
                <dl class="mt-3 space-y-2">
                    @foreach ($environment as $row)
                        <div class="flex items-center justify-between gap-3 border-b border-dashed border-stone-200 pb-2 last:border-0 last:pb-0">
                            <dt class="text-[11px] font-semibold tracking-wide text-stone-400">{{ $row['label'] }}</dt>
                            <dd class="truncate text-xs font-bold text-stone-700">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <!-- Hasil Eksekusi -->
        <div class="xl:col-span-2 space-y-5">
            @php
                $steps = session('system_steps', $history['steps'] ?? []);
                $historyTitle = $history['action'] ?? null;
                $historyAt = $history['at'] ?? null;
                $historyDuration = $history['duration_ms'] ?? null;
            @endphp

            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Hasil Eksekusi Terakhir</h2>
                    @if ($historyAt)
                        <span class="text-[11px] font-semibold text-stone-400">
                            {{ $historyTitle }} &middot; {{ $historyAt }}
                            @if ($historyDuration !== null)
                                &middot; {{ $historyDuration }} ms
                            @endif
                        </span>
                    @endif
                </div>

                @if (empty($steps))
                    <div class="mt-4 rounded-xl border border-dashed border-stone-200 bg-stone-50 px-4 py-8 text-center">
                        <p class="text-xs font-semibold text-stone-500">Belum ada riwayat eksekusi pada server ini.</p>
                        <p class="mt-1 text-[11px] text-stone-400">Klik salah satu tombol di sebelah kiri untuk menjalankan perintah pertama.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-2.5">
                        @foreach ($steps as $step)
                            <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <span class="h-2 w-2 shrink-0 rounded-full {{ $step['success'] ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <code class="truncate text-xs font-bold text-stone-700">{{ $step['command'] }}</code>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider {{ $step['success'] ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $step['success'] ? 'Berhasil' : 'Gagal' }}
                                    </span>
                                </div>
                                @if (! empty($step['output']))
                                    <pre class="mt-2 max-h-40 overflow-auto whitespace-pre-wrap break-words rounded-lg bg-stone-900 px-3 py-2 text-[10.5px] leading-relaxed text-stone-100">{{ $step['output'] }}</pre>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
                <h2 class="font-serif text-lg font-bold text-stone-900">Apa Saja yang Dilakukan Optimize?</h2>
                <ul class="mt-3 space-y-2.5 text-xs leading-relaxed text-stone-600">
                    <li class="flex gap-2.5">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#b55322]"></span>
                        <span><code class="rounded bg-stone-100 px-1 py-0.5 font-semibold">config:cache</code> &mdash; mengunci seluruh konfigurasi &amp; nilai <code class="rounded bg-stone-100 px-1 py-0.5 font-semibold">.env</code> agar tidak dibaca tiap request.</span>
                    </li>
                    <li class="flex gap-2.5">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#b55322]"></span>
                        <span><code class="rounded bg-stone-100 px-1 py-0.5 font-semibold">event:cache</code> &mdash; memuat daftar event sekali saja (mempercepat-siapa handler listener).</span>
                    </li>
                    <li class="flex gap-2.5">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#b55322]"></span>
                        <span><code class="rounded bg-stone-100 px-1 py-0.5 font-semibold">route:cache</code> &mdash; menyimpan peta route agar tidak dihitung ulang pada setiap request.</span>
                    </li>
                    <li class="flex gap-2.5">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#b55322]"></span>
                        <span><code class="rounded bg-stone-100 px-1 py-0.5 font-semibold">view:cache</code> &mdash; mengompilasi seluruh template Blade sekali, termasuk halaman baru.</span>
                    </li>
                </ul>
                <p class="mt-4 rounded-xl bg-[#f5f2ec] px-3.5 py-3 text-[11px] leading-relaxed text-stone-600">
                    Menjalankan optimize <strong>tidak menghapus data</strong> di database. Gunakan tombol
                    <strong>Bangun Ulang Cache</strong> setiap kali selesai <code class="rounded bg-white px-1 py-0.5 font-semibold">git pull</code> di server.
                </p>
            </div>
        </div>
    </div>
@endsection
