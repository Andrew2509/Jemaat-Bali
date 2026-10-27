@extends('layouts.app')

@section('title', 'Beli Kaos Saja — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pesan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': pilih desain, ukuran, dan tentukan jumlah. Tanpa langkah donasi.')

@php
    $designData = $designs->map(fn ($d) => [
        'id' => $d->id,
        'nama' => $d->nama,
        'harga' => $d->harga,
        'deskripsi' => $d->deskripsi ?? '',
        'gambar' => $d->gambarUrl(),
        'sizes' => $d->pilihanUkuran(),
    ])->values()->all();

    $heroShirts = $heroShirts ?? $designs->take(2)->values();

    $wzSteps = [
        1 => 'PILIH KAOS',
        2 => 'DATA & ALAMAT',
        3 => 'PEMBAYARAN',
    ];
@endphp

@section('content')
    {{-- ================= HERO HEADER (PERSIS DENGAN KAOS + DONASI) ================= --}}
    <section class="border-b border-line bg-canvas">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid items-center gap-8 py-10 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <nav class="text-xs font-semibold uppercase tracking-widest text-muted" aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2">
                            <li><a href="{{ route('home') }}" class="transition hover:text-primary">Home</a></li>
                            <li class="text-line">&rsaquo;</li>
                            <li class="text-ink">Pesanan</li>
                        </ol>
                    </nav>
                    <h1 class="mt-3 font-serif text-4xl font-extrabold tracking-tight text-secondary sm:text-5xl">Beli Kaos Saja</h1>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted sm:text-base">
                        Dukung pembangunan {{ $programInfo->nama_program ?? 'GMAHK Haleluya, Bali' }} dengan memesan kaos pilihanmu. Tanpa langkah donasi.
                    </p>
                </div>

                <div class="hidden lg:col-span-5 lg:block">
                    <div class="flex flex-col items-center">
                        <p class="-rotate-2 font-script text-3xl text-secondary">Satu Kaos, Sejuta Arti</p>
                        <div class="mt-4 flex items-start justify-center">
                            @if ($heroShirts->get(1))
                                <div class="z-10 -rotate-3 overflow-hidden rounded-2xl border border-line bg-white shadow-md">
                                    <img src="{{ $heroShirts->get(1)->gambarUrl() }}" alt="{{ $heroShirts->get(1)->nama }}" class="h-36 w-36 object-contain p-3">
                                </div>
                            @endif
                            @if ($heroShirts->first())
                                <div class="-ml-4 mt-5 rotate-2 overflow-hidden rounded-2xl border border-line bg-white shadow-lg">
                                    <img src="{{ $heroShirts->first()->gambarUrl() }}" alt="{{ $heroShirts->first()->nama }}" class="h-40 w-40 object-contain p-3">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= KONTEN UTAMA ================= --}}
    <section class="mx-auto max-w-6xl px-4 pt-10 pb-28 lg:pb-10 sm:px-6">
        @if ($errors->any())
            <div id="server-error-box" class="mb-8 rounded-2xl border border-error/30 bg-error/5 p-5">
                <p class="font-display font-bold text-error">Mohon periksa kembali isian Anda:</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- PILIH JENIS DUKUNGAN (toggle pills) --}}
        <div class="mb-8 flex flex-wrap items-center gap-x-4 gap-y-2">
            <div class="inline-flex rounded-full border border-line bg-white p-1 shadow-xs">
                <a href="{{ route('checkout.pemesanan') }}"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer bg-secondary text-white shadow-xs">
                    Beli Kaos Saja
                </a>
                <a href="{{ route('shop') }}?tab=kaos_donasi"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-ink">
                    Pesan Kaos + Donasi
                </a>
                <a href="{{ route('shop') }}?tab=donasi_saja"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-ink">
                    Donasi Saja
                </a>
            </div>
            <p class="text-xs text-muted">
                <span class="font-semibold text-ink">Beli Kaos Saja:</span> tanpa donasi tambahan
                &bull; <span class="font-semibold text-ink">Kaos + Donasi:</span> minimal Rp50.000
                &bull; <span class="font-semibold text-ink">Donasi Saja:</span> sukarela
            </p>
        </div>

        <form action="{{ route('checkout.data.store') }}" method="POST" id="form-kaos-saja" novalidate class="grid gap-8 lg:grid-cols-12 items-start">
            @csrf
            <input type="hidden" name="tipe" value="kaos_saja">
            <input type="hidden" name="metode" id="input-metode" value="{{ old('metode', '') }}">

            {{-- ========================================================================= --}}
            {{-- KOLOM KIRI --}}
            {{-- ========================================================================= --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- === Indikator Langkah === --}}
                <ol id="wizard-steps" class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-2xl border border-line bg-white px-4 py-2.5">
                    @foreach ($wzSteps as $n => $label)
                        <li data-wz-ind="{{ $n }}" class="flex items-center gap-1.5 {{ $n <= 2 ? 'cursor-pointer' : 'opacity-60' }}">
                            <span data-wz-num class="grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold {{ $n === 1 ? 'bg-primary text-white' : 'border border-line bg-white text-stone-400' }}">{{ $n }}</span>
                            <span data-wz-label class="text-[11px] font-semibold uppercase tracking-wide {{ $n === 1 ? 'text-ink font-bold' : 'text-stone-400' }}">{{ $label }}</span>
                        </li>
                        @if ($n < count($wzSteps))
                            <li class="mx-1 h-px w-5 bg-line sm:w-8" aria-hidden="true"></li>
                        @endif
                    @endforeach
                </ol>

                {{-- ====================================================== --}}
                {{-- LANGKAH 1: PILIH PRODUK KAOS (PERSIS DENGAN SHOP) --}}
                {{-- ====================================================== --}}
                <section id="wz-step-1" class="space-y-6">
                    <div class="card p-6 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-secondary font-display text-sm font-extrabold text-white">1</span>
                            <div>
                                <h2 class="font-serif text-lg font-bold text-stone-900">Pilih Produk Kaos</h2>
                                <p class="text-xs text-muted">Pilih warna, tentukan jumlah per ukuran, dan tambahkan ke pesanan.</p>
                            </div>
                        </div>

                        @error('items')
                            <div class="mt-4 rounded-xl border border-error/30 bg-error/5 p-3 text-sm font-medium text-error">{{ $message }}</div>
                        @enderror
                        <p id="wz-step1-error" class="mt-4 hidden rounded-xl border border-error/30 bg-error/5 p-3 text-sm font-medium text-error">
                            Pilih minimal satu kaos terlebih dahulu sebelum melanjutkan.
                        </p>

                        {{-- Kartu produk (5 kolom) --}}
                        <div id="product-cards" class="mt-5 grid grid-cols-3 gap-2.5 sm:grid-cols-5"></div>

                        {{-- Detail produk terpilih + stepper ukuran (S, M, L, XL, XXL) --}}
                        <div id="product-detail" class="mt-5"></div>

                        {{-- Produk lain (akordeon) --}}
                        <div id="other-products" class="mt-5 space-y-3"></div>

                        {{-- Tombol pemicu modal ukuran (untuk delegasi & accessibility) --}}
                        <button type="button" id="size-guide-trigger" data-open-size-chart class="hidden" aria-hidden="true" tabindex="-1">Panduan Ukuran</button>

                        {{-- Strip kepercayaan --}}
                        <div class="mt-6 grid gap-4 rounded-2xl border border-line bg-canvas/60 p-4 sm:grid-cols-3 sm:divide-x sm:divide-line">
                            <div class="flex items-start gap-2.5 sm:pr-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60 60 0 0 1 19.5 0M.375 14.5h23.25m-22.875 8.375H23.25m-19.875-8.5h18.375a1.125 1.125 0 0 0 1.114-1.283l-.862-6.03a1.125 1.125 0 0 0-.962-.85l-13.5-1.5a1.125 1.125 0 0 0-1.204.846L4.875 8.25" />
                                </svg>
                                <div>
                                    <p class="text-xs font-bold text-ink">Pengiriman ke seluruh Indonesia</p>
                                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Estimasi 3&ndash;7 hari (tergantung lokasi).</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 sm:px-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.96 11.96 0 0 1 3.598 6 12 12 0 0 0 12 21.75 12 12 0 0 0 20.402 6 11.96 11.96 0 0 1 12 2.714Z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-bold text-ink">Pembayaran Aman</p>
                                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Transfer bank, QRIS, atau e-wallet.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 sm:pl-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-bold text-ink">Setiap Pembelian Berdampak</p>
                                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Dukungan untuk pembangunan {{ $programInfo->nama_program ?? 'GMAHK Haleluya, Bali' }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ====================================================== --}}
                {{-- LANGKAH 2: DATA PRIBADI & ALAMAT PENGIRIMAN --}}
                {{-- ====================================================== --}}
                <section id="wz-step-2" class="hidden space-y-6">
                    <div class="card p-6 sm:p-8 space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-secondary font-display text-sm font-extrabold text-white">2</span>
                            <div>
                                <h2 class="font-serif text-lg font-bold text-stone-900">Data Pribadi</h2>
                                <p class="text-xs text-muted">Untuk konfirmasi pesanan melalui WhatsApp.</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="nama" class="label">Nama <span class="text-error">*</span></label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $draft['nama'] ?? '') }}" placeholder="Nama lengkap"
                                    class="input @error('nama') input-error @enderror" required>
                                @error('nama')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="label">Email <span class="text-error">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $draft['email'] ?? '') }}" placeholder="nama@email.com"
                                    class="input @error('email') input-error @enderror" required>
                                @error('email')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="nomor" class="label">Nomor Telepon / WhatsApp <span class="text-error">*</span></label>
                            <input type="tel" id="nomor" name="nomor" value="{{ old('nomor', $draft['nomor'] ?? '') }}" placeholder="Contoh: 08123456789"
                                class="input @error('nomor') input-error @enderror" required>
                            @error('nomor')<p class="error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="border-t border-line pt-6">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h2 class="font-serif text-lg font-bold text-stone-900">Alamat Pengiriman</h2>
                                @if (!empty($isRajaOngkirLive))
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> RajaOngkir Terhubung
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-stone-500">
                                Pilih wilayah secara berurutan: <strong class="text-stone-700">Provinsi &rarr; Kabupaten/Kota &rarr; Kecamatan &rarr; Kelurahan</strong>. Kode pos terisi otomatis.
                            </p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="provinsi" class="label">Provinsi <span class="text-error">*</span></label>
                                        <span id="loading-provinsi" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat...
                                        </span>
                                    </div>
                                    <select id="provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" required>
                                        <option value="">— Pilih Provinsi —</option>
                                        @foreach ($provinsi as $p)
                                            @php
                                                $pName = is_array($p) ? ($p['name'] ?? '') : $p;
                                                $pId = is_array($p) ? ($p['id'] ?? '') : '';
                                            @endphp
                                            <option value="{{ $pName }}" data-id="{{ $pId }}" @selected(strtoupper((string) old('provinsi', $draft['provinsi'] ?? '')) === strtoupper((string) $pName))>
                                                {{ $pName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provinsi')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kabupaten" class="label">Kabupaten / Kota <span class="text-error">*</span></label>
                                        <span id="loading-kabupaten" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat kota...
                                        </span>
                                    </div>
                                    <select id="kabupaten" name="kabupaten" class="input @error('kabupaten') input-error @enderror" required disabled>
                                        <option value="">— Pilih Provinsi Dahulu —</option>
                                    </select>
                                    <input type="hidden" id="old-kabupaten" value="{{ old('kabupaten', $draft['kabupaten'] ?? '') }}">
                                    @error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kecamatan" class="label">Kecamatan <span class="text-error">*</span></label>
                                        <span id="loading-kecamatan" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat kecamatan...
                                        </span>
                                    </div>
                                    <select id="kecamatan" name="kecamatan" class="input @error('kecamatan') input-error @enderror" required disabled>
                                        <option value="">— Pilih Kota/Kabupaten Dahulu —</option>
                                    </select>
                                    <input type="hidden" id="old-kecamatan" value="{{ old('kecamatan', $draft['kecamatan'] ?? '') }}">
                                    @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kelurahan" class="label">Kelurahan / Desa</label>
                                        <span id="loading-kelurahan" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat kelurahan...
                                        </span>
                                    </div>
                                    <select id="kelurahan" name="kelurahan" class="input @error('kelurahan') input-error @enderror" disabled>
                                        <option value="">— Pilih Kecamatan Dahulu —</option>
                                    </select>
                                    <input type="hidden" id="old-kelurahan" value="{{ old('kelurahan', $draft['kelurahan'] ?? '') }}">
                                    @error('kelurahan')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kode-pos" class="label">Kode Pos <span class="text-error">*</span></label>
                                        <span id="badge-kodepos" class="hidden text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            Otomatis RajaOngkir ✓
                                        </span>
                                    </div>
                                    <input type="text" id="kode-pos" name="kode_pos" value="{{ old('kode_pos', $draft['kode_pos'] ?? '') }}" placeholder="Masukkan kode pos"
                                        class="input @error('kode_pos') input-error @enderror" required>
                                    <p class="mt-1 text-[11px] text-stone-500">Terisi otomatis saat memilih kelurahan / kecamatan.</p>
                                    @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="alamat" class="label">Alamat Lengkap <span class="text-error">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="2" placeholder="Jalan, nomor rumah, RT/RW, patokan"
                                        class="input @error('alamat') input-error @enderror" required>{{ old('alamat', $draft['alamat'] ?? '') }}</textarea>
                                    @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="catatan" class="label">Catatan <span class="text-xs font-normal text-muted">(opsional)</span></label>
                                <textarea id="catatan" name="catatan" rows="2" placeholder="Instruksi khusus pengiriman (opsional)" class="input @error('catatan') input-error @enderror">{{ old('catatan', $draft['catatan'] ?? '') }}</textarea>
                                @error('catatan')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Pilihan Metode Pembayaran (Tampil di Layar Mobile / Tablet) --}}
                        <div id="mobile-pilih-metode" class="block lg:hidden border-t border-line pt-6">
                            <h2 class="font-serif text-lg font-bold text-stone-900">Pilih Metode Pembayaran</h2>
                            <p class="mt-0.5 text-xs text-stone-500 mb-4">Pilih cara pembayaran yang Anda inginkan.</p>

                            <div class="space-y-3" id="mobile-payment-options">
                                {{-- Option 1: QRIS --}}
                                <div data-method-card="qris" class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eaf4ec] text-[#245236]">
                                            <svg class="h-6 w-6 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <rect width="6" height="6" x="3" y="3" rx="1" />
                                                <rect width="6" height="6" x="15" y="3" rx="1" />
                                                <rect width="6" height="6" x="3" y="15" rx="1" />
                                                <path d="M14 14.5h2.5v2.5H14zM18.5 14.5H21v2.5h-2.5zM14 19h2.5v2.5H14zM18.5 19H21v2.5h-2.5z" fill="currentColor" stroke="none" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M17 7h.01M7 17h.01" stroke-width="2.5" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-stone-900 leading-tight">QRIS</p>
                                            <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">Scan QRIS untuk melakukan pembayaran.</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 pl-2">
                                        <span data-radio-indicator="qris" class="w-5 h-5 rounded-full border border-stone-300 flex items-center justify-center transition">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#245236] hidden"></span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Option 2: Transfer Bank --}}
                                <div data-method-card="transfer" class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
                                    <div class="flex items-center gap-3.5">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eaf4ec] text-[#245236]">
                                            <svg class="h-6 w-6 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 8h18l-9-5Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 9v8m4-8v8m6-8v8m4-8v8M3 17h18M3 21h18" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-stone-900 leading-tight">Transfer Bank</p>
                                            <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">Transfer melalui rekening bank terdaftar.</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 pl-2">
                                        <span data-radio-indicator="transfer" class="w-5 h-5 rounded-full border border-stone-300 flex items-center justify-center transition">
                                            <span class="w-2.5 h-2.5 rounded-full bg-[#245236] hidden"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p class="metode-error-msg hidden mt-2 text-xs font-semibold text-error">
                                Pilih salah satu metode pembayaran (QRIS atau Transfer Bank).
                            </p>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <button type="button" data-wz-back="1"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:border-stone-300 hover:text-ink cursor-pointer">
                                &larr; Kembali ke Produk
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-7 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                                Lanjut ke Pembayaran &rarr;
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Hidden input keranjang untuk form submit --}}
                <div id="cart-hidden">
                    <input type="hidden" name="design_id" value="{{ $selected->id }}">
                    @foreach ((array) old('items', []) as $oldItem)
                        @if (!empty($oldItem['design_id']))
                            <input type="hidden" data-old-item data-design-id="{{ $oldItem['design_id'] }}" data-ukuran="{{ $oldItem['ukuran'] ?? '' }}" data-gender="{{ $oldItem['gender'] ?? 'pria' }}" data-jumlah="{{ $oldItem['jumlah'] ?? 1 }}">
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- KOLOM KANAN: SIDEBAR RINGKASAN PESANAN (DESKTOP)                          --}}
            {{-- ========================================================================= --}}
            <aside class="hidden lg:block lg:col-span-4">
                <div class="space-y-6 sticky top-6">
                    {{-- Card 1: Ringkasan Pesanan --}}
                    <div class="card p-6 bg-white border border-stone-200 rounded-2xl shadow-xs">
                        <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan Pesanan</h2>
                        <div id="side-items" class="mt-4 space-y-3"></div>

                        <dl class="mt-4 pt-4 border-t border-stone-200 space-y-2 text-xs sm:text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-stone-500">Subtotal (<span id="side-count">0</span> kaos)</dt>
                                <dd id="side-subtotal" class="font-bold text-stone-900">Rp 0</dd>
                            </div>
                        </dl>

                        <div class="mt-4 rounded-xl bg-[#efede3] p-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-serif text-sm font-bold text-stone-900">Total Pembayaran</span>
                                <span id="side-total" class="text-xl font-extrabold text-stone-900">Rp 0</span>
                            </div>
                            <button type="button" id="side-next-btn"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-5 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                                Lanjut ke Data &amp; Alamat &rarr;
                            </button>
                            <div id="side-step2-notice" class="hidden mt-3 flex items-center gap-2.5 rounded-xl border border-[#f2cebe] bg-[#faebe5] px-3.5 py-2.5 text-xs font-semibold text-[#c85a32]">
                                <svg class="h-4 w-4 shrink-0 text-[#c85a32]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="5" rx="2" />
                                    <line x1="2" x2="22" y1="10" y2="10" />
                                </svg>
                                <span>Pilih metode pembayaran di bawah</span>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-stone-400 leading-relaxed">
                            Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening &amp; total final setelah pesanan terkonfirmasi.
                        </p>

                        <a href="{{ route('progres') }}" class="text-xs font-semibold text-primary hover:underline mt-4 inline-block">
                            Lihat Progres dulu &rarr;
                        </a>
                    </div>

                    {{-- Card 2: Pilih Metode Pembayaran (Desktop - Khusus Langkah 2) --}}
                    <div id="card-pilih-metode" class="hidden card p-6 bg-white border border-stone-200 rounded-2xl shadow-xs">
                        <h2 class="font-serif text-lg font-bold text-stone-900">Pilih Metode Pembayaran</h2>

                        <div class="mt-4 space-y-3" id="desktop-payment-options">
                            {{-- Option 1: QRIS --}}
                            <div data-method-card="qris" class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eaf4ec] text-[#245236]">
                                        <svg class="h-6 w-6 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect width="6" height="6" x="3" y="3" rx="1" />
                                            <rect width="6" height="6" x="15" y="3" rx="1" />
                                            <rect width="6" height="6" x="3" y="15" rx="1" />
                                            <path d="M14 14.5h2.5v2.5H14zM18.5 14.5H21v2.5h-2.5zM14 19h2.5v2.5H14zM18.5 19H21v2.5h-2.5z" fill="currentColor" stroke="none" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M17 7h.01M7 17h.01" stroke-width="2.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-stone-900 leading-tight">QRIS</p>
                                        <p class="text-[11px] sm:text-xs text-stone-500 mt-0.5 leading-snug">Scan QRIS untuk melakukan pembayaran.</p>
                                    </div>
                                </div>
                                <div class="shrink-0 pl-2">
                                    <span data-radio-indicator="qris" class="w-5 h-5 rounded-full border border-stone-300 flex items-center justify-center transition">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#245236] hidden"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Option 2: Transfer Bank --}}
                            <div data-method-card="transfer" class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eaf4ec] text-[#245236]">
                                        <svg class="h-6 w-6 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 8h18l-9-5Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 9v8m4-8v8m6-8v8m4-8v8M3 17h18M3 21h18" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-stone-900 leading-tight">Transfer Bank</p>
                                        <p class="text-[11px] sm:text-xs text-stone-500 mt-0.5 leading-snug">Transfer melalui rekening bank terdaftar.</p>
                                    </div>
                                </div>
                                <div class="shrink-0 pl-2">
                                    <span data-radio-indicator="transfer" class="w-5 h-5 rounded-full border border-stone-300 flex items-center justify-center transition">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#245236] hidden"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="metode-error-msg hidden mt-3 text-xs font-semibold text-error">
                            Pilih salah satu metode pembayaran (QRIS atau Transfer Bank).
                        </p>
                    </div>
                </div>
            </aside>
        </form>

        {{-- ========================================================================= --}}
        {{-- FLOATING EXPANDABLE RINGKASAN PESANAN (KHUSUS MODE HP)                    --}}
        {{-- ========================================================================= --}}
        <div id="mobile-floating-bar"
            class="fixed bottom-0 inset-x-0 z-40 lg:hidden transition-all duration-300 ease-out transform translate-y-full opacity-0 pointer-events-none">

            {{-- Backdrop transparan gelap saat mengembang --}}
            <div id="mobile-drawer-backdrop"
                class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none -z-10"></div>

            <div class="relative bg-white border-t border-stone-200/90 shadow-[0_-8px_30px_rgba(0,0,0,0.12)] rounded-t-2xl overflow-hidden flex flex-col max-h-[85vh]">
                {{-- Handle Bar / Tap to Expand --}}
                <button type="button" id="mobile-drawer-toggle-btn" class="w-full pt-2.5 pb-1 flex flex-col items-center justify-center cursor-pointer select-none group" aria-label="Buka ringkasan pesanan">
                    <span class="h-1.5 w-12 rounded-full bg-stone-300 group-hover:bg-stone-400 transition"></span>
                </button>

                {{-- Bagian Mengembang (Drawer Rincian Lengkap) --}}
                <div id="mobile-drawer-body" class="hidden overflow-y-auto px-4 pb-3 space-y-3">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-2.5 pt-0.5">
                        <div>
                            <h3 class="font-serif text-base font-bold text-stone-900">Ringkasan Pesanan</h3>
                            <p class="text-[11px] text-stone-500"><span id="mobile-drawer-count">0</span> kaos dipilih</p>
                        </div>
                        <button type="button" id="mobile-drawer-close-btn" class="grid h-8 w-8 place-items-center rounded-full bg-stone-100 text-stone-500 hover:text-stone-800 transition cursor-pointer" aria-label="Tutup ringkasan">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- List Item yang dipilih --}}
                    <div id="mobile-drawer-items" class="space-y-2.5 max-h-[38vh] overflow-y-auto pr-1"></div>

                    {{-- Rincian Biaya --}}
                    <div class="rounded-xl bg-[#f8f6f0] p-3 text-xs space-y-1.5 border border-[#ede8df]">
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Subtotal Kaos</span>
                            <span id="mobile-drawer-subtotal" class="font-semibold text-stone-900">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-stone-200/60 pt-1.5 font-bold text-stone-900">
                            <span>Total Pembayaran</span>
                            <span id="mobile-drawer-total" class="text-sm font-extrabold text-primary">Rp 0</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-stone-400 leading-relaxed">
                        Pembayaran dilakukan manual (transfer/QRIS) setelah pesanan dikonfirmasi.
                    </p>
                </div>

                {{-- Bar Bawah Kompak (Selalu Mengambang di Bawah saat ada kaos) --}}
                <div class="p-3 sm:px-5 flex items-center justify-between gap-3 bg-white border-t border-stone-100 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                    <div class="min-w-0 flex-1 cursor-pointer select-none" id="mobile-bar-summary-trigger">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-secondary/10 px-2 py-0.5 text-[11px] font-bold text-secondary">
                                <span id="mobile-float-count">0</span> kaos
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-stone-600 hover:text-primary transition">
                                <span>Lihat Rincian</span>
                                <svg id="mobile-drawer-chevron" class="h-3.5 w-3.5 transition-transform duration-200 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <p id="mobile-float-total" class="font-serif text-lg font-bold text-stone-900 leading-tight mt-0.5">Rp 0</p>
                    </div>

                    <button type="button" id="mobile-float-next-btn"
                        class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-primary hover:bg-primary-dark px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs transition active:scale-95 cursor-pointer">
                        <span id="mobile-float-next-label">Lanjut</span> &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>

    @include('components.size-chart-modal')
