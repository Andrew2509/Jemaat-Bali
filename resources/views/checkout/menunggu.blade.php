@extends('layouts.app')

@section('title', ($order->status_pembayaran === 'terverifikasi' ? 'Pembayaran Terverifikasi' : 'Menunggu Konfirmasi') . ' — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Status pembayaran pesanan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': ' . ($order->status_pembayaran === 'terverifikasi' ? 'pembayaran terverifikasi.' : 'menunggu konfirmasi panitia.'))

@section('content')
    @php
        $statusLabel = [
            'belum_bayar' => 'Belum bayar',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            'bukti_ulang' => 'Bukti ulang',
        ];
        $badgeClass = [
            'menunggu_verifikasi' => 'bg-amber-50 text-amber-800 border border-amber-200',
            'terverifikasi' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'ditolak' => 'bg-red-50 text-red-700 border border-red-200',
            'bukti_ulang' => 'bg-red-50 text-red-700 border border-red-200',
            'belum_bayar' => 'bg-stone-100 text-stone-600 border border-stone-200',
        ];
        $status = $order->status_pembayaran;
        $buktiExt = $order->bukti_path ? strtolower(pathinfo($order->bukti_path, PATHINFO_EXTENSION)) : null;
        $buktiIsImage = in_array($buktiExt, ['jpg', 'jpeg', 'png', 'webp'], true);
        $totalSementara = (int) $order->subtotal_kaos;
        $waLink = $programInfo->wa_url ?? '#';
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
            <a href="{{ route('design') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
                &larr; Kembali ke koleksi
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <nav class="text-xs font-semibold uppercase tracking-widest text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('design') }}" class="hover:text-primary">Koleksi kaos</a></li>
                <li>&gt;</li>
                <li><a href="{{ route('checkout.pemesanan') }}" class="hover:text-primary">Pemesanan</a></li>
                <li>&gt;</li>
                <li class="text-ink">{{ $status === 'terverifikasi' ? 'Pembayaran Terverifikasi' : 'Menunggu Konfirmasi' }}</li>
            </ol>
        </nav>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-8">
                {{-- Status utama --}}
                <div class="card overflow-hidden">
                    @if ($status === 'terverifikasi')
                        {{-- Tampilan JIKA TERVERIFIKASI (Hijau Emerald Segar & Elegan) --}}
                        <div class="relative bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-950 px-6 py-10 text-center sm:px-10 text-white shadow-inner">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-emerald-500/20 text-emerald-200 ring-2 ring-emerald-400/40 shadow-lg shadow-emerald-950/40">
                                <svg class="h-7 w-7 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="mt-5 inline-flex items-center gap-2 rounded-full bg-emerald-500/25 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100 ring-1 ring-emerald-400/40">
                                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Status: Terverifikasi
                            </span>
                            <h1 class="mt-4 font-serif text-3xl font-extrabold text-white sm:text-4xl">Pembayaran Terverifikasi</h1>
                            <p class="mt-3 text-sm leading-relaxed text-emerald-100/90 max-w-xl mx-auto">
                                Terima kasih! Bukti transfer pembayaran Anda telah diperiksa dan disetujui oleh panitia. Pesanan Anda kini resmi masuk ke antrean produksi.
                            </p>
                            <p class="mt-5 font-display text-lg font-extrabold tracking-widest text-emerald-300">{{ $order->nomor_pesanan }}</p>
                        </div>
                    @elseif ($status === 'ditolak' || $status === 'bukti_ulang')
                        {{-- Tampilan JIKA DITOLAK / PERLU BUKTI ULANG (Merah) --}}
                        <div class="relative bg-gradient-to-br from-red-950 via-rose-950 to-stone-900 px-6 py-10 text-center sm:px-10 text-white shadow-inner">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-500/20 text-red-200 ring-2 ring-red-400/40 shadow-lg shadow-black/40">
                                <svg class="h-7 w-7 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </span>
                            <span class="mt-5 inline-flex items-center gap-2 rounded-full bg-red-500/20 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-red-200 ring-1 ring-red-400/40">
                                <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                Status: {{ $statusLabel[$status] ?? $status }}
                            </span>
                            <h1 class="mt-4 font-serif text-3xl font-extrabold text-white sm:text-4xl">
                                {{ $status === 'ditolak' ? 'Pembayaran Ditolak' : 'Bukti Perlu Diunggah Ulang' }}
                            </h1>
                            <p class="mt-3 text-sm leading-relaxed text-red-100/90 max-w-xl mx-auto">
                                {{ $order->catatan_panitia ?: 'Bukti pembayaran perlu diperbaiki atau diunggah ulang agar pesanan dapat diproses.' }}
                            </p>
                            <p class="mt-5 font-display text-lg font-extrabold tracking-widest text-red-300">{{ $order->nomor_pesanan }}</p>
                        </div>
                    @else
                        {{-- Tampilan JIKA BELUM TERVERIFIKASI / MENUNGGU KONFIRMASI (Warm Amber / Charcoal) --}}
                        <div class="relative bg-gradient-to-br from-stone-900 via-stone-900 to-amber-950/70 px-6 py-10 text-center sm:px-10 text-white shadow-inner">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-amber-500/20 text-amber-200 ring-2 ring-amber-400/40 shadow-lg shadow-black/40">
                                <svg class="h-7 w-7 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>
                            <span class="mt-5 inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-200 ring-1 ring-amber-400/30">
                                <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                                Status: Menunggu Verifikasi
                            </span>
                            <h1 class="mt-4 font-serif text-3xl font-extrabold text-white sm:text-4xl">Menunggu Konfirmasi</h1>
                            <p class="mt-3 text-sm leading-relaxed text-amber-100/80 max-w-xl mx-auto">
                                Bukti pembayaran Anda sudah kami terima. Panitia akan memeriksa secara manual melalui mutasi bank &amp; WhatsApp.
                            </p>
                            <p class="mt-5 font-display text-lg font-extrabold tracking-widest text-amber-200">{{ $order->nomor_pesanan }}</p>
                        </div>
                    @endif

                    {{-- Progress steps --}}
                    <div class="border-b border-line bg-canvas px-6 py-6 sm:px-8">
                        <ol class="flex items-center gap-2 sm:gap-4">
                            <li class="flex flex-1 items-center gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary text-xs font-extrabold text-white shadow-xs">1</span>
                                <span class="hidden text-xs font-bold uppercase tracking-wider text-ink sm:block">Pemesanan</span>
                            </li>
                            <li class="h-px flex-1 bg-line"></li>
                            <li class="flex flex-1 items-center gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary text-xs font-extrabold text-white shadow-xs">2</span>
                                <span class="hidden text-xs font-bold uppercase tracking-wider text-ink sm:block">Pembayaran</span>
                            </li>
                            <li class="h-px flex-1 bg-line"></li>
                            <li class="flex flex-1 items-center gap-3">
                                @if ($status === 'terverifikasi')
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-600 text-xs font-extrabold text-white shadow-xs">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <span class="hidden text-xs font-bold uppercase tracking-wider text-emerald-800 sm:block">Terverifikasi &#10003;</span>
                                @elseif ($status === 'ditolak' || $status === 'bukti_ulang')
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-red-100 text-xs font-extrabold text-red-700 ring-2 ring-red-300">!</span>
                                    <span class="hidden text-xs font-bold uppercase tracking-wider text-red-700 sm:block">Perlu Tindakan</span>
                                @else
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-amber-100 text-xs font-extrabold text-amber-800 ring-2 ring-amber-300">3</span>
                                    <span class="hidden text-xs font-bold uppercase tracking-wider text-amber-800 sm:block">Verifikasi manual</span>
                                @endif
                            </li>
                        </ol>
                    </div>

                    {{-- Banner Notifikasi Jika TERVERIFIKASI --}}
                    @if ($status === 'terverifikasi')
                        <div class="border-b border-emerald-200 bg-emerald-50/90 px-6 py-4 sm:px-8">
                            <div class="flex items-start gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-600 text-white text-xs shadow-xs">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <div class="text-xs leading-relaxed text-emerald-950">
                                    <p class="font-bold text-emerald-900">Pembayaran Sah &amp; Terverifikasi</p>
                                    <p class="mt-0.5 text-emerald-800">
                                        Bukti transfer pembayaran Anda telah diperiksa dan disetujui. Pesanan kaos masuk antrean produksi. Rincian nota pemesanan juga telah dikonfirmasi ke nomor WhatsApp Anda (<b>{{ $order->nomor }}</b>).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Status Pengiriman Notifikasi WhatsApp --}}
                    @if ($status === 'menunggu_verifikasi')
                        <div class="border-b border-line bg-emerald-50/70 px-6 py-4 sm:px-8">
                            <div class="flex items-start gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-800 text-xs">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                </span>
                                <div class="text-xs leading-relaxed text-emerald-900">
                                    <p class="font-bold">Notifikasi Otomatis Terkirim ke WhatsApp</p>
                                    <p class="mt-0.5 text-emerald-800">
                                        Pesan konfirmasi pesanan telah dikirimkan secara otomatis ke nomor WhatsApp Anda (<b>{{ $order->nomor }}</b>). Panitia akan segera memverifikasi bukti transfer Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($status === 'bukti_ulang')
                        <div class="border-b border-line bg-red-50 px-6 py-5 sm:px-8">
                            <p class="font-serif font-bold text-red-700">Bukti pembayaran perlu diunggah ulang.</p>
                            @if ($order->catatan_panitia)
                                <p class="mt-1 text-sm leading-relaxed text-red-700">{{ $order->catatan_panitia }}</p>
                            @endif
                            <a href="{{ route('checkout.bayar', $order) }}" class="btn btn-primary mt-4 !px-6 !py-2.5 text-xs font-bold uppercase tracking-widest">
                                Unggah bukti ulang
                            </a>
                        </div>
                    @endif

                    @if ($status === 'ditolak')
                        <div class="border-b border-line bg-red-50 px-6 py-5 sm:px-8">
                            <p class="font-serif font-bold text-red-700">Bukti pembayaran ditolak.</p>
                            @if ($order->catatan_panitia)
                                <p class="mt-1 text-sm leading-relaxed text-red-700">{{ $order->catatan_panitia }}</p>
                            @endif
                            <a href="{{ route('checkout.bayar', $order) }}" class="btn btn-outline mt-4 !px-6 !py-2.5 text-xs font-bold uppercase tracking-widest">
                                Ulangi pembayaran
                            </a>
                        </div>
                    @endif

                    <div class="px-6 py-6 sm:px-8">
                        <h2 class="font-serif text-lg font-bold">Apa yang terjadi selanjutnya?</h2>
                        <ol class="mt-5 space-y-5">
                            <li class="flex gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">01</span>
                                <div>
                                    <p class="text-sm font-semibold text-ink">Panitia memeriksa bukti dan mutasi rekening</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-muted">Pemeriksaan dilakukan manual, biasanya pada jam kerja.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">02</span>
                                <div>
                                    <p class="text-sm font-semibold text-ink">Konfirmasi total final dan ongkir via WhatsApp</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-muted">Estimasi ongkir dan total final dikonfirmasi melalui WhatsApp {{ $program->whatsapp ?? 'panitia' }}.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">03</span>
                                <div>
                                    <p class="text-sm font-semibold text-ink">Pesanan kaos masuk antrean produksi</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-muted">Setelah pembayaran terverifikasi dan ongkir dikonfirmasi.</p>
                                </div>
                            </li>
                        </ol>

                        <div class="mt-6 rounded-xl border border-line bg-canvas p-4 text-sm">
                            <p class="font-semibold text-ink">Belum menerima kabar?</p>
                            <p class="mt-1 leading-relaxed text-muted">
                                Status diperbarui manual oleh panitia. Periksa WhatsApp Anda, atau hubungi panitia bila konfirmasi belum diterima.
                            </p>
                            <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-outline mt-4 !px-6 !py-2.5 text-xs font-bold uppercase tracking-widest">
                                Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div class="card sticky top-24 !p-0 overflow-hidden">
                    <div class="relative aspect-[4/3] bg-stone-100">
                        <span class="absolute left-3 top-3 rounded-full bg-stone-900/90 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-xs">Edisi 2026</span>
                        <span class="absolute right-3 top-3 text-[10px] font-bold uppercase tracking-widest text-white/90 drop-shadow">Jemaat Bali</span>
                        @php($firstItem = $order->items_list[0] ?? null)
                        @php($itemDesign = $firstItem ? \App\Models\Design::find($firstItem['design_id']) : null)
                        @if ($itemDesign && $itemDesign->gambar)
                            <img src="{{ asset($itemDesign->gambar) }}" alt="{{ $firstItem['nama'] }}" class="h-full w-full object-cover">
                        @endif
                    </div>

                    <div class="p-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Ringkasan pesanan</p>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="font-display text-xl font-extrabold tracking-tight text-ink">#{{ $order->nomor_pesanan }}</p>
                            <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-widest {{ $badgeClass[$status] ?? 'bg-stone-100 text-stone-600' }}">
                                {{ $statusLabel[$status] ?? $status }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-xs font-semibold uppercase tracking-wider text-muted">Pesan Kaos</p>

                        @foreach ($order->items_list as $item)
                            <div class="mt-3 border-t border-line pt-3">
                                <p class="font-serif text-base font-bold text-ink">{{ $item['nama'] }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-muted">Ukuran {{ $item['ukuran'] }} · {{ $item['jumlah'] }} kaos · Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach

                        <dl class="mt-4 space-y-2.5 border-t border-line pt-4 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-muted">Subtotal produk</dt>
                                <dd class="font-semibold">Rp{{ number_format($order->subtotal_kaos, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-muted">Ongkir</dt>
                                <dd class="font-semibold text-muted">Belum dihitung</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex items-center justify-between border-t border-line pt-4">
                            <span class="font-serif font-bold">Total sementara</span>
                            <span class="font-serif text-2xl font-extrabold text-primary">Rp{{ number_format($totalSementara, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('design') }}" class="btn btn-outline mt-5 w-full">Kembali ke koleksi kaos</a>
                        <a href="{{ route('progres') }}" class="btn btn-outline mt-3 w-full">Lihat Progres</a>

                        @if ($order->bukti_path && $order->buktiUrl())
                            <div class="mt-6 border-t border-line pt-5">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Bukti pembayaran</p>
                                @if ($buktiIsImage)
                                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener" class="mt-3 block">
                                        <img src="{{ $order->buktiUrl() }}" alt="Bukti pembayaran {{ $order->nomor_pesanan }}"
                                            class="w-full rounded-xl border border-line object-cover">
                                    </a>
                                @else
                                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener"
                                        class="mt-3 flex items-center gap-3 rounded-xl border border-line bg-canvas p-4 text-sm font-semibold text-primary hover:border-primary/40">
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        Lihat bukti pembayaran ({{ strtoupper($buktiExt) }})
                                    </a>
                                @endif
                                <p class="mt-2 text-xs text-muted">Diunggah {{ $order->updated_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
