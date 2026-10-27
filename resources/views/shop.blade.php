@extends('layouts.app')

@section('title', 'Shop + Donasi — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Dukung pembangunan ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': pesan kaos pilihanmu, lengkapi data pengiriman, tentukan donasi, lalu lakukan pembayaran.')

@php
    $designData = $designs->map(fn ($d) => [
        'id' => $d->id,
        'nama' => $d->nama,
        'harga' => $d->harga,
        'deskripsi' => $d->deskripsi ?? '',
        'gambar' => $d->gambarUrl(),
        'sizes' => $d->pilihanUkuran(),
    ])->values()->all();

    $heroShirts = $designs->take(2)->values();

    $wzSteps = [
        1 => 'Pilih Kaos',
        2 => 'Data & Alamat',
        3 => 'Donasi',
        4 => 'Pembayaran',
    ];
@endphp

@section('content')
    {{-- ================= HERO HEADER ================= --}}
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
                    <h1 class="mt-3 font-serif text-4xl font-extrabold tracking-tight text-secondary sm:text-5xl">Kaos + Donasi</h1>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted sm:text-base">
                        Dukung pembangunan {{ $programInfo->nama_program ?? 'GMAHK Haleluya, Bali' }} dengan memesan kaos pilihanmu dan berdonasi secara langsung.
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

    {{-- ================= KONTEN ================= --}}
    <section class="mx-auto max-w-6xl px-4 pt-10 pb-28 sm:px-6 lg:pb-10">
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

        @if ($berhasil ?? false)
            <div class="mb-8 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <p class="text-sm leading-relaxed text-emerald-800">
                    Dukungan Anda telah tercatat. Panitia akan segera menghubungi Anda melalui WhatsApp.
                </p>
            </div>
        @endif

        {{-- PILIH JENIS DUKUNGAN (toggle kecil) --}}
        <div class="mb-8 flex flex-wrap items-center gap-x-4 gap-y-2">
            <div class="inline-flex rounded-full border border-line bg-white p-1 shadow-xs">
                <a href="{{ route('checkout.pemesanan') }}"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-ink">
                    Beli Kaos Saja
                </a>
                <button type="button" id="card-choice-kaos"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer {{ $tab === 'kaos_donasi' ? 'bg-secondary text-white shadow-xs' : 'text-stone-500 hover:text-ink' }}">
                    Pesan Kaos + Donasi
                </button>
                <button type="button" id="card-choice-donasi"
                    class="type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer {{ $tab === 'donasi_saja' ? 'bg-secondary text-white shadow-xs' : 'text-stone-500 hover:text-ink' }}">
                    Donasi Saja
                </button>
            </div>
            <p class="text-xs text-muted">
                <span class="font-semibold text-ink">Beli Kaos Saja:</span> tanpa donasi tambahan
                &bull; <span class="font-semibold text-ink">Kaos + Donasi:</span> minimal Rp50.000
                &bull; <span class="font-semibold text-ink">Donasi Saja:</span> sukarela
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-12 items-start">
            {{-- ========================================================================= --}}
            {{-- KOLOM KIRI --}}
            {{-- ========================================================================= --}}
            <div class="lg:col-span-8">

                {{-- ----------------------------------------------------------------- --}}
                {{-- DONASI SAJA (form tunggal) --}}
                {{-- ----------------------------------------------------------------- --}}
                <div id="wrapper-donasi-saja" class="{{ $tab === 'donasi_saja' ? '' : 'hidden' }}">
                    <form action="{{ route('shop.store') }}" method="POST" id="form-donasi-saja" class="card p-6 sm:p-8 bg-white border border-stone-200 rounded-2xl space-y-6">
                        @csrf
                        <input type="hidden" name="jenis" value="donasi_saja">

                        {{-- Data Pribadi --}}
                        <div>
                            <h2 class="font-serif text-lg font-bold text-stone-900">Data Pribadi</h2>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="ds-nama" class="label">Nama <span class="text-error">*</span></label>
                                    <input type="text" id="ds-nama" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap"
                                        class="input @error('nama') input-error @enderror" {{ $tab === 'donasi_saja' ? 'required' : '' }}>
                                    @error('nama')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="ds-email" class="label">Email <span class="text-error">*</span></label>
                                    <input type="email" id="ds-email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                                        class="input @error('email') input-error @enderror" {{ $tab === 'donasi_saja' ? 'required' : '' }}>
                                    @error('email')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="ds-nomor" class="label">Nomor Telepon / WhatsApp <span class="text-error">*</span></label>
                                <input type="tel" id="ds-nomor" name="nomor" value="{{ old('nomor') }}" placeholder="Contoh: 08123456789"
                                    class="input @error('nomor') input-error @enderror" {{ $tab === 'donasi_saja' ? 'required' : '' }}>
                                @error('nomor')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Donasi --}}
                        <div class="pt-2">
                            <h2 class="font-serif text-lg font-bold text-stone-900">Donasi</h2>
                            <div class="mt-3 rounded-xl bg-[#fff7ed] p-3 text-xs font-medium text-[#c2410c]">
                                Donasi sukarela. Tidak ada batas minimum.
                            </div>

                            <div class="mt-4">
                                <label for="ds-donasi" class="label">Nominal Donasi (Rp) <span class="text-error">*</span></label>
                                <input type="number" id="ds-donasi" name="donasi" min="1000" step="1000"
                                    value="{{ old('donasi') }}" placeholder="Masukkan nominal donasi"
                                    class="input @error('donasi') input-error @enderror" {{ $tab === 'donasi_saja' ? 'required' : '' }}>
                                @error('donasi')<p class="error-text">{{ $message }}</p>@enderror
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2.5">
                                @foreach ([50000, 100000, 250000, 500000] as $chip)
                                    <button type="button" data-nominal="{{ $chip }}"
                                        class="ds-chip rounded-full border border-stone-200 bg-white px-5 py-2 text-xs font-semibold text-stone-700 transition hover:border-[#b05525] hover:text-[#b05525] cursor-pointer">
                                        Rp {{ number_format($chip, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <label for="ds-catatan" class="label">Catatan &mdash; opsional</label>
                                <textarea id="ds-catatan" name="catatan" rows="3" placeholder="Pesan untuk panitia"
                                    class="input @error('catatan') input-error @enderror">{{ old('catatan') }}</textarea>
                                @error('catatan')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Footer Form --}}
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <p class="text-xs text-stone-400">
                                Data Anda digunakan panitia untuk konfirmasi donasi.
                            </p>
                            <button type="submit" class="rounded-xl bg-[#b05525] hover:bg-[#964319] text-white px-7 py-3 text-sm font-bold shadow-xs transition cursor-pointer">
                                Kirim Donasi
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ----------------------------------------------------------------- --}}
                {{-- KAOS + DONASI: WIZARD 3 LANGKAH --}}
                {{-- Langkah 4 (Pembayaran) = halaman /shop/{order}/pembayaran --}}
                {{-- ----------------------------------------------------------------- --}}
                <div id="wrapper-kaos-donasi" class="{{ $tab === 'kaos_donasi' ? '' : 'hidden' }}">
                    <form action="{{ route('shop.store') }}" method="POST" id="form-kaos-donasi" novalidate class="space-y-6">
                        @csrf
                        <input type="hidden" name="jenis" value="kaos_donasi">

                        {{-- === Indikator Langkah === --}}
                        <ol id="wizard-steps" class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-2xl border border-line bg-white px-4 py-2.5">
                            @foreach ($wzSteps as $n => $label)
                                <li data-wz-ind="{{ $n }}" class="flex cursor-pointer items-center gap-1.5">
                                    <span data-wz-num class="grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold">{{ $n }}</span>
                                    <span data-wz-label class="text-[11px] font-semibold uppercase tracking-wide">{{ $label }}</span>
                                </li>
                                @if ($n < 4)
                                    <li class="mx-1 h-px w-5 bg-line sm:w-8" aria-hidden="true"></li>
                                @endif
                            @endforeach
                        </ol>

                        {{-- ====================================================== --}}
                        {{-- LANGKAH 1: PILIH PRODUK KAOS --}}
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

                                {{-- Kartu produk --}}
                                <div id="product-cards" class="mt-5 grid grid-cols-3 gap-2.5 sm:grid-cols-5"></div>

                                {{-- Detail produk terpilih + stepper ukuran --}}
                                <div id="product-detail" class="mt-5"></div>

                                {{-- Produk lain (akordeon) --}}
                                <div id="other-products" class="mt-5 space-y-3"></div>

                                {{-- Tombol pemicu modal ukuran (tersembunyi, di-klik via delegasi) --}}
                                <button type="button" id="size-guide-trigger" data-open-size-chart class="hidden" aria-hidden="true" tabindex="-1"></button>

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
                                        <label for="kd-nama" class="label">Nama <span class="text-error">*</span></label>
                                        <input type="text" id="kd-nama" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap"
                                            class="input @error('nama') input-error @enderror" required>
                                        @error('nama')<p class="error-text">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="kd-email" class="label">Email <span class="text-error">*</span></label>
                                        <input type="email" id="kd-email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                                            class="input @error('email') input-error @enderror" required>
                                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="kd-nomor" class="label">Nomor Telepon / WhatsApp <span class="text-error">*</span></label>
                                    <input type="tel" id="kd-nomor" name="nomor" value="{{ old('nomor') }}" placeholder="Contoh: 08123456789"
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
                                                <label for="kd-provinsi" class="label">Provinsi <span class="text-error">*</span></label>
                                                <span id="kd-loading-provinsi" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Memuat...
                                                </span>
                                            </div>
                                            <select id="kd-provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" required>
                                                <option value="">— Pilih Provinsi —</option>
                                                @foreach ($provinsi as $p)
                                                    @php
                                                        $pName = is_array($p) ? ($p['name'] ?? '') : $p;
                                                        $pId = is_array($p) ? ($p['id'] ?? '') : '';
                                                    @endphp
                                                    <option value="{{ $pName }}" data-id="{{ $pId }}" @selected(strtoupper((string) old('provinsi')) === strtoupper((string) $pName))>
                                                        {{ $pName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('provinsi')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label for="kd-kabupaten" class="label">Kabupaten / Kota <span class="text-error">*</span></label>
                                                <span id="kd-loading-kabupaten" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Memuat kota...
                                                </span>
                                            </div>
                                            <select id="kd-kabupaten" name="kabupaten" class="input @error('kabupaten') input-error @enderror" required disabled>
                                                <option value="">— Pilih Provinsi Dahulu —</option>
                                            </select>
                                            <input type="hidden" id="kd-old-kabupaten" value="{{ old('kabupaten') }}">
                                            @error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label for="kd-kecamatan" class="label">Kecamatan <span class="text-error">*</span></label>
                                                <span id="kd-loading-kecamatan" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Memuat kecamatan...
                                                </span>
                                            </div>
                                            <select id="kd-kecamatan" name="kecamatan" class="input @error('kecamatan') input-error @enderror" required disabled>
                                                <option value="">— Pilih Kota/Kabupaten Dahulu —</option>
                                            </select>
                                            <input type="hidden" id="kd-old-kecamatan" value="{{ old('kecamatan') }}">
                                            @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label for="kd-kelurahan" class="label">Kelurahan / Desa</label>
                                                <span id="kd-loading-kelurahan" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Memuat kelurahan...
                                                </span>
                                            </div>
                                            <select id="kd-kelurahan" name="kelurahan" class="input @error('kelurahan') input-error @enderror" disabled>
                                                <option value="">— Pilih Kecamatan Dahulu —</option>
                                            </select>
                                            <input type="hidden" id="kd-old-kelurahan" value="{{ old('kelurahan') }}">
                                            @error('kelurahan')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label for="kd-kode-pos" class="label">Kode Pos <span class="text-error">*</span></label>
                                                <span id="kd-badge-kodepos" class="hidden text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                    Otomatis RajaOngkir ✓
                                                </span>
                                            </div>
                                            <input type="text" id="kd-kode-pos" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="Masukkan kode pos"
                                                class="input @error('kode_pos') input-error @enderror" required>
                                            <p class="mt-1 text-[11px] text-stone-500">Terisi otomatis saat memilih kelurahan / kecamatan.</p>
                                            @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label for="kd-alamat" class="label">Alamat Lengkap <span class="text-error">*</span></label>
                                            <textarea id="kd-alamat" name="alamat" rows="2" placeholder="Jalan, nomor rumah, RT/RW, patokan"
                                                class="input @error('alamat') input-error @enderror" required>{{ old('alamat') }}</textarea>
                                            @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div class="border-t border-line pt-6">
                                        <div class="flex items-center justify-between">
                                            <h2 class="font-serif text-lg font-bold text-stone-900">Pilih Metode Pembayaran</h2>
                                            <span class="text-xs text-stone-500">Pilih salah satu</span>
                                        </div>
                                        <p class="mt-1 text-xs text-stone-500">
                                            Pilih cara pembayaran yang paling nyaman untuk Anda.
                                        </p>
                                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                            {{-- Option 1: QRIS --}}
                                            <label class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
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
                                                        <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">Scan QRIS (BCA, GoPay, OVO, dll).</p>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 pl-2">
                                                    <input type="radio" name="metode" value="qris" class="h-4 w-4 text-secondary focus:ring-secondary cursor-pointer" {{ old('metode', 'qris') === 'qris' ? 'checked' : '' }}>
                                                </div>
                                            </label>

                                            {{-- Option 2: Transfer Bank --}}
                                            <label class="payment-card flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition cursor-pointer select-none">
                                                <div class="flex items-center gap-3.5">
                                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#eaf4ec] text-[#245236]">
                                                        <svg class="h-6 w-6 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 8h18l-9-5Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 9v8m4-8v8m6-8v8m4-8v8M3 17h18M3 21h18" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm text-stone-900 leading-tight">Transfer Bank</p>
                                                        <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">Transfer manual ke rekening panitia.</p>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 pl-2">
                                                    <input type="radio" name="metode" value="transfer" class="h-4 w-4 text-secondary focus:ring-secondary cursor-pointer" {{ old('metode') === 'transfer' ? 'checked' : '' }}>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                                    <button type="button" data-wz-back="1"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:border-stone-300 hover:text-ink cursor-pointer">
                                        &larr; Kembali ke Produk
                                    </button>
                                    <button type="button" data-wz-next="3"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-7 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                                        Lanjut ke Donasi &rarr;
                                    </button>
                                </div>
                            </div>
                        </section>

                        {{-- ====================================================== --}}
                        {{-- LANGKAH 3: DONASI --}}
                        {{-- ====================================================== --}}
                        <section id="wz-step-3" class="hidden space-y-6">
                            <div class="card p-6 sm:p-8 space-y-6">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-secondary font-display text-sm font-extrabold text-white">3</span>
                                    <div>
                                        <h2 class="font-serif text-lg font-bold text-stone-900">Donasi</h2>
                                        <p class="text-xs text-muted">Tentukan nominal donasi Anda untuk pembangunan {{ $programInfo->nama_program ?? 'GMAHK Haleluya, Bali' }}.</p>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-[#fff7ed] p-3 text-xs font-medium text-[#c2410c]">
                                    Pesan Kaos + Donasi: nominal minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}.
                                </div>

                                <div>
                                    <label for="kd-donasi" class="label">Nominal Donasi (Rp) <span class="text-error">*</span></label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-muted">Rp</span>
                                        <input type="number" id="kd-donasi" name="donasi" min="{{ config('donation.min_kaos_donasi') }}" step="10000"
                                            value="{{ old('donasi') }}" placeholder="Minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}"
                                            class="input pl-10 @error('donasi') input-error @enderror" required>
                                    </div>
                                    @error('donasi')<p class="error-text">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <p class="label !mb-2">Pilih nominal cepat</p>
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach (config('donation.preset_kaos_donasi') as $chip)
                                            <button type="button" data-nominal="{{ $chip }}"
                                                class="kd-chip rounded-full border border-stone-200 bg-white px-5 py-2 text-xs font-semibold text-stone-700 transition hover:border-[#b05525] hover:text-[#b05525] cursor-pointer">
                                                Rp {{ number_format($chip, 0, ',', '.') }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label for="kd-catatan" class="label">Catatan &mdash; opsional</label>
                                    <textarea id="kd-catatan" name="catatan" rows="3" placeholder="Catatan pengiriman atau pesan untuk panitia"
                                        class="input @error('catatan') input-error @enderror">{{ old('catatan') }}</textarea>
                                    @error('catatan')<p class="error-text">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                                    <button type="button" data-wz-back="2"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-2.5 text-sm font-bold text-stone-600 transition hover:border-stone-300 hover:text-ink cursor-pointer">
                                        &larr; Kembali ke Data &amp; Alamat
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-7 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                                        Kirim &amp; Lanjut ke Pembayaran &rarr;
                                    </button>
                                </div>
                            </div>

                            {{-- Ringkasan akhir --}}
                            <div class="card p-6 sm:p-8">
                                <h3 class="font-serif text-base font-bold text-stone-900">Ringkasan Pesanan</h3>
                                <dl class="mt-4 space-y-2.5 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <dt class="text-stone-500">Subtotal kaos (<span id="sum-qty">0</span> kaos)</dt>
                                        <dd id="sum-subtotal" class="font-bold text-stone-900">Rp 0</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <dt class="text-stone-500">Donasi</dt>
                                        <dd id="sum-donasi" class="font-bold text-stone-900">Rp 0</dd>
                                    </div>
                                </dl>
                                <div class="mt-4 flex items-center justify-between border-t border-line pt-4">
                                    <span class="font-serif font-bold text-stone-900">Total</span>
                                    <span id="sum-total" class="text-xl font-extrabold text-primary">Rp 0</span>
                                </div>
                                <p class="mt-2 text-xs leading-relaxed text-stone-400">
                                    Belum termasuk ongkir. Total final (termasuk ongkir) akan dikonfirmasi panitia melalui WhatsApp sebelum pembayaran.
                                </p>
                            </div>
                        </section>

                        {{-- Hidden inputs keranjang (dibangun ulang oleh JS dari old()) --}}
                        <div id="kd-cart-hidden">
                            @foreach ((array) old('items', []) as $oldItem)
                                @if (!empty($oldItem['design_id']))
                                    <input type="hidden" data-old-item data-design-id="{{ $oldItem['design_id'] }}" data-ukuran="{{ $oldItem['ukuran'] ?? '' }}" data-gender="{{ $oldItem['gender'] ?? 'pria' }}" data-jumlah="{{ $oldItem['jumlah'] ?? 1 }}">
                                @endif
                            @endforeach
                        </div>
                    </form>
                </div>

            </div>

            {{-- ========================================================================= --}}
            {{-- KOLOM KANAN: SIDEBAR RINGKASAN --}}
            {{-- ========================================================================= --}}
            <aside class="hidden lg:block lg:col-span-4">
                <div class="card p-6 bg-white border border-stone-200 rounded-2xl">

                    {{-- Sidebar Wizard Kaos + Donasi --}}
                    <div id="side-kaos-donasi" class="{{ $tab === 'kaos_donasi' ? '' : 'hidden' }}">
                        <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan Pesanan</h2>
                        <div id="kd-side-items" class="mt-4 space-y-3"></div>

                        <dl class="mt-4 pt-4 border-t border-stone-200 space-y-2 text-xs sm:text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-stone-500">Subtotal (<span id="kd-side-count">0</span> kaos)</dt>
                                <dd id="kd-side-subtotal" class="font-bold text-stone-900">Rp 0</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-stone-500">Donasi <span class="text-stone-400">&middot; Langkah 3</span></dt>
                                <dd id="kd-side-donasi" class="font-bold text-stone-900">Rp 0</dd>
                            </div>
                        </dl>

                        <div class="mt-4 rounded-xl bg-[#efede3] p-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-serif text-sm font-bold text-stone-900">Total Pembayaran</span>
                                <span id="kd-side-total" class="text-xl font-extrabold text-stone-900">Rp 0</span>
                            </div>
                            <button type="button" id="side-next-btn"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-5 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                                Lanjut ke Data Pribadi &rarr;
                            </button>
                        </div>

                        <div class="mt-5 rounded-xl border border-line bg-white p-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                                <p class="text-xs font-bold text-ink">Donasi</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-muted">
                                Nominal donasi ditentukan di <span class="font-semibold text-ink">Langkah 3</span> (minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}).
                            </p>
                        </div>

                        <p class="mt-4 text-xs text-stone-400 leading-relaxed">
                            Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening &amp; total final setelah pesanan terkirim.
                        </p>

                        <a href="{{ route('progres') }}" class="text-xs font-semibold text-primary hover:underline mt-4 inline-block">
                            Lihat Progres dulu &rarr;
                        </a>
                    </div>

                    {{-- Sidebar Donasi Saja --}}
                    <div id="side-donasi-saja" class="{{ $tab === 'donasi_saja' ? '' : 'hidden' }}">
                        <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan</h2>

                        <dl class="mt-4 space-y-3 text-xs sm:text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Jenis</dt>
                                <dd class="font-bold text-stone-900 text-right">Donasi Saja</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Ketentuan</dt>
                                <dd class="font-bold text-stone-900 text-right">Sukarela</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Minimal</dt>
                                <dd class="font-bold text-stone-900 text-right">Tidak ada</dd>
                            </div>
                        </dl>

                        <div class="border-t border-stone-200 my-4 pt-4 flex items-center justify-between">
                            <span class="font-bold text-stone-900 text-sm">Total</span>
                            <span id="ds-side-total" class="font-serif text-2xl font-bold text-[#b05525]">Rp 0</span>
                        </div>

                        <p class="text-xs text-stone-400 leading-relaxed">
                            Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening setelah formulir terkirim.
                        </p>

                        <a href="{{ route('progres') }}" class="text-xs font-semibold text-[#b05525] hover:underline mt-4 inline-block">
                            Lihat Progres dulu &rarr;
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        {{-- ========================================================================= --}}
        {{-- FLOATING EXPANDABLE RINGKASAN PESANAN & RINGKASAN (KHUSUS MODE HP)        --}}
        {{-- ========================================================================= --}}
        <div id="kd-mobile-floating-bar"
            class="fixed bottom-0 inset-x-0 z-40 lg:hidden transition-all duration-300 ease-out transform translate-y-full opacity-0 pointer-events-none">

            {{-- Backdrop transparan gelap saat mengembang --}}
            <div id="kd-mobile-drawer-backdrop"
                class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none -z-10"></div>

            <div class="relative bg-white border-t border-stone-200/90 shadow-[0_-8px_30px_rgba(0,0,0,0.12)] rounded-t-2xl overflow-hidden flex flex-col max-h-[85vh]">
                {{-- Handle Bar / Tap to Expand --}}
                <button type="button" id="kd-mobile-drawer-toggle-btn" class="w-full pt-2.5 pb-1 flex flex-col items-center justify-center cursor-pointer select-none group" aria-label="Buka ringkasan pesanan">
                    <span class="h-1.5 w-12 rounded-full bg-stone-300 group-hover:bg-stone-400 transition"></span>
                </button>

                {{-- Bagian Mengembang (Drawer Rincian Lengkap) --}}
                <div id="kd-mobile-drawer-body" class="hidden overflow-y-auto px-4 pb-3 space-y-3">
                    {{-- Header Kaos Donasi --}}
                    <div id="kd-drawer-kaos-header" class="flex items-center justify-between border-b border-stone-100 pb-2.5 pt-0.5">
                        <div>
                            <h3 class="font-serif text-base font-bold text-stone-900">Ringkasan Pesanan</h3>
                            <p class="text-[11px] text-stone-500"><span id="kd-mobile-drawer-count">0</span> kaos dipilih</p>
                        </div>
                        <button type="button" id="kd-mobile-drawer-close-btn" class="grid h-8 w-8 place-items-center rounded-full bg-stone-100 text-stone-500 hover:text-stone-800 transition cursor-pointer" aria-label="Tutup ringkasan">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Header Donasi Saja --}}
                    <div id="kd-drawer-ds-header" class="hidden items-center justify-between border-b border-stone-100 pb-2.5 pt-0.5">
                        <div>
                            <h3 class="font-serif text-base font-bold text-stone-900">Ringkasan</h3>
                            <p class="text-[11px] text-stone-500">Donasi Pembangunan</p>
                        </div>
                        <button type="button" id="kd-mobile-drawer-close-btn-ds" class="grid h-8 w-8 place-items-center rounded-full bg-stone-100 text-stone-500 hover:text-stone-800 transition cursor-pointer" aria-label="Tutup ringkasan">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Content Kaos Donasi --}}
                    <div id="kd-drawer-kaos-content" class="space-y-3">
                        {{-- List Item yang dipilih --}}
                        <div id="kd-mobile-drawer-items" class="space-y-2.5 max-h-[36vh] overflow-y-auto pr-1"></div>

                        {{-- Rincian Biaya --}}
                        <div class="rounded-xl bg-[#f8f6f0] p-3 text-xs space-y-1.5 border border-[#ede8df]">
                            <div class="flex items-center justify-between text-stone-600">
                                <span>Subtotal Kaos</span>
                                <span id="kd-mobile-drawer-subtotal" class="font-semibold text-stone-900">Rp 0</span>
                            </div>
                            <div class="flex items-center justify-between text-stone-600">
                                <span>Donasi</span>
                                <span id="kd-mobile-drawer-donasi" class="font-semibold text-stone-900">Rp 0</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-stone-200/60 pt-1.5 font-bold text-stone-900">
                                <span>Total Pembayaran</span>
                                <span id="kd-mobile-drawer-total" class="text-sm font-extrabold text-primary">Rp 0</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-stone-400 leading-relaxed">
                            Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening &amp; total final setelah pesanan terkirim.
                        </p>
                    </div>

                    {{-- Content Donasi Saja --}}
                    <div id="kd-drawer-ds-content" class="hidden space-y-3">
                        <div class="rounded-xl bg-[#f8f6f0] p-3.5 text-xs space-y-2 border border-[#ede8df]">
                            <div class="flex justify-between text-stone-600">
                                <span>Jenis Dukungan</span>
                                <span class="font-bold text-stone-900">Donasi Saja</span>
                            </div>
                            <div class="flex justify-between text-stone-600">
                                <span>Ketentuan</span>
                                <span class="font-bold text-stone-900">Sukarela</span>
                            </div>
                            <div class="flex justify-between border-t border-stone-200/60 pt-2 font-bold text-stone-900">
                                <span>Nominal Donasi</span>
                                <span id="kd-mobile-drawer-ds-total" class="text-base font-extrabold text-primary">Rp 0</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-stone-400 leading-relaxed">
                            Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening setelah formulir terkirim.
                        </p>
                    </div>
                </div>

                {{-- Bar Bawah Kompak (Selalu Mengambang di Bawah saat ada kaos/donasi) --}}
                <div class="p-3 sm:px-5 flex items-center justify-between gap-3 bg-white border-t border-stone-100 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                    <div class="min-w-0 flex-1 cursor-pointer select-none" id="kd-mobile-bar-summary-trigger">
                        <div class="flex items-center gap-1.5">
                            <span id="kd-mobile-float-badge" class="inline-flex items-center rounded-md bg-secondary/10 px-2 py-0.5 text-[11px] font-bold text-secondary">
                                <span id="kd-mobile-float-count">0</span> kaos
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-stone-600 hover:text-primary transition">
                                <span>Lihat Rincian</span>
                                <svg id="kd-mobile-drawer-chevron" class="h-3.5 w-3.5 transition-transform duration-200 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <p id="kd-mobile-float-total" class="font-serif text-lg font-bold text-stone-900 leading-tight mt-0.5">Rp 0</p>
                    </div>

                    <button type="button" id="kd-mobile-float-next-btn"
                        class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-primary hover:bg-primary-dark px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs transition active:scale-95 cursor-pointer">
                        <span id="kd-mobile-float-next-label">Lanjut</span> &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Panduan Ukuran Kaos --}}
    @include('components.size-chart-modal')
