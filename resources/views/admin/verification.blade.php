@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')
@section('breadcrumb', 'Verifikasi Pembayaran')

@section('content')
    <!-- Toast / Session Notification -->
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-red-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 8.25h.008v.008H12v-.008Z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-amber-600 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI PROGRAM</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Verifikasi Pembayaran</h1>
            <p class="mt-1 text-sm text-stone-500">Periksa bukti, cocokkan nominal, lalu konfirmasi pembayaran secara manual.</p>
        </div>
        <a href="{{ route('admin.verification', ['status' => 'terverifikasi']) }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>Riwayat verifikasi</span>
        </a>
    </div>

    <!-- 4 Metrics Row (Horizontal matching mockup) -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center gap-3.5">
            <span class="font-sans text-2xl sm:text-[28px] font-bold text-stone-900 leading-none shrink-0 min-w-[24px]">{{ $statMenunggu }}</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-stone-900 truncate">Menunggu verifikasi</p>
                <p class="text-[11px] text-stone-400 mt-0.5 truncate">Rp{{ number_format($rpMenunggu, 0, ',', '.') }} · bukti terkirim</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center gap-3.5">
            <span class="font-sans text-2xl sm:text-[28px] font-bold text-stone-900 leading-none shrink-0 min-w-[24px]">{{ $statBuktiUlang }}</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-stone-900 truncate">Bukti ulang</p>
                <p class="text-[11px] text-stone-400 mt-0.5 truncate">Rp{{ number_format($rpBuktiUlang, 0, ',', '.') }} · perlu dilengkapi</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center gap-3.5">
            <span class="font-sans text-2xl sm:text-[28px] font-bold text-stone-900 leading-none shrink-0 min-w-[24px]">{{ $statTerverifikasi }}</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-stone-900 truncate">Terverifikasi</p>
                <p class="text-[11px] text-stone-400 mt-0.5 truncate">Sudah dicocokkan panitia</p>
            </div>
        </div>

        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center gap-3.5">
            <span class="font-sans text-2xl sm:text-[28px] font-bold text-stone-900 leading-none shrink-0 min-w-[24px]">{{ $statBelumBayar }}</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-stone-900 truncate">Belum bayar</p>
                <p class="text-[11px] text-stone-400 mt-0.5 truncate">Rp{{ number_format($rpBelumBayar, 0, ',', '.') }} · belum ada bukti</p>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Master-Detail Layout -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left Column (List of Transactions & Checklists) -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">
            
            <!-- Filters Bar -->
            <form method="GET" action="{{ route('admin.verification') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[240px] flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau ID transaksi" class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                </div>

                <select name="jenis" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                    <option value="">Semua jenis</option>
                    <option value="kaos_donasi" @selected(request('jenis') === 'kaos_donasi')>Pesan Kaos</option>
                    <option value="donasi_saja" @selected(request('jenis') === 'donasi_saja')>Donasi Saja</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                    <option value="">Semua status</option>
                    <option value="menunggu_verifikasi" @selected(request('status') === 'menunggu_verifikasi')>Menunggu verifikasi</option>
                    <option value="bukti_ulang" @selected(request('status') === 'bukti_ulang')>Bukti ulang</option>
                    <option value="terverifikasi" @selected(request('status') === 'terverifikasi')>Terverifikasi</option>
                    <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum bayar</option>
                </select>

                @if(request()->anyFilled(['q', 'jenis', 'status']))
                    <a href="{{ route('admin.verification') }}" class="text-xs font-semibold text-stone-400 hover:text-stone-700">Reset</a>
                @endif
            </form>

            <!-- Transaksi Masuk Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <!-- Card Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Transaksi masuk</h2>
                        <div class="mt-0.5 flex items-center gap-2 text-xs text-stone-400">
                            <span>Prioritas: bukti terbaru yang belum terverifikasi</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-stone-400">{{ $transaksi->total() }} transaksi</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M6.75 12h10.5m-7.5 5.25h4.5" />
                        </svg>
                    </div>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <!-- Table Header Bar -->
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[660px]">
                        <div class="col-span-3">Pemesanan / ID</div>
                        <div class="col-span-2">Jenis dukungan</div>
                        <div class="col-span-2">Tagihan</div>
                        <div class="col-span-2">Bukti terkirim</div>
                        <div class="col-span-3 text-right pr-2">Pembayaran</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="mt-1 divide-y divide-stone-100 min-w-[660px]">
                        @forelse ($transaksi as $t)
                            @php
                                $isOrder = $t instanceof App\Models\Order;
                                $status = $isOrder ? $t->status_pembayaran : $t->status;
                                $tipe = $isOrder ? 'order' : 'donation';
                                $idLabel = $isOrder ? $t->nomor_pesanan : $t->kode;
                                $tagihan = $isOrder ? $t->totalTagihan() : $t->nominal;
                                $isSelected = $selectedItem && $selectedItem->id === $t->id && $selectedTipe === $tipe;
                            @endphp
                            <a href="{{ route('admin.verification', array_merge(request()->query(), ['selected_id' => $t->id, 'selected_tipe' => $tipe])) }}" 
                               class="grid grid-cols-12 gap-2 items-center px-4 py-3 rounded-xl transition cursor-pointer {{ $isSelected ? 'bg-[#faefe8] border border-[#f0dcd0]' : 'hover:bg-stone-50/80 border border-transparent' }}">
                                <div class="col-span-3">
                                    <p class="text-xs font-bold text-stone-900 leading-tight">{{ $t->nama }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">{{ $idLabel }}</p>
                                </div>
                                <div class="col-span-2">
                                    @if ($isOrder)
                                        <p class="text-xs text-stone-800 leading-tight">Pesan Kaos</p>
                                        <p class="text-[11px] text-stone-400 mt-0.5">+ Donasi</p>
                                    @else
                                        <p class="text-xs text-stone-800 leading-tight">Donasi Saja</p>
                                    @endif
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-semibold text-stone-900">Rp{{ number_format($tagihan, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-span-2">
                                    @if ($t->bukti_path || $status === 'menunggu_verifikasi' || $status === 'terverifikasi')
                                        <p class="text-xs text-stone-800 leading-tight">{{ $t->updated_at ? $t->updated_at->translatedFormat('d M') : '11 Okt' }}</p>
                                        <p class="text-[11px] text-stone-400 mt-0.5">{{ $t->updated_at ? $t->updated_at->format('H.i') : '16.40' }}</p>
                                    @else
                                        <span class="text-xs text-stone-400">&mdash;</span>
                                    @endif
                                </div>
                                <div class="col-span-3 flex justify-end pr-2">
                                    @if ($status === 'terverifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @elseif ($status === 'menunggu_verifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @elseif ($status === 'bukti_ulang')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-red-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Bukti ulang
                                        </span>
                                    @elseif ($status === 'ditolak')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-2.5 py-0.5 text-[11px] font-semibold text-stone-500 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                            Belum bayar
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="py-10 text-center text-xs text-stone-400">Tidak ada transaksi yang cocok dengan filter.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $transaksi->count() }} dari {{ $transaksi->total() }} transaksi</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($transaksi->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $transaksi->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        <span class="grid h-7 w-7 place-items-center rounded-lg border border-[#e8e4dc] bg-white font-bold text-stone-900 shadow-2xs">{{ $transaksi->currentPage() }}</span>

                        @if ($transaksi->hasMorePages())
                            <a href="{{ $transaksi->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
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
                <span>Pembayaran manual memerlukan verifikasi panitia. Status 'Terverifikasi' hanya diberikan setelah bukti dan mutasi cocok.</span>
            </div>

            <!-- Daftar Pemeriksaan Panitia -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6">
                <h2 class="font-serif text-lg font-bold text-stone-900">Daftar pemeriksaan panitia</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="font-sans text-xs font-bold text-[#b55322] shrink-0">01</span>
                        <p class="text-xs text-stone-700">Cocokkan nama pemesan dan ID transaksi.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="font-sans text-xs font-bold text-[#b55322] shrink-0">02</span>
                        <p class="text-xs text-stone-700">Periksa nominal, tanggal, dan penerima pada bukti.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="font-sans text-xs font-bold text-[#b55322] shrink-0">03</span>
                        <p class="text-xs text-stone-700">Pastikan dana masuk di mutasi rekening panitia.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column (Detail / Inspector Panel) -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-20">
            @if ($selectedItem)
                @php
                    $selIsOrder = $selectedTipe === 'order';
                    $selId = $selIsOrder ? $selectedItem->nomor_pesanan : $selectedItem->kode;
                    $selStatus = $selIsOrder ? $selectedItem->status_pembayaran : $selectedItem->status;
                    $selTagihan = $selIsOrder ? $selectedItem->totalTagihan() : $selectedItem->nominal;
                    $selNama = $selectedItem->nama;
                    $selJenis = $selIsOrder ? ($selectedItem->jenis === 'kaos_donasi' ? 'Pesan Kaos + Donasi' : 'Pesan Kaos') : 'Donasi Saja';

                    $buktiPath = $selectedItem->bukti_path;
                    $buktiUrl = $selectedItem->buktiUrl();
                    $buktiExt = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                    $isImage = in_array($buktiExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                    $isPdf = $buktiExt === 'pdf';
                    $fileSize = null;
                    if ($buktiPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($buktiPath)) {
                        $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($buktiPath);
                        $fileSize = $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : round($bytes / 1024) . ' KB';
                    }
                @endphp

                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                    <!-- Inspector Header -->
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="font-bold text-2xl text-stone-900 tracking-tight">{{ $selId }}</h2>
                            @if ($selStatus === 'terverifikasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Terverifikasi
                                </span>
                            @elseif ($selStatus === 'menunggu_verifikasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-semibold text-amber-800">
                                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu verifikasi
                                </span>
                            @elseif ($selStatus === 'bukti_ulang')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-semibold text-red-700">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                    Bukti ulang
                                </span>
                            @elseif ($selStatus === 'ditolak')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-3 py-1 text-xs font-semibold text-stone-600">
                                    <span class="h-2 w-2 rounded-full bg-stone-500"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-3 py-1 text-xs font-semibold text-stone-500">
                                    <span class="h-2 w-2 rounded-full bg-stone-400"></span>
                                    Belum bayar
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs font-bold text-stone-800">{{ $selNama }} &middot; {{ $selJenis }}</p>
                    </div>

                    <!-- Proof Preview Box -->
                    <div class="rounded-2xl bg-[#f8f6f0] border border-[#ede8df] p-4 sm:p-5">
                        @if ($buktiPath && $isImage)
                            <div class="relative group rounded-xl border border-stone-200/90 bg-white p-2 shadow-xs flex items-center justify-center overflow-hidden">
                                <div class="max-h-72 sm:max-h-80 w-full overflow-hidden rounded-lg bg-stone-100/60 flex items-center justify-center">
                                    <img src="{{ $buktiUrl }}" alt="Bukti Pembayaran {{ $selId }}"
                                        class="max-h-72 sm:max-h-80 w-auto object-contain cursor-pointer transition-transform duration-200 group-hover:scale-[1.02]"
                                        onclick="openProofLightbox('{{ $buktiUrl }}', '{{ basename($buktiPath) }}')">
                                </div>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                    <span class="rounded-full bg-white/95 text-stone-900 px-3 py-1.5 text-xs font-bold shadow-md flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-[#b55322]">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                        Klik untuk perbesar
                                    </span>
                                </div>
                            </div>
                        @elseif ($buktiPath && $isPdf)
                            <div class="rounded-xl border border-stone-200 bg-white p-6 text-center shadow-xs">
                                <div class="mx-auto w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-2.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-stone-900 truncate">{{ basename($buktiPath) }}</p>
                                <p class="text-[11px] text-stone-500 mt-0.5">Dokumen Bukti Transfer PDF</p>
                                <a href="{{ $buktiUrl }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-stone-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-stone-800 transition">
                                    <span>Buka Dokumen PDF</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" /></svg>
                                </a>
                            </div>
                        @else
                            {{-- State Belum Ada Bukti Transfer --}}
                            <div class="rounded-xl border-2 border-dashed border-[#dcd6ca] bg-white/70 p-6 text-center">
                                <div class="mx-auto w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mb-2.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-stone-800">Belum Ada Bukti Pembayaran</p>
                                <p class="mt-1 text-[11px] text-stone-500 leading-relaxed">
                                    @if ($selStatus === 'belum_bayar')
                                        Pemesan/donatur belum mengunggah foto bukti transfer.
                                    @else
                                        Bukti transfer belum diunggah atau perlu diminta bukti ulang.
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Proof File Info & Expand Link -->
                        <div class="mt-3.5 flex items-center justify-between text-xs text-stone-500">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 shrink-0 text-stone-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                @if ($buktiPath)
                                    <span class="truncate text-[11px] sm:text-xs font-medium text-stone-600">{{ basename($buktiPath) }} @if($fileSize) &middot; {{ $fileSize }} @endif</span>
                                @else
                                    <span class="text-[11px] text-stone-400">Tidak ada berkas bukti</span>
                                @endif
                            </div>
                            @if ($buktiPath)
                                <button type="button" onclick="openProofLightbox('{{ $buktiUrl }}', '{{ basename($buktiPath) }}')" class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-[#b55322] hover:underline shrink-0 cursor-pointer">
                                    <span>Perbesar</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Breakdown Financial Lines -->
                    <div class="divide-y divide-stone-100 text-xs">
                        <div class="space-y-2 pb-3">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Ditagih</span>
                                <span class="font-bold text-stone-900">Rp{{ number_format($selTagihan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Dibayar pada bukti</span>
                                <span class="font-semibold text-stone-800">Rp{{ number_format($selTagihan, 0, ',', '.') }} <span class="text-stone-400 font-normal">(klaim pemesan)</span></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Selisih nominal</span>
                                <span class="font-semibold text-stone-800">Rp0</span>
                            </div>
                        </div>

                        <div class="space-y-2 py-3">
                            @if ($selIsOrder)
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Subtotal kaos</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->subtotal_kaos, 0, ',', '.') }} <span class="text-stone-400 font-normal">&middot; {{ $selectedItem->jumlah }} kaos, {{ $selectedItem->ukuran }}</span></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Donasi</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->donasi, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Ongkir</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Donasi sukarela</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->nominal, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">WhatsApp</span>
                                <span class="font-semibold text-stone-900">{{ $selectedItem->nomor ?: '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Email</span>
                                <span class="font-semibold text-stone-900">{{ $selectedItem->email ?: '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Bukti terkirim</span>
                                <span class="font-semibold text-stone-900">
                                    {{ $selectedItem->updated_at ? $selectedItem->updated_at->translatedFormat('d M Y') . ' · ' . $selectedItem->updated_at->format('H.i') . ' WITA' : '11 Okt 2026 · 16.40 WITA' }}
                                </span>
                            </div>
                            @if ($selIsOrder && ($selectedItem->alamat || $selectedItem->kecamatan))
                                <div class="pt-2 border-t border-stone-100">
                                    <span class="text-stone-500 block mb-0.5 text-[11px]">Alamat pengiriman</span>
                                    <span class="font-medium text-stone-800 text-[11px] leading-relaxed block">
                                        @php
                                            $vAlamatParts = array_filter([
                                                ltrim(trim($selectedItem->alamat ?? ''), ', '),
                                                $selectedItem->kecamatan ? 'Kec. ' . $selectedItem->kecamatan : null,
                                                $selectedItem->kabupaten,
                                                $selectedItem->provinsi,
                                            ]);
                                            $vAlamatText = implode(', ', $vAlamatParts) . ($selectedItem->kode_pos ? ' ' . $selectedItem->kode_pos : '');
                                        @endphp
                                        {{ $vAlamatText ?: '—' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Area -->
                    @if ($selStatus === 'terverifikasi')
                        <div class="space-y-3 pt-1">
                            @if ($selectedItem->catatan_panitia)
                                <div>
                                    <label class="block text-xs font-bold text-stone-800 mb-1.5">Catatan pemeriksaan</label>
                                    <div class="w-full rounded-xl border border-[#e8e4dc] bg-[#fbf9f5] p-3 text-xs text-stone-700">
                                        {{ $selectedItem->catatan_panitia }}
                                    </div>
                                </div>
                            @endif

                            <!-- Status callout -->
                            <div class="flex items-start gap-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <div>
                                    <p class="font-bold">Pembayaran telah terverifikasi</p>
                                    <p class="mt-0.5 text-[11px] text-emerald-700 leading-relaxed">
                                        Data otomatis sudah masuk dan aktif di menu <strong>{{ $selIsOrder ? 'Pesanan Kaos' : 'Donasi' }}</strong>.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2 pt-1">
                                @if ($selectedItem->nomor)
                                    @php $waSetting = \App\Models\WhatsappSetting::getSetting(); @endphp
                                    <form id="form-resend-wa" method="POST" action="{{ route('admin.verification.resend_wa', [$selectedItem->id, $selectedTipe]) }}">
                                        @csrf
                                        <button type="submit" id="btn-resend-wa" class="w-full rounded-xl border border-emerald-300 bg-emerald-50/60 hover:bg-emerald-100/60 py-2.5 text-xs font-bold text-emerald-900 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>Kirim Ulang Rincian ke WhatsApp ({{ $selectedItem->nomor }})</span>
                                        </button>
                                    </form>
                                    <div class="flex items-center justify-between text-[11px] text-stone-500 px-1 pt-0.5">
                                        @if ($waSetting->isConnected())
                                            <span class="inline-flex items-center gap-1.5 text-emerald-700 font-medium">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Gateway Fonnte: {{ $waSetting->device_name ?: 'Terhubung' }} (Kuota: {{ $waSetting->quota ?? '-' }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-amber-700 font-medium">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Gateway Fonnte: {{ $waSetting->device_status ?: 'Belum terhubung' }}
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.provider') }}" class="text-[#b55322] hover:underline font-semibold">Cek Kuota &rarr;</a>
                                    </div>

                                    @php
                                        $directWaUrl = app(\App\Services\FonnteService::class)->getDirectWaLink($selectedItem, $selectedTipe);
                                    @endphp
                                    <a href="{{ $directWaUrl }}" target="_blank" rel="noopener" class="w-full rounded-xl border border-stone-200 bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        <span>Buka WhatsApp Web Langsung (wa.me) &nearr;</span>
                                    </a>
                                @endif

                                @if ($selIsOrder)
                                    <a href="{{ route('admin.orders', ['selected_id' => $selectedItem->id]) }}" class="w-full rounded-xl bg-[#444d41] hover:bg-[#353c33] py-2.5 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2">
                                        <span>Kelola Pengiriman di Pesanan Kaos &rarr;</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.donations', ['selected_id' => $selectedItem->id]) }}" class="w-full rounded-xl bg-[#444d41] hover:bg-[#353c33] py-2.5 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2">
                                        <span>Lihat di Buku Donasi &rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Form Keputusan Verifikasi untuk status belum terverifikasi -->
                        <form method="POST" action="{{ route('admin.verification.action', [$selectedItem->id, $selectedTipe]) }}" class="space-y-3 pt-1">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-stone-800 mb-1.5">Catatan pemeriksaan</label>
                                <textarea name="catatan_panitia" rows="2" class="w-full rounded-xl border border-[#e8e4dc] p-3 text-xs text-stone-800 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none" placeholder="Nominal sesuai tagihan. Cocokkan dana masuk pada mutasi sebelum verifikasi.">{{ old('catatan_panitia', $selectedItem->catatan_panitia ?? 'Nominal sesuai tagihan. Cocokkan dana masuk pada mutasi sebelum verifikasi.') }}</textarea>
                            </div>

                            <div class="flex items-start gap-2 rounded-xl bg-[#fbf7ee] border border-[#f3ecd8] p-3 text-xs text-stone-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500 shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                                <p class="leading-relaxed">Belum terverifikasi. Transaksi ini belum muncul di menu {{ $selIsOrder ? 'Pesanan Kaos' : 'Donasi' }} sampai Anda melakukan verifikasi.</p>
                            </div>

                            <!-- WhatsApp auto notification toggle -->
                            <div class="rounded-xl border border-emerald-200/80 bg-emerald-50/50 p-3 text-xs">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="kirim_wa" value="1" class="mt-0.5 rounded text-[#25D366] focus:ring-[#25D366]" checked>
                                    <div class="min-w-0 flex-1">
                                        <span class="font-bold text-emerald-950 flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            Kirim otomatis rincian pesanan ke WhatsApp
                                        </span>
                                        <p class="mt-0.5 text-[11px] text-emerald-800 leading-relaxed">
                                            Kirim pesan konfirmasi lengkap ke nomor <strong>{{ $selectedItem->nomor ?: '—' }}</strong> saat tombol verifikasi ditekan.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- Buttons -->
                            <div class="space-y-2 pt-1">
                                <button type="submit" name="aksi" value="verifikasi" class="w-full rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-3 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Verifikasi Pembayaran &amp; Kirim Pesanan ke WA</span>
                                </button>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" name="aksi" value="tolak" class="rounded-xl border border-stone-200 bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-stone-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        <span>Tolak</span>
                                    </button>
                                    <button type="submit" name="aksi" value="bukti_ulang" class="rounded-xl border border-stone-200 bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-stone-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <span>Minta Bukti Ulang</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            @else
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 text-center text-xs text-stone-400">
                    Pilih transaksi dari tabel untuk melihat rincian bukti dan melakukan verifikasi.
                </div>
            @endif
        </div>

    </div>

    {{-- Lightbox Modal untuk Perbesar Bukti Transfer --}}
    <div id="proofLightbox" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6" onclick="closeProofLightbox(event)">
        <div class="relative max-w-3xl w-full max-h-[92vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-stone-200 bg-stone-50">
                <div class="flex items-center gap-2 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-[#b55322] shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                    </svg>
                    <span id="proofLightboxTitle" class="text-xs sm:text-sm font-bold text-stone-800 truncate">Bukti Transfer</span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a id="proofLightboxExternal" href="#" target="_blank" rel="noopener" class="text-xs font-semibold text-[#b55322] hover:underline flex items-center gap-1">
                        <span>Buka Tab Baru</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" /></svg>
                    </a>
                    <button type="button" onclick="closeProofLightbox()" class="rounded-lg p-1.5 text-stone-400 hover:bg-stone-200 hover:text-stone-700 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-4 sm:p-6 overflow-auto flex items-center justify-center bg-stone-900/5 min-h-[350px]">
                <img id="proofLightboxImg" src="" alt="Bukti Transfer" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-sm border border-stone-200/80 bg-white">
            </div>
        </div>
    </div>

    <script>
        function openProofLightbox(url, title) {
            const modal = document.getElementById('proofLightbox');
            const img = document.getElementById('proofLightboxImg');
            const titleEl = document.getElementById('proofLightboxTitle');
            const extLink = document.getElementById('proofLightboxExternal');
            if (!modal || !img) return;
            img.src = url;
            if (titleEl) titleEl.textContent = title || 'Bukti Transfer';
            if (extLink) extLink.href = url;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProofLightbox(e) {
            const modal = document.getElementById('proofLightbox');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeProofLightbox();
        });

        // AJAX Kirim Ulang WA
        const resendForm = document.getElementById('form-resend-wa');
        if (resendForm) {
            resendForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('btn-resend-wa');
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Mengirim pesan WhatsApp via Fonnte...</span>
                `;

                try {
                    const formData = new FormData(resendForm);
                    const resp = await fetch(resendForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await resp.json();
                    if (resp.ok && data.success) {
                        btn.classList.remove('border-emerald-300', 'bg-emerald-50/60', 'hover:bg-emerald-100/60', 'text-emerald-900');
                        btn.classList.add('border-emerald-500', 'bg-emerald-600', 'text-white');
                        btn.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>${data.message || 'Pesan berhasil dikirim ke WhatsApp!'}</span>
                        `;
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-75', 'cursor-not-allowed', 'border-emerald-500', 'bg-emerald-600', 'text-white');
                            btn.classList.add('border-emerald-300', 'bg-emerald-50/60', 'hover:bg-emerald-100/60', 'text-emerald-900');
                            btn.innerHTML = origHtml;
                        }, 5000);
                    } else {
                        alert('Gagal mengirim WhatsApp: ' + (data.message || 'Terjadi kesalahan'));
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        btn.innerHTML = origHtml;
                    }
                } catch (err) {
                    // Fallback ke normal submit jika ada error fetch
                    resendForm.submit();
                }
            });
        }
    </script>
@endsection
