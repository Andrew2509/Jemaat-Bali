@extends('layouts.app')

@section('title', ($order->status_pembayaran === 'terverifikasi' ? 'Pembayaran Berhasil' : 'Menunggu Konfirmasi') . ' — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Status pembayaran pesanan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': ' . ($order->status_pembayaran === 'terverifikasi' ? 'pembayaran berhasil dikonfirmasi.' : 'menunggu konfirmasi panitia.'))

@section('content')
    @php
        $status = $order->status_pembayaran;
        $isVerified = $status === 'terverifikasi';
        $nomor = $order->nomor_pesanan ? '#' . $order->nomor_pesanan : '#' . $order->id;
        $waLink = $programInfo->wa_url ?? '#';
        $firstItem = $order->items_list[0] ?? null;
        $itemDesign = $firstItem && !empty($firstItem['design_id']) ? \App\Models\Design::find($firstItem['design_id']) : null;
        $displayDesign = $itemDesign ?? $order->design;
        $rawName = $firstItem['nama'] ?? ($displayDesign->nama ?? 'Satu Jemaat');
        $cleanName = trim(preg_replace('/^kaos\s+/i', '', $rawName), '"\' ');
        $formattedName = 'Kaos "' . $cleanName . '"';
        $itemPrice = $firstItem['harga'] ?? ($displayDesign->harga ?? 135000);
        $totalQty = collect($order->items_list)->sum('jumlah') ?: ($order->jumlah ?: 1);
        $totalBayar = $order->totalTagihan() > 0 ? $order->totalTagihan() : $order->subtotal_kaos;
        $bankNama = $programInfo->bank_nama ?? 'Mandiri';
    @endphp

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{-- Breadcrumb (Gambar 2: Beranda > Pembangunan Jemaat Bali > Pembayaran) --}}
        <nav class="text-xs text-stone-500 font-medium mb-3" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-primary transition">Beranda</a></li>
                <li class="text-stone-300">&gt;</li>
                <li><a href="{{ route('home') }}" class="hover:text-primary transition">{{ $programInfo->nama_program ?? 'Pembangunan Jemaat Bali' }}</a></li>
                <li class="text-stone-300">&gt;</li>
                <li class="text-[#c85a32] font-semibold">
                    {{ $isVerified ? 'Pembayaran' : 'Menunggu Konfirmasi' }}
                </li>
            </ol>
        </nav>

        {{-- Eyebrow, Heading & Subtitle --}}
        <div class="mt-2">
            <p class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">
                PEMBAYARAN KAOS
            </p>
            <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight leading-tight">
                {{ $isVerified ? 'Pembayaran Berhasil' : 'Menunggu Konfirmasi' }}
            </h1>
            <p class="mt-2 text-sm text-stone-600 max-w-2xl leading-relaxed">
                Terima kasih atas dukungan dan partisipasinya untuk pembangunan jemaat Bali. Tuhan memberkati!
            </p>
        </div>

        {{-- Stepper (Gambar 2: All Green with 3) --}}
        <div class="mt-6 flex items-center gap-2 sm:gap-3">
            {{-- Step 1: Pilih Kaos (Checked Green) --}}
            <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-full bg-[#245236] text-[10px] sm:text-xs font-bold text-white shadow-2xs">
                    <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-xs sm:text-sm font-bold text-stone-800">Pilih Kaos</span>
            </div>

            {{-- Connector 1-2 (Solid Green) --}}
            <span class="h-[2px] w-8 sm:w-16 bg-[#245236]"></span>

            {{-- Step 2: Data & Alamat (Checked Green) --}}
            <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-full bg-[#245236] text-[10px] sm:text-xs font-bold text-white shadow-2xs">
                    <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-xs sm:text-sm font-bold text-stone-800">Data &amp; Alamat</span>
            </div>

            {{-- Connector 2-3 (Solid Green) --}}
            <span class="h-[2px] w-8 sm:w-16 bg-[#245236]"></span>

            {{-- Step 3: Pembayaran (Green circle with 3) --}}
            <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-full bg-[#245236] text-[10px] sm:text-xs font-bold text-white shadow-2xs">
                    3
                </span>
                <span class="text-xs sm:text-sm font-bold text-stone-800">Pembayaran</span>
            </div>
        </div>

        {{-- Main Grid Content --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            {{-- Left Column --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Banner Status --}}
                @if ($status === 'ditolak' || $status === 'bukti_ulang')
                    <div class="flex items-start gap-3.5 rounded-2xl border border-red-200 bg-red-50 p-4 sm:p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-700 text-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-bold text-red-900">
                                {{ $status === 'ditolak' ? 'Pembayaran Ditolak' : 'Bukti Pembayaran Perlu Diunggah Ulang' }}
                            </h2>
                            <p class="mt-0.5 text-xs text-red-700 leading-relaxed">
                                {{ $order->catatan_panitia ?: 'Bukti pembayaran perlu diperbaiki atau diunggah ulang agar pesanan dapat diproses.' }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('checkout.bayar', $order) }}"
                                    class="inline-block rounded-lg bg-red-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-red-800 transition">
                                    Unggah Bukti Ulang &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3.5 rounded-2xl border border-[#d6e5d8] bg-[#ebf4ec] p-4 sm:p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#245236] text-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-stone-900">
                                {{ $isVerified ? 'Pembayaran berhasil!' : 'Pembayaran berhasil dikirim!' }}
                            </h2>
                            <p class="mt-0.5 text-xs text-stone-600 leading-relaxed">
                                @if ($isVerified)
                                    Terima kasih, pembayaran Anda telah dikonfirmasi.
                                @else
                                    Terima kasih, bukti pembayaran Anda telah kami terima dan sedang diverifikasi admin secara manual. Menunggu Konfirmasi.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Detail Pembayaran & Informasi Pengiriman Card --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 sm:p-7 shadow-[0_1px_3px_rgba(0,0,0,0.03)] space-y-6">
                    {{-- Detail Pembayaran Section --}}
                    <div>
                        <h3 class="text-base font-bold text-stone-900">Detail Pembayaran</h3>
                        <dl class="mt-4 space-y-3 text-xs sm:text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Nomor Transaksi</dt>
                                <dd class="font-bold text-stone-900">#{{ $order->nomor_pesanan }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Tanggal Pembayaran</dt>
                                <dd class="text-stone-700">
                                    {{ ($order->verified_at ?? $order->updated_at ?? now())->translatedFormat('d F Y, H:i') }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Metode Pembayaran</dt>
                                <dd class="text-stone-700 font-semibold">
                                    {{ $order->metode === 'transfer' ? 'Transfer Bank (' . $bankNama . ')' : 'QRIS' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-stone-500">Status</dt>
                                <dd>
                                    @if ($isVerified)
                                        <span class="inline-block rounded-full bg-[#ebf4ec] px-3 py-0.5 text-xs font-bold text-[#245236]">Berhasil</span>
                                    @elseif ($status === 'ditolak' || $status === 'bukti_ulang')
                                        <span class="inline-block rounded-full bg-red-100 px-3 py-0.5 text-xs font-bold text-red-700">Perlu Tindakan</span>
                                    @else
                                        <span class="inline-block rounded-full bg-amber-100 px-3 py-0.5 text-xs font-bold text-amber-800">Menunggu Verifikasi</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        {{-- Total Pembayaran Row --}}
                        <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-bold text-stone-800">Total Pembayaran</span>
                            <span class="text-sm sm:text-base font-bold text-stone-900">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                        </div>

                        {{-- Product Row Item --}}
                        <div class="mt-3.5 flex items-center justify-between rounded-xl border border-stone-200/80 bg-stone-50/50 p-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-stone-100 text-stone-600">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l3.75 3.75 3.75-3.75M4.5 9l3.75-4.5h7.5L19.5 9l-3 1.5v9h-9v-9l-3-1.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-stone-900">
                                        {{ $formattedName }}
                                    </h4>
                                    <p class="text-[11px] text-stone-500">
                                        {{ count($order->items_list) }} produk · Kaos merchandise
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-stone-500">{{ $totalQty }} pcs</p>
                                <p class="text-xs sm:text-sm font-bold text-stone-900">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Pengiriman Section --}}
                    <div class="pt-5 border-t border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#edf5ee] text-[#245236]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V18.75m0-11.25H4.5a2.25 2.25 0 0 0-2.25 2.25v6.75" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-stone-900">Informasi Pengiriman</h3>
                        </div>

                        <div class="mt-4 pl-1">
                            <h4 class="text-xs font-semibold text-stone-700">Alamat pengiriman</h4>
                            <div class="mt-1 text-xs text-stone-600 leading-relaxed">
                                <p>{{ $order->alamat }}</p>
                                <p>
                                    @if ($order->kelurahan) {{ $order->kelurahan }}, @endif
                                    @if ($order->kecamatan) {{ $order->kecamatan }}, @endif
                                    {{ $order->kabupaten }} {{ $order->kode_pos }}
                                </p>
                                <p>{{ $order->provinsi ?? 'Indonesia' }}</p>
                            </div>

                            <p class="mt-3.5 text-xs text-stone-500 leading-relaxed">
                                Informasi pengiriman akan dikirim melalui WhatsApp setelah pembayaran terverifikasi.
                            </p>
                        </div>
                    </div>

                    {{-- Thank You / Quote Card --}}
                    <div class="rounded-xl bg-[#edf4ee] p-4 sm:p-5 flex flex-wrap sm:flex-nowrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#245236] text-white">
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-stone-800 leading-snug">
                                Bersama kita wujudkan pembangunan jemaat Bali menjadi lebih baik.
                            </p>
                        </div>
                        <div class="shrink-0 text-right w-full sm:w-auto">
                            <span class="font-script text-2xl font-bold text-stone-800">Terima Kasih</span>
                        </div>
                    </div>

                    @if ($order->bukti_path && $order->buktiUrl())
                        <div class="pt-2 flex justify-end">
                            <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener"
                                class="text-[11px] text-stone-500 hover:text-stone-800 underline transition">
                                Lihat bukti pembayaran yang diunggah &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column --}}
            <aside class="space-y-4">
                {{-- Product Card --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-4 sm:p-5 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                    <div class="relative aspect-square sm:aspect-[4/3] w-full overflow-hidden rounded-xl bg-stone-50">
                        <span class="absolute left-2.5 top-2.5 z-10 rounded-md bg-stone-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">
                            EDISI 2026
                        </span>
                        @if ($displayDesign && $displayDesign->gambar)
                            <img src="{{ asset($displayDesign->gambar) }}" alt="{{ $formattedName }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full w-full place-items-center bg-stone-100 text-xs text-stone-400">
                                Gambar kaos
                            </div>
                        @endif
                    </div>

                    <h2 class="mt-3.5 text-base font-bold text-stone-900">
                        {{ $formattedName }}
                    </h2>
                    <p class="mt-0.5 text-xs text-stone-500">
                        {{ count($order->items_list) }} produk · Kaos merchandise
                    </p>
                    <p class="mt-1 text-sm font-bold text-stone-900">
                        Rp {{ number_format($itemPrice, 0, ',', '.') }}
                        <span class="text-xs font-normal text-stone-400">/ pcs</span>
                    </p>

                    <div class="mt-4 border-t border-stone-100 pt-3 text-xs space-y-2">
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Total qty</span>
                            <span class="font-medium text-stone-900">{{ $totalQty }} pcs</span>
                        </div>
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Subtotal kaos</span>
                            <span class="font-medium text-stone-900">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->donasi > 0)
                            <div class="flex items-center justify-between text-stone-600">
                                <span>Donasi sukarela</span>
                                <span class="font-medium text-stone-900">Rp {{ number_format($order->donasi, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($order->ongkir > 0)
                            <div class="flex items-center justify-between text-stone-600">
                                <span>Ongkir</span>
                                <span class="font-medium text-stone-900">Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Total Pembayaran Box --}}
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-[#eaf4ec] p-3.5 sm:p-4">
                        <span class="text-xs sm:text-sm font-medium text-stone-700">Total Pembayaran</span>
                        <span class="text-sm sm:text-base font-bold text-[#245236]">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Butuh Bantuan Card --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#edf5ee] text-[#245236]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0-9-9c0 1.48.36 2.88 1 4.11L3 21l4.89-1A8.94 8.94 0 0 0 12 21Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-stone-900">Butuh bantuan?</h3>
                            <p class="mt-1 text-xs text-stone-500 leading-relaxed">
                                Hubungi kami melalui WhatsApp jika ada kendala terkait pesanan Anda.
                            </p>
                        </div>
                    </div>
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-stone-200/90 py-2.5 px-4 text-xs font-semibold text-stone-800 transition hover:bg-stone-50">
                        <svg class="h-4 w-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Hubungi via WhatsApp
                    </a>
                </div>
            </aside>
        </div>
    </section>
@endsection
