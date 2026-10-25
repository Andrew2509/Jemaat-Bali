@extends('layouts.app')

@section('title', 'Informasi Pengiriman — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <a href="{{ route('checkout.pemesanan', ['desain' => $selected->id]) }}" class="text-sm font-semibold text-muted hover:text-primary">&larr; Kembali ke data pemesan</a>
    <div class="mt-6 flex gap-3 text-xs font-bold uppercase tracking-widest"><span class="text-muted">01 Data pemesan</span><span class="text-primary">— 02 Pengiriman</span><span class="text-muted">— 03 Pembayaran</span></div>
    <h1 class="mt-4 font-serif text-3xl font-extrabold">Informasi Pengiriman</h1>
    <p class="mt-2 text-muted">Langkah 2 dari 3 · Pastikan alamat tujuan lengkap dan benar.</p>
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <form action="{{ route('checkout.pengiriman.store') }}" method="POST" class="card space-y-5 p-6 sm:p-8 lg:col-span-2">
            @csrf
            @if ($errors->any())<div class="rounded-xl bg-error/5 p-4 text-sm text-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <input type="hidden" name="_identity" value="">
            <div><label for="alamat" class="label">Alamat lengkap *</label><textarea id="alamat" name="alamat" rows="3" class="input" placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan" required>{{ old('alamat', $draft['alamat'] ?? '') }}</textarea>@error('alamat')<p class="error-text">{{ $message }}</p>@enderror</div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label for="provinsi" class="label">Provinsi *</label><select id="provinsi" name="provinsi" class="input" required><option value="">— Pilih Provinsi —</option>@foreach ($provinsi as $p) @php($pName = is_array($p) ? ($p['name'] ?? '') : $p)<option value="{{ $pName }}" @selected(strtoupper(old('provinsi', $draft['provinsi'] ?? '')) === strtoupper($pName))>{{ $pName }}</option>@endforeach</select>@error('provinsi')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kabupaten" class="label">Kota / Kabupaten *</label><input id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $draft['kabupaten'] ?? '') }}" class="input" required>@error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kecamatan" class="label">Kecamatan *</label><input id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $draft['kecamatan'] ?? '') }}" class="input" required>@error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kelurahan" class="label">Kelurahan / Desa</label><input id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $draft['kelurahan'] ?? '') }}" class="input">@error('kelurahan')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kode_pos" class="label">Kode pos *</label><input id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $draft['kode_pos'] ?? '') }}" class="input" inputmode="numeric" required>@error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="ukuran" class="label">Ukuran kaos *</label><div class="flex items-center justify-between"><span></span><button type="button" data-open-size-chart class="text-xs font-bold text-primary hover:underline">Panduan ukuran</button></div><select id="ukuran" name="ukuran" class="input" required><option value="">— Pilih ukuran —</option>@foreach ($ukuran as $u) @php($available = $selected->isUkuranTersedia($u))<option value="{{ $u }}" @disabled(!$available) @selected(old('ukuran', $draft['ukuran'] ?? '') === $u)>{{ $u }}{{ $available ? '' : ' (Habis)' }}</option>@endforeach</select>@error('ukuran')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="jumlah" class="label">Jumlah *</label><input id="jumlah" name="jumlah" type="number" min="1" max="100" value="{{ old('jumlah', $draft['jumlah'] ?? 1) }}" class="input" required>@error('jumlah')<p class="error-text">{{ $message }}</p>@enderror</div>
            </div>
            <div><label for="catatan" class="label">Catatan <span class="font-normal text-muted">(opsional)</span></label><input id="catatan" name="catatan" value="{{ old('catatan', $draft['catatan'] ?? '') }}" class="input" maxlength="500"></div>
            @if (($tipe ?? 'kaos_saja') === 'kaos_donasi')
                <div><label for="donasi" class="label">Donasi sukarela (Rp)</label><input id="donasi" name="donasi" type="number" min="0" max="100000000" step="5000" value="{{ old('donasi', $draft['donasi'] ?? 50000) }}" class="input"></div>
            @endif
            <div class="rounded-xl bg-primary/5 p-4 text-sm text-ink"><strong>Ongkir belum dihitung.</strong> Panitia akan mengonfirmasi ongkir dan total melalui WhatsApp.</div>
            <button class="btn btn-primary w-full !py-3.5" type="submit">Simpan alamat &amp; lanjut ke pembayaran &rarr;</button>
        </form>
        <aside class="card overflow-hidden p-0"><img src="{{ asset($selected->gambar) }}" alt="{{ $selected->nama }}" class="aspect-[4/3] w-full object-cover"><div class="p-5"><h2 class="font-serif text-lg font-bold">{{ $selected->nama }}</h2><p class="mt-2 text-sm text-muted">{{ $draft['nama'] }}</p><p class="mt-2 font-bold">Rp {{ number_format($selected->harga, 0, ',', '.') }} / kaos</p></div></aside>
    </div>
</section>
@include('components.size-chart-modal')
@endsection
