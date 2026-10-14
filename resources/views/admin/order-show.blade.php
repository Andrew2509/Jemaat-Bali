@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('breadcrumb', 'Pesanan Kaos')

@section('content')
    @php
        $bayarLabel = match ($order->status_pembayaran) {
            'terverifikasi' => 'Terverifikasi',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'bukti_ulang' => 'Bukti ulang',
            'ditolak' => 'Ditolak',
            default => 'Belum bayar',
        };
        $bayarBadge = match ($order->status_pembayaran) {
            'terverifikasi' => 'bg-emerald-100 text-emerald-700',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
            'bukti_ulang' => 'bg-red-100 text-red-700',
            'ditolak' => 'bg-stone-100 text-stone-600',
            default => 'border border-stone-300 bg-white text-stone-500',
        };
        $kirimLabel = match ($order->status_pengiriman) {
            'siap_kirim' => 'Siap kirim',
            'produksi' => 'Produksi',
            'terkirim' => 'Terkirim',
            default => 'Belum diproduksi',
        };
        $kirimBadge = match ($order->status_pengiriman) {
            'siap_kirim' => 'bg-amber-100 text-amber-800',
            'produksi' => 'bg-blue-100 text-blue-700',
            'terkirim' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-stone-100 text-stone-600',
        };
        $jenisLabel = $order->jenisLabel();
        $donasiLabel = $order->isDonasiOnly() ? 'Donasi Saja' : 'Donasi bersama kaos';
        $alamatLengkap = $order->isDonasiOnly()
            ? '—'
            : $order->alamatLengkap();
    @endphp

    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
            <p class="mt-1 text-sm font-medium text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
            <span>&larr;</span> Kembali ke daftar
        </a>
        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Jemaat Bali / Administrasi Program</p>
        <h1 class="mt-1 font-serif text-3xl font-bold">Detail Pesanan</h1>
        <p class="mt-1 text-sm text-muted">{{ $order->nomor_pesanan }} &middot; {{ $jenisLabel }} &middot; {{ $order->nama }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5 sm:p-6">
                <h2 class="font-serif text-lg font-bold">Data pemesan</h2>
                <div class="mt-4 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Nama</span>
                        <span class="font-semibold">{{ $order->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Nomor HP</span>
                        <span class="font-semibold">{{ $order->nomor }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Email</span>
                        <span class="font-semibold">{{ $order->email ?? '—' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-3">
                        <span class="shrink-0 text-muted">Alamat</span>
                        <span class="max-w-[65%] text-right font-semibold leading-relaxed">{{ $alamatLengkap }}</span>
                    </div>
                    @if ($order->catatan)
                        <div class="flex items-start justify-between gap-4 py-3">
                            <span class="shrink-0 text-muted">Catatan</span>
                            <span class="max-w-[65%] text-right font-semibold leading-relaxed">{{ $order->catatan }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card p-5 sm:p-6">
                <h2 class="font-serif text-lg font-bold">Rincian pesanan</h2>

                @if ($order->design)
                    <div class="mt-4 flex items-center gap-4 rounded-xl bg-canvas p-4">
                        <img src="{{ asset($order->design->gambar) }}" alt="{{ $order->design->nama }}" class="h-20 w-20 shrink-0 rounded-xl border border-line bg-white object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="font-serif text-lg font-bold">{{ $order->design->nama }}</p>
                            <p class="mt-0.5 text-xs text-muted">Rp{{ number_format($order->design->harga, 0, ',', '.') }} / kaos</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">Ukuran / Jumlah</p>
                            <p class="mt-1 font-serif text-xl font-bold">{{ $order->ukuran }} &middot; {{ $order->jumlah }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-4 divide-y divide-line text-sm">
                    @unless ($order->isDonasiOnly())
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-muted">Subtotal kaos</span>
                            <span class="font-semibold">Rp{{ number_format($order->subtotal_kaos, 0, ',', '.') }}</span>
                        </div>
                    @endunless
                    @if ((int) $order->donasi > 0)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-muted">{{ $donasiLabel }}</span>
                            <span class="font-semibold">Rp{{ number_format($order->donasi, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Ongkir</span>
                        <span class="font-semibold">
                            @if ($order->ongkir > 0)
                                Rp{{ number_format($order->ongkir, 0, ',', '.') }}
                            @else
                                Belum dihitung
                            @endif
                        </span>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-4 rounded-2xl bg-canvas p-5">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-muted">Total tagihan</span>
                    <span class="font-serif text-3xl font-bold">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="card p-5 sm:p-6">
                <h2 class="font-serif text-lg font-bold">Riwayat status</h2>
                <ol class="relative mt-5 space-y-6 border-l-2 border-line pl-6">
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full bg-primary ring-4 ring-white"></span>
                        <p class="text-sm font-bold">Dibuat</p>
                        <p class="mt-0.5 text-xs text-muted">{{ $order->created_at->format('d M Y, H:i') }} &middot; Pesanan tercatat dalam sistem.</p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $order->status_pembayaran === 'terverifikasi' ? 'bg-emerald-500' : 'bg-amber-400' }} ring-4 ring-white"></span>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-bold">Pembayaran</p>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $bayarBadge }}">{{ $bayarLabel }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted">
                            @if ($order->verified_at)
                                Diverifikasi {{ $order->verified_at->format('d M Y, H:i') }} oleh {{ $order->verifier?->nama ?? $order->verifier?->name ?? 'Panitia' }}.
                            @else
                                Menunggu pemeriksaan bukti transfer oleh panitia.
                            @endif
                        </p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $order->dikirim_at ? 'bg-emerald-500' : 'bg-stone-300' }} ring-4 ring-white"></span>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-bold">Pengiriman</p>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $kirimBadge }}">{{ $kirimLabel }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted">
                            @if ($order->dikirim_at)
                                Dikirim {{ $order->dikirim_at->format('d M Y, H:i') }}{{ $order->resi ? ' · Resi ' . $order->resi : '' }}.
                            @elseif ($order->resi)
                                Resi {{ $order->resi }} dicatat.
                            @else
                                Belum ada pembaruan pengiriman.
                            @endif
                        </p>
                    </li>
                </ol>
            </div>

            @if ($order->bukti_path)
                <div class="card p-5 sm:p-6">
                    <h2 class="font-serif text-lg font-bold">Bukti pembayaran</h2>
                    <p class="mt-1 text-xs text-muted">Bukti transfer yang diunggah pemesan.</p>
                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener" class="mt-4 flex items-center gap-4 rounded-xl border border-line bg-white p-3.5 transition hover:border-primary">
                        <img src="{{ $order->buktiUrl() }}" alt="Bukti pembayaran {{ $order->nomor_pesanan }}" class="h-20 w-24 shrink-0 rounded-lg border border-line object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold">{{ basename($order->bukti_path) }}</span>
                            <span class="text-xs text-muted">Klik untuk memperbesar bukti transfer</span>
                        </span>
                        <span class="shrink-0 text-xs font-bold text-primary">Perbesar &rarr;</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-1">
            <div class="card p-5">
                <p class="font-serif text-lg font-bold">{{ $order->nomor_pesanan }}</p>
                <p class="mt-0.5 text-xs text-muted">{{ $jenisLabel }}</p>

                <div class="mt-4 rounded-2xl bg-amber-50 p-5 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700">Total tagihan</p>
                    <p class="mt-2 font-serif text-4xl font-bold text-amber-700">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>
                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $bayarBadge }}">{{ $bayarLabel }}</span>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $kirimBadge }}">{{ $kirimLabel }}</span>
                    </div>
                </div>

                <div class="mt-4 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-muted">Metode</span>
                        <span class="font-semibold">{{ $order->metode ?? 'Transfer manual' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-muted">Dicatat pada</span>
                        <span class="font-semibold">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if ($order->resi)
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <span class="text-muted">Resi</span>
                            <span class="font-semibold">{{ $order->resi }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if ($order->jenis === 'kaos_donasi')
                <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="card p-5">
                    @csrf
                    <h2 class="font-serif text-lg font-bold">Perbarui pengiriman</h2>
                    <p class="mt-1 text-xs text-muted">Atur tahap produksi, siap kirim, atau sudah dikirim beserta nomor resinya.</p>

                    <div class="mt-4">
                        <label for="status_pengiriman" class="label">Status pengiriman</label>
                        <select name="status_pengiriman" id="status_pengiriman" class="input">
                            <option value="belum_diproduksi" @selected(old('status_pengiriman', $order->status_pengiriman) === 'belum_diproduksi')>Belum diproduksi</option>
                            <option value="produksi" @selected(old('status_pengiriman', $order->status_pengiriman) === 'produksi')>Produksi</option>
                            <option value="siap_kirim" @selected(old('status_pengiriman', $order->status_pengiriman) === 'siap_kirim')>Siap kirim</option>
                            <option value="terkirim" @selected(old('status_pengiriman', $order->status_pengiriman) === 'terkirim')>Terkirim</option>
                        </select>
                        @error('status_pengiriman')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-4">
                        <label for="resi" class="label">Nomor resi <span class="font-normal text-muted">— opsional</span></label>
                        <input type="text" name="resi" id="resi" value="{{ old('resi', $order->resi) }}" placeholder="Nomor resi pengiriman" class="input">
                        @error('resi')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-4 w-full">Simpan status</button>

                    <p class="mt-3 flex items-start gap-2 rounded-xl bg-canvas p-3 text-xs leading-relaxed text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-4 w-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                        Status pengiriman hanya dapat diubah setelah pembayaran terverifikasi.
                    </p>
                </form>
            @endif

            @if ($order->catatan_panitia)
                <div class="card p-5">
                    <h2 class="font-serif text-lg font-bold">Catatan panitia</h2>
                    <div class="mt-3 rounded-xl bg-canvas p-4 text-sm leading-relaxed">{{ $order->catatan_panitia }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection
