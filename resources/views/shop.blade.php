@extends('layouts.app')

@section('title', 'Shop + Donasi — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pilih jenis dukungan Anda untuk pembangunan ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': pesan kaos + donasi atau donasi saja.')

@section('content')
    {{-- ================= HEADER ================= --}}
    <section class="border-b border-line bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <p class="text-xs font-bold uppercase tracking-wider text-[#b05525]">FORMULIR</p>
            <h1 class="mt-2 font-serif text-3xl font-extrabold text-stone-900 sm:text-4xl">Shop + Donasi</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">
                Pilih jenis dukungan Anda, lalu lengkapi formulir di bawah ini. Panitia akan menghubungi Anda untuk konfirmasi.
            </p>
        </div>
    </section>

    {{-- ================= KONTEN FORMULIR ================= --}}
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        @if ($errors->any())
            <div class="mb-8 rounded-2xl border border-error/30 bg-error/5 p-5">
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

        {{-- PILIH JENIS DUKUNGAN --}}
        <div class="mb-8">
            <h2 class="font-serif text-lg font-bold text-stone-900 mb-3">Pilih jenis dukungan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-3xl">
                {{-- Opsi 1: Pesan Kaos + Donasi --}}
                <button type="button" id="card-choice-kaos"
                    class="choice-card group flex items-start gap-3.5 rounded-xl border p-4 text-left transition-all cursor-pointer {{ $tab === 'kaos_donasi' ? 'border-[#b05525] bg-white ring-1 ring-[#b05525]' : 'border-stone-200 bg-white hover:border-stone-300' }}">
                    <div class="radio-indicator mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full {{ $tab === 'kaos_donasi' ? 'bg-[#b05525]' : 'border-2 border-stone-300 bg-white' }}"></div>
                    <div>
                        <h3 class="font-bold text-sm text-stone-900">Pesan Kaos + Donasi</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }} &bull; Lengkapi data pengiriman</p>
                    </div>
                </button>

                {{-- Opsi 2: Donasi Saja --}}
                <button type="button" id="card-choice-donasi"
                    class="choice-card group flex items-start gap-3.5 rounded-xl border p-4 text-left transition-all cursor-pointer {{ $tab === 'donasi_saja' ? 'border-[#b05525] bg-white ring-1 ring-[#b05525]' : 'border-stone-200 bg-white hover:border-stone-300' }}">
                    <div class="radio-indicator mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full {{ $tab === 'donasi_saja' ? 'bg-[#b05525]' : 'border-2 border-stone-300 bg-white' }}"></div>
                    <div>
                        <h3 class="font-bold text-sm text-stone-900">Donasi Saja</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Sukarela &bull; Tanpa minimal donasi</p>
                    </div>
                </button>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-12 items-start">
            {{-- ========================================================================= --}}
            {{-- KOLOM KIRI: FORMULIR --}}
            {{-- ========================================================================= --}}
            <div class="lg:col-span-8">
                {{-- ----------------------------------------------------------------- --}}
                {{-- 1. FORM DONASI SAJA (Persis Screenshot 1) --}}
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

                            {{-- Chips Donasi Saja: 50.000, 100.000, 250.000, 500.000 --}}
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
                {{-- 2. FORM PESAN KAOS + DONASI (Persis Screenshot 2) --}}
                {{-- ----------------------------------------------------------------- --}}
                <div id="wrapper-kaos-donasi" class="{{ $tab === 'kaos_donasi' ? '' : 'hidden' }}">
                    <form action="{{ route('shop.store') }}" method="POST" id="form-kaos-donasi" class="card p-6 sm:p-8 bg-white border border-stone-200 rounded-2xl space-y-6">
                        @csrf
                        <input type="hidden" name="jenis" value="kaos_donasi">

                        {{-- Data Pribadi --}}
                        <div>
                            <h2 class="font-serif text-lg font-bold text-stone-900">Data Pribadi</h2>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="kd-nama" class="label">Nama <span class="text-error">*</span></label>
                                    <input type="text" id="kd-nama" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap"
                                        class="input @error('nama') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('nama')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-email" class="label">Email <span class="text-error">*</span></label>
                                    <input type="email" id="kd-email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                                        class="input @error('email') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('email')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="kd-nomor" class="label">Nomor Telepon / WhatsApp <span class="text-error">*</span></label>
                                <input type="tel" id="kd-nomor" name="nomor" value="{{ old('nomor') }}" placeholder="Contoh: 08123456789"
                                    class="input @error('nomor') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                @error('nomor')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Alamat Pengiriman --}}
                        <div class="pt-2">
                            <h2 class="font-serif text-lg font-bold text-stone-900">Alamat Pengiriman</h2>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kd-provinsi" class="label">Provinsi <span class="text-error">*</span></label>
                                        <span id="kd-loading-provinsi" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat...
                                        </span>
                                    </div>
                                    <select id="kd-provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                        <option value="">Pilih provinsi &blacktriangledown;</option>
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
                                    <select id="kd-kabupaten" name="kabupaten" class="input @error('kabupaten') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }} disabled>
                                        <option value="">Pilih kabupaten / kota</option>
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
                                    <select id="kd-kecamatan" name="kecamatan" class="input @error('kecamatan') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }} disabled>
                                        <option value="">Pilih kecamatan</option>
                                    </select>
                                    <input type="hidden" id="kd-old-kecamatan" value="{{ old('kecamatan') }}">
                                    @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="kd-kelurahan" class="label">Kelurahan / Desa <span class="text-error">*</span></label>
                                        <span id="kd-loading-kelurahan" class="hidden text-[11px] text-[#b05525] font-medium flex items-center gap-1">
                                            <svg class="animate-spin h-3 w-3 text-[#b05525]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Memuat kelurahan...
                                        </span>
                                    </div>
                                    <select id="kd-kelurahan" name="kelurahan" class="input @error('kelurahan') input-error @enderror" disabled>
                                        <option value="">Pilih kelurahan / desa</option>
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
                                        class="input @error('kode_pos') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    <p class="mt-1 text-[11px] text-stone-500">Terisi otomatis saat memilih kelurahan / kecamatan.</p>
                                    @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-alamat" class="label">Alamat Lengkap <span class="text-error">*</span></label>
                                    <textarea id="kd-alamat" name="alamat" rows="2" placeholder="Jalan, nomor rumah, RT/RW, patokan"
                                        class="input @error('alamat') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>{{ old('alamat') }}</textarea>
                                    @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Pesanan Kaos --}}
                        <div class="pt-2">
                            <div class="flex items-center justify-between">
                                <h2 class="font-serif text-lg font-bold text-stone-900">Pesanan Kaos</h2>
                                <a href="{{ route('design') }}" class="text-xs font-semibold text-[#b05525] hover:underline">
                                    Lihat desain &rarr;
                                </a>
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="kd-desain" class="label">Desain Kaos <span class="text-error">*</span></label>
                                    <select id="kd-desain" name="design_id" class="input @error('design_id') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                        <option value="" data-harga="0" data-nama="Belum dipilih">Pilih desain &blacktriangledown;</option>
                                        @foreach ($designs as $d)
                                            <option value="{{ $d->id }}" data-harga="{{ $d->harga }}" data-nama="{{ $d->nama }}"
                                                data-sizes="{{ json_encode($d->pilihanUkuran()) }}"
                                                @selected(old('design_id', $selectedDesign->id ?? null) == $d->id)>
                                                {{ $d->nama }} &mdash; Rp {{ number_format($d->harga, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('design_id')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="kd-ukuran" class="label !mb-0">Ukuran Kaos <span class="text-error">*</span></label>
                                        <button type="button" data-open-size-chart class="text-[11px] font-bold text-[#b05525] hover:underline cursor-pointer inline-flex items-center gap-1">
                                            <span>📏 Panduan Ukuran</span>
                                        </button>
                                    </div>
                                    <select id="kd-ukuran" name="ukuran" class="input @error('ukuran') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                        <option value="">Pilih ukuran &blacktriangledown;</option>
                                        @foreach ($ukuran as $u)
                                            <option value="{{ $u }}" @selected(old('ukuran', request('ukuran', 'L')) === $u)>{{ $u }}</option>
                                        @endforeach
                                    </select>
                                    @error('ukuran')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="kd-jumlah" class="label">Jumlah Kaos <span class="text-error">*</span></label>
                                    <input type="number" id="kd-jumlah" name="jumlah" min="1" max="100" value="{{ old('jumlah', request('jumlah', 1)) }}"
                                        class="input @error('jumlah') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('jumlah')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-subtotal-display" class="label">Subtotal Kaos</label>
                                    <input type="text" id="kd-subtotal-display" readonly value="Rp 0"
                                        class="input bg-stone-50 text-stone-700 font-semibold cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        {{-- Donasi --}}
                        <div class="pt-2">
                            <h2 class="font-serif text-lg font-bold text-stone-900">Donasi</h2>
                            <div class="mt-3 rounded-xl bg-[#fff7ed] p-3 text-xs font-medium text-[#c2410c]">
                                Pesan Kaos + Donasi: nominal minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}.
                            </div>

                            <div class="mt-4">
                                <label for="kd-donasi" class="label">Nominal Donasi (Rp) <span class="text-error">*</span></label>
                                <input type="number" id="kd-donasi" name="donasi" min="{{ config('donation.min_kaos_donasi') }}" step="10000"
                                    value="{{ old('donasi') }}" placeholder="Minimal {{ config('donation.min_kaos_donasi') }}"
                                    class="input @error('donasi') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                @error('donasi')<p class="error-text">{{ $message }}</p>@enderror
                            </div>

                            {{-- Chips Kaos + Donasi --}}
                            <div class="mt-3 flex flex-wrap gap-2.5">
                                @foreach (config('donation.preset_kaos_donasi') as $chip)
                                    <button type="button" data-nominal="{{ $chip }}"
                                        class="kd-chip rounded-full border border-stone-200 bg-white px-5 py-2 text-xs font-semibold text-stone-700 transition hover:border-[#b05525] hover:text-[#b05525] cursor-pointer">
                                        Rp {{ number_format($chip, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <label for="kd-catatan" class="label">Catatan &mdash; opsional</label>
                                <textarea id="kd-catatan" name="catatan" rows="3" placeholder="Catatan pengiriman atau pesan untuk panitia"
                                    class="input @error('catatan') input-error @enderror">{{ old('catatan') }}</textarea>
                                @error('catatan')<p class="error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Footer Form --}}
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <p class="text-xs text-stone-400">
                                Data Anda digunakan panitia untuk konfirmasi pesanan dan donasi.
                            </p>
                            <button type="submit" class="rounded-xl bg-[#b05525] hover:bg-[#964319] text-white px-7 py-3 text-sm font-bold shadow-xs transition cursor-pointer">
                                Kirim Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- KOLOM KANAN: SIDEBAR RINGKASAN (Persis Screenshot 1 & 2) --}}
            {{-- ========================================================================= --}}
            <aside class="lg:col-span-4 order-first lg:order-none">
                <div class="card p-6 bg-white border border-stone-200 rounded-2xl sticky top-24">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan</h2>

                    {{-- Ringkasan Mode 1: Donasi Saja (Screenshot 1) --}}
                    <div id="side-donasi-saja" class="{{ $tab === 'donasi_saja' ? '' : 'hidden' }}">
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

                    {{-- Ringkasan Mode 2: Kaos + Donasi (Screenshot 2) --}}
                    <div id="side-kaos-donasi" class="{{ $tab === 'kaos_donasi' ? '' : 'hidden' }}">
                        <dl class="mt-4 space-y-3 text-xs sm:text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Jenis</dt>
                                <dd class="font-bold text-stone-900 text-right">Kaos + Donasi</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Desain</dt>
                                <dd id="kd-side-desain" class="font-bold text-stone-900 text-right">Belum dipilih</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Ukuran / Jumlah</dt>
                                <dd id="kd-side-ukuran-jumlah" class="font-bold text-stone-900 text-right">&mdash; / 1</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Subtotal Kaos</dt>
                                <dd id="kd-side-subtotal" class="font-bold text-stone-900 text-right">Rp 0</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Minimal Donasi</dt>
                                <dd id="kd-side-min-donasi" class="font-bold text-stone-900 text-right">Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <div class="border-t border-stone-200 my-4 pt-4 flex items-center justify-between">
                            <span class="font-bold text-stone-900 text-sm">Total</span>
                            <span id="kd-side-total" class="font-serif text-2xl font-bold text-[#b05525]">Rp 0</span>
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
    </section>

    {{-- Modal Panduan Ukuran Kaos --}}
    @include('components.size-chart-modal')
@endsection

@push('scripts')
    <script>
        (function () {
            // Choice cards
            const cardKaos = document.getElementById('card-choice-kaos');
            const cardDonasi = document.getElementById('card-choice-donasi');

            // Form wrappers
            const wrapKaos = document.getElementById('wrapper-kaos-donasi');
            const wrapDonasi = document.getElementById('wrapper-donasi-saja');

            // Sidebars
            const sideKaos = document.getElementById('side-kaos-donasi');
            const sideDonasi = document.getElementById('side-donasi-saja');

            // Donasi Saja inputs
            const dsDonasi = document.getElementById('ds-donasi');
            const dsSideTotal = document.getElementById('ds-side-total');
            const dsChips = document.querySelectorAll('.ds-chip');

            // Kaos + Donasi inputs
            const kdDesain = document.getElementById('kd-desain');
            const kdUkuran = document.getElementById('kd-ukuran');
            const kdJumlah = document.getElementById('kd-jumlah');
            const kdSubtotalDisplay = document.getElementById('kd-subtotal-display');
            const kdDonasi = document.getElementById('kd-donasi');
            const kdChips = document.querySelectorAll('.kd-chip');

            // Kaos + Donasi sidebar elements
            const kdSideDesain = document.getElementById('kd-side-desain');
            const kdSideUkuranJumlah = document.getElementById('kd-side-ukuran-jumlah');
            const kdSideSubtotal = document.getElementById('kd-side-subtotal');
            const kdSideMinDonasi = document.getElementById('kd-side-min-donasi');
            const kdSideTotal = document.getElementById('kd-side-total');

            function rupiah(n) {
                return Number(n || 0).toLocaleString('id-ID');
            }

            // Tab / Option Switcher
            function setMode(mode) {
                const isKaos = (mode === 'kaos_donasi');

                // Toggle visibility
                wrapKaos?.classList.toggle('hidden', !isKaos);
                wrapDonasi?.classList.toggle('hidden', isKaos);
                sideKaos?.classList.toggle('hidden', !isKaos);
                sideDonasi?.classList.toggle('hidden', isKaos);

                // Required attributes management so invisible form doesn't block submission
                wrapKaos?.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.hasAttribute('data-req-orig') || el.required) {
                        el.setAttribute('data-req-orig', '1');
                        el.required = isKaos;
                    }
                });
                wrapDonasi?.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.hasAttribute('data-req-orig') || el.required) {
                        el.setAttribute('data-req-orig', '1');
                        el.required = !isKaos;
                    }
                });

                // Update card styles & radio indicators
                if (cardKaos) {
                    cardKaos.className = 'choice-card group flex items-start gap-3.5 rounded-xl border p-4 text-left transition-all cursor-pointer ' +
                        (isKaos ? 'border-[#b05525] bg-white ring-1 ring-[#b05525]' : 'border-stone-200 bg-white hover:border-stone-300');
                    const rad = cardKaos.querySelector('.radio-indicator');
                    if (rad) rad.className = 'radio-indicator mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full ' +
                        (isKaos ? 'bg-[#b05525]' : 'border-2 border-stone-300 bg-white');
                }

                if (cardDonasi) {
                    cardDonasi.className = 'choice-card group flex items-start gap-3.5 rounded-xl border p-4 text-left transition-all cursor-pointer ' +
                        (!isKaos ? 'border-[#b05525] bg-white ring-1 ring-[#b05525]' : 'border-stone-200 bg-white hover:border-stone-300');
                    const rad = cardDonasi.querySelector('.radio-indicator');
                    if (rad) rad.className = 'radio-indicator mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full ' +
                        (!isKaos ? 'bg-[#b05525]' : 'border-2 border-stone-300 bg-white');
                }

                // Update URL parameter
                const url = new URL(window.location);
                url.searchParams.set('tab', mode);
                window.history.replaceState({}, '', url);
            }

            cardKaos?.addEventListener('click', () => setMode('kaos_donasi'));
            cardDonasi?.addEventListener('click', () => setMode('donasi_saja'));

            // Calc Donasi Saja
            function renderDonasiSaja() {
                const val = parseInt(dsDonasi?.value || '0', 10) || 0;
                if (dsSideTotal) {
                    dsSideTotal.textContent = val > 0 ? 'Rp ' + rupiah(val) : 'Rp 0';
                }
            }

            dsDonasi?.addEventListener('input', renderDonasiSaja);
            dsChips.forEach(chip => {
                chip.addEventListener('click', function () {
                    if (dsDonasi) {
                        dsDonasi.value = this.dataset.nominal;
                        renderDonasiSaja();
                    }
                });
            });

            // Calc Kaos + Donasi
            function renderKaosDonasi() {
                const selOpt = kdDesain?.options[kdDesain.selectedIndex];
                const harga = parseInt(selOpt?.dataset.harga || '0', 10) || 0;
                const nama = selOpt?.dataset.nama || 'Belum dipilih';

                // Sync ukuran availability based on chosen design
                if (selOpt && selOpt.dataset.sizes && kdUkuran) {
                    try {
                        const avail = JSON.parse(selOpt.dataset.sizes);
                        Array.from(kdUkuran.options).forEach(opt => {
                            if (!opt.value) return;
                            const isAv = avail.includes(opt.value);
                            opt.disabled = !isAv;
                            opt.textContent = isAv ? opt.value : opt.value + ' (Habis)';
                        });
                        if (kdUkuran.value && !avail.includes(kdUkuran.value)) {
                            kdUkuran.value = avail[0] || '';
                        }
                    } catch (e) {}
                }

                const ukuran = kdUkuran?.value || '—';
                const qty = Math.max(1, parseInt(kdJumlah?.value || '1', 10) || 1);
                const subtotal = harga * qty;
                const donasi = parseInt(kdDonasi?.value || '0', 10) || 0;
                const total = subtotal + donasi;

                if (kdSubtotalDisplay) {
                    kdSubtotalDisplay.value = 'Rp ' + rupiah(subtotal);
                }

                if (kdSideDesain) kdSideDesain.textContent = nama;
                if (kdSideUkuranJumlah) kdSideUkuranJumlah.textContent = ukuran + ' / ' + qty;
                if (kdSideSubtotal) kdSideSubtotal.textContent = 'Rp ' + rupiah(subtotal);
                if (kdSideMinDonasi) {
                    kdSideMinDonasi.textContent = donasi > 0 ? 'Rp' + rupiah(donasi) : 'Rp' + rupiah(@js(config('donation.min_kaos_donasi')));
                }
                if (kdSideTotal) {
                    kdSideTotal.textContent = total > 0 ? 'Rp ' + rupiah(total) : 'Rp 0';
                }
            }

            kdDesain?.addEventListener('change', renderKaosDonasi);
            kdUkuran?.addEventListener('change', renderKaosDonasi);
            kdJumlah?.addEventListener('input', renderKaosDonasi);
            kdDonasi?.addEventListener('input', renderKaosDonasi);

            kdChips.forEach(chip => {
                chip.addEventListener('click', function () {
                    if (kdDonasi) {
                        kdDonasi.value = this.dataset.nominal;
                        renderKaosDonasi();
                    }
                });
            });

            // Initial calls
            setMode('{{ $tab }}');
            renderDonasiSaja();
            renderKaosDonasi();
        })();

        // Integrasi RajaOngkir pada form Shop (Pesan Kaos + Donasi)
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
                    kabSelect.innerHTML = '<option value="">Pilih kabupaten / kota</option>';
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
                    kecSelect.innerHTML = '<option value="">Pilih kecamatan</option>';
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
                    kelSelect.innerHTML = '<option value="">Pilih kelurahan / desa</option>';
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
                    kabSelect.innerHTML = '<option value="">Pilih kabupaten / kota</option>';
                    kecSelect.disabled = true;
                    kecSelect.innerHTML = '<option value="">Pilih kecamatan</option>';
                    if (kelSelect) {
                        kelSelect.disabled = true;
                        kelSelect.innerHTML = '<option value="">Pilih kelurahan / desa</option>';
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
                    kecSelect.innerHTML = '<option value="">Pilih kecamatan</option>';
                    if (kelSelect) {
                        kelSelect.disabled = true;
                        kelSelect.innerHTML = '<option value="">Pilih kelurahan / desa</option>';
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
                        kelSelect.innerHTML = '<option value="">Pilih kelurahan / desa</option>';
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
