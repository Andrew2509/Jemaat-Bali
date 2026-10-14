@extends('layouts.app')

@section('title', 'Pemesanan Kaos — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Selesaikan pemesanan kaos ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ': data pemesan, alamat pengiriman, dan metode pembayaran.')

@section('content')
    @php
        $subtotal = (int) $selected->harga * (int) old('jumlah', 1);
        $penerimaSama = old('penerima_sama', true);
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
                <li class="text-ink">Pemesanan</li>
            </ol>
        </nav>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-serif text-3xl font-extrabold uppercase tracking-tight sm:text-4xl">Selesaikan Pemesanan</h1>
                <p class="mt-3 max-w-2xl text-base leading-relaxed text-muted">
                    Kaos pilihan Anda sudah siap. Lengkapi data pemesan, alamat pengiriman, dan metode pembayaran.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2">
                @if ($tipe === 'kaos_donasi')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 border border-primary/25 px-4 py-2 text-xs font-bold uppercase tracking-wider text-primary shadow-xs">
                        <span>✦ Mode: Kaos + Donasi</span>
                    </span>
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => 'kaos_saja']) }}" class="text-xs text-muted hover:text-stone-900 underline font-medium">
                        Ganti: Kaos Saja
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-4 py-2 text-xs font-bold uppercase tracking-wider text-stone-800 shadow-xs">
                        <span>Mode: Beli Kaos Saja</span>
                    </span>
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => 'kaos_donasi']) }}" class="text-xs text-primary hover:text-primary-dark underline font-bold">
                        + Tambah Donasi
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="lg:col-span-2 space-y-8">
                @csrf
                <input type="hidden" name="design_id" value="{{ old('design_id', $selected->id) }}">

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

                {{-- 01 Data pemesan --}}
                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">01</span>
                        <div>
                            <h2 class="font-serif text-lg font-bold">Data pemesan</h2>
                            <p class="text-sm text-muted">Gunakan nomor WhatsApp aktif untuk kabar pesanan Anda.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="nama" class="label">Nama lengkap <span class="text-error">*</span></label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Nama sesuai penerima"
                                class="input @error('nama') input-error @enderror" required>
                            @error('nama')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nomor" class="label">Nomor WhatsApp <span class="text-error">*</span></label>
                            <input type="tel" id="nomor" name="nomor" value="{{ old('nomor') }}" placeholder="Contoh: 0812 3456 7890"
                                class="input @error('nomor') input-error @enderror" required>
                            @error('nomor')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="label">Alamat email <span class="font-normal text-muted">(opsional)</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                                class="input @error('email') input-error @enderror">
                            @error('email')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- 02 Informasi pengiriman --}}
                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">02</span>
                        <div>
                            <h2 class="font-serif text-lg font-bold">Informasi pengiriman</h2>
                            <p class="text-sm text-muted">Pastikan alamat lengkap agar kaos sampai ke tujuan yang tepat.</p>
                        </div>
                    </div>

                    <label class="mt-5 flex cursor-pointer items-center gap-2.5 text-sm text-ink">
                        <input type="checkbox" name="penerima_sama" value="1" class="h-4 w-4 rounded border-line text-primary focus:ring-primary/40" @checked($penerimaSama)>
                        Nama dan nomor penerima sama dengan pemesan
                    </label>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="alamat" class="label">Alamat lengkap <span class="text-error">*</span></label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan"
                                class="input @error('alamat') input-error @enderror">{{ old('alamat') }}</textarea>
                            @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="provinsi" class="label">Provinsi <span class="text-error">*</span></label>
                            <select id="provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" required>
                                <option value="">Pilih provinsi</option>
                                @foreach ($provinsi as $p)
                                    <option value="{{ $p }}" @selected(old('provinsi') === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('provinsi')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="kabupaten" class="label">Kota / Kabupaten <span class="text-error">*</span></label>
                            <input type="text" id="kabupaten" name="kabupaten" value="{{ old('kabupaten') }}" placeholder="Pilih kota / kabupaten"
                                class="input @error('kabupaten') input-error @enderror" required>
                            @error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="kecamatan" class="label">Kecamatan <span class="text-error">*</span></label>
                            <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Pilih kecamatan"
                                class="input @error('kecamatan') input-error @enderror" required>
                            @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="kode_pos" class="label">Kode pos <span class="text-error">*</span></label>
                            <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="Masukkan kode pos"
                                class="input @error('kode_pos') input-error @enderror" required>
                            @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-primary/5 p-4 text-sm text-ink">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                        <p><span class="font-semibold">Ongkir belum dihitung.</span> Pilihan pengiriman dan ongkir akan dikonfirmasi setelah alamat lengkap diterima.</p>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-3">
                        <div>
                            <label for="ukuran" class="label">Ukuran <span class="text-error">*</span></label>
                            <select id="ukuran" name="ukuran" class="input @error('ukuran') input-error @enderror" required>
                                <option value="">— Pilih —</option>
                                @foreach ($ukuran as $u)
                                    <option value="{{ $u }}" @selected(old('ukuran') === $u || request('ukuran') === $u)>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('ukuran')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="jumlah" class="label">Jumlah <span class="text-error">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" min="1" max="100" step="1"
                                value="{{ old('jumlah', request('jumlah', 1)) }}" class="input @error('jumlah') input-error @enderror" required>
                            @error('jumlah')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="catatan" class="label">Catatan <span class="font-normal text-muted">(opsional)</span></label>
                            <input type="text" id="catatan" name="catatan" value="{{ old('catatan') }}" placeholder="Catatan pesanan"
                                class="input @error('catatan') input-error @enderror">
                            @error('catatan')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- 03 Donasi Sukarela (Khusus Mode Kaos + Donasi atau Opsional) --}}
                @if ($tipe === 'kaos_donasi')
                    <div class="card p-6 sm:p-8 border-2 border-primary/20 bg-primary/[0.02]">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">03</span>
                                <div>
                                    <h2 class="font-serif text-lg font-bold text-stone-900">Donasi Sukarela Pembangunan</h2>
                                    <p class="text-sm text-muted">Donasi Anda disalurkan 100% untuk percepatan pembangunan Jemaat Bali.</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-primary/10 px-3 py-1 text-[11px] font-bold text-primary">Sukarela</span>
                        </div>

                        <div class="mt-6">
                            <label for="donasi" class="label">Nominal Donasi Tambahan (Rp)</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <input type="number" id="donasi" name="donasi" min="0" step="5000"
                                        value="{{ old('donasi', 50000) }}" placeholder="Contoh: 50000"
                                        class="input @error('donasi') input-error @enderror">
                                    @error('donasi')<p class="error-text">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach ([25000, 50000, 100000, 250000] as $dn)
                                        <button type="button" data-donasi="{{ $dn }}"
                                            class="chip-donasi rounded-full border border-stone-300 bg-white px-3 py-1.5 text-xs font-semibold text-stone-800 transition hover:border-primary hover:text-primary hover:bg-primary/5 cursor-pointer">
                                            +Rp {{ number_format($dn, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                    <button type="button" data-donasi="0"
                                        class="chip-donasi rounded-full border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-500 transition hover:text-stone-800 cursor-pointer">
                                        Tanpa Donasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 text-xs text-stone-600 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span>Anda memilih <b>Beli Kaos Saja</b> (tanpa donasi tambahan).</span>
                        </div>
                        <a href="{{ request()->fullUrlWithQuery(['tipe' => 'kaos_donasi']) }}" class="shrink-0 font-bold text-primary hover:underline">
                            + Mau tambah donasi sukarela?
                        </a>
                    </div>
                @endif

                {{-- {{ $tipe === 'kaos_donasi' ? '04' : '03' }} Pembayaran --}}
                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary font-display text-sm font-extrabold text-white">{{ $tipe === 'kaos_donasi' ? '04' : '03' }}</span>
                        <div>
                            <h2 class="font-serif text-lg font-bold">Pembayaran</h2>
                            <p class="text-sm text-muted">Pembayaran akan dilanjutkan ke halaman terpisah setelah data pemesan dan pengiriman dikonfirmasi.</p>
                        </div>
                    </div>

                    <label class="mt-5 flex items-start gap-3 rounded-xl border-2 border-primary bg-primary/5 p-4 cursor-pointer">
                        <input type="radio" name="metode" value="qris" class="mt-1 h-4 w-4 border-line text-primary focus:ring-primary/40" @checked(old('metode', 'qris') === 'qris') required>
                        <span class="flex-1">
                            <span class="block font-serif font-bold text-ink">QRIS Manual</span>
                            <span class="mt-0.5 block text-sm leading-relaxed text-muted">Pembayaran dilakukan di halaman berikutnya setelah data pemesan dan pengiriman dikonfirmasi.</span>
                        </span>
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h4.5v4.5h-4.5V4.5Zm0 10.5h4.5v4.5h-4.5V15Zm10.5-10.5h4.5v4.5h-4.5V4.5Zm0 10.5h2.25M15 15h2.25v2.25H15V15Zm4.5 0h.75M15 19.5h1.5M19.5 19.5h.75" />
                        </svg>
                    </label>
                    @error('metode')<p class="error-text mt-2">{{ $message }}</p>@enderror

                    <p class="mt-4 text-xs leading-relaxed text-muted">
                        Ongkir akan dikonfirmasi panitia melalui WhatsApp setelah alamat Anda diverifikasi.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between lg:hidden">
                    <a href="{{ route('design') }}" class="btn btn-outline">Kembali ke koleksi</a>
                    <button type="submit" class="btn btn-primary !px-7 !py-3.5">Lanjut ke Pembayaran</button>
                </div>
            </form>

            <aside class="lg:col-span-1">
                <div class="card sticky top-24 !p-0 overflow-hidden">
                    <div class="relative aspect-[4/3] bg-stone-100">
                        <span class="absolute left-3 top-3 rounded-full bg-stone-900/90 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-xs">Edisi 2026</span>
                        <span class="absolute right-3 top-3 text-[10px] font-bold uppercase tracking-widest text-white/90 drop-shadow">Jemaat Bali</span>
                        <img src="{{ asset($selected->gambar) }}" alt="{{ $selected->nama }}" class="h-full w-full object-cover">
                    </div>

                    <div class="p-6">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                            #{{ $selected->id }} &middot; Produk pilihan anda
                        </span>
                        <h2 class="mt-3 font-serif text-lg font-bold leading-snug">{{ $selected->nama }}</h2>
                        <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $selected->deskripsi }}</p>
                        <p class="mt-2 font-serif text-base font-bold text-ink">
                            Rp {{ number_format($selected->harga, 0, ',', '.') }}
                            <span class="font-sans text-xs font-medium text-muted">/ kaos</span>
                        </p>

                        <div class="mt-4 flex items-center justify-between border-t border-line pt-4 text-sm">
                            <span class="text-muted">Ukuran</span>
                            <span class="grid h-7 min-w-7 place-items-center rounded-md bg-stone-900 px-2 text-xs font-bold text-white" id="sum-ukuran">—</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-muted">Jumlah</span>
                            <span class="font-semibold" id="sum-jumlah">1 kaos</span>
                        </div>

                        <div class="mt-5 border-t border-line pt-5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-ink">Ringkasan pembayaran</p>
                            <dl class="mt-3 space-y-2.5 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Subtotal produk (<span id="sum-label-jumlah">1</span> kaos)</dt>
                                    <dd class="font-semibold">Rp <span id="sum-subtotal">{{ number_format($subtotal, 0, ',', '.') }}</span></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3 {{ $tipe === 'kaos_donasi' ? '' : 'hidden' }}" id="row-sum-donasi">
                                    <dt class="text-muted">Donasi sukarela</dt>
                                    <dd class="font-semibold text-primary">Rp <span id="sum-donasi">0</span></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-muted">Ongkir</dt>
                                    <dd class="font-semibold text-muted">Belum dihitung</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex items-center justify-between border-t border-line pt-4">
                                <span class="font-serif font-bold">Total sementara</span>
                                <span class="font-serif text-2xl font-extrabold text-primary">Rp <span id="sum-total">{{ number_format($subtotal, 0, ',', '.') }}</span></span>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-muted">
                                Belum termasuk ongkir. Total akhir akan dikonfirmasi sebelum pembayaran.
                            </p>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary mt-5 w-full !py-3.5 text-sm font-bold uppercase tracking-widest">
                            Lanjut ke pembayaran &rarr;
                        </button>
                        <p class="mt-3 text-xs leading-relaxed text-muted">
                            Periksa kembali data pemesan dan pengiriman. Langkah berikutnya adalah halaman pembayaran terpisah.
                        </p>
                    </div>
                </div>

                <div class="card mt-6 p-6">
                    <p class="font-serif text-base font-bold">Setelah pemesanan dikirim</p>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        Anda akan melanjutkan ke halaman pembayaran terpisah untuk melihat QRIS dan mengunggah bukti pembayaran.
                    </p>
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var jumlah = document.getElementById('jumlah');
            var ukuran = document.getElementById('ukuran');
            var donasiInput = document.getElementById('donasi');
            var harga = {{ (int) $selected->harga }};
            var subtotalEl = document.getElementById('sum-subtotal');
            var sumDonasiEl = document.getElementById('sum-donasi');
            var rowSumDonasi = document.getElementById('row-sum-donasi');
            var totalEl = document.getElementById('sum-total');
            var sumJumlah = document.getElementById('sum-jumlah');
            var labelJumlah = document.getElementById('sum-label-jumlah');
            var sumUkuran = document.getElementById('sum-ukuran');

            function rupiah(n) { return Number(n || 0).toLocaleString('id-ID'); }

            function render() {
                var qty = Math.max(1, parseInt(jumlah.value || '1', 10) || 1);
                var sub = harga * qty;
                var don = donasiInput ? Math.max(0, parseInt(donasiInput.value || '0', 10) || 0) : 0;
                var tot = sub + don;

                subtotalEl.textContent = rupiah(sub);
                if (sumDonasiEl) sumDonasiEl.textContent = rupiah(don);
                if (rowSumDonasi) rowSumDonasi.classList.toggle('hidden', don <= 0 && '{{ $tipe }}' !== 'kaos_donasi');
                totalEl.textContent = rupiah(tot);
                sumJumlah.textContent = qty + ' kaos';
                labelJumlah.textContent = qty;
                sumUkuran.textContent = ukuran.value || '—';
            }

            jumlah.addEventListener('input', render);
            ukuran.addEventListener('change', render);
            if (donasiInput) {
                donasiInput.addEventListener('input', render);
                donasiInput.addEventListener('change', render);
            }

            document.querySelectorAll('.chip-donasi').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (donasiInput) {
                        donasiInput.value = btn.dataset.donasi;
                        render();
                    }
                });
            });

            render();
        })();
    </script>
@endpush
