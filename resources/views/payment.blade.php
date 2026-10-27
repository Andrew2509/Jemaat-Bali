 @extends('layouts.app')

@section('title', 'Pembayaran — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Pembayaran donasi ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': QRIS manual dan unggah bukti pembayaran.')

@section('content')
    @php
        $nomor = $order->nomor_pesanan ? '#' . $order->nomor_pesanan : '#' . $order->id;
        $totalSementara = (int) $order->subtotal_kaos + (int) $order->donasi;
    @endphp

    <section class="border-b border-line bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('design') }}" class="flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-primary font-display text-xs font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                <span class="leading-tight">
                    <span class="block font-display text-sm font-extrabold tracking-tight text-ink">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                    <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-muted">Merch Resmi &middot; Edisi {{ date('Y') }}</span>
                </span>
            </a>
            <a href="{{ route('shop') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
                &larr; Kembali ke Shop + Donasi
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <nav class="text-xs font-semibold uppercase tracking-widest text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('shop') }}" class="hover:text-primary">Shop + Donasi</a></li>
                <li>&gt;</li>
                <li><a href="{{ route('shop') }}" class="hover:text-primary">Data &amp; Donasi</a></li>
                <li>&gt;</li>
                <li class="text-ink">Pembayaran</li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <h1 class="font-display text-3xl font-extrabold uppercase tracking-tight sm:text-4xl">Pembayaran Donasi</h1>
            <span class="rounded-full bg-primary/10 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-widest text-primary">QRIS Manual</span>
        </div>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-muted">
            Bayar sesuai total final yang dikonfirmasi panitia, lalu unggah bukti pembayaran untuk verifikasi manual.
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-bold uppercase tracking-[0.18em]">
            <span class="text-muted">01 / Pemesanan</span>
            <span class="text-line">&mdash;</span>
            <span class="text-primary">02 / Pembayaran</span>
            <span class="text-line">&mdash;</span>
            <span class="text-muted">03 / Verifikasi Manual</span>
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

                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">01</span>
                        <h2 class="font-serif text-lg font-bold">Bayar dengan QRIS</h2>
                    </div>

                    <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-primary/5 p-4 text-sm text-ink">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                        <p>
                            <span class="font-semibold">Menunggu konfirmasi ongkir &amp; total final.</span><br>
                            Pembayaran belum dapat dilakukan. Tunggu total final dan QRIS merchant dari admin melalui WhatsApp.
                        </p>
                    </div>

                    @if(!empty($programInfo->qris_code))
                        <div class="mt-5 mx-auto w-full max-w-[280px]" data-qris-wrapper>
                            <div class="relative aspect-square w-full overflow-hidden rounded-2xl border border-line bg-white p-3 shadow-sm">
                                <div class="grid h-full w-full place-items-center rounded-xl bg-canvas px-4 text-center text-xs font-medium text-muted" data-qris-placeholder>Memuat QRIS&hellip;</div>
                                <img data-qris-code="{{ $programInfo->qris_code }}" alt="QRIS Merchant {{ $programInfo->nama_program ?? 'Jemaat Bali' }}" class="hidden absolute inset-0 m-auto h-full w-full rounded-2xl object-contain p-3">
                            </div>
                            <p class="mt-2 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-muted">QRIS &middot; Scan to Pay</p>
                        </div>
                        <p class="mt-4 text-xs leading-relaxed text-muted">
                            Pindai QRIS di atas dengan aplikasi bank atau e-wallet. Periksa nama merchant dan bayar sesuai total final dari admin.
                        </p>
                    @elseif(!empty($programInfo->qris_image_url))
                        <div class="mt-5 mx-auto w-full max-w-[280px] overflow-hidden rounded-2xl border border-line bg-white p-3 shadow-sm">
                            <img src="{{ $programInfo->qris_image_url }}" alt="QRIS Merchant {{ $programInfo->nama_program ?? 'Jemaat Bali' }}" class="w-full rounded-xl object-contain">
                        </div>
                        <p class="mt-4 text-xs leading-relaxed text-muted">
                            Pindai QRIS di atas dengan aplikasi bank atau e-wallet. Periksa nama merchant dan bayar sesuai total final dari admin.
                        </p>
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
                        <div class="mt-5 rounded-2xl border border-line bg-canvas p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted">Atau Transfer Bank</p>
                            <div class="mt-2 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                @if (!empty($programInfo->bank_nama))
                                    <span class="font-serif text-base font-bold text-ink">{{ $programInfo->bank_nama }}</span>
                                @endif
                                @if (!empty($programInfo->bank_rek))
                                    <span class="font-mono text-lg font-bold tracking-wide text-ink">{{ $programInfo->bank_rek }}</span>
                                @endif
                            </div>
                            @if (!empty($programInfo->bank_atas_nama))
                                <p class="mt-1 text-xs text-muted">a.n. {{ $programInfo->bank_atas_nama }}</p>
                            @endif
                        </div>
                    @endif

                    <h3 class="mt-8 font-serif text-base font-bold">Cara pembayaran manual</h3>
                    <ol class="mt-4 space-y-4">
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">1</span>
                            <div>
                                <p class="text-sm font-semibold text-ink">Tunggu konfirmasi total final</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted">Admin mengonfirmasi pesanan dan ongkir melalui WhatsApp. Jangan gunakan total sementara sebagai nominal pembayaran.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">2</span>
                            <div>
                                <p class="text-sm font-semibold text-ink">Bayar melalui QRIS merchant</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted">Setelah QRIS tersedia, pindai dengan aplikasi bank atau e-wallet. Periksa nama merchant dan bayar sesuai total final dari admin.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">3</span>
                            <div>
                                <p class="text-sm font-semibold text-ink">Simpan dan kirim bukti pembayaran</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted">Unggah bukti transaksi yang jelas, lalu kirim untuk verifikasi manual. Pembayaran tidak otomatis berstatus lunas.</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">02</span>
                        <h2 class="font-serif text-lg font-bold">Unggah bukti pembayaran</h2>
                    </div>

                    <form action="{{ route('shop.payment.store', $order) }}" method="POST" enctype="multipart/form-data" class="mt-6">
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
                            Kirim Bukti Pembayaran &rarr;
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
                @if (!$order->isDonasiOnly())
                <div class="card overflow-hidden !p-0">
                    <div class="relative aspect-[4/3] bg-stone-100">
                        @php($firstItem = $order->items_list[0] ?? null)
                        @php($itemDesign = $firstItem ? \App\Models\Design::find($firstItem['design_id']) : null)
                        @if ($itemDesign && $itemDesign->gambar)
                            <img src="{{ asset($itemDesign->gambar) }}" alt="{{ $firstItem['nama'] ?? 'Kaos' }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset($order->design->gambar) }}" alt="{{ $order->design->nama }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="p-6">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted">{{ $nomor }} &middot; Ringkasan Pesanan</p>
                        @if ($firstItem)
                            <h2 class="mt-2 font-display text-lg font-bold leading-snug">{{ $firstItem['nama'] }}</h2>
                            <p class="mt-1.5 text-sm text-muted">{{ count($order->items_list) }} {{ count($order->items_list) > 1 ? 'item' : 'kaos' }}</p>
                            <p class="mt-2 font-display text-sm font-bold text-ink">Rp {{ number_format($firstItem['harga'], 0, ',', '.') }} <span class="font-sans text-xs font-medium text-muted">/ pcs</span></p>
                        @else
                            <h2 class="mt-2 font-display text-lg font-bold leading-snug">{{ $order->design->nama }}</h2>
                            <p class="mt-2 font-display text-sm font-bold text-ink">Rp {{ number_format($order->design->harga, 0, ',', '.') }} <span class="font-sans text-xs font-medium text-muted">/ kaos</span></p>
                        @endif

                        <div class="mt-4 flex items-center justify-between border-t border-line pt-4 text-sm">
                            <span class="text-muted">Total qty</span>
                            <span class="font-semibold">{{ collect($order->items_list)->sum('jumlah') }} kaos</span>
                        </div>
                @else
                <div class="card overflow-hidden !p-0">
                    <div class="p-6">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted">{{ $nomor }} &middot; Ringkasan Donasi</p>
                        <h2 class="mt-2 font-display text-lg font-bold leading-snug">Donasi Saja</h2>
                        <p class="mt-1.5 text-sm text-muted">Donasi sukarela untuk program pembangunan Jemaat Bali.</p>
                @endif

                        <div class="mt-5 border-t border-line pt-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Rincian pembayaran</p>
                            <dl class="mt-3 space-y-2.5 text-sm">
                                @if ($order->design)
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Subtotal produk ({{ collect($order->items_list)->sum('jumlah') }} kaos)</dt>
                                    <dd class="font-semibold">Rp {{ number_format($order->subtotal_kaos, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Ongkir</dt>
                                    <dd class="font-semibold">Belum dihitung</dd>
                                </div>
                                @endif
                                @if ($order->donasi > 0)
                                    <div class="flex items-start justify-between gap-3">
                                        <dt class="text-muted">Donasi</dt>
                                        <dd class="font-semibold">Rp {{ number_format($order->donasi, 0, ',', '.') }}</dd>
                                    </div>
                                @endif
                            </dl>
                            <div class="mt-4 flex items-center justify-between border-t border-line pt-4">
                                <span class="font-serif font-bold">Total sementara</span>
                                <span class="font-serif text-xl font-extrabold text-primary">Rp {{ number_format($totalSementara, 0, ',', '.') }}</span>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-muted">
                                Belum termasuk ongkir. Total akhir akan dikonfirmasi sebelum pembayaran.
                            </p>
                        </div>

                        <div class="mt-5 rounded-xl border border-line bg-canvas p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">Total Final</p>
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
