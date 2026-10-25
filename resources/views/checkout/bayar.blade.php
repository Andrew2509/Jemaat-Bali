@extends('layouts.app')

@section('title', 'Pembayaran Kaos — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pembayaran pesanan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': QRIS manual dan unggah bukti pembayaran.')

@section('content')
    @php
        $nomor = $order->nomor_pesanan ? '#' . $order->nomor_pesanan : '#' . $order->id;
        $totalSementara = (int) $order->subtotal_kaos;
        $waLink = $programInfo->wa_url ?? '#';
    @endphp

    <section class="border-b border-line bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('checkout.pemesanan') }}" class="flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-primary font-display text-xs font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                <span class="leading-tight">
                    <span class="block font-display text-sm font-extrabold tracking-tight text-ink">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                    <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-muted">Merch Resmi &middot; Edisi {{ date('Y') }}</span>
                </span>
            </a>
            <a href="{{ route('checkout.pemesanan') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
                &larr; Kembali ke pemesanan
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
                <li class="text-ink">Pembayaran</li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <h1 class="font-display text-3xl font-extrabold uppercase tracking-tight sm:text-4xl">Pembayaran Kaos</h1>
            <span class="rounded-full bg-primary/10 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-widest text-primary">QRIS Manual</span>
        </div>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-muted">
            Bayar setelah total final dikonfirmasi, lalu unggah bukti pembayaran untuk verifikasi manual.
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-bold uppercase tracking-[0.18em]">
            <span class="text-muted">01 / Pemesanan</span>
            <span class="text-line">&mdash;</span>
            <span class="text-primary">02 / Pembayaran</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">03 / Verifikasi manual</span>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                @if ($errors->any())
                    <div class="rounded-2xl border border-error/30 bg-error/5 p-5">
                        <p class="font-display font-bold text-error">Mohon periksa kembali isian Anda:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-error">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card relative overflow-hidden p-6 pb-8 sm:p-9 sm:pb-10">
                    {{-- Dekorasi ombak di kiri bawah kartu --}}
                    <svg class="pointer-events-none absolute bottom-0 left-0 h-20 w-60 sm:h-28 sm:w-96" viewBox="0 0 400 120" preserveAspectRatio="none" fill="none" aria-hidden="true">
                        <path d="M0 120 L0 60 C70 30 145 48 215 78 C285 108 335 116 400 119 L400 120 Z" fill="#c7cbb5" fill-opacity=".9" />
                        <path d="M0 120 L0 88 C65 66 135 82 200 104 C265 126 330 122 400 120 Z" fill="#8b9179" />
                    </svg>

                    <div class="relative flex flex-wrap items-start justify-between gap-4">
                        <span class="inline-flex items-center gap-2.5 rounded-full bg-[#4d5642] px-4 py-2.5 text-[11px] font-bold uppercase tracking-[0.18em] text-white shadow-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h4.5v4.5h-4.5V4.5Zm0 10.5h4.5v4.5h-4.5V15Zm10.5-10.5h4.5v4.5h-4.5V4.5Zm0 10.5h4.5v4.5h-4.5V15Zm-6-6h6M9 15h1.5M15 15h.75M15 19.5h1.5M19.5 19.5h.75" />
                            </svg>
                            Bayar dengan QRIS
                        </span>
                        <div class="ml-auto text-right">
                            <p class="font-display text-sm font-extrabold uppercase tracking-wide text-[#2e3427]">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</p>
                            <p class="mt-0.5 text-[9px] font-semibold uppercase tracking-[0.22em] text-[#8a8f7c]">{{ $programInfo->tagline_or_default ?? ($programInfo->tagline ?? '') }}</p>
                        </div>
                    </div>

                    <p class="relative mt-4 text-[15px] leading-relaxed text-[#5f6457]">Pembayaran diproses manual oleh admin Jemaat Bali.</p>

                    <div class="relative mt-5 flex items-start gap-3 rounded-2xl bg-[#efeee6] p-4">
                        <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-[#4d5642] font-serif text-[13px] font-bold italic leading-none text-white">i</span>
                        <p class="text-[15px] leading-relaxed text-[#5f6457]">
                            <span class="font-bold text-[#2e3427]">Menunggu konfirmasi ongkir &amp; total final.</span><br>
                            @if (!empty($programInfo->qris_code) || !empty($programInfo->qris_image_url))
                                Pembayaran belum dapat dilakukan. Tunggu total final dari admin melalui WhatsApp.
                            @else
                                Pembayaran belum dapat dilakukan. Tunggu total final dan QRIS merchant dari admin melalui WhatsApp.
                            @endif
                        </p>
                    </div>

                    @if (!empty($programInfo->qris_image_url))
                        <div class="relative mx-auto mt-6 w-full max-w-[340px] overflow-hidden rounded-2xl border border-line bg-white p-4 shadow-sm">
                            <img src="{{ $programInfo->qris_image_url }}" alt="QRIS Merchant {{ $programInfo->nama_program ?? 'Jemaat Bali' }}" class="w-full rounded-xl object-contain">
                        </div>
                    @elseif (!empty($programInfo->qris_code))
                        <div class="relative mx-auto mt-6 w-full max-w-[340px]" data-qris-wrapper>
                            <div class="relative aspect-square w-full overflow-hidden rounded-2xl border border-line bg-white p-4 shadow-sm">
                                <div class="grid h-full w-full place-items-center rounded-xl bg-canvas px-4 text-center text-xs font-medium text-muted" data-qris-placeholder>Memuat QRIS&hellip;</div>
                                <img data-qris-code="{{ $programInfo->qris_code }}" alt="QRIS Merchant {{ $programInfo->nama_program ?? 'Jemaat Bali' }}" class="hidden absolute inset-0 m-auto h-full w-full rounded-2xl object-contain p-4">
                            </div>
                            <p class="mt-3 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-muted">QRIS &middot; Scan to Pay</p>
                        </div>
                    @else
                        <div class="mt-5 grid aspect-square w-full max-w-[280px] mx-auto place-items-center rounded-2xl border-2 border-dashed border-line bg-canvas p-6">
                            <div class="text-center">
                                <svg class="mx-auto h-10 w-10 text-stone-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008H12V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <p class="mt-3 text-sm font-semibold text-ink">QRIS merchant belum tersedia</p>
                                <p class="mt-1 text-xs text-muted">Bukan kode pembayaran</p>
                            </div>
                        </div>

                        <p class="mt-4 text-xs leading-relaxed text-muted">
                            QRIS asli akan ditampilkan setelah tersedia dari admin. Jangan melakukan pembayaran sebelum dikonfirmasi.
                        </p>
                    @endif

                    {{-- Informasi rekening bank: diisi admin di Konten → Informasi program --}}
                    @if (!empty($programInfo->bank_nama) || !empty($programInfo->bank_rek))
                        <div class="relative mt-6 flex items-center gap-4 rounded-2xl bg-[#eae9e0] p-5 sm:gap-5 sm:p-6">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#4d5642] text-white">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3.7 3.8 8h16.4L12 3.7Z" />
                                    <path d="M5.5 8.7v7M9.5 8.7v7M14.5 8.7v7M18.5 8.7v7" />
                                    <path d="M4 16.6h16M3 19.6h18" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#6f7466]">Atau Transfer Bank</p>
                                <div class="mt-1 flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
                                    @if (!empty($programInfo->bank_nama))
                                        <span class="text-2xl font-extrabold tracking-tight text-[#2e3427]">{{ $programInfo->bank_nama }}</span>
                                    @endif
                                    @if (!empty($programInfo->bank_rek))
                                        <span class="text-2xl font-extrabold tabular-nums tracking-wide text-[#2e3427]">{{ $programInfo->bank_rek }}</span>
                                    @endif
                                </div>
                                @if (!empty($programInfo->bank_atas_nama))
                                    <p class="mt-0.5 text-[15px] text-[#5f6457]">a.n. {{ $programInfo->bank_atas_nama }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="relative mt-9 flex items-center gap-4">
                        <h3 class="font-serif text-xl font-bold text-[#2e3427] sm:text-2xl">Cara pembayaran manual</h3>
                        <span class="hidden h-px flex-1 bg-[#dcd9cc] sm:block"></span>
                    </div>

                    <ol class="relative mt-2">
                        <li class="flex gap-4 border-b border-[#e3e1d6] py-4">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#4d5642] text-sm font-bold text-white">1</span>
                            <div>
                                <p class="text-[15px] font-bold text-[#2e3427]">Tunggu konfirmasi total final</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6c7163]">Admin mengonfirmasi pesanan dan ongkir melalui WhatsApp. Jangan gunakan total sementara sebagai nominal pembayaran.</p>
                            </div>
                        </li>
                        <li class="flex gap-4 border-b border-[#e3e1d6] py-4">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#4d5642] text-sm font-bold text-white">2</span>
                            <div>
                                <p class="text-[15px] font-bold text-[#2e3427]">Bayar melalui QRIS merchant</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6c7163]">Setelah QRIS tersedia, pindai dengan aplikasi bank atau e-wallet. Periksa nama merchant dan bayar sesuai total final dari admin.</p>
                            </div>
                        </li>
                        <li class="flex gap-4 py-4">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#4d5642] text-sm font-bold text-white">3</span>
                            <div>
                                <p class="text-[15px] font-bold text-[#2e3427]">Simpan dan kirim bukti pembayaran</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6c7163]">Unggah bukti transaksi yang jelas, lalu kirim untuk verifikasi manual. Pembayaran tidak otomatis berstatus lunas.</p>
                            </div>
                        </li>
                    </ol>

                    <div class="relative mt-4 flex justify-end">
                        <p class="text-right font-script text-[26px] leading-tight text-[#3d4435]">
                            Terima Kasih<br>
                            <span class="inline-flex items-center gap-2">
                                <span class="relative inline-block">atas Dukungan Anda
                                    <svg class="pointer-events-none absolute -bottom-1.5 left-0 h-2.5 w-full" viewBox="0 0 160 10" preserveAspectRatio="none" fill="none" aria-hidden="true">
                                        <path d="M2 7C40 2 110 1 158 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".7" />
                                    </svg>
                                </span>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8.5c0 4.5-9 10-9 10s-9-5.5-9-10a5 5 0 0 1 9-3 5 5 0 0 1 9 3Z" />
                                </svg>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="card p-6 sm:p-8">
                    <span class="inline-flex items-center gap-2.5 rounded-full bg-[#4d5642] px-4 py-2.5 text-[11px] font-bold uppercase tracking-[0.18em] text-white shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 4.5-4.5M12 16.5l-4.5-4.5" />
                        </svg>
                        Unggah bukti pembayaran
                    </span>

                    <p class="mt-4 text-[15px] leading-relaxed text-[#5f6457]">Bukti pembayaran wajib dikirim setelah transaksi berhasil.</p>

                    <form action="{{ route('checkout.bayar.store', $order) }}" method="POST" enctype="multipart/form-data" class="mt-6">
                        @csrf

                        <div class="flex items-center gap-2">
                            <label for="bukti" class="label !mb-0">Bukti pembayaran QRIS <span class="text-error">*</span></label>
                            <span class="rounded-full bg-error/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-error">Wajib</span>
                        </div>

                        <input type="file" id="bukti" name="bukti" accept="image/jpeg,image/png,image/webp,application/pdf" class="sr-only" required>
                        <label for="bukti" class="dropzone mt-3 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-line bg-canvas px-6 py-10 text-center transition hover:border-primary/50">
                            <svg class="h-9 w-9 text-stone-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            <span class="mt-3 text-sm font-semibold text-ink">Pilih atau tarik file ke sini</span>
                            <span class="mt-1 text-xs text-muted">JPG, PNG, atau PDF &middot; Maksimal 5 MB</span>
                            <span id="nama-file" class="mt-2 text-xs font-medium text-muted">Belum ada file dipilih</span>
                            <span class="mt-4 rounded-full bg-primary px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow-sm transition hover:bg-primary-dark">Pilih File</span>
                        </label>
                        @error('bukti')<p class="error-text">{{ $message }}</p>@enderror

                        <p class="mt-4 text-xs leading-relaxed text-muted">
                            Pastikan nominal, tanggal transaksi, dan nama merchant terlihat jelas. Bukti wajib diunggah sebelum dapat dikirim.
                        </p>

                        <button type="submit" class="btn btn-primary mt-6 w-full !py-3.5 text-sm font-bold uppercase tracking-widest">
                            Kirim bukti pembayaran &rarr;
                        </button>
                        <p class="mt-3 text-center text-xs text-muted">Tombol aktif setelah total final dikonfirmasi dan bukti diunggah.</p>
                    </form>

                    <div class="mt-6 flex items-start gap-2.5 rounded-xl border border-line bg-canvas p-4 text-sm">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                        </svg>
                        <p>
                            <span class="font-semibold">Setelah dikirim: menunggu verifikasi.</span><br>
                            <span class="text-muted">Admin akan memeriksa bukti secara manual. Status belum lunas sampai pembayaran dikonfirmasi melalui WhatsApp.</span>
                        </p>
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
                            <img src="{{ asset($itemDesign->gambar) }}" alt="{{ $firstItem['nama'] ?? 'Kaos' }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset($order->design->gambar) }}" alt="{{ $order->design->nama }}" class="h-full w-full object-cover">
                        @endif
                    </div>

                    <div class="p-6">
                        @if ($firstItem)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                                {{ count($order->items_list) }} {{ count($order->items_list) > 1 ? 'item' : 'kaos' }} &middot; Ringkasan pesanan
                            </span>
                            <h2 class="mt-3 font-serif text-lg font-bold leading-snug">{{ $firstItem['nama'] }}</h2>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ count($order->items_list) }} produk kaos merchandise</p>
                            <p class="mt-2 font-serif text-base font-bold text-ink">
                                Rp {{ number_format($firstItem['harga'], 0, ',', '.') }}
                                <span class="font-sans text-xs font-medium text-muted">/ pcs</span>
                            </p>

                            <div class="mt-4 flex items-center justify-between border-t border-line pt-4 text-sm">
                                <span class="text-muted">Total qty</span>
                                <span class="font-semibold">{{ collect($order->items_list)->sum('jumlah') }} kaos</span>
                            </div>
                        @endif

                        <div class="mt-5 border-t border-line pt-5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-ink">Rincian pembayaran</p>
                            <dl class="mt-3 space-y-2.5 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Subtotal produk ({{ collect($order->items_list)->sum('jumlah') }} kaos)</dt>
                                    <dd class="font-semibold">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Ongkir</dt>
                                    <dd class="font-semibold text-muted">Belum dihitung</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex items-center justify-between border-t border-line pt-4">
                                <span class="font-serif font-bold">Total sementara</span>
                                <span class="font-serif text-2xl font-extrabold text-primary">Rp {{ number_format($totalSementara, 0, ',', '.') }}</span>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-muted">
                                Belum termasuk ongkir. Total akhir akan dikonfirmasi sebelum pembayaran.
                            </p>
                        </div>

                        <div class="mt-5 rounded-xl border border-line bg-canvas p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Total final</p>
                            <p class="mt-1.5 font-serif text-base font-bold text-ink">Menunggu konfirmasi admin</p>
                            <p class="mt-1 text-xs leading-relaxed text-muted">
                                Nominal pembayaran akan disampaikan melalui WhatsApp setelah ongkir dihitung.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card mt-6 p-6">
                    <p class="font-serif text-base font-bold">Butuh bantuan pembayaran?</p>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        Hubungi admin melalui percakapan WhatsApp konfirmasi pesanan Anda. Jangan membayar ke QRIS yang belum dikonfirmasi.
                    </p>
                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-outline mt-4 w-full">Hubungi via WhatsApp</a>
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.getElementById('bukti');
            var nama = document.getElementById('nama-file');
            if (!input || !nama) return;
            input.addEventListener('change', function () {
                nama.textContent = input.files.length ? input.files[0].name : 'Belum ada file dipilih';
                nama.classList.toggle('text-primary', input.files.length > 0);
            });
        })();
    </script>
@endpush
