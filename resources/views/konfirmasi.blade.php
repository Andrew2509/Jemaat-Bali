@extends('layouts.app')

@section('title', 'Terima Kasih — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pembayaran terverifikasi. Terima kasih atas dukungan Anda untuk ' . ($programInfo->nama_program ?? 'pembangunan Jemaat Bali') . '.')

@section('content')
    @php
        $isKaos = $order->jenis === 'kaos_donasi';
        $terverifikasi = $order->status_pembayaran === 'terverifikasi';
    @endphp

    <section class="border-b border-line bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-primary font-display text-xs font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                <span class="leading-tight">
                    <span class="block font-display text-sm font-extrabold tracking-tight text-ink">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                    <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-muted">Merch Resmi &middot; Edisi {{ date('Y') }}</span>
                </span>
            </a>
            <a href="{{ route('shop') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
                &larr; Kembali ke Shop
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-bold uppercase tracking-[0.18em]">
            <span class="text-muted">01 / Pemesanan &#10003;</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">02 / Pembayaran &#10003;</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">03 / Verifikasi Manual &#10003;</span>
        </div>

        <div class="mt-8 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-600 text-white shadow-lg">
                <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                </svg>
            </span>
            <h1 class="mt-6 font-serif text-4xl font-extrabold sm:text-5xl">Terima Kasih</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-muted">
                Pembayaran Anda sudah terverifikasi. Dukungan Anda membantu {{ $programInfo->nama_program ?? 'pembangunan Gereja Jemaat Bali' }} berjalan lebih pasti.
            </p>
        </div>

        <div class="mx-auto mt-10 max-w-2xl">
            <div class="card p-6 text-center sm:p-8">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                    </svg>
                </span>

                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Nomor pesanan</p>
                <p class="mt-1 font-display text-2xl font-extrabold tracking-tight text-ink">#{{ $order->nomor_pesanan }}</p>

                <p class="mt-5 font-serif text-4xl font-extrabold text-primary">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>

                @if ($order->verified_at)
                    <p class="mt-3 text-sm text-muted">
                        Terverifikasi {{ $order->verified_at->translatedFormat('d F Y') }}
                    </p>
                @endif

                @if ($isKaos && count($order->items_list) > 0)
                    <div class="mt-6 border-t border-line pt-6">
                        @foreach ($order->items_list as $item)
                            <div class="mt-2 flex items-center justify-center gap-3">
                                @php($itemDesign = \App\Models\Design::find($item['design_id']))
                                @if ($itemDesign && $itemDesign->gambar)
                                    <img src="{{ asset($itemDesign->gambar) }}" alt="{{ $item['nama'] }}" class="h-12 w-12 rounded-lg object-cover">
                                @endif
                                <div class="text-left">
                                    <p class="font-serif text-base font-bold text-ink">{{ $item['nama'] }}</p>
                                    <p class="text-sm text-muted">Ukuran {{ $item['ukuran'] }} &middot; {{ $item['jumlah'] }} kaos &middot; Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($isKaos)
                    <div class="mt-6 border-t border-line pt-6 text-left">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Langkah selanjutnya</p>
                        <div class="mt-4">
                            <div class="flex items-start">
                                <p class="flex-1 text-center text-sm font-semibold text-ink">Pesanan diproduksi</p>
                                <p class="flex-1 text-center text-sm font-semibold text-ink">Dikirim</p>
                                <p class="flex-1 text-center text-sm font-semibold text-stone-500">Diterima</p>
                            </div>
                            <div class="mt-3 flex items-center">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                                <span class="h-px flex-1 bg-primary"></span>
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                                <span class="h-px flex-1 bg-stone-300"></span>
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-stone-300"></span>
                            </div>
                        </div>
                        <p class="mt-4 text-xs leading-relaxed text-muted">
                            Nomor resi akan dikirim via WhatsApp setelah pesanan dikirim. Ongkir dikonfirmasi terpisah oleh panitia.
                        </p>
                    </div>
                @else
                    <div class="mt-6 rounded-xl border border-line bg-canvas p-4 text-left">
                        <p class="text-sm font-semibold text-ink">Donasi Anda tercatat sebagai dana pembangunan program.</p>
                        <p class="mt-1.5 text-xs leading-relaxed text-muted">
                            Panitia akan memasukkan donasi Anda dalam laporan penggunaan dana.
                        </p>
                    </div>
                @endif
            </div>

            @if ($program)
                <div class="card mt-6 p-6 sm:p-8">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Program</p>
                    <h2 class="mt-2 font-serif text-lg font-bold text-ink">{{ $program->nama_program }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $program->deskripsi }}</p>
                    <p class="mt-4 border-t border-line pt-4 text-xs leading-relaxed text-muted">
                        Dana ini masuk ke rekening khusus pembangunan. Laporan penggunaan dana dipublikasikan di laporan panitia.
                    </p>
                </div>
            @endif

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('progres') }}" class="btn btn-primary !px-8">Lihat Progres</a>
                <a href="{{ route('home') }}" class="btn btn-outline !px-8">Kembali ke Home</a>
            </div>

            @if (! $terverifikasi)
                <p class="mt-6 text-center text-xs leading-relaxed text-muted">
                    Halaman ini ditampilkan sebagai contoh tampilan setelah verifikasi. Status pesanan Anda saat ini:
                    {{ $order->status_pembayaran }}.
                </p>
            @endif
        </div>
    </section>
@endsection
