@extends('layouts.admin')

@section('title', 'Pesanan Kaos')
@section('breadcrumb', 'Pesanan Kaos')

@section('content')
    <!-- Toast / Session Notification -->
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI PROGRAM</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Pesanan Kaos</h1>
            <p class="mt-1 text-sm text-stone-500">Kelola pesanan, produksi, dan pengiriman tanpa mencampur status pembayaran.</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <span>Ekspor pesanan</span>
        </a>
    </div>

    <!-- 4 Metrics Row matching design -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Semua pesanan</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $semuaPesanan }} pesanan</p>
            <p class="mt-2 text-xs text-stone-400">7 kaos &middot; Juli–Oktober 2026</p>
        </div>

        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Siap dikirim</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-9v9l9 5.25" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $siapKirim }} pesanan</p>
            <p class="mt-2 text-xs text-stone-400">2 kaos &middot; pembayaran terverifikasi</p>
        </div>

        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Dalam produksi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 0 .824 3.428m11.968-3.428a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $dalamProduksi }} pesanan</p>
            <p class="mt-2 text-xs text-stone-400">1 kaos &middot; pembayaran terverifikasi</p>
        </div>

        <a href="{{ route('admin.verification') }}" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between hover:border-[#b55322]/50 hover:shadow-xs transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500 group-hover:text-stone-900 transition">Menunggu verifikasi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $menungguPembayaran }} pesanan</p>
            <p class="mt-2 text-xs text-[#b55322] font-semibold flex items-center gap-1">Periksa di Verifikasi &rarr;</p>
        </a>
    </div>

    <!-- Main Two-Column Master-Detail Layout -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left Column (Orders List & Rekap Kaos) -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">
            
            <!-- Filters Bar -->
            <form method="GET" action="{{ route('admin.orders') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[240px] flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau ID pesanan terverifikasi" class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                </div>

                <select name="pengiriman" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                    <option value="">Status Pengiriman (Semua)</option>
                    <option value="belum_diproduksi" @selected(request('pengiriman') === 'belum_diproduksi')>Belum diproduksi</option>
                    <option value="produksi" @selected(request('pengiriman') === 'produksi')>Produksi</option>
                    <option value="siap_kirim" @selected(request('pengiriman') === 'siap_kirim')>Siap kirim</option>
                    <option value="terkirim" @selected(request('pengiriman') === 'terkirim')>Terkirim</option>
                </select>

                @if(request()->anyFilled(['q', 'pengiriman']))
                    <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-stone-400 hover:text-stone-700">Reset</a>
                @endif
            </form>

            <!-- Daftar Pesanan Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <!-- Card Header -->
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Daftar pesanan</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        {{ $pesanan->total() }} pesanan
                    </span>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <!-- Table Header Bar -->
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[660px]">
                        <div class="col-span-3">Pemesanan / tanggal</div>
                        <div class="col-span-2">Desain kaos</div>
                        <div class="col-span-2">Uk. / Jml.</div>
                        <div class="col-span-3">Pembayaran</div>
                        <div class="col-span-2 text-right pr-2">Produksi / kirim</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="mt-1 divide-y divide-stone-100 min-w-[660px]">
                        @forelse ($pesanan as $o)
                            @php
                                $bayarStatus = $o->status_pembayaran;
                                $kirimStatus = $o->status_pengiriman;
                                $isSelected = $selectedOrder && $selectedOrder->id === $o->id;
                                $designNama = trim(str_replace(['Kaos', '"'], '', $o->design?->nama ?? 'Keluarga Kudus'));
                                $hargaSatuan = $o->jumlah > 0 ? ($o->subtotal_kaos / $o->jumlah) : 145000;
                                
                                // Clean matching logic for produksi / kirim badge from design mockup
                                $kirimLabel = match ($kirimStatus) {
                                    'siap_kirim' => 'Siap kirim',
                                    'produksi' => 'Produksi',
                                    'terkirim' => 'Terkirim',
                                    default => ($bayarStatus === 'menunggu_verifikasi' ? 'Ditahan' : 'Belum diproses'),
                                };
                            @endphp
                            <a href="{{ route('admin.orders', array_merge(request()->query(), ['selected_id' => $o->id])) }}" 
                               class="grid grid-cols-12 gap-2 items-center px-4 py-3 rounded-xl transition cursor-pointer {{ $isSelected ? 'bg-[#faefe8] border border-[#f0dcd0]' : 'hover:bg-stone-50/80 border border-transparent' }}">
                                <div class="col-span-3">
                                    <p class="text-xs font-bold text-stone-900 leading-tight">{{ $o->nama }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">{{ $o->nomor_pesanan }} &middot; {{ $o->created_at ? $o->created_at->translatedFormat('d M') : '10 Okt' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-stone-800 leading-tight">{{ $designNama }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">Rp{{ number_format($hargaSatuan, 0, ',', '.') }} / kaos</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-stone-700 font-medium">{{ $o->ukuran }} &middot; {{ $o->jumlah }}</p>
                                </div>
                                <div class="col-span-3">
                                    @if ($bayarStatus === 'terverifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @elseif ($bayarStatus === 'menunggu_verifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @elseif ($bayarStatus === 'bukti_ulang')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-red-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Bukti ulang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-2.5 py-0.5 text-[11px] font-semibold text-stone-500 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                            Belum bayar
                                        </span>
                                    @endif
                                </div>
                                <div class="col-span-2 flex justify-end pr-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 whitespace-nowrap">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                                        {{ $kirimLabel }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="py-10 text-center text-xs text-stone-400">Tidak ada pesanan yang cocok dengan filter.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $pesanan->count() }} dari {{ $pesanan->total() }} pesanan</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($pesanan->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $pesanan->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        <span class="grid h-7 w-7 place-items-center rounded-lg border border-[#e8e4dc] bg-white font-bold text-stone-900 shadow-2xs">{{ $pesanan->currentPage() }}</span>

                        @if ($pesanan->hasMorePages())
                            <a href="{{ $pesanan->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
                        @else
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shield Alert Note -->
            <div class="flex items-center gap-3 rounded-2xl border border-[#e8e4dc] bg-[#f8f6f0] p-4 text-xs text-stone-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 text-stone-600 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Pesanan baru diproduksi setelah pembayaran terverifikasi. Bukti terkirim tidak mengubah status menjadi lunas.</span>
            </div>

            <!-- Rekap Kaos Terverifikasi Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <h2 class="font-serif text-lg font-bold text-stone-900">Rekap kaos terverifikasi</h2>

                <div class="mt-4 overflow-x-auto">
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[500px]">
                        <div class="col-span-5">Desain</div>
                        <div class="col-span-2">Ukuran</div>
                        <div class="col-span-2">Jumlah</div>
                        <div class="col-span-3 text-right pr-2">Tahap</div>
                    </div>

                    <div class="mt-1 divide-y divide-stone-100 min-w-[500px]">
                        @forelse ($rekapKaos as $r)
                            @php
                                $rLabel = match ($r->status_pengiriman) {
                                    'siap_kirim' => 'Siap kirim',
                                    'produksi' => 'Produksi',
                                    'terkirim' => 'Terkirim',
                                    default => 'Antrean produksi',
                                };
                            @endphp
                            <div class="grid grid-cols-12 gap-2 items-center px-4 py-3">
                                <div class="col-span-5 font-bold text-xs text-stone-900">
                                    {{ 'Kaos ' . trim(str_replace(['Kaos', '"'], '', $r->design?->nama ?? 'Keluarga Kudus')) }}
                                </div>
                                <div class="col-span-2 text-xs text-stone-700">
                                    {{ $r->ukuran }}
                                </div>
                                <div class="col-span-2 text-xs text-stone-700">
                                    {{ $r->jumlah }} kaos
                                </div>
                                <div class="col-span-3 flex justify-end pr-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 whitespace-nowrap">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                                        {{ $rLabel }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-stone-400">Belum ada kaos terverifikasi.</div>
                        @endforelse
                    </div>
                </div>

                <p class="mt-4 text-xs text-stone-400">{{ $rekapKaos->sum('jumlah') }} kaos terverifikasi &middot; {{ $menungguPembayaran }} pesanan lain belum masuk antrean produksi.</p>
            </div>

        </div>

        <!-- Right Column (Detail / Inspector Panel) -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-20">
            @if ($selectedOrder)
                @php
                    $selKirimLabel = match ($selectedOrder->status_pengiriman) {
                        'siap_kirim' => 'Siap kirim',
                        'produksi' => 'Produksi',
                        'terkirim' => 'Terkirim',
                        default => 'Belum diproduksi',
                    };
                    $selBayarLabel = match ($selectedOrder->status_pembayaran) {
                        'terverifikasi' => 'Terverifikasi',
                        'menunggu_verifikasi' => 'Menunggu verifikasi',
                        'bukti_ulang' => 'Bukti ulang',
                        default => 'Belum bayar',
                    };
                    $hargaSatuanSel = $selectedOrder->jumlah > 0 ? ($selectedOrder->subtotal_kaos / $selectedOrder->jumlah) : 145000;
                    $designGambar = $selectedOrder->design?->gambar;
                @endphp

                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                    <!-- Inspector Header -->
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="font-bold text-2xl text-stone-900 tracking-tight">{{ $selectedOrder->nomor_pesanan }}</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-xs font-semibold text-stone-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                                {{ $selKirimLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- Product Card -->
                    <div class="rounded-2xl bg-[#fbf8f3] border border-[#ede8df] p-4 flex items-center gap-4">
                        <div class="h-16 w-16 shrink-0 grid place-items-center rounded-xl bg-white border border-[#ede8df] p-1 overflow-hidden shadow-2xs">
                            @if ($designGambar && file_exists(public_path($designGambar)))
                                <img src="{{ asset($designGambar) }}" alt="Kaos" class="h-full w-full object-contain">
                            @elseif ($designGambar && file_exists(public_path('designs/' . basename($designGambar))))
                                <img src="{{ asset('designs/' . basename($designGambar)) }}" alt="Kaos" class="h-full w-full object-contain">
                            @else
                                <img src="{{ asset('designs/kaos-keluarga-kudus.svg') }}" alt="Kaos" class="h-full w-full object-contain">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-stone-900 truncate">{{ 'Kaos ' . trim(str_replace(['Kaos', '"'], '', $selectedOrder->design?->nama ?? 'Keluarga Kudus')) }}</h3>
                            <p class="text-[11px] text-stone-500 mt-0.5">Marun &middot; tulisan emas</p>
                            <p class="text-[10px] text-stone-400">100% Cotton 24s/30s</p>
                            <p class="mt-1 text-xs font-bold text-stone-900">
                                Rp{{ number_format($hargaSatuanSel, 0, ',', '.') }} / kaos &middot; {{ $selectedOrder->ukuran }} x {{ $selectedOrder->jumlah }}
                            </p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-stone-900">{{ $selectedOrder->nama }}</h4>
                        <div class="flex items-center justify-between text-xs pt-1">
                            <span class="text-stone-500">WhatsApp</span>
                            <span class="font-semibold text-stone-900">{{ $selectedOrder->nomor ?: '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-stone-500">Email</span>
                            <span class="font-semibold text-stone-900">{{ $selectedOrder->email ?: '—' }}</span>
                        </div>
                    </div>

                    <!-- Shipping Address Box -->
                    <div class="rounded-xl bg-[#f5f0e8] border border-[#ede8df] p-3.5 text-xs">
                        <p class="font-bold text-[11px] text-stone-800">Alamat pengiriman</p>
                        <p class="mt-1 text-stone-600 leading-relaxed">
                            @php
                                $cleanAlamat = ltrim(trim($selectedOrder->alamat ?? ''), ', ');
                                $orderAlamatParts = array_filter([
                                    $cleanAlamat ?: null,
                                    $selectedOrder->kelurahan ? 'Kel./Desa ' . $selectedOrder->kelurahan : null,
                                    $selectedOrder->kecamatan ? 'Kec. ' . $selectedOrder->kecamatan : null,
                                    $selectedOrder->kabupaten ? (str_starts_with(strtolower($selectedOrder->kabupaten), 'kota') || str_starts_with(strtolower($selectedOrder->kabupaten), 'kab') ? $selectedOrder->kabupaten : 'Kota/Kab. ' . $selectedOrder->kabupaten) : null,
                                    $selectedOrder->provinsi,
                                ]);
                                $orderAlamatText = implode(', ', $orderAlamatParts) . ($selectedOrder->kode_pos ? ' ' . $selectedOrder->kode_pos : '');
                            @endphp
                            {{ $orderAlamatText ?: ($selectedOrder->isDonasiOnly() ? 'Donasi saja (tidak memerlukan pengiriman fisik).' : 'Belum ada alamat pengiriman.') }}
                        </p>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="divide-y divide-stone-100 text-xs">
                        <div class="space-y-2 pb-3">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Subtotal kaos</span>
                                <span class="font-semibold text-stone-900">Rp{{ number_format($selectedOrder->subtotal_kaos, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Donasi</span>
                                <span class="font-semibold text-stone-900">Rp{{ number_format($selectedOrder->donasi, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Ongkir</span>
                                <span class="font-semibold text-stone-900">Rp{{ number_format($selectedOrder->ongkir, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Total final</span>
                                <span class="font-serif text-base font-bold text-stone-900">Rp{{ number_format($selectedOrder->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Pembayaran</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-[11px] font-semibold text-stone-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                                    {{ $selBayarLabel }}
                                </span>
                            </div>
                            <p class="text-[11px] text-stone-400 pt-0.5">
                                Diperiksa panitia &middot; {{ $selectedOrder->verified_at ? $selectedOrder->verified_at->translatedFormat('d M Y, H.i') . ' WITA' : '12 Okt 2026, 08.10 WITA' }}
                            </p>
                        </div>
                    </div>

                    <!-- Form Pengiriman -->
                    <form method="POST" action="{{ route('admin.orders.ship', $selectedOrder) }}" class="space-y-3 pt-2 border-t border-stone-100">
                        @csrf
                        <h4 class="font-serif text-lg font-bold text-stone-900">Pengiriman</h4>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-stone-500 mb-1">Jasa pengiriman</label>
                                <input type="text" name="kurir" value="JNE REG" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-2.5 text-xs text-stone-800 focus:border-[#b55322] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-stone-500 mb-1">Ongkir final</label>
                                <input type="text" readonly value="Rp{{ number_format($selectedOrder->ongkir, 0, ',', '.') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50/50 p-2.5 text-xs text-stone-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-stone-500 mb-1">Nomor resi</label>
                            <input type="text" name="resi" value="{{ old('resi', $selectedOrder->resi) }}" placeholder="Belum diisi — masukkan setelah dikirim" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-2.5 text-xs text-stone-800 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-stone-500 mb-1">Status produksi / pengiriman</label>
                            <select name="status_pengiriman" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-2.5 text-xs text-stone-800 focus:border-[#b55322] focus:outline-none">
                                <option value="siap_kirim" @selected($selectedOrder->status_pengiriman === 'siap_kirim')>Siap kirim</option>
                                <option value="produksi" @selected($selectedOrder->status_pengiriman === 'produksi')>Produksi</option>
                                <option value="terkirim" @selected($selectedOrder->status_pengiriman === 'terkirim')>Terkirim</option>
                                <option value="belum_diproduksi" @selected($selectedOrder->status_pengiriman === 'belum_diproduksi')>Belum diproduksi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-stone-500 mb-1">Catatan panitia</label>
                            <textarea name="catatan_panitia" rows="2" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-2.5 text-xs text-stone-800 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">{{ old('catatan_panitia', $selectedOrder->catatan_panitia ?? '2 kaos sudah dikemas. Ongkir prabayar sudah dibukukan; tunggu penjemputan kurir.') }}</textarea>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-3 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.948c0-.621-.504-1.125-1.125-1.125H2.25" />
                            </svg>
                            <span>Simpan pengiriman</span>
                        </button>

                        <p class="text-[11px] text-stone-400 text-center">Resi dan status kirim tidak mengubah status pembayaran.</p>
                    </form>

                    <!-- Delete Order Button -->
                    <div class="pt-3 border-t border-stone-100">
                        <form method="POST" action="{{ route('admin.orders.destroy', $selectedOrder) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan {{ $selectedOrder->nomor_pesanan }} ({{ $selectedOrder->nama }})? Tindakan ini akan menghapus data pesanan dan berkas bukti secara permanen.');">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50/60 hover:bg-red-100/80 py-2.5 text-xs font-semibold text-red-700 transition flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>Hapus Pesanan Ini</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 text-center text-xs text-stone-400">
                    Pilih pesanan dari tabel untuk melihat rincian dan mengelola status pengiriman.
                </div>
            @endif
        </div>

    </div>
@endsection
