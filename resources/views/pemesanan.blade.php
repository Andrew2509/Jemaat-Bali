@extends('layouts.app')

@section('title', 'Konfirmasi Pemesanan — Jemaat Bali')
@section('description', 'Konfirmasi donasi Jemaat Bali. Langkah berikutnya: pembayaran dan unggah bukti.')

@section('content')
    @php
        $totalSementara = (int) $order->subtotal_kaos + (int) $order->donasi;
        $isKaos = !$order->isDonasiOnly();
    @endphp

    <section class="border-b border-line bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-primary font-display text-xs font-extrabold text-white">JB</span>
                <span class="leading-tight">
                    <span class="block font-display text-sm font-extrabold tracking-tight text-ink">Jemaat Bali</span>
                    <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-muted">Merch Resmi &middot; Edisi 2026</span>
                </span>
            </a>
            <a href="{{ route('shop') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
                &larr; Kembali ke formulir donasi
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-bold uppercase tracking-[0.18em]">
            <span class="text-primary">01 / Pemesanan</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">02 / Pembayaran</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">03 / Verifikasi Manual</span>
        </div>

        <div class="mt-6 flex items-start gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:items-center">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                </svg>
            </span>
            <div>
                <p class="font-serif text-base font-bold text-emerald-900">Pemesanan berhasil dikirim</p>
                <p class="mt-1 text-sm leading-relaxed text-emerald-800">
                    Kode pesanan <span class="font-bold">{{ $order->nomor_pesanan }}</span> sudah tercatat.
                    Selanjutnya, bayar pesanan Anda dan unggah bukti pembayaran.
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                <div class="card p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <h1 class="font-serif text-xl font-bold">Ringkasan pesanan</h1>
                        <span class="rounded-full {{ $isKaos ? 'bg-primary/10 text-primary' : 'bg-stone-100 text-stone-600' }} px-3 py-1 text-[10px] font-bold uppercase tracking-widest">
                            {{ $order->jenisLabel() }}
                        </span>
                    </div>

                    @if ($isKaos && $order->design)
                        <div class="mt-5 flex items-start gap-4 rounded-xl border border-line bg-canvas p-4">
                            <img src="{{ asset($order->design->gambar) }}" alt="{{ $order->design->nama }}"
                                class="h-20 w-20 shrink-0 rounded-lg object-cover">
                            <div class="min-w-0">
                                <p class="font-serif text-base font-bold text-ink">{{ $order->design->nama }}</p>
                                <p class="mt-1 text-sm font-semibold text-primary">
                                    Rp {{ number_format($order->design->harga, 0, ',', '.') }}
                                    <span class="font-sans text-xs font-medium text-muted">/ kaos</span>
                                </p>
                                <p class="mt-1.5 text-sm text-muted">Ukuran {{ $order->ukuran }} &middot; {{ $order->jumlah }} kaos</p>
                            </div>
                        </div>
                    @endif

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-4 border-b border-line pb-3">
                            <dt class="text-muted">Nama</dt>
                            <dd class="text-right font-semibold text-ink">{{ $order->nama }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-line pb-3">
                            <dt class="text-muted">Email</dt>
                            <dd class="text-right font-semibold text-ink">{{ $order->email ?: '—' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-line pb-3">
                            <dt class="text-muted">Nomor HP</dt>
                            <dd class="text-right font-semibold text-ink">{{ $order->nomor }}</dd>
                        </div>
                        @if ($isKaos && $order->alamat)
                            <div class="flex items-start justify-between gap-4 border-b border-line pb-3">
                                <dt class="shrink-0 text-muted">Alamat</dt>
                                <dd class="max-w-sm text-right font-semibold leading-relaxed text-ink">
                                    {{ $order->alamat }},
                                    {{ collect([$order->kecamatan, $order->kabupaten])->filter()->implode(', ') }}
                                    {{ $order->kode_pos ? 'Kode Pos ' . $order->kode_pos : '' }}<br>
                                    <span class="font-medium text-muted">{{ $order->provinsi }}</span>
                                </dd>
                            </div>
                        @endif
                        @if ($order->catatan)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="shrink-0 text-muted">Catatan</dt>
                                <dd class="max-w-sm text-right font-medium leading-relaxed text-ink">{{ $order->catatan }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="card p-6 sm:p-8">
                    <h2 class="font-serif text-xl font-bold">Rincian pembayaran</h2>

                    <dl class="mt-5 space-y-3 text-sm">
                        @if ($isKaos)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-muted">Subtotal produk ({{ $order->jumlah }} kaos)</dt>
                                <dd class="font-semibold text-ink">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                        @if ($order->donasi > 0)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-muted">
                                    Donasi
                                    <span class="block text-xs text-stone-400">
                                        {{ $order->isDonasiOnly() ? 'donasi saja' : 'bersama pesanan kaos' }}
                                    </span>
                                </dt>
                                <dd class="font-semibold text-ink">Rp {{ number_format($order->donasi, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                        @if ($isKaos)
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-muted">Ongkir</dt>
                                <dd class="font-semibold text-muted">Belum dihitung</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-5">
                        <span class="font-serif text-base font-bold text-ink">Total {{ $isKaos ? 'sementara' : '' }}</span>
                        <span class="font-serif text-3xl font-extrabold text-primary">Rp {{ number_format($totalSementara, 0, ',', '.') }}</span>
                    </div>
                    @if ($isKaos)
                        <p class="mt-3 text-xs leading-relaxed text-muted">
                            Belum termasuk ongkir. Total akhir dikonfirmasi panitia setelah alamat diverifikasi.
                        </p>
                    @endif
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div class="card sticky top-24 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-display text-base font-extrabold tracking-tight text-ink">#{{ $order->nomor_pesanan }}</p>
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-stone-600">Belum bayar</span>
                    </div>
                    <p class="mt-1.5 text-xs font-semibold uppercase tracking-wider text-muted">
                        {{ $order->jenisLabel() }}
                    </p>

                    <div class="mt-5 rounded-xl border border-line bg-canvas p-4">
                        <p class="font-serif text-3xl font-extrabold text-ink">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wider text-muted">Total sementara</p>
                    </div>

                    <a href="{{ route('shop.payment', $order) }}" class="btn btn-primary mt-5 w-full !py-3.5">
                        Bayar Sekarang &rarr;
                    </a>
                    <a href="{{ route('shop') }}" class="btn btn-outline mt-3 w-full">
                        Kembali ke formulir donasi
                    </a>

                    <div class="mt-6 rounded-xl border border-line bg-canvas p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Langkah berikutnya</p>
                        <ol class="mt-3 space-y-3">
                            <li class="flex gap-2.5">
                                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">1</span>
                                <p class="text-sm leading-relaxed text-ink">Bayar sesuai total final dari admin.</p>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">2</span>
                                <p class="text-sm leading-relaxed text-ink">Unggah bukti pembayaran.</p>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">3</span>
                                <p class="text-sm leading-relaxed text-ink">Tunggu verifikasi panitia via WhatsApp.</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