@endsection

@push('scripts')
    <script>
        (function () {
            const rupiah = function (n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); };
            const num = function (n) { return Number(n || 0).toLocaleString('id-ID'); };

            const DESIGN_URL = '{{ route('design') }}';
            const DESIGNS = @json($designData);
            const MIN_DONASI = {{ (int) config('donation.min_kaos_donasi') }};
            const SERVER_ERROR_FIELDS = @json($errors->keys());

            // ===== Mode: kaos_donasi / donasi_saja =====
            const cardKaos = document.getElementById('card-choice-kaos');
            const cardDonasi = document.getElementById('card-choice-donasi');
            const wrapKaos = document.getElementById('wrapper-kaos-donasi');
            const wrapDonasi = document.getElementById('wrapper-donasi-saja');
            const sideKaos = document.getElementById('side-kaos-donasi');
            const sideDonasiSaja = document.getElementById('side-donasi-saja');
            const dsDonasi = document.getElementById('ds-donasi');
            const dsSideTotal = document.getElementById('ds-side-total');
            const dsChips = document.querySelectorAll('.ds-chip');

            // ===== Wizard Elements =====
            const form = document.getElementById('form-kaos-donasi');
            const cardsWrap = document.getElementById('product-cards');
            const detailWrap = document.getElementById('product-detail');
            const othersWrap = document.getElementById('other-products');
            const sideItems = document.getElementById('kd-side-items');
            const sideCount = document.getElementById('kd-side-count');
            const sideSubtotal = document.getElementById('kd-side-subtotal');
            const sideDonasi = document.getElementById('kd-side-donasi');
            const sideTotal = document.getElementById('kd-side-total');
            const sideNext = document.getElementById('side-next-btn');
            const donasiInput = document.getElementById('kd-donasi');
            const hiddenWrap = document.getElementById('kd-cart-hidden');
            const step1Error = document.getElementById('wz-step1-error');
            const indicatorItems = document.querySelectorAll('[data-wz-ind]');
            const sumQty = document.getElementById('sum-qty');
            const sumSubtotal = document.getElementById('sum-subtotal');
            const sumDonasi = document.getElementById('sum-donasi');
            const sumTotal = document.getElementById('sum-total');

            // ===== Mobile Floating Bar & Drawer =====
            const kdMobileFloatingBar = document.getElementById('kd-mobile-floating-bar');
            const kdMobileDrawerBackdrop = document.getElementById('kd-mobile-drawer-backdrop');
            const kdMobileDrawerBody = document.getElementById('kd-mobile-drawer-body');
            const kdMobileDrawerItems = document.getElementById('kd-mobile-drawer-items');
            const kdMobileDrawerCount = document.getElementById('kd-mobile-drawer-count');
            const kdMobileDrawerSubtotal = document.getElementById('kd-mobile-drawer-subtotal');
            const kdMobileDrawerDonasi = document.getElementById('kd-mobile-drawer-donasi');
            const kdMobileDrawerTotal = document.getElementById('kd-mobile-drawer-total');
            const kdMobileFloatBadge = document.getElementById('kd-mobile-float-badge');
            const kdMobileFloatCount = document.getElementById('kd-mobile-float-count');
            const kdMobileFloatTotal = document.getElementById('kd-mobile-float-total');
            const kdMobileFloatNextBtn = document.getElementById('kd-mobile-float-next-btn');
            const kdMobileFloatNextLabel = document.getElementById('kd-mobile-float-next-label');
            const kdMobileDrawerChevron = document.getElementById('kd-mobile-drawer-chevron');
            const kdMobileDrawerToggleBtn = document.getElementById('kd-mobile-drawer-toggle-btn');
            const kdMobileDrawerCloseBtn = document.getElementById('kd-mobile-drawer-close-btn');
            const kdMobileDrawerCloseBtnDs = document.getElementById('kd-mobile-drawer-close-btn-ds');
            const kdMobileBarSummaryTrigger = document.getElementById('kd-mobile-bar-summary-trigger');
            const kdDrawerKaosHeader = document.getElementById('kd-drawer-kaos-header');
            const kdDrawerDsHeader = document.getElementById('kd-drawer-ds-header');
            const kdDrawerKaosContent = document.getElementById('kd-drawer-kaos-content');
            const kdDrawerDsContent = document.getElementById('kd-drawer-ds-content');
            const kdMobileDrawerDsTotal = document.getElementById('kd-mobile-drawer-ds-total');

            let currentMode = '{{ $tab }}'; // kaos_donasi, donasi_saja
            let isKdDrawerOpen = false;

            const designById = function (id) {
                return DESIGNS.find(function (d) { return String(d.id) === String(id); });
            };

            let cart = [];
            let selectedId = null;
            let draft = {};      // { [designId]: { pria: { [ukuran]: jumlah }, wanita: {...} } }
            let genderSel = {};  // { [designId]: 'pria' | 'wanita' }
            let step = 1;

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

            // --- Muat ulang keranjang dari old() setelah validasi gagal ---
            if (hiddenWrap) {
                hiddenWrap.querySelectorAll('[data-old-item]').forEach(function (el) {
                    const d = designById(el.dataset.designId);
                    if (!d) return;
                    const jumlah = Math.max(1, parseInt(el.dataset.jumlah || '1', 10));
                    cart.push({
                        design_id: d.id, nama: d.nama, harga: d.harga, gambar: d.gambar,
                        ukuran: el.dataset.ukuran, gender: (el.dataset.gender === 'wanita') ? 'wanita' : 'pria',
                        jumlah: jumlah, subtotal: d.harga * jumlah,
                    });
                });
                hiddenWrap.innerHTML = '';
            }

            selectedId = cart.length ? cart[0].design_id : (DESIGNS[0] ? DESIGNS[0].id : null);
            if (selectedId !== null) initDraft(selectedId);

            function toggleKdDrawer(open) {
                isKdDrawerOpen = typeof open === 'boolean' ? open : !isKdDrawerOpen;
                if (isKdDrawerOpen) {
                    kdMobileDrawerBody?.classList.remove('hidden');
                    kdMobileDrawerBackdrop?.classList.remove('opacity-0', 'pointer-events-none');
                    kdMobileDrawerBackdrop?.classList.add('opacity-100', 'pointer-events-auto');
                    kdMobileDrawerChevron?.classList.add('rotate-180');
                } else {
                    kdMobileDrawerBody?.classList.add('hidden');
                    kdMobileDrawerBackdrop?.classList.remove('opacity-100', 'pointer-events-auto');
                    kdMobileDrawerBackdrop?.classList.add('opacity-0', 'pointer-events-none');
                    kdMobileDrawerChevron?.classList.remove('rotate-180');
                }
            }

            function setMode(mode) {
                currentMode = mode;
                const isKaosDonasi = (mode === 'kaos_donasi');
                const isDonasiSaja = (mode === 'donasi_saja');

                // Show/hide wrappers
                wrapKaos?.classList.toggle('hidden', !isKaosDonasi);
                wrapDonasi?.classList.toggle('hidden', !isDonasiSaja);
                sideKaos?.classList.toggle('hidden', !isKaosDonasi);
                sideDonasiSaja?.classList.toggle('hidden', !isDonasiSaja);

                const onCls = 'type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer bg-secondary text-white shadow-xs';
                const offCls = 'type-option rounded-full px-4 py-2 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-ink';
                if (cardKaos) cardKaos.className = isKaosDonasi ? onCls : offCls;
                if (cardDonasi) cardDonasi.className = isDonasiSaja ? onCls : offCls;

                const url = new URL(window.location);
                url.searchParams.set('tab', mode);
                window.history.replaceState({}, '', url);

                if (typeof updateKdMobileFloating === 'function') {
                    updateKdMobileFloating();
                }
            }

            cardKaos?.addEventListener('click', () => setMode('kaos_donasi'));
            cardDonasi?.addEventListener('click', () => setMode('donasi_saja'));

            // ===== Donasi Saja =====
            function renderDonasiSaja() {
                const val = parseInt(dsDonasi?.value || '0', 10) || 0;
                if (dsSideTotal) dsSideTotal.textContent = rupiah(val);
                if (typeof updateKdMobileFloating === 'function') {
                    updateKdMobileFloating();
                }
            }
            dsDonasi?.addEventListener('input', renderDonasiSaja);
            dsChips.forEach(chip => chip.addEventListener('click', function () {
                if (dsDonasi) { dsDonasi.value = this.dataset.nominal; renderDonasiSaja(); }
            }));

            function updateKdMobileFloating() {
                if (!kdMobileFloatingBar) return;

                if (currentMode === 'donasi_saja') {
                    const val = parseInt(dsDonasi?.value || '0', 10) || 0;
                    kdDrawerKaosHeader?.classList.add('hidden');
                    kdDrawerDsHeader?.classList.remove('hidden');
                    kdDrawerDsHeader?.classList.add('flex');
                    kdDrawerKaosContent?.classList.add('hidden');
                    kdDrawerDsContent?.classList.remove('hidden');

                    if (kdMobileFloatBadge) {
                        kdMobileFloatBadge.innerHTML = 'Donasi Saja';
                    }
                    if (kdMobileFloatTotal) kdMobileFloatTotal.textContent = rupiah(val);
                    if (kdMobileDrawerDsTotal) kdMobileDrawerDsTotal.textContent = rupiah(val);
                    if (kdMobileFloatNextLabel) kdMobileFloatNextLabel.textContent = 'Kirim Donasi';

                    if (val > 0) {
                        kdMobileFloatingBar.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                        kdMobileFloatingBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    } else {
                        kdMobileFloatingBar.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                        kdMobileFloatingBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                        toggleKdDrawer(false);
                    }
                    return;
                }

                // Mode kaos_donasi
                kdDrawerKaosHeader?.classList.remove('hidden');
                kdDrawerDsHeader?.classList.add('hidden');
                kdDrawerDsHeader?.classList.remove('flex');
                kdDrawerKaosContent?.classList.remove('hidden');
                kdDrawerDsContent?.classList.add('hidden');

                let displayItems = [];
                let totalQty = 0;
                let subtotal = 0;
                const donasi = parseInt((donasiInput && donasiInput.value) || '0', 10) || 0;

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
                    kdMobileFloatingBar.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                    kdMobileFloatingBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                } else {
                    kdMobileFloatingBar.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                    kdMobileFloatingBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    toggleKdDrawer(false);
                }

                if (kdMobileFloatBadge) {
                    kdMobileFloatBadge.innerHTML = '<span id="kd-mobile-float-count">' + totalQty + '</span> kaos';
                }
                const grandTotal = subtotal + donasi;
                if (kdMobileFloatTotal) kdMobileFloatTotal.textContent = rupiah(grandTotal);
                if (kdMobileDrawerCount) kdMobileDrawerCount.textContent = String(totalQty);
                if (kdMobileDrawerSubtotal) kdMobileDrawerSubtotal.textContent = rupiah(subtotal);
                if (kdMobileDrawerDonasi) kdMobileDrawerDonasi.textContent = rupiah(donasi);
                if (kdMobileDrawerTotal) kdMobileDrawerTotal.textContent = rupiah(grandTotal);

                if (kdMobileFloatNextLabel) {
                    if (step === 1) kdMobileFloatNextLabel.textContent = 'Lanjut ke Data Pribadi';
                    else if (step === 2) kdMobileFloatNextLabel.textContent = 'Lanjut ke Donasi';
                    else kdMobileFloatNextLabel.textContent = 'Kirim & Lanjut ke Pembayaran';
                }

                if (kdMobileDrawerItems) {
                    if (!displayItems.length) {
                        kdMobileDrawerItems.innerHTML = '<p class="text-xs text-stone-400 italic py-2">Belum ada kaos dipilih.</p>';
                    } else {
                        const groups = {};
                        displayItems.forEach(function (i) {
                            const gender = i.gender || 'pria';
                            const key = String(i.design_id) + '|' + gender;
                            if (!groups[key]) groups[key] = { id: i.design_id, nama: i.nama, gambar: i.gambar, gender: gender, items: [], subtotal: 0 };
                            groups[key].items.push(i);
                            groups[key].subtotal += i.subtotal;
                        });

                        kdMobileDrawerItems.innerHTML = Object.keys(groups).map(function (key) {
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

            // ============================ RENDER ============================
            function renderAll() {
                renderCards();
                renderDetail();
                renderOthers();
                renderSidebar();
                syncHidden();
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

                const sizesHtml = d.sizes.map(function (s) {
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
                                        '<button type="button" data-open-size-chart-proxy class="text-[11px] font-semibold text-primary hover:underline cursor-pointer">Panduan Ukuran</button>' +
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

            function renderSidebar() {
                const subtotal = cart.reduce(function (a, i) { return a + i.subtotal; }, 0);
                const totalQty = cart.reduce(function (a, i) { return a + i.jumlah; }, 0);
                const donasi = parseInt((donasiInput && donasiInput.value) || '0', 10) || 0;

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

                if (sideCount) sideCount.textContent = String(totalQty);
                if (sideSubtotal) sideSubtotal.textContent = rupiah(subtotal);
                if (sideDonasi) sideDonasi.textContent = rupiah(donasi);
                if (sideTotal) sideTotal.textContent = rupiah(subtotal + donasi);

                if (sumQty) sumQty.textContent = totalQty;
                if (sumSubtotal) sumSubtotal.textContent = rupiah(subtotal);
                if (sumDonasi) sumDonasi.textContent = rupiah(donasi);
                if (sumTotal) sumTotal.textContent = rupiah(subtotal + donasi);

                updateKdMobileFloating();
            }

            function syncHidden() {
                hiddenWrap.innerHTML = cart.map(function (item, idx) {
                    return (
                        '<input type="hidden" name="items[' + idx + '][design_id]" value="' + item.design_id + '">' +
                        '<input type="hidden" name="items[' + idx + '][ukuran]" value="' + item.ukuran + '">' +
                        '<input type="hidden" name="items[' + idx + '][gender]" value="' + (item.gender || 'pria') + '">' +
                        '<input type="hidden" name="items[' + idx + '][jumlah]" value="' + item.jumlah + '">'
                    );
                }).join('');
            }

            function selectDesign(id, gender) {
                selectedId = id;
                if (!draft[id]) initDraft(id);
                if (gender) genderSel[id] = (gender === 'wanita') ? 'wanita' : 'pria';
                else if (!genderSel[id]) genderSel[id] = 'pria';
                renderCards();
                renderDetail();
                renderOthers();
                const detail = document.getElementById('product-detail');
                if (detail && step === 1) detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
                // Sinkronkan hanya entri desain + gender ini
                cart = cart.filter(function (i) {
                    return !(String(i.design_id) === String(selectedId) && (i.gender || 'pria') === gender);
                });
                Object.keys(counts).forEach(function (size) {
                    const n = counts[size];
                    if (n > 0) {
                        cart.push({
                            design_id: d.id, nama: d.nama, harga: d.harga, gambar: d.gambar,
                            ukuran: size, gender: gender, jumlah: n, subtotal: d.harga * n,
                        });
                    }
                });
                renderCards();
                renderSidebar();
                syncHidden();
            }

            // ======================= EVENT DELEGATION =======================
            function handleCardClick(wrap) {
                wrap?.addEventListener('click', function (e) {
                    const genderBtnEl = e.target.closest('[data-card-gender]');
                    if (genderBtnEl) {
                        const host = genderBtnEl.closest('[data-card-design]');
                        if (host) {
                            if (String(selectedId) !== String(host.dataset.cardDesign)) {
                                selectDesign(host.dataset.cardDesign, genderBtnEl.dataset.cardGender);
                            } else {
                                genderSel[selectedId] = genderBtnEl.dataset.cardGender;
                                renderCards();
                                renderDetail();
                                renderOthers();
                            }
                        }
                        return;
                    }
                    const card = e.target.closest('[data-card-design]');
                    if (card) selectDesign(card.dataset.cardDesign);
                });
            }
            handleCardClick(cardsWrap);
            handleCardClick(othersWrap);

            detailWrap?.addEventListener('click', function (e) {
                const genderBtnEl = e.target.closest('[data-detail-gender]');
                if (genderBtnEl && selectedId !== null) {
                    genderSel[selectedId] = genderBtnEl.dataset.detailGender;
                    renderDetail();
                    renderCards();
                    renderOthers();
                    updateKdMobileFloating();
                    return;
                }
                const qtyBtn = e.target.closest('[data-qty-btn]');
                if (qtyBtn && selectedId !== null) {
                    if (!draft[selectedId]) initDraft(selectedId);
                    const gender = genderOf(selectedId);
                    if (!draft[selectedId][gender]) draft[selectedId][gender] = {};
                    const size = qtyBtn.dataset.size;
                    const dir = parseInt(qtyBtn.dataset.dir, 10);
                    draft[selectedId][gender][size] = Math.min(100, Math.max(0, (draft[selectedId][gender][size] || 0) + dir));
                    renderDetail();
                    updateKdMobileFloating();
                    return;
                }
                if (e.target.closest('#btn-add-draft')) { addDraft(); updateKdMobileFloating(); return; }
                if (e.target.closest('[data-open-size-chart-proxy]')) {
                    const trigger = document.getElementById('size-guide-trigger');
                    if (trigger) trigger.click();
                }
            });

            sideItems?.addEventListener('click', function (e) {
                const edit = e.target.closest('[data-side-edit]');
                if (edit) {
                    showStep(1);
                    selectDesign(edit.dataset.sideEdit, edit.dataset.sideGender || null);
                    return;
                }
                const del = e.target.closest('[data-side-remove]');
                if (del) {
                    const parts = String(del.dataset.sideRemove).split('|');
                    const id = parts[0];
                    const gender = parts[1] || 'pria';
                    cart = cart.filter(function (i) {
                        return !(String(i.design_id) === String(id) && (i.gender || 'pria') === gender);
                    });
                    initDraft(id);
                    renderAll();
                    updateKdMobileFloating();
                }
            });

            // ========================= WIZARD NAV =========================
            const stepOfField = function (field) {
                if (field === 'items') return 1;
                if (['nama', 'email', 'nomor', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'kode_pos', 'alamat'].indexOf(field) !== -1) return 2;
                return 3;
            };

            function paintIndicator() {
                indicatorItems.forEach(function (li) {
                    const n = parseInt(li.dataset.wzInd, 10);
                    const numEl = li.querySelector('[data-wz-num]');
                    const labelEl = li.querySelector('[data-wz-label]');
                    if (!numEl) return;
                    let numCls = 'grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold ';
                    let labelCls = 'text-[11px] font-semibold uppercase tracking-wide ';
                    if (n < step) {
                        numCls += 'bg-secondary text-white';
                        labelCls += 'text-secondary';
                    } else if (n === step) {
                        numCls += 'bg-primary text-white';
                        labelCls += 'text-ink font-bold';
                    } else {
                        numCls += 'border border-line bg-white text-stone-400';
                        labelCls += 'text-stone-400';
                    }
                    numEl.className = numCls;
                    if (labelEl) labelEl.className = labelCls;
                });
            }

            function showStep(n) {
                step = Math.min(3, Math.max(1, n));
                [1, 2, 3].forEach(function (i) {
                    const panel = document.getElementById('wz-step-' + i);
                    if (panel) panel.classList.toggle('hidden', i !== step);
                });
                paintIndicator();
                paintSideNext();
                updateKdMobileFloating();
                const url = new URL(window.location);
                url.searchParams.set('langkah', String(step));
                window.history.replaceState({}, '', url);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function paintSideNext() {
                if (!sideNext) return;
                if (step === 1) sideNext.innerHTML = 'Lanjut ke Data Pribadi &rarr;';
                else if (step === 2) {
                    sideNext.innerHTML = 'Lanjut ke Donasi &rarr;';
                } else {
                    sideNext.innerHTML = 'Kirim &amp; Lanjut ke Pembayaran &rarr;';
                }
            }

            function validateStep1() {
                if (cart.length === 0 && selectedId !== null && draftTotal(selectedId) > 0) {
                    addDraft();
                }
                if (cart.length > 0) {
                    step1Error?.classList.add('hidden');
                    return true;
                }
                if (step1Error) {
                    step1Error.classList.remove('hidden');
                    step1Error.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
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
                return true;
            }

            function validateStep3() {
                const fields = document.querySelectorAll('#wz-step-3 input, #wz-step-3 textarea');
                for (let i = 0; i < fields.length; i++) {
                    const el = fields[i];
                    if (el.disabled || el.type === 'hidden') continue;
                    if (!el.checkValidity()) {
                        el.reportValidity();
                        return false;
                    }
                }
                return true;
            }

            function goTo(n) {
                if (n === 2 && !validateStep1()) return;
                if (n === 3 && !validateStep2()) return;
                showStep(n);
            }

            // Tombol lanjut / kembali di dalam wizard
            form.addEventListener('click', function (e) {
                const next = e.target.closest('[data-wz-next]');
                if (next) { goTo(parseInt(next.dataset.wzNext, 10)); return; }
                const back = e.target.closest('[data-wz-back]');
                if (back) showStep(parseInt(back.dataset.wzBack, 10));
            });

            // Tombol lanjut di sidebar
            sideNext?.addEventListener('click', function () {
                if (step === 1) goTo(2);
                else if (step === 2) goTo(3);
                else if (validateStep3()) form.requestSubmit();
            });

            // Mobile drawer event listeners
            kdMobileDrawerToggleBtn?.addEventListener('click', function () {
                toggleKdDrawer();
            });

            kdMobileBarSummaryTrigger?.addEventListener('click', function () {
                toggleKdDrawer();
            });

            kdMobileDrawerCloseBtn?.addEventListener('click', function () {
                toggleKdDrawer(false);
            });

            kdMobileDrawerCloseBtnDs?.addEventListener('click', function () {
                toggleKdDrawer(false);
            });

            kdMobileDrawerBackdrop?.addEventListener('click', function () {
                toggleKdDrawer(false);
            });

            kdMobileDrawerItems?.addEventListener('click', function (e) {
                const edit = e.target.closest('[data-mobile-edit]');
                if (edit) {
                    toggleKdDrawer(false);
                    showStep(1);
                    selectDesign(edit.dataset.mobileEdit, edit.dataset.mobileGender || null);
                    return;
                }
                const del = e.target.closest('[data-mobile-remove]');
                if (del) {
                    const parts = String(del.dataset.mobileRemove).split('|');
                    const id = parts[0];
                    const gender = parts[1] || 'pria';
                    cart = cart.filter(function (i) {
                        return !(String(i.design_id) === String(id) && (i.gender || 'pria') === gender);
                    });
                    initDraft(id);
                    renderAll();
                    updateKdMobileFloating();
                }
            });

            kdMobileFloatNextBtn?.addEventListener('click', function () {
                if (currentMode === 'donasi_saja') {
                    document.getElementById('form-donasi-saja')?.requestSubmit();
                    return;
                }
                if (step === 1) {
                    if (cart.length === 0 && selectedId !== null && draftTotal(selectedId) > 0) {
                        addDraft();
                    }
                    if (validateStep1()) {
                        toggleKdDrawer(false);
                        goTo(2);
                    }
                } else if (step === 2) {
                    if (validateStep2()) {
                        toggleKdDrawer(false);
                        goTo(3);
                    }
                } else {
                    if (validateStep3()) {
                        form.requestSubmit();
                    }
                }
            });

            // Klik indikator langkah (mundur bebas, maju harus lolos validasi)
            indicatorItems.forEach(function (li) {
                li.addEventListener('click', function () {
                    const n = parseInt(li.dataset.wzInd, 10);
                    if (n === step || n > 3) return;
                    if (n < step) { showStep(n); return; }
                    goTo(n);
                });
            });

            // Submit: validasi seluruh langkah sebelum kirim ke server
            form.addEventListener('submit', function (e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    showStep(1);
                    validateStep1();
                    return;
                }
                if (!validateStep2()) {
                    e.preventDefault();
                    showStep(2);
                    return;
                }
                if (!validateStep3()) e.preventDefault();
            });

            // =========================== DONASI ===========================
            donasiInput?.addEventListener('input', renderSidebar);
            document.querySelectorAll('.kd-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    if (donasiInput) {
                        donasiInput.value = chip.dataset.nominal;
                        renderSidebar();
                    }
                });
            });

            // ============================ INIT ============================
            setMode('{{ $tab }}');
            renderDonasiSaja();
            if (form && DESIGNS.length) {
                renderAll();
                // Langkah awal: dari URL (?langkah=), atau lompat ke langkah yang bermasalah saat validasi server gagal
                let initialStep = parseInt(new URLSearchParams(window.location.search).get('langkah') || '1', 10);
                if (SERVER_ERROR_FIELDS.length) {
                    initialStep = stepOfField(String(SERVER_ERROR_FIELDS[0]).split('.')[0]);
                }
                showStep(isNaN(initialStep) ? 1 : initialStep);
            }
            updateKdMobileFloating();
        })();

        // Integrasi RajaOngkir pada wizard Shop (Langkah 2 — Alamat Pengiriman)
        (function () {
            var provSelect = document.getElementById('kd-provinsi');
            var kabSelect = document.getElementById('kd-kabupaten');
            var kecSelect = document.getElementById('kd-kecamatan');
            var kelSelect = document.getElementById('kd-kelurahan');
            var posInput = document.getElementById('kd-kode-pos');
            var oldKab = document.getElementById('kd-old-kabupaten') ? document.getElementById('kd-old-kabupaten').value : '';
            var oldKec = document.getElementById('kd-old-kecamatan') ? document.getElementById('kd-old-kecamatan').value : '';
            var oldKel = document.getElementById('kd-old-kelurahan') ? document.getElementById('kd-old-kelurahan').value : '';
            var loadKab = document.getElementById('kd-loading-kabupaten');
            var loadKec = document.getElementById('kd-loading-kecamatan');
            var loadKel = document.getElementById('kd-loading-kelurahan');
            var badgePos = document.getElementById('kd-badge-kodepos');

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
