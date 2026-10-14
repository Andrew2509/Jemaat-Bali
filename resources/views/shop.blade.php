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
                        <p class="text-xs text-stone-500 mt-0.5">Minimal Rp200.000 &bull; Lengkapi data pengiriman</p>
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
                                    <label for="kd-provinsi" class="label">Provinsi <span class="text-error">*</span></label>
                                    <select id="kd-provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                        <option value="">Pilih provinsi &blacktriangledown;</option>
                                        @foreach ($provinsi as $p)
                                            <option value="{{ $p }}" @selected(old('provinsi') === $p)>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                    @error('provinsi')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-kabupaten" class="label">Kabupaten / Kota <span class="text-error">*</span></label>
                                    <input type="text" id="kd-kabupaten" name="kabupaten" value="{{ old('kabupaten') }}" placeholder="Pilih kabupaten / kota"
                                        class="input @error('kabupaten') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="kd-kecamatan" class="label">Kecamatan <span class="text-error">*</span></label>
                                    <input type="text" id="kd-kecamatan" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Pilih kecamatan"
                                        class="input @error('kecamatan') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-kode-pos" class="label">Kode Pos <span class="text-error">*</span></label>
                                    <input type="text" id="kd-kode-pos" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="Masukkan kode pos"
                                        class="input @error('kode_pos') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                    @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="kd-alamat" class="label">Alamat Lengkap <span class="text-error">*</span></label>
                                <textarea id="kd-alamat" name="alamat" rows="2" placeholder="Jalan, nomor rumah, RT/RW, kelurahan"
                                    class="input @error('alamat') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>{{ old('alamat') }}</textarea>
                                @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
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
                                                @selected(old('design_id', $selectedDesign->id ?? null) == $d->id)>
                                                {{ $d->nama }} &mdash; Rp {{ number_format($d->harga, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('design_id')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="kd-ukuran" class="label">Ukuran Kaos <span class="text-error">*</span></label>
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
                                Pesan Kaos + Donasi: nominal minimal Rp200.000.
                            </div>

                            <div class="mt-4">
                                <label for="kd-donasi" class="label">Nominal Donasi (Rp) <span class="text-error">*</span></label>
                                <input type="number" id="kd-donasi" name="donasi" min="200000" step="10000"
                                    value="{{ old('donasi') }}" placeholder="Minimal 200000"
                                    class="input @error('donasi') input-error @enderror" {{ $tab === 'kaos_donasi' ? 'required' : '' }}>
                                @error('donasi')<p class="error-text">{{ $message }}</p>@enderror
                            </div>

                            {{-- Chips Kaos + Donasi: 200.000, 250.000, 500.000, 1.000.000 --}}
                            <div class="mt-3 flex flex-wrap gap-2.5">
                                @foreach ([200000, 250000, 500000, 1000000] as $chip)
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
            <aside class="lg:col-span-4">
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
                                <dd id="kd-side-min-donasi" class="font-bold text-stone-900 text-right">Rp200.000</dd>
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
                    kdSideMinDonasi.textContent = donasi > 0 ? 'Rp' + rupiah(donasi) : 'Rp200.000';
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
    </script>
@endpush
