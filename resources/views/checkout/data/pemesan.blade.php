@extends('layouts.app')

@section('title', 'Data Pemesan — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <a href="{{ route('design') }}" class="text-sm font-semibold text-muted hover:text-primary">&larr; Kembali ke koleksi</a>
    <div class="mt-6 flex gap-3 text-xs font-bold uppercase tracking-widest"><span class="text-primary">01 Data pemesan</span><span class="text-muted">— 02 Pengiriman — 03 Pembayaran</span></div>
    <h1 class="mt-4 font-serif text-3xl font-extrabold">Beli Kaos Saja</h1>
    <p class="mt-2 text-muted">Langkah 1 dari 3 · Isi data pemesan.</p>
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <form action="{{ route('checkout.data.store') }}" method="POST" class="card space-y-5 p-6 sm:p-8 lg:col-span-2">
            @csrf
            @if ($errors->any())<div class="rounded-xl bg-error/5 p-4 text-sm text-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <div><label for="nama" class="label">Nama lengkap *</label><input id="nama" name="nama" value="{{ old('nama', $draft['nama'] ?? '') }}" class="input" autocomplete="name" required>@error('nama')<p class="error-text">{{ $message }}</p>@enderror</div>
            <div><label for="nomor" class="label">Nomor WhatsApp *</label><input id="nomor" name="nomor" type="tel" value="{{ old('nomor', $draft['nomor'] ?? '') }}" class="input" autocomplete="tel" required>@error('nomor')<p class="error-text">{{ $message }}</p>@enderror</div>
            <div><label for="email" class="label">Email <span class="font-normal text-muted">(opsional)</span></label><input id="email" name="email" type="email" value="{{ old('email', $draft['email'] ?? '') }}" class="input" autocomplete="email">@error('email')<p class="error-text">{{ $message }}</p>@enderror</div>
            <input type="hidden" name="design_id" value="{{ $selected->id }}">
            <input type="hidden" name="tipe" value="{{ $draft['tipe'] ?? 'kaos_saja' }}">
            <input type="hidden" name="ukuran" value="{{ old('ukuran', $draft['ukuran'] ?? request('ukuran')) }}">
            <input type="hidden" name="jumlah" value="{{ old('jumlah', $draft['jumlah'] ?? request('jumlah', 1)) }}">
            <button class="btn btn-primary w-full !py-3.5" type="submit">Lanjut ke pengiriman &rarr;</button>
        </form>
        <aside class="card overflow-hidden p-0"><img src="{{ asset($selected->gambar) }}" alt="{{ $selected->nama }}" class="aspect-[4/3] w-full object-cover"><div class="p-5"><h2 class="font-serif text-lg font-bold">{{ $selected->nama }}</h2><p class="mt-2 font-bold">Rp {{ number_format($selected->harga, 0, ',', '.') }} / kaos</p></div></aside>
    </div>
</section>
@endsection
