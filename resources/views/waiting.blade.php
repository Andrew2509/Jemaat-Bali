@extends('layouts.app')

@section('title', 'Menunggu Verifikasi — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Status pembayaran pesanan ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': menunggu verifikasi manual panitia.')

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
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
            'terverifikasi' => 'bg-emerald-100 text-emerald-800',
            'ditolak' => 'bg-red-100 text-red-700',
            'bukti_ulang' => 'bg-red-100 text-red-700',
            'belum_bayar' => 'bg-stone-100 text-stone-600',
        ];
        $status = $order->status_pembayaran;
        $buktiExt = $order->bukti_path ? strtolower(pathinfo($order->bukti_path, PATHINFO_EXTENSION)) : null;
        $buktiIsImage = in_array($buktiExt, ['jpg', 'jpeg', 'png', 'webp'], true);
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
                &larr; Kembali ke formulir donasi
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-bold uppercase tracking-[0.18em]">
            <span class="text-muted">01 / Pemesanan &#10003;</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">02 / Pembayaran &#10003;</span>
            <span class="text-line">&mdash;</span>
            @if ($status === 'terverifikasi')
                <span class="text-emerald-700 font-extrabold">03 / Terverifikasi &#10003;</span>
            @elseif ($status === 'ditolak' || $status === 'bukti_ulang')
                <span class="text-red-700 font-extrabold">03 / Perlu Tindakan</span>
            @else
                <span class="text-amber-700 font-extrabold">03 / Menunggu Verifikasi</span>
            @endif
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            @if ($status === 'terverifikasi')
                <h1 class="font-serif text-3xl font-extrabold sm:text-4xl text-emerald-950">Pembayaran Terverifikasi</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-200 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-widest text-emerald-800">
                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                    </svg>
                    Terverifikasi
                </span>
            @elseif ($status === 'ditolak' || $status === 'bukti_ulang')
                <h1 class="font-serif text-3xl font-extrabold sm:text-4xl text-red-950">{{ $status === 'ditolak' ? 'Pembayaran Ditolak' : 'Bukti Perlu Diunggah Ulang' }}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 border border-red-200 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-widest text-red-800">
                    Perlu Tindakan
                </span>
            @else
                <h1 class="font-serif text-3xl font-extrabold sm:text-4xl text-stone-900">Menunggu Verifikasi</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-200 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-800">
                    <svg class="h-3.5 w-3.5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5a.75.75 0 0 0 .37.65l2 1.125a.75.75 0 1 0 .75-1.3l-1.62-.917V6.75Z" clip-rule="evenodd" />
                    </svg>
                    Menunggu Verifikasi
                </span>
            @endif
        </div>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-muted">
            @if ($status === 'terverifikasi')
                Terima kasih! Bukti transfer pembayaran donasi Anda telah diperiksa dan disetujui oleh panitia.
            @else
                Bukti pembayaran Anda sudah kami terima. Panitia akan memeriksa secara manual melalui WhatsApp.
            @endif
        </p>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                @if ($status === 'menunggu_verifikasi')
                    <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-800 text-xs">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1 text-xs leading-relaxed text-emerald-900">
                            <p class="font-bold">Notifikasi Otomatis Terkirim ke WhatsApp</p>
                            <p class="mt-0.5 text-emerald-800">
                                Pesan konfirmasi telah dikirimkan secara otomatis ke nomor WhatsApp Anda (<b>{{ $order->nomor }}</b>). Panitia akan segera memverifikasi bukti transfer Anda.
                            </p>
                        </div>
                    </div>
                @endif
                @if ($status === 'bukti_ulang')
                    <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="font-serif font-bold text-red-800">Panitia meminta bukti ulang.</p>
                            @if ($order->catatan_panitia)
                                <p class="mt-1 text-sm leading-relaxed text-red-700">{{ $order->catatan_panitia }}</p>
                            @endif
                            <a href="{{ route('shop.payment', $order) }}" class="btn btn-primary mt-4 !px-6 !py-2.5 text-xs font-bold uppercase tracking-widest">
                                Unggah Bukti Ulang
                            </a>
                        </div>
                    </div>
                @endif

                @if ($status === 'ditolak')
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-serif font-bold text-red-800">Pembayaran ditolak.</p>
                                @if ($order->catatan_panitia)
                                    <p class="mt-1 text-sm leading-relaxed text-red-700">{{ $order->catatan_panitia }}</p>
                                @endif
                            </div>
                        </div>
                        <p class="mt-4 border-t border-red-200 pt-3 text-sm text-red-700">Hubungi panitia jika ini keliru.</p>
                    </div>
                @endif

                @if ($status === 'terverifikasi')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex items-start gap-3">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-serif font-bold text-emerald-900">Pembayaran terverifikasi. Pesanan Anda masuk antrean produksi.</p>
                                @if ($order->jenis === 'kaos_donasi')
                                    <p class="mt-1 text-sm leading-relaxed text-emerald-800">
                                        Estimasi ongkir dan total final akan dikonfirmasi via WhatsApp {{ $program->whatsapp ?? 'panitia' }}.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card p-6 sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Nomor pesanan</p>
                            <p class="mt-1 font-display text-xl font-extrabold tracking-tight text-ink">#{{ $order->nomor_pesanan }}</p>
                            @if (!$order->isDonasiOnly())
                                @php($firstItem = $order->items_list[0] ?? null)
                                <p class="mt-2 font-serif text-base font-bold text-ink">{{ $firstItem['nama'] ?? 'Kaos Merchandise' }}</p>
                                <p class="text-sm text-muted">
                                    {{ $order->jenisLabel() }} &middot; {{ count($order->items_list) }} item (&middot; {{ collect($order->items_list)->sum('jumlah') }} kaos)
                                </p>
                            @else
                                <p class="mt-2 text-sm font-semibold text-muted">
                                    {{ $order->isDonasiOnly() ? 'Donasi Saja' : 'Pesan Kaos + Donasi' }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-serif text-2xl font-extrabold text-primary">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>
                            <span class="mt-2 inline-block rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-widest {{ $badgeClass[$status] ?? 'bg-stone-100 text-stone-600' }}">
                                {{ $statusLabel[$status] ?? $status }}
                            </span>
                        </div>
                    </div>

                    @if ($order->bukti_path && $order->buktiUrl())
                        <div class="mt-6 border-t border-line pt-6">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Bukti pembayaran</p>
                            <div class="mt-3 flex items-start gap-4 rounded-xl border border-line bg-canvas p-4">
                                @if ($buktiIsImage)
                                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener">
                                        <img src="{{ $order->buktiUrl() }}" alt="Bukti pembayaran {{ $order->nomor_pesanan }}"
                                            class="h-24 w-24 rounded-lg border border-line object-cover">
                                    </a>
                                @else
                                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-lg border border-line bg-white text-primary">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener"
                                        class="text-sm font-semibold text-primary hover:underline">Lihat file bukti &rarr;</a>
                                    <p class="mt-1 text-xs text-muted">
                                        Diunggah {{ $order->updated_at->translatedFormat('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card p-6 sm:p-8">
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
                                <p class="text-sm font-semibold text-ink">Status dikonfirmasi via WhatsApp</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted">Anda akan dihubungi jika ada perubahan atau konfirmasi.</p>
                            </div>
                        </li>
                        @if ($order->jenis === 'kaos_donasi')
                            <li class="flex gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">03</span>
                                <div>
                                    <p class="text-sm font-semibold text-ink">Pesanan kaos masuk antrean produksi</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-muted">Setelah pembayaran terverifikasi dan ongkir dikonfirmasi.</p>
                                </div>
                            </li>
                        @endif
                    </ol>
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div class="card sticky top-24 p-6">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Status pembayaran</p>
                    <p class="mt-2 font-serif text-lg font-bold text-ink">{{ $statusLabel[$status] ?? $status }}</p>
                    <p class="mt-1 text-sm text-muted">#{{ $order->nomor_pesanan }}</p>

                    <div class="mt-4 rounded-xl border border-line bg-canvas p-4">
                        <p class="font-serif text-2xl font-extrabold text-ink">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wider text-muted">Total tagihan</p>
                    </div>

                    <a href="{{ route('shop') }}" class="btn btn-outline mt-5 w-full">Kembali ke formulir donasi</a>
                    <a href="{{ route('progres') }}" class="btn btn-outline mt-3 w-full">Lihat Progres</a>

                    <div class="mt-6 flex items-start gap-2.5 rounded-xl border border-line bg-canvas p-4 text-sm">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                        <p class="leading-relaxed text-muted">Status diperbarui manual oleh panitia. Periksa WhatsApp Anda untuk konfirmasi.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