@endsection

@push('scripts')
<script>
    (function () {
        const rupiah = function (n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); };
        const num = function (n) { return Number(n || 0).toLocaleString('id-ID'); };
        const DESIGN_URL = '{{ route('design') }}';
        const DESIGNS = @json($designData);

        const form = document.getElementById('form-kaos-saja');
        if (!form || !DESIGNS.length) return;

        const cardsWrap = document.getElementById('product-cards');
        const detailWrap = document.getElementById('product-detail');
        const othersWrap = document.getElementById('other-products');
        const sideItems = document.getElementById('side-items');
        const sideCount = document.getElementById('side-count');
        const sideSubtotal = document.getElementById('side-subtotal');
        const sideTotal = document.getElementById('side-total');
        const sideNext = document.getElementById('side-next-btn');
        const sideStep2Notice = document.getElementById('side-step2-notice');
        const cardPilihMetode = document.getElementById('card-pilih-metode');
        const mobilePilihMetode = document.getElementById('mobile-pilih-metode');
        const inputMetode = document.getElementById('input-metode');
        const hiddenWrap = document.getElementById('cart-hidden');
        const step1Error = document.getElementById('wz-step1-error');
        const indicatorItems = document.querySelectorAll('[data-wz-ind]');

        const mobileFloatingBar = document.getElementById('mobile-floating-bar');
        const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');
        const mobileDrawerBody = document.getElementById('mobile-drawer-body');
        const mobileDrawerItems = document.getElementById('mobile-drawer-items');
        const mobileDrawerCount = document.getElementById('mobile-drawer-count');
        const mobileDrawerSubtotal = document.getElementById('mobile-drawer-subtotal');
        const mobileDrawerTotal = document.getElementById('mobile-drawer-total');
        const mobileFloatCount = document.getElementById('mobile-float-count');
        const mobileFloatTotal = document.getElementById('mobile-float-total');
        const mobileFloatNextBtn = document.getElementById('mobile-float-next-btn');
        const mobileFloatNextLabel = document.getElementById('mobile-float-next-label');
        const mobileDrawerChevron = document.getElementById('mobile-drawer-chevron');
        const mobileDrawerToggleBtn = document.getElementById('mobile-drawer-toggle-btn');
        const mobileDrawerCloseBtn = document.getElementById('mobile-drawer-close-btn');
        const mobileBarSummaryTrigger = document.getElementById('mobile-bar-summary-trigger');

        const designById = function (id) {
            return DESIGNS.find(function (d) { return String(d.id) === String(id); });
        };

        let cart = [];
        let selectedId = {{ $selected ? $selected->id : 'null' }} || (DESIGNS[0] ? DESIGNS[0].id : null);
        let draft = {};      // { [designId]: { pria: { [ukuran]: jumlah }, wanita: {...} } }
        let genderSel = {};  // { [designId]: 'pria' | 'wanita' }
        let step = 1;

        // Restore items dari old() atau pre-fill dari query params
        const oldEls = hiddenWrap.querySelectorAll('[data-old-item]');
        if (oldEls.length > 0) {
            oldEls.forEach(function (el) {
                const d = designById(el.dataset.designId);
                if (!d) return;
                const jumlah = Math.max(1, parseInt(el.dataset.jumlah || '1', 10));
                cart.push({
                    design_id: d.id,
                    nama: d.nama,
                    harga: d.harga,
                    gambar: d.gambar,
                    ukuran: el.dataset.ukuran || (d.sizes ? d.sizes[0] : 'M'),
                    gender: (el.dataset.gender === 'wanita') ? 'wanita' : 'pria',
                    jumlah: jumlah,
                    subtotal: d.harga * jumlah,
                });
            });
        } else {
            // Jika ada query param ukuran & jumlah dari halaman sebelumnya
            const paramUkuran = @json(request('ukuran', $draft['ukuran'] ?? ''));
            const paramJumlah = parseInt(@json(request('jumlah', $draft['jumlah'] ?? 0)), 10) || 0;
            if (paramUkuran && paramJumlah > 0 && selectedId) {
                const selD = designById(selectedId);
                if (selD) {
                    cart.push({
                        design_id: selD.id,
                        nama: selD.nama,
                        harga: selD.harga,
                        gambar: selD.gambar,
                        ukuran: paramUkuran,
                        gender: 'pria',
                        jumlah: paramJumlah,
                        subtotal: selD.harga * paramJumlah,
                    });
                }
            }
        }

        if (cart.length > 0 && !selectedId) {
            selectedId = cart[0].design_id;
        }

        function cartForDesign(id) {
            return cart.filter(function (i) { return String(i.design_id) === String(id); });
        }

        function cartForDesignGender(id, g) {
            return cartForDesign(id).filter(function (i) { return (i.gender || 'pria') === g; });
        }

        function genderOf(id) {
            if (genderSel[id]) return genderSel[id];
            const items = cartForDesign(id);
            return items.length ? (items[0].gender || 'pria') : 'pria';
        }

        function initDraft(id) {
            draft[id] = { pria: {}, wanita: {} };
            const d = designById(id);
            (d ? d.sizes : []).forEach(function (s) {
                draft[id].pria[s] = 0;
                draft[id].wanita[s] = 0;
            });
            ['pria', 'wanita'].forEach(function (g) {
                cartForDesignGender(id, g).forEach(function (i) {
                    draft[id][g][i.ukuran] = (draft[id][g][i.ukuran] || 0) + i.jumlah;
                });
            });
            if (!genderSel[id]) {
                genderSel[id] = cartForDesign(id).length ? (cartForDesign(id)[0].gender || 'pria') : 'pria';
            }
        }

        function draftTotal(id) {
            const counts = (draft[id] && draft[id][genderOf(id)]) || {};
            return Object.values(counts).reduce(function (a, b) { return a + b; }, 0);
        }

        function genderBtn(id, g) {
            const isActive = genderOf(id) === g;
            const icon = g === 'pria'
                ? '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" /></svg>'
                : '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="4"></circle><path stroke-linecap="round" stroke-linejoin="round" d="M12 12v8M9 17h6" /></svg>';
            return (
                '<button type="button" data-card-gender="' + g + '" class="inline-flex items-center justify-center gap-1 rounded-full border px-1.5 py-1 text-[10px] font-bold transition cursor-pointer ' +
                    (isActive ? 'border-secondary bg-secondary text-white' : 'border-line bg-white text-stone-500 hover:border-stone-300 hover:text-ink') + '">' +
                    icon + (g === 'pria' ? 'Pria' : 'Wanita') +
                '</button>'
            );
        }

        function renderCards() {
            const promoCard =
                '<a href="' + DESIGN_URL + '" class="group flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-line bg-canvas p-3 text-center transition hover:border-primary hover:bg-white">' +
                    '<span class="grid h-10 w-10 place-items-center rounded-xl bg-secondary/10 text-secondary">' +
                        '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0-16 3 2a5 5 0 0 0 5-2v11l-5 5H9l-5-5V4a5 5 0 0 0 5 2l3-2Z" /></svg>' +
                    '</span>' +
                    '<span class="text-[13px] font-bold text-ink leading-tight">Lihat Semua Produk Kaos</span>' +
                    '<span class="whitespace-nowrap inline-flex items-center gap-1 rounded-full bg-secondary px-2.5 py-1.5 text-[10px] font-bold text-white transition group-hover:bg-[#0b3b22]">Lihat Koleksi &rarr;</span>' +
                '</a>';

            cardsWrap.innerHTML = DESIGNS.map(function (d) {
                const isSel = String(d.id) === String(selectedId);
                if (!draft[d.id]) initDraft(d.id);
                return (
                    '<div data-card-design="' + d.id + '" class="group relative overflow-hidden rounded-xl border-2 p-2 text-left transition cursor-pointer ' +
                        (isSel ? 'border-secondary bg-white' : 'border-line bg-white hover:border-stone-300') + '">' +
                        (isSel
                            ? '<span class="absolute right-3 top-3 z-10 grid h-6 w-6 place-items-center rounded-full bg-secondary text-white shadow-sm">' +
                                '<svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" /></svg>' +
                              '</span>'
                            : '') +
                        '<div class="aspect-square w-full overflow-hidden rounded-lg bg-canvas">' +
                            '<img src="' + d.gambar + '" alt="' + d.nama + '" loading="lazy" class="h-full w-full object-contain transition duration-300 group-hover:scale-105">' +
                        '</div>' +
                        '<p class="mt-2 line-clamp-2 min-h-[2.2em] text-[12px] font-bold leading-tight text-ink">' + d.nama + '</p>' +
                        '<p class="text-[13px] text-muted">Rp ' + num(d.harga) + '</p>' +
                        '<div class="mt-1.5 grid grid-cols-2 gap-1">' +
                            genderBtn(d.id, 'pria') + genderBtn(d.id, 'wanita') +
                        '</div>' +
                    '</div>'
                );
            }).join('') + promoCard;
        }

        function renderDetail() {
            const d = designById(selectedId);
            if (!d) { detailWrap.innerHTML = ''; return; }
            if (!draft[selectedId]) initDraft(selectedId);
            const gender = genderOf(selectedId);
            const genderLabel = gender === 'wanita' ? 'Wanita' : 'Pria';
            const counts = draft[selectedId][gender] || {};
            const total = draftTotal(selectedId);

            const sizesHtml = (d.sizes || []).map(function (s) {
                return (
                    '<div class="rounded-xl border border-line bg-white px-1.5 py-2.5 text-center">' +
                        '<p class="text-[11px] font-bold uppercase tracking-wider text-muted">' + s + '</p>' +
                        '<div class="mt-1.5 flex items-center justify-center gap-1">' +
                            '<button type="button" data-qty-btn data-dir="-1" data-size="' + s + '" class="grid h-7 w-7 place-items-center rounded-lg border border-line text-stone-500 transition hover:border-primary hover:text-primary cursor-pointer">&minus;</button>' +
                            '<span class="w-7 text-center text-sm font-bold text-ink">' + (counts[s] || 0) + '</span>' +
                            '<button type="button" data-qty-btn data-dir="1" data-size="' + s + '" class="grid h-7 w-7 place-items-center rounded-lg border border-line text-stone-500 transition hover:border-primary hover:text-primary cursor-pointer">+</button>' +
                        '</div>' +
                    '</div>'
                );
            }).join('');

            const genderToggle =
                '<div class="inline-flex items-center gap-1.5">' +
                    '<button type="button" data-detail-gender="pria" class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-bold transition cursor-pointer ' +
                        (gender === 'pria' ? 'border-secondary bg-secondary text-white' : 'border-line bg-white text-stone-500 hover:border-stone-300 hover:text-ink') + '">' +
                        '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" /></svg>Pria' +
                    '</button>' +
                    '<button type="button" data-detail-gender="wanita" class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-bold transition cursor-pointer ' +
                        (gender === 'wanita' ? 'border-secondary bg-secondary text-white' : 'border-line bg-white text-stone-500 hover:border-stone-300 hover:text-ink') + '">' +
                        '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="4"></circle><path stroke-linecap="round" stroke-linejoin="round" d="M12 12v8M9 17h6" /></svg>Wanita' +
                    '</button>' +
                '</div>';

            detailWrap.innerHTML =
                '<div class="card overflow-hidden !p-0">' +
                    '<div class="grid sm:grid-cols-12">' +
                        '<div class="relative sm:col-span-4 border-r border-line bg-white p-4 flex items-center justify-center">' +
                            '<span class="absolute left-3 top-3 rounded-full bg-secondary px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">' + genderLabel + '</span>' +
                            '<img src="' + d.gambar + '" alt="' + d.nama + '" class="max-h-52 w-full rounded-xl object-contain">' +
                        '</div>' +
                        '<div class="sm:col-span-8 p-5 sm:p-6">' +
                            '<div class="flex flex-wrap items-start justify-between gap-2">' +
                                '<div>' +
                                    '<h3 class="font-serif text-lg font-bold text-ink">' + d.nama + '</h3>' +
                                    '<p class="mt-0.5 text-sm font-bold text-primary">Rp ' + num(d.harga) + ' <span class="text-xs font-medium text-muted">/ kaos</span></p>' +
                                '</div>' +
                                '<span class="rounded-full bg-secondary/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-secondary">Dipilih</span>' +
                            '</div>' +
                            (d.deskripsi ? '<p class="mt-2 text-sm leading-relaxed text-muted">' + d.deskripsi + '</p>' : '') +
                            '<div class="mt-4 flex flex-wrap items-center justify-between gap-2">' +
                                '<p class="text-xs font-bold uppercase tracking-wider text-muted">Pilih Ukuran &amp; Jumlah (' + genderLabel + ')</p>' +
                                '<div class="flex items-center gap-2.5">' + genderToggle +
                                    '<button type="button" data-open-size-chart class="text-[11px] font-semibold text-primary hover:underline cursor-pointer">Panduan Ukuran</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="mt-2.5 grid grid-cols-3 gap-2 sm:grid-cols-5">' + sizesHtml + '</div>' +
                            '<div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">' +
                                '<p class="text-sm text-ink"><span class="font-bold">Total (' + genderLabel + '): ' + total + ' kaos</span>' +
                                    '<span class="ml-2 text-lg font-extrabold">Rp ' + num(d.harga * total) + '</span></p>' +
                                '<button type="button" id="btn-add-draft" class="inline-flex items-center gap-2 rounded-xl bg-secondary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#0b3b22] cursor-pointer">' +
                                    '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>' +
                                    'Tambahkan ke Pesanan' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        }

        function renderOthers() {
            const rest = DESIGNS.filter(function (d) { return String(d.id) !== String(selectedId); });
            if (!rest.length) { othersWrap.innerHTML = ''; return; }
            othersWrap.innerHTML = rest.map(function (d) {
                if (!draft[d.id]) initDraft(d.id);
                const g = genderOf(d.id);
                return (
                    '<div data-card-design="' + d.id + '" class="flex w-full flex-wrap items-center gap-3 rounded-xl border border-line bg-white p-2.5 text-left transition hover:border-stone-300 cursor-pointer">' +
                        '<img src="' + d.gambar + '" alt="' + d.nama + '" loading="lazy" class="h-14 w-14 shrink-0 rounded-lg bg-canvas object-contain p-1">' +
                        '<span class="min-w-0 flex-1">' +
                            '<span class="block truncate text-sm font-bold text-ink">' + d.nama + '</span>' +
                            '<span class="block text-sm text-muted">Rp ' + num(d.harga) + '</span>' +
                        '</span>' +
                        '<span class="inline-flex items-center gap-1.5" data-gender-group="' + d.id + '">' +
                            '<button type="button" data-card-gender="pria" class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-bold transition cursor-pointer ' +
                                (g === 'pria' ? 'border-secondary bg-secondary text-white' : 'border-line bg-white text-stone-500 hover:border-stone-300 hover:text-ink') + '">' +
                                '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" /></svg>Pria' +
                            '</button>' +
                            '<button type="button" data-card-gender="wanita" class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-bold transition cursor-pointer ' +
                                (g === 'wanita' ? 'border-secondary bg-secondary text-white' : 'border-line bg-white text-stone-500 hover:border-stone-300 hover:text-ink') + '">' +
                                '<svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="4"></circle><path stroke-linecap="round" stroke-linejoin="round" d="M12 12v8M9 17h6" /></svg>Wanita' +
                            '</button>' +
                        '</span>' +
                        '<svg class="h-4 w-4 shrink-0 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>' +
                    '</div>'
                );
            }).join('');
        }

        let isDrawerOpen = false;

        function toggleDrawer(open) {
            isDrawerOpen = typeof open === 'boolean' ? open : !isDrawerOpen;
            if (isDrawerOpen) {
                mobileDrawerBody?.classList.remove('hidden');
                mobileDrawerBackdrop?.classList.remove('opacity-0', 'pointer-events-none');
                mobileDrawerBackdrop?.classList.add('opacity-100', 'pointer-events-auto');
                mobileDrawerChevron?.classList.add('rotate-180');
            } else {
                mobileDrawerBody?.classList.add('hidden');
                mobileDrawerBackdrop?.classList.remove('opacity-100', 'pointer-events-auto');
                mobileDrawerBackdrop?.classList.add('opacity-0', 'pointer-events-none');
                mobileDrawerChevron?.classList.remove('rotate-180');
            }
        }

        function updateMobileFloating() {
            if (!mobileFloatingBar) return;

            let displayItems = [];
            let totalQty = 0;
            let subtotal = 0;

            if (cart.length > 0) {
                displayItems = cart;
                totalQty = cart.reduce(function (a, i) { return a + i.jumlah; }, 0);
                subtotal = cart.reduce(function (a, i) { return a + i.subtotal; }, 0);
            } else if (selectedId !== null && draftTotal(selectedId) > 0) {
                const d = designById(selectedId);
                const gen = genderOf(selectedId);
                const counts = (draft[selectedId] && draft[selectedId][gen]) || {};
                Object.keys(counts).forEach(function (sz) {
                    const cnt = counts[sz];
                    if (cnt > 0 && d) {
                        displayItems.push({
                            design_id: d.id,
                            nama: d.nama,
                            harga: d.harga,
                            gambar: d.gambar,
                            ukuran: sz,
                            gender: gen,
                            jumlah: cnt,
                            subtotal: d.harga * cnt
                        });
                        totalQty += cnt;
                        subtotal += (d.harga * cnt);
                    }
                });
            }

            if (totalQty > 0) {
                mobileFloatingBar.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                mobileFloatingBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            } else {
                mobileFloatingBar.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                mobileFloatingBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                toggleDrawer(false);
            }

            if (mobileFloatCount) mobileFloatCount.textContent = String(totalQty);
            if (mobileFloatTotal) mobileFloatTotal.textContent = rupiah(subtotal);
            if (mobileDrawerCount) mobileDrawerCount.textContent = String(totalQty);
            if (mobileDrawerSubtotal) mobileDrawerSubtotal.textContent = rupiah(subtotal);
            if (mobileDrawerTotal) mobileDrawerTotal.textContent = rupiah(subtotal);

            if (mobileFloatNextLabel) {
                if (step === 1) {
                    mobileFloatNextLabel.textContent = 'Lanjut ke Data & Alamat';
                } else {
                    mobileFloatNextLabel.textContent = 'Kirim & Lanjut ke Pembayaran';
                }
            }

            if (mobileDrawerItems) {
                if (!displayItems.length) {
                    mobileDrawerItems.innerHTML = '<p class="text-xs text-stone-400 italic py-2">Belum ada kaos dipilih.</p>';
                } else {
                    const groups = {};
                    displayItems.forEach(function (i) {
                        const gender = i.gender || 'pria';
                        const key = String(i.design_id) + '|' + gender;
                        if (!groups[key]) groups[key] = { id: i.design_id, nama: i.nama, gambar: i.gambar, gender: gender, items: [], subtotal: 0 };
                        groups[key].items.push(i);
                        groups[key].subtotal += i.subtotal;
                    });

                    mobileDrawerItems.innerHTML = Object.keys(groups).map(function (key) {
                        const g = groups[key];
                        const groupQty = g.items.reduce(function (a, i) { return a + i.jumlah; }, 0);
                        const genderLabel = g.gender === 'wanita' ? 'Wanita' : 'Pria';
                        return (
                            '<div class="flex items-start gap-2.5 rounded-xl border border-stone-100 bg-[#faf9f5] p-2.5">' +
                                '<img src="' + g.gambar + '" alt="" class="h-12 w-12 shrink-0 rounded-lg border border-line bg-white object-contain p-1">' +
                                '<div class="min-w-0 flex-1">' +
                                    '<div class="flex items-start justify-between gap-1.5">' +
                                        '<div class="min-w-0">' +
                                            '<p class="truncate text-xs font-bold text-ink">' + g.nama + '</p>' +
                                            '<p class="text-[10px] text-muted">' + genderLabel + ' &middot; ' + groupQty + ' kaos</p>' +
                                        '</div>' +
                                        (cart.length > 0 ?
                                            '<button type="button" data-mobile-remove="' + key + '" aria-label="Hapus item" class="text-base leading-none text-stone-300 hover:text-error cursor-pointer p-0.5">&times;</button>'
                                            : '') +
                                    '</div>' +
                                    '<div class="mt-1 flex flex-wrap gap-1">' +
                                        g.items.map(function (i) {
                                            return '<span class="rounded bg-white border border-stone-200 px-1.5 py-0.5 text-[10px] font-semibold text-ink">' + i.ukuran + ' &times; ' + i.jumlah + '</span>';
                                        }).join('') +
                                    '</div>' +
                                    '<div class="mt-1 flex items-center justify-between">' +
                                        '<button type="button" data-mobile-edit="' + g.id + '" data-mobile-gender="' + g.gender + '" class="text-[10px] font-semibold text-primary hover:underline cursor-pointer">Ubah Ukuran</button>' +
                                        '<span class="text-xs font-bold text-ink">Rp ' + num(g.subtotal) + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                    }).join('');
                }
            }
        }

        function renderSidebar() {
            const subtotal = cart.reduce(function (a, i) { return a + i.subtotal; }, 0);
            const totalQty = cart.reduce(function (a, i) { return a + i.jumlah; }, 0);

            if (!cart.length) {
                sideItems.innerHTML = '<p class="text-sm text-stone-400 italic py-2">Belum ada kaos dipilih. Pilih produk di Langkah 1.</p>';
            } else {
                const groups = {};
                cart.forEach(function (i) {
                    const gender = i.gender || 'pria';
                    const key = String(i.design_id) + '|' + gender;
                    if (!groups[key]) groups[key] = { id: i.design_id, nama: i.nama, gambar: i.gambar, gender: gender, items: [], subtotal: 0 };
                    groups[key].items.push(i);
                    groups[key].subtotal += i.subtotal;
                });
                sideItems.innerHTML = Object.keys(groups).map(function (key) {
                    const g = groups[key];
                    const groupQty = g.items.reduce(function (a, i) { return a + i.jumlah; }, 0);
                    const genderLabel = g.gender === 'wanita' ? 'Wanita' : 'Pria';
                    return (
                        '<div class="flex items-start gap-3 border-b border-line pb-3">' +
                            '<img src="' + g.gambar + '" alt="" class="h-14 w-14 shrink-0 rounded-lg border border-line bg-canvas object-contain p-1">' +
                            '<div class="min-w-0 flex-1">' +
                                '<div class="flex items-start justify-between gap-2">' +
                                    '<div class="min-w-0">' +
                                        '<p class="truncate text-xs font-bold text-ink">' + g.nama + '</p>' +
                                        '<p class="text-[11px] text-muted">' + genderLabel + '</p>' +
                                        '<button type="button" data-side-edit="' + g.id + '" data-side-gender="' + g.gender + '" class="text-[11px] font-semibold text-primary hover:underline cursor-pointer">Edit</button>' +
                                    '</div>' +
                                    '<button type="button" data-side-remove="' + key + '" aria-label="Hapus item" class="text-lg leading-none text-stone-300 transition hover:text-error cursor-pointer">&times;</button>' +
                                '</div>' +
                                '<div class="mt-1.5 flex flex-wrap gap-1.5">' +
                                    g.items.map(function (i) {
                                        return '<span class="rounded-md border border-line bg-canvas px-2 py-0.5 text-[11px] font-semibold text-ink">' + i.ukuran + ' &times; ' + i.jumlah + '</span>';
                                    }).join('') +
                                '</div>' +
                                '<p class="mt-1 text-[11px] text-muted">' + groupQty + ' kaos</p>' +
                            '</div>' +
                            '<span class="whitespace-nowrap text-xs font-bold text-ink">Rp ' + num(g.subtotal) + '</span>' +
                        '</div>'
                    );
                }).join('');
            }

            sideCount.textContent = String(totalQty);
            sideSubtotal.textContent = rupiah(subtotal);
            sideTotal.textContent = rupiah(subtotal);

            if (step === 1) {
                if (sideNext) {
                    sideNext.classList.remove('hidden');
                    sideNext.innerHTML = 'Lanjut ke Data &amp; Alamat &rarr;';
                }
                if (sideStep2Notice) sideStep2Notice.classList.add('hidden');
                if (cardPilihMetode) cardPilihMetode.classList.add('hidden');
            } else {
                if (sideNext) sideNext.classList.add('hidden');
                if (sideStep2Notice) sideStep2Notice.classList.remove('hidden');
                if (cardPilihMetode) cardPilihMetode.classList.remove('hidden');
            }

            syncHidden();
            updateMobileFloating();
        }

        function syncHidden() {
            let html = '';
            cart.forEach(function (item, idx) {
                html += '<input type="hidden" name="items[' + idx + '][design_id]" value="' + item.design_id + '">';
                html += '<input type="hidden" name="items[' + idx + '][ukuran]" value="' + item.ukuran + '">';
                html += '<input type="hidden" name="items[' + idx + '][gender]" value="' + (item.gender || 'pria') + '">';
                html += '<input type="hidden" name="items[' + idx + '][jumlah]" value="' + item.jumlah + '">';
            });
            // Compatibility fields
            if (cart.length > 0) {
                html += '<input type="hidden" name="design_id" value="' + cart[0].design_id + '">';
                html += '<input type="hidden" name="ukuran" value="' + cart[0].ukuran + '">';
                html += '<input type="hidden" name="jumlah" value="' + cart[0].jumlah + '">';
            } else if (selectedId) {
                html += '<input type="hidden" name="design_id" value="' + selectedId + '">';
            }
            hiddenWrap.innerHTML = html;
        }

        function selectDesign(id, gender) {
            selectedId = id;
            if (!draft[id]) initDraft(id);
            if (gender) genderSel[id] = (gender === 'wanita') ? 'wanita' : 'pria';
            else if (!genderSel[id]) genderSel[id] = 'pria';
            renderCards();
            renderDetail();
            renderOthers();
        }

        function addDraft() {
            if (selectedId === null) return;
            if (!draft[selectedId]) initDraft(selectedId);
            const gender = genderOf(selectedId);
            const counts = draft[selectedId][gender] || {};
            const total = Object.values(counts).reduce(function (a, b) { return a + b; }, 0);
            if (total < 1) {
                alert('Tentukan jumlah kaos minimal 1 terlebih dahulu.');
                return;
            }
            const d = designById(selectedId);
            // Hapus item desain dan gender ini sebelumnya dari keranjang
            cart = cart.filter(function (i) {
                return !(String(i.design_id) === String(selectedId) && (i.gender || 'pria') === gender);
            });
            // Tambahkan item yang dipilih
            Object.keys(counts).forEach(function (size) {
                const n = counts[size];
                if (n > 0) {
                    cart.push({
                        design_id: d.id,
                        nama: d.nama,
                        harga: d.harga,
                        gambar: d.gambar,
                        ukuran: size,
                        gender: gender,
                        jumlah: n,
                        subtotal: d.harga * n,
                    });
                }
            });
            step1Error?.classList.add('hidden');
            renderCards();
            renderSidebar();
        }

        function showStep(n) {
            step = Math.min(2, Math.max(1, n));
            document.getElementById('wz-step-1').classList.toggle('hidden', step !== 1);
            document.getElementById('wz-step-2').classList.toggle('hidden', step !== 2);

            indicatorItems.forEach(function (li) {
                const idx = parseInt(li.dataset.wzInd, 10);
                const numEl = li.querySelector('[data-wz-num]');
                const labelEl = li.querySelector('[data-wz-label]');
                if (idx < step) {
                    numEl.className = 'grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold bg-secondary text-white';
                    labelEl.className = 'text-[11px] font-semibold uppercase tracking-wide text-secondary font-bold';
                } else if (idx === step) {
                    numEl.className = 'grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold bg-[#b85429] text-white';
                    labelEl.className = 'text-[11px] font-semibold uppercase tracking-wide text-ink font-bold';
                } else {
                    numEl.className = 'grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold border border-line bg-white text-stone-400';
                    labelEl.className = 'text-[11px] font-semibold uppercase tracking-wide text-stone-400';
                }
            });

            if (step === 1) {
                if (sideNext) {
                    sideNext.classList.remove('hidden');
                    sideNext.innerHTML = 'Lanjut ke Data &amp; Alamat &rarr;';
                }
                if (sideStep2Notice) sideStep2Notice.classList.add('hidden');
                if (cardPilihMetode) cardPilihMetode.classList.add('hidden');
            } else {
                if (sideNext) sideNext.classList.add('hidden');
                if (sideStep2Notice) sideStep2Notice.classList.remove('hidden');
                if (cardPilihMetode) cardPilihMetode.classList.remove('hidden');
            }

            updateMobileFloating();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep1() {
            if (cart.length === 0 && selectedId !== null && draftTotal(selectedId) > 0) {
                addDraft();
            }
            if (cart.length > 0) {
                step1Error?.classList.add('hidden');
                return true;
            }
            step1Error?.classList.remove('hidden');
            step1Error?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        function validateStep2() {
            const fields = document.querySelectorAll('#wz-step-2 input, #wz-step-2 select, #wz-step-2 textarea');
            for (let i = 0; i < fields.length; i++) {
                const el = fields[i];
                if (el.disabled || el.type === 'hidden') continue;
                if (!el.checkValidity()) {
                    el.reportValidity();
                    return false;
                }
            }
            if (!inputMetode || !inputMetode.value) {
                const targetCard = (window.innerWidth >= 1024) ? cardPilihMetode : mobilePilihMetode;
                if (targetCard) {
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetCard.classList.add('ring-2', 'ring-[#c85a32]');
                    setTimeout(function () {
                        targetCard.classList.remove('ring-2', 'ring-[#c85a32]');
                    }, 2500);
                }
                document.querySelectorAll('.metode-error-msg').forEach(function (el) {
                    el.classList.remove('hidden');
                });
                return false;
            }
            return true;
        }

        function setMetode(val) {
            if (!val) return;
            if (inputMetode) inputMetode.value = val;
            document.querySelectorAll('.metode-error-msg').forEach(function (el) {
                el.classList.add('hidden');
            });

            document.querySelectorAll('[data-method-card]').forEach(function (card) {
                const method = card.dataset.methodCard;
                const isSelected = (method === val);
                const indicator = card.querySelector('[data-radio-indicator]');
                const dot = indicator ? indicator.querySelector('span') : null;

                if (isSelected) {
                    card.classList.add('border-[#245236]', 'ring-1', 'ring-[#245236]/30', 'bg-[#fbfdfb]');
                    card.classList.remove('border-stone-200', 'bg-white');
                    if (indicator) {
                        indicator.classList.add('border-[#245236]');
                        indicator.classList.remove('border-stone-300');
                    }
                    if (dot) dot.classList.remove('hidden');
                } else {
                    card.classList.remove('border-[#245236]', 'ring-1', 'ring-[#245236]/30', 'bg-[#fbfdfb]');
                    card.classList.add('border-stone-200', 'bg-white');
                    if (indicator) {
                        indicator.classList.remove('border-[#245236]');
                        indicator.classList.add('border-stone-300');
                    }
                    if (dot) dot.classList.add('hidden');
                }
            });
        }

        document.addEventListener('click', function (e) {
            const card = e.target.closest('[data-method-card]');
            if (card) {
                setMetode(card.dataset.methodCard);
            }
        });

        const initialMetode = @json(old('metode', ''));
        if (initialMetode) {
            setMetode(initialMetode);
        }

        // Event delegation
        cardsWrap?.addEventListener('click', function (e) {
            const genderBtn = e.target.closest('[data-card-gender]');
            const card = e.target.closest('[data-card-design]');
            if (card) {
                const g = genderBtn ? genderBtn.dataset.cardGender : null;
                selectDesign(card.dataset.cardDesign, g);
            }
        });

        othersWrap?.addEventListener('click', function (e) {
            const genderBtn = e.target.closest('[data-card-gender]');
            const card = e.target.closest('[data-card-design]');
            if (card) {
                const g = genderBtn ? genderBtn.dataset.cardGender : null;
                selectDesign(card.dataset.cardDesign, g);
                window.scrollTo({ top: detailWrap.offsetTop - 80, behavior: 'smooth' });
            }
        });

        detailWrap?.addEventListener('click', function (e) {
            const g = e.target.closest('[data-detail-gender]');
            if (g && selectedId !== null) {
                genderSel[selectedId] = g.dataset.detailGender;
                renderDetail();
                updateMobileFloating();
                return;
            }
            const q = e.target.closest('[data-qty-btn]');
            if (q && selectedId !== null) {
                if (!draft[selectedId]) initDraft(selectedId);
                const gen = genderOf(selectedId);
                const sz = q.dataset.size;
                const dir = parseInt(q.dataset.dir, 10);
                draft[selectedId][gen][sz] = Math.min(100, Math.max(0, (draft[selectedId][gen][sz] || 0) + dir));
                renderDetail();
                updateMobileFloating();
                return;
            }
            if (e.target.closest('#btn-add-draft')) {
                addDraft();
                updateMobileFloating();
                return;
            }
        });

        sideItems?.addEventListener('click', function (e) {
            const edit = e.target.closest('[data-side-edit]');
            if (edit) {
                showStep(1);
                selectDesign(edit.dataset.sideEdit, edit.dataset.sideGender);
                window.scrollTo({ top: detailWrap.offsetTop - 80, behavior: 'smooth' });
                return;
            }
            const del = e.target.closest('[data-side-remove]');
            if (del) {
                const parts = del.dataset.sideRemove.split('|');
                cart = cart.filter(function (i) {
                    return !(String(i.design_id) === String(parts[0]) && (i.gender || 'pria') === parts[1]);
                });
                if (parts[0]) initDraft(parts[0]);
                renderSidebar();
                renderCards();
                renderDetail();
                renderOthers();
                updateMobileFloating();
            }
        });

        sideNext?.addEventListener('click', function () {
            if (step === 1) {
                if (validateStep1()) {
                    showStep(2);
                }
            } else {
                form.requestSubmit();
            }
        });

        // Mobile drawer handlers
        mobileDrawerToggleBtn?.addEventListener('click', function () {
            toggleDrawer();
        });

        mobileBarSummaryTrigger?.addEventListener('click', function () {
            toggleDrawer();
        });

        mobileDrawerCloseBtn?.addEventListener('click', function () {
            toggleDrawer(false);
        });

        mobileDrawerBackdrop?.addEventListener('click', function () {
            toggleDrawer(false);
        });

        mobileDrawerItems?.addEventListener('click', function (e) {
            const edit = e.target.closest('[data-mobile-edit]');
            if (edit) {
                toggleDrawer(false);
                showStep(1);
                selectDesign(edit.dataset.mobileEdit, edit.dataset.mobileGender);
                window.scrollTo({ top: detailWrap.offsetTop - 80, behavior: 'smooth' });
                return;
            }
            const del = e.target.closest('[data-mobile-remove]');
            if (del) {
                const parts = del.dataset.mobileRemove.split('|');
                cart = cart.filter(function (i) {
                    return !(String(i.design_id) === String(parts[0]) && (i.gender || 'pria') === parts[1]);
                });
                if (parts[0]) initDraft(parts[0]);
                renderSidebar();
                renderCards();
                renderDetail();
                renderOthers();
                updateMobileFloating();
            }
        });

        mobileFloatNextBtn?.addEventListener('click', function () {
            if (step === 1) {
                if (validateStep1()) {
                    toggleDrawer(false);
                    showStep(2);
                }
            } else {
                form.requestSubmit();
            }
        });

        document.querySelectorAll('[data-wz-back]').forEach(function (b) {
            b.addEventListener('click', function () {
                showStep(parseInt(b.dataset.wzBack, 10));
            });
        });

        indicatorItems.forEach(function (li) {
            li.addEventListener('click', function () {
                const n = parseInt(li.dataset.wzInd, 10);
                if (n === 1) {
                    showStep(1);
                } else if (n === 2) {
                    if (validateStep1()) showStep(2);
                }
            });
        });

        form.addEventListener('submit', function (e) {
            if (!validateStep1()) {
                e.preventDefault();
                showStep(1);
                return;
            }
            if (step === 1) {
                e.preventDefault();
                showStep(2);
                return;
            }
            if (!validateStep2()) {
                e.preventDefault();
                return;
            }
        });

        // Global delegate untuk panduan ukuran
        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-open-size-chart], #size-guide')) {
                e.preventDefault();
                if (window.SizeChartModal) {
                    window.SizeChartModal.open();
                }
            }
        });

        if (selectedId !== null) initDraft(selectedId);
        renderCards();
        renderDetail();
        renderOthers();
        renderSidebar();
        updateMobileFloating();

        // Lompat ke langkah 2 jika terdapat server error
        const SERVER_ERROR_FIELDS = @json(array_keys($errors->toArray()));
        if (SERVER_ERROR_FIELDS.length > 0) {
            showStep(2);
        }
    })();

    // Integrasi RajaOngkir pada wizard (Langkah 2 — Alamat Pengiriman)
    (function () {
        var provSelect = document.getElementById('provinsi');
        var kabSelect = document.getElementById('kabupaten');
        var kecSelect = document.getElementById('kecamatan');
        var kelSelect = document.getElementById('kelurahan');
        var posInput = document.getElementById('kode-pos');
        var oldKab = document.getElementById('old-kabupaten') ? document.getElementById('old-kabupaten').value : '';
        var oldKec = document.getElementById('old-kecamatan') ? document.getElementById('old-kecamatan').value : '';
        var oldKel = document.getElementById('old-kelurahan') ? document.getElementById('old-kelurahan').value : '';
        var loadKab = document.getElementById('loading-kabupaten');
        var loadKec = document.getElementById('loading-kecamatan');
        var loadKel = document.getElementById('loading-kelurahan');
        var badgePos = document.getElementById('badge-kodepos');

        if (!provSelect || !kabSelect || !kecSelect || !posInput) return;

        var cityCache = {};
        var districtCache = {};
        var subdistrictCache = {};

        function getSelectedOption(select) {
            if (!select || select.selectedIndex < 0) return null;
            return select.options[select.selectedIndex];
        }

        function loadCities(provId, selectedCityName) {
            kabSelect.disabled = true;
            kabSelect.innerHTML = '<option value="">Memuat kota/kabupaten...</option>';
            kecSelect.disabled = true;
            kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
            if (kelSelect) {
                kelSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
            }
            if (loadKab) loadKab.classList.remove('hidden');

            function populate(cities) {
                if (loadKab) loadKab.classList.add('hidden');
                kabSelect.innerHTML = '<option value="">— Pilih Kota / Kabupaten —</option>';
                var matchedCityId = null;

                cities.forEach(function (c) {
                    var opt = document.createElement('option');
                    opt.value = c.name;
                    opt.textContent = c.name;
                    opt.dataset.id = c.id;
                    if (selectedCityName && selectedCityName.toUpperCase() === c.name.toUpperCase()) {
                        opt.selected = true;
                        matchedCityId = c.id;
                    }
                    kabSelect.appendChild(opt);
                });

                kabSelect.disabled = false;

                if (matchedCityId) {
                    loadDistricts(matchedCityId, oldKec);
                }
            }

            if (cityCache[provId]) {
                populate(cityCache[provId]);
                return;
            }

            fetch('/api/rajaongkir/cities?province_id=' + encodeURIComponent(provId))
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    var data = (res && res.data) ? res.data : [];
                    cityCache[provId] = data;
                    populate(data);
                })
                .catch(function () {
                    if (loadKab) loadKab.classList.add('hidden');
                    kabSelect.innerHTML = '<option value="">Gagal memuat kota, silakan coba lagi</option>';
                    kabSelect.disabled = false;
                });
        }

        function loadDistricts(cityId, selectedDistrictName) {
            kecSelect.disabled = true;
            kecSelect.innerHTML = '<option value="">Memuat kecamatan...</option>';
            if (kelSelect) {
                kelSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
            }
            if (loadKec) loadKec.classList.remove('hidden');

            function populate(districts) {
                if (loadKec) loadKec.classList.add('hidden');
                kecSelect.innerHTML = '<option value="">— Pilih Kecamatan —</option>';
                var matchedDistId = null;
                var matchedZip = null;

                districts.forEach(function (d) {
                    var opt = document.createElement('option');
                    opt.value = d.name;
                    opt.textContent = d.name;
                    opt.dataset.id = d.id;
                    opt.dataset.zip = d.zip_code || '';
                    if (selectedDistrictName && selectedDistrictName.toUpperCase() === d.name.toUpperCase()) {
                        opt.selected = true;
                        matchedDistId = d.id;
                        matchedZip = d.zip_code || '';
                    }
                    kecSelect.appendChild(opt);
                });

                kecSelect.disabled = false;

                if (matchedDistId) {
                    loadSubDistricts(matchedDistId, oldKel, matchedZip);
                }
            }

            if (districtCache[cityId]) {
                populate(districtCache[cityId]);
                return;
            }

            fetch('/api/rajaongkir/districts?city_id=' + encodeURIComponent(cityId))
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    var data = (res && res.data) ? res.data : [];
                    districtCache[cityId] = data;
                    populate(data);
                })
                .catch(function () {
                    if (loadKec) loadKec.classList.add('hidden');
                    kecSelect.innerHTML = '<option value="">Gagal memuat kecamatan, silakan coba lagi</option>';
                    kecSelect.disabled = false;
                });
        }

        function loadSubDistricts(districtId, selectedSubDistrictName, fallbackZip) {
            if (!kelSelect) return;
            kelSelect.disabled = true;
            kelSelect.innerHTML = '<option value="">Memuat kelurahan...</option>';
            if (loadKel) loadKel.classList.remove('hidden');

            function populate(subdistricts) {
                if (loadKel) loadKel.classList.add('hidden');
                kelSelect.innerHTML = '<option value="">— Pilih Kelurahan / Desa —</option>';
                var matchedZip = null;

                if (subdistricts && subdistricts.length > 0) {
                    subdistricts.forEach(function (s) {
                        var opt = document.createElement('option');
                        opt.value = s.name;
                        opt.textContent = s.name + (s.zip_code ? ' (' + s.zip_code + ')' : '');
                        opt.dataset.id = s.id;
                        opt.dataset.zip = s.zip_code || '';
                        if (selectedSubDistrictName && selectedSubDistrictName.toUpperCase() === s.name.toUpperCase()) {
                            opt.selected = true;
                            matchedZip = s.zip_code || '';
                        }
                        kelSelect.appendChild(opt);
                    });
                    kelSelect.disabled = false;
                } else {
                    kelSelect.innerHTML = '<option value="">— Tidak ada data kelurahan khusus —</option>';
                    kelSelect.disabled = false;
                }

                var finalZip = matchedZip || fallbackZip;
                if (finalZip && (!posInput.value || posInput.value === '0')) {
                    posInput.value = finalZip;
                    if (badgePos) badgePos.classList.remove('hidden');
                }
            }

            if (subdistrictCache[districtId]) {
                populate(subdistrictCache[districtId]);
                return;
            }

            fetch('/api/rajaongkir/subdistricts?district_id=' + encodeURIComponent(districtId))
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    var data = (res && res.data) ? res.data : [];
                    subdistrictCache[districtId] = data;
                    populate(data);
                })
                .catch(function () {
                    if (loadKel) loadKel.classList.add('hidden');
                    kelSelect.innerHTML = '<option value="">Gagal memuat kelurahan, silakan coba lagi</option>';
                    kelSelect.disabled = false;
                });
        }

        provSelect.addEventListener('change', function () {
            var opt = getSelectedOption(provSelect);
            var provId = opt ? opt.dataset.id : null;
            posInput.value = '';
            if (badgePos) badgePos.classList.add('hidden');

            if (!provId) {
                kabSelect.disabled = true;
                kabSelect.innerHTML = '<option value="">— Pilih Provinsi Dahulu —</option>';
                kecSelect.disabled = true;
                kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
                if (kelSelect) {
                    kelSelect.disabled = true;
                    kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
                }
                return;
            }

            loadCities(provId, null);
        });

        kabSelect.addEventListener('change', function () {
            var opt = getSelectedOption(kabSelect);
            var cityId = opt ? opt.dataset.id : null;
            posInput.value = '';
            if (badgePos) badgePos.classList.add('hidden');

            if (!cityId) {
                kecSelect.disabled = true;
                kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
                if (kelSelect) {
                    kelSelect.disabled = true;
                    kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
                }
                return;
            }

            loadDistricts(cityId, null);
        });

        kecSelect.addEventListener('change', function () {
            var opt = getSelectedOption(kecSelect);
            if (!opt || !opt.value) {
                posInput.value = '';
                if (badgePos) badgePos.classList.add('hidden');
                if (kelSelect) {
                    kelSelect.disabled = true;
                    kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
                }
                return;
            }

            var zip = opt.dataset.zip;
            var distId = opt.dataset.id;

            if (zip && zip !== '0') {
                posInput.value = zip;
                if (badgePos) badgePos.classList.remove('hidden');
            }

            if (distId) {
                loadSubDistricts(distId, null, zip);
            }
        });

        if (kelSelect) {
            kelSelect.addEventListener('change', function () {
                var opt = getSelectedOption(kelSelect);
                if (!opt || !opt.value) return;

                var zip = opt.dataset.zip;
                if (zip && zip !== '0') {
                    posInput.value = zip;
                    if (badgePos) badgePos.classList.remove('hidden');
                }
            });
        }

        var initOpt = getSelectedOption(provSelect);
        if (initOpt && initOpt.dataset.id) {
            loadCities(initOpt.dataset.id, oldKab);
        }
    })();
</script>
@endpush
