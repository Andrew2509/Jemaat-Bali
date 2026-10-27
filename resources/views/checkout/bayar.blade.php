@extends('layouts.app')

@section('title', 'Pembayaran Kaos — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pembayaran pesanan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': selesaikan pembayaran setelah total final dikonfirmasi admin.')

@section('content')
    @php
        $nomor = $order->nomor_pesanan ? '#' . $order->nomor_pesanan : '#' . $order->id;
        $totalSementara = (int) $order->subtotal_kaos;
        $waLink = $programInfo->wa_url ?? '#';
        $firstItem = $order->items_list[0] ?? null;
        $itemDesign = $firstItem && !empty($firstItem['design_id']) ? \App\Models\Design::find($firstItem['design_id']) : null;
        $displayDesign = $itemDesign ?? $order->design;
        $rawName = $firstItem['nama'] ?? ($displayDesign->nama ?? 'Satu Jemaat');
        $cleanName = trim(preg_replace('/^kaos\s+/i', '', $rawName), '"\' ');
        $formattedName = 'Kaos "' . $cleanName . '"';
        $itemPrice = $firstItem['harga'] ?? ($displayDesign->harga ?? 135000);
        $totalQty = collect($order->items_list)->sum('jumlah') ?: ($order->jumlah ?: 1);
        $bankNama = $programInfo->bank_nama ?? 'MANDIRI';
        $bankRek = $programInfo->bank_rek ?? '123456789';
        $bankAtasNama = $programInfo->bank_atas_nama ?? 'Yayasan';
        $hasBank = !empty($programInfo->bank_nama) || !empty($programInfo->bank_rek);
        $isTransfer = ($order->metode === 'transfer' && $hasBank);
        $totalBayar = $order->totalTagihan() > 0 ? $order->totalTagihan() : ($order->total > 0 ? $order->total : ($order->subtotal_kaos + $order->donasi));
    @endphp

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-stone-500 font-medium mb-3" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('design') }}" class="hover:text-primary transition">Koleksi Kaos</a></li>
                <li class="text-stone-300">&gt;</li>
                <li><a href="{{ route('checkout.pemesanan') }}" class="hover:text-primary transition">Pemesanan</a></li>
                <li class="text-stone-300">&gt;</li>
                <li class="text-[#c85a32] font-semibold">Pembayaran</li>
            </ol>
        </nav>

        {{-- Heading & Subtitle --}}
        <div class="mt-2">
            <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight leading-tight">
                Pembayaran Kaos
            </h1>
            <p class="mt-2 text-sm text-stone-600 max-w-2xl leading-relaxed">
                Selesaikan pembayaran setelah total final dikonfirmasi admin.
            </p>
        </div>

        {{-- Stepper (Gambar 1) --}}
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

            {{-- Connector 1-2 --}}
            <span class="h-[1px] w-8 sm:w-16 bg-stone-300"></span>

            {{-- Step 2: Data & Alamat (Checked Green) --}}
            <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-full bg-[#245236] text-[10px] sm:text-xs font-bold text-white shadow-2xs">
                    <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-xs sm:text-sm font-bold text-stone-800">Data &amp; Alamat</span>
            </div>

            {{-- Connector 2-3 --}}
            <span class="h-[1px] w-8 sm:w-16 bg-[#e2ad99]"></span>

            {{-- Step 3: Pembayaran (Active Terracotta 3) --}}
            <div class="flex items-center gap-2">
                <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-full bg-[#c85a32] text-[10px] sm:text-xs font-bold text-white shadow-2xs">
                    3
                </span>
                <span class="text-xs sm:text-sm font-bold text-stone-800">Pembayaran</span>
            </div>
        </div>

        {{-- Main Grid Content --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            {{-- Left Column: Cards --}}
            <div class="space-y-6 lg:col-span-2">
                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50/80 p-5">
                        <p class="font-bold text-red-800 text-sm">Mohon periksa kembali isian Anda:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Notice Banner --}}
                <div class="flex items-start gap-3.5 rounded-2xl border border-[#d6e5d8] bg-[#edf4ee] p-4 sm:p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#dbeadd] text-[#245236]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-[#1b3d26]">Menunggu konfirmasi admin</h2>
                        <p class="mt-0.5 text-xs sm:text-sm text-[#2e5239] leading-relaxed">
                            Biaya pengiriman dan total akhir akan dikonfirmasi melalui WhatsApp sebelum Anda melakukan pembayaran.
                        </p>
                    </div>
                </div>

                {{-- QRIS & Transfer Bank Card (Menyesuaikan dengan pilihan metode) --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 sm:p-7 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-100 pb-4 mb-5">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base sm:text-lg font-bold text-stone-900" id="payment-box-title">
                                    {{ $isTransfer ? 'Transfer Bank' : 'QRIS' }}
                                </h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf4ec] px-2.5 py-0.5 text-[11px] font-bold text-[#245236] border border-[#245236]/20">
                                    Metode Dipilih
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-0.5">Selesaikan pembayaran sesuai instruksi di bawah ini.</p>
                        </div>

                        @if ($hasBank)
                            <div class="inline-flex rounded-xl bg-stone-100 p-1 border border-stone-200/70" role="tablist">
                                <button type="button" id="tab-btn-qris" data-method="qris"
                                    class="tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer {{ !$isTransfer ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                                    <svg class="h-3.5 w-3.5 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="6" height="6" x="3" y="3" rx="1" /><rect width="6" height="6" x="15" y="3" rx="1" /><rect width="6" height="6" x="3" y="15" rx="1" />
                                        <path d="M14 14.5h2.5v2.5H14zM18.5 14.5H21v2.5h-2.5zM14 19h2.5v2.5H14zM18.5 19H21v2.5h-2.5z" fill="currentColor" stroke="none" />
                                    </svg>
                                    QRIS
                                </button>
                                <button type="button" id="tab-btn-transfer" data-method="transfer"
                                    class="tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer {{ $isTransfer ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                                    <svg class="h-3.5 w-3.5 text-[#245236]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 8h18l-9-5Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 9v8m4-8v8m6-8v8m4-8v8M3 17h18M3 21h18" />
                                    </svg>
                                    Transfer Bank
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- QRIS Section --}}
                    <div id="view-qris" class="{{ $isTransfer ? 'hidden' : '' }}">
                        <h3 class="text-sm font-bold text-stone-900 mb-2">Pembayaran via QRIS</h3>
                        @if (!empty($programInfo->qris_image_url))
                            <div class="mt-2 flex flex-col items-center justify-center rounded-xl border border-stone-200 bg-[#faf9f6] p-6 text-center">
                                <img src="{{ $programInfo->qris_image_url }}" alt="QRIS Merchant" class="max-h-64 w-auto rounded-lg object-contain">
                                <p class="mt-3 text-xs font-bold uppercase tracking-wider text-stone-600">Scan to Pay</p>
                            </div>
                        @else
                            <div class="mt-2 rounded-xl border-2 border-dashed border-stone-200 bg-[#faf9f6] py-10 px-6 text-center">
                                <svg class="mx-auto h-10 w-10 text-stone-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                                <p class="text-sm font-bold text-stone-800 leading-snug">
                                    QRIS merchant akan tersedia<br>setelah konfirmasi admin
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Transfer Bank Section --}}
                    @if ($hasBank)
                        <div id="view-transfer" class="{{ $isTransfer ? '' : 'hidden' }}">
                            <span class="sr-only">Atau Transfer Bank</span>
                            <h3 class="text-sm font-bold text-stone-900 mb-2">Rekening Transfer Bank</h3>
                            <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-4 rounded-xl bg-[#eef5f0] p-4 sm:p-5 border border-[#d6e5d8]">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#245236] text-white">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 3 8h18l-9-5Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 9v8m4-8v8m6-8v8m4-8v8M3 17h18M3 21h18" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-extrabold text-stone-900 tracking-wide uppercase">{{ $bankNama }}</h4>
                                        <p class="text-xs text-stone-500 mt-0.5">a.n. {{ $bankAtasNama }}</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right w-full sm:w-auto">
                                    <p class="text-xl font-extrabold text-stone-900 tracking-wider tabular-nums">{{ $bankRek }}</p>
                                    <div class="mt-1 flex sm:justify-end">
                                        <button type="button" data-copy-rek="{{ $bankRek }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200/90 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50 cursor-pointer">
                                            <svg class="h-3.5 w-3.5 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                            </svg>
                                            <span class="copy-text">Salin nomor</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Cara Pembayaran --}}
                    <div class="mt-6 pt-5 border-t border-stone-100">
                        <h3 class="text-sm sm:text-base font-bold text-stone-900 mb-3">Cara pembayaran:</h3>
                        <ol class="space-y-2.5 text-xs sm:text-sm text-stone-700">
                            <li class="flex items-start gap-3">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#245236] text-[11px] font-bold text-white mt-0.5">
                                    1
                                </span>
                                <span>Tunggu konfirmasi total akhir dari admin.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#245236] text-[11px] font-bold text-white mt-0.5">
                                    2
                                </span>
                                <span id="step-instruction-text">
                                    {{ $isTransfer ? 'Lakukan transfer ke rekening bank di atas.' : 'Lakukan pembayaran menggunakan scan QRIS di atas.' }}
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#245236] text-[11px] font-bold text-white mt-0.5">
                                    3
                                </span>
                                <span>Unggah bukti pembayaran.</span>
                            </li>
                        </ol>
                    </div>
                </div>

                {{-- Unggah Bukti Pembayaran Card --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 sm:p-7 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#edf5ee] text-[#245236]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <h2 class="font-serif text-xl font-bold text-stone-900">Unggah Bukti Pembayaran</h2>
                    </div>

                    <form action="{{ route('checkout.bayar.store', $order) }}" method="POST" enctype="multipart/form-data" class="mt-5">
                        @csrf
                        <input type="hidden" name="metode" id="input-metode-bayar" value="{{ $isTransfer ? 'transfer' : 'qris' }}">
                        <input type="file" id="bukti" name="bukti" accept="image/jpeg,image/png,image/webp,application/pdf" class="sr-only" required>

                        <label for="bukti" id="dropzone-box"
                            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-200 bg-[#faf9f6] p-8 text-center cursor-pointer transition hover:border-[#c85a32]/60">
                            <svg class="h-8 w-8 text-stone-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            <span class="text-sm font-bold text-stone-800">Pilih atau tarik file ke sini</span>
                            <span class="mt-1 text-xs text-stone-400">JPG, PNG, PDF - Maksimal 5 MB</span>
                            <span id="nama-file" class="mt-2 text-xs font-semibold text-[#c85a32] hidden"></span>
                            <span class="mt-3 inline-block rounded-lg bg-[#c85a32] px-5 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#b04b25]">
                                Pilih File
                            </span>
                        </label>
                        @error('bukti')
                            <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <button type="submit" id="btn-kirim-bukti"
                            class="mt-5 w-full rounded-xl bg-[#c85a32] py-3.5 text-sm font-bold text-white shadow-xs transition hover:bg-[#b04b25] cursor-pointer">
                            Kirim Bukti Pembayaran
                        </button>

                        <div class="mt-3 flex items-center justify-center gap-1.5 text-xs text-stone-400">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                            </svg>
                            <span>Pembayaran akan diverifikasi admin secara manual.</span>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Order Summary & Help --}}
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

                    <div class="mt-4 space-y-2.5 border-t border-stone-100 pt-3 text-xs">
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Jumlah</span>
                            <span class="font-medium text-stone-900">{{ $totalQty }} kaos</span>
                        </div>
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span class="font-bold text-stone-900">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->donasi > 0)
                            <div class="flex items-center justify-between text-stone-600">
                                <span>Donasi sukarela</span>
                                <span class="font-bold text-stone-900">Rp {{ number_format($order->donasi, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Ongkir</span>
                            @if ($order->ongkir > 0)
                                <span class="font-bold text-stone-900">Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                            @else
                                <span class="font-semibold text-[#c85a32]">Menunggu konfirmasi</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-stone-600">
                            <span>Metode</span>
                            <span id="sidebar-metode-name" class="font-bold text-stone-900">{{ $isTransfer ? 'Transfer Bank' : 'QRIS' }}</span>
                        </div>
                    </div>

                    {{-- Total Box --}}
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-[#eaf4ec] p-3.5 sm:p-4">
                        <span class="text-xs sm:text-sm font-medium text-stone-700">Total</span>
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

@push('scripts')
    <script>
        (function () {
            // Tab switch QRIS / Transfer Bank
            var tabQris = document.getElementById('tab-btn-qris');
            var tabTransfer = document.getElementById('tab-btn-transfer');
            var viewQris = document.getElementById('view-qris');
            var viewTransfer = document.getElementById('view-transfer');
            var titleBox = document.getElementById('payment-box-title');
            var instructionText = document.getElementById('step-instruction-text');
            var inputMetode = document.getElementById('input-metode-bayar');
            var sidebarMetode = document.getElementById('sidebar-metode-name');

            function setMethod(method) {
                if (method === 'transfer') {
                    if (viewQris) viewQris.classList.add('hidden');
                    if (viewTransfer) viewTransfer.classList.remove('hidden');
                    if (titleBox) titleBox.textContent = 'Transfer Bank';
                    if (instructionText) instructionText.textContent = 'Lakukan transfer ke rekening bank di atas.';
                    if (sidebarMetode) sidebarMetode.textContent = 'Transfer Bank';
                    if (inputMetode) inputMetode.value = 'transfer';

                    if (tabTransfer) {
                        tabTransfer.className = 'tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer bg-white text-stone-900 shadow-2xs';
                    }
                    if (tabQris) {
                        tabQris.className = 'tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-stone-900';
                    }
                } else {
                    if (viewTransfer) viewTransfer.classList.add('hidden');
                    if (viewQris) viewQris.classList.remove('hidden');
                    if (titleBox) titleBox.textContent = 'QRIS';
                    if (instructionText) instructionText.textContent = 'Lakukan pembayaran menggunakan scan QRIS di atas.';
                    if (sidebarMetode) sidebarMetode.textContent = 'QRIS';
                    if (inputMetode) inputMetode.value = 'qris';

                    if (tabQris) {
                        tabQris.className = 'tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer bg-white text-stone-900 shadow-2xs';
                    }
                    if (tabTransfer) {
                        tabTransfer.className = 'tab-btn inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition cursor-pointer text-stone-500 hover:text-stone-900';
                    }
                }
            }

            if (tabQris) {
                tabQris.addEventListener('click', function () {
                    setMethod('qris');
                });
            }
            if (tabTransfer) {
                tabTransfer.addEventListener('click', function () {
                    setMethod('transfer');
                });
            }

            // Salin nomor rekening
            document.querySelectorAll('[data-copy-rek]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var rek = this.getAttribute('data-copy-rek');
                    if (!rek) return;
                    navigator.clipboard.writeText(rek).then(function() {
                        var label = btn.querySelector('.copy-text');
                        if (label) {
                            var original = label.textContent;
                            label.textContent = 'Tersalin!';
                            setTimeout(function() { label.textContent = original; }, 2000);
                        }
                    });
                });
            });

            // File upload & dropzone feedback
            var input = document.getElementById('bukti');
            var nama = document.getElementById('nama-file');
            var dropzone = document.getElementById('dropzone-box');

            function updateFileInfo(file) {
                if (!file) return;
                if (nama) {
                    nama.textContent = file.name;
                    nama.classList.remove('hidden');
                }
            }

            if (input) {
                input.addEventListener('change', function () {
                    if (input.files.length) {
                        updateFileInfo(input.files[0]);
                    }
                });
            }

            if (dropzone && input) {
                ['dragenter', 'dragover'].forEach(function(evt) {
                    dropzone.addEventListener(evt, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-[#c85a32]');
                    });
                });
                ['dragleave', 'drop'].forEach(function(evt) {
                    dropzone.addEventListener(evt, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-[#c85a32]');
                    });
                });
                dropzone.addEventListener('drop', function(e) {
                    if (e.dataTransfer && e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        updateFileInfo(e.dataTransfer.files[0]);
                    }
                });
            }
        })();
    </script>
@endpush
