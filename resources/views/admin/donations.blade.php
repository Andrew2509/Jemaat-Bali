@extends('layouts.admin')

@section('title', 'Donasi')
@section('breadcrumb', 'Donasi')

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
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Donasi</h1>
            <p class="mt-1 text-sm text-stone-500">Setiap dukungan dicatat dengan jelas, termasuk donasi yang menyertai pesanan kaos.</p>
        </div>
        <a href="{{ route('admin.donations') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <span>Ekspor donasi</span>
        </a>
    </div>

    <!-- 4 Metrics Row matching design -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1: Dark Forest Olive Card -->
        <div class="rounded-2xl bg-[#444d41] p-5 text-white flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-white/70">Donasi terverifikasi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4.5 w-4.5 text-white/70">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-white leading-none">Rp{{ number_format($totalTerverifikasi, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-white/60">{{ $countTerverifikasi }} donasi &middot; sudah diterima panitia</p>
        </div>

        <!-- Card 2 -->
        <a href="{{ route('admin.verification') }}" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between hover:border-[#b55322]/50 hover:shadow-xs transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500 group-hover:text-stone-900 transition">Donasi tertunda</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($totalTertunda, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-[#b55322] font-semibold flex items-center gap-1">{{ $countTertunda }} donasi &middot; periksa di Verifikasi &rarr;</p>
        </a>

        <!-- Card 3 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Donasi Saja terverifikasi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($totalDonasiSaja, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">{{ $countDonasiSaja }} donasi &middot; sukarela tanpa minimum</p>
        </div>

        <!-- Card 4 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Donasi dari pesanan kaos</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($totalDariKaos, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">{{ $countDariKaos }} donasi terverifikasi &middot; bukan omzet kaos</p>
        </div>
    </div>

    <!-- Main Two-Column Master-Detail Layout -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left Column (Donors List Table) -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">
            
            <!-- Filters Bar -->
            <form method="GET" action="{{ route('admin.donations') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[240px] flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, ID, atau catatan donasi terverifikasi" class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                </div>

                <select name="jenis" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                    <option value="">Semua jenis donasi</option>
                    <option value="donasi_saja" @selected(request('jenis') === 'donasi_saja')>Donasi Saja</option>
                    <option value="kaos_donasi" @selected(request('jenis') === 'kaos_donasi')>Pesan Kaos</option>
                </select>

                @if(request()->anyFilled(['q', 'jenis']))
                    <a href="{{ route('admin.donations') }}" class="text-xs font-semibold text-stone-400 hover:text-stone-700">Reset</a>
                @endif
            </form>

            <!-- Daftar Donatur Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <!-- Card Header -->
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Daftar donatur</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        {{ $donasi->total() }} donasi
                    </span>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <!-- Table Header Bar -->
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[660px]">
                        <div class="col-span-3">Donatur / sumber</div>
                        <div class="col-span-2">Nominal donasi</div>
                        <div class="col-span-2">Tanggal</div>
                        <div class="col-span-3">Catatan</div>
                        <div class="col-span-2 text-right pr-2">Pembayaran</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="mt-1 divide-y divide-stone-100 min-w-[660px]">
                        @forelse ($donasi as $d)
                            @php
                                $isSelected = $selectedDonation && $selectedDonation->id === $d->id;
                                $relatedOrder = App\Models\Order::where('nama', $d->nama)->first();
                                $sumberSub = ($relatedOrder && $relatedOrder->donasi > 0)
                                    ? $relatedOrder->nomor_pesanan
                                    : 'Donasi Saja';
                            @endphp
                            <a href="{{ route('admin.donations', array_merge(request()->query(), ['selected_id' => $d->id])) }}" 
                               class="grid grid-cols-12 gap-2 items-center px-4 py-3 rounded-xl transition cursor-pointer {{ $isSelected ? 'bg-[#faefe8] border border-[#f0dcd0]' : 'hover:bg-stone-50/80 border border-transparent' }}">
                                <div class="col-span-3">
                                    <p class="text-xs font-bold text-stone-900 leading-tight">{{ $d->nama }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">{{ $d->kode }} &middot; {{ $sumberSub }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-semibold text-stone-900">Rp{{ number_format($d->nominal, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-stone-700">{{ $d->tanggal ? $d->tanggal->translatedFormat('d M Y') : '11 Okt 2026' }}</p>
                                </div>
                                <div class="col-span-3">
                                    <p class="text-xs text-stone-600 truncate">{{ $d->catatan ?: '—' }}</p>
                                </div>
                                <div class="col-span-2 flex justify-end pr-2">
                                    @if ($d->status === 'terverifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @elseif ($d->status === 'menunggu_verifikasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @elseif ($d->status === 'bukti_ulang')
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
                            </a>
                        @empty
                            <div class="py-10 text-center text-xs text-stone-400">Tidak ada donasi yang cocok dengan filter.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $donasi->count() }} dari {{ $donasi->total() }} donasi</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($donasi->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $donasi->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        <span class="grid h-7 w-7 place-items-center rounded-lg border border-[#e8e4dc] bg-white font-bold text-stone-900 shadow-2xs">{{ $donasi->currentPage() }}</span>

                        @if ($donasi->hasMorePages())
                            <a href="{{ $donasi->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
                        @else
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column (Detail / Inspector Panel & Ketentuan Donasi) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-6 sticky top-20">
            @if ($selectedDonation)
                @php
                    $init1 = strtoupper(substr($selectedDonation->nama, 0, 1));
                    $parts = explode(' ', $selectedDonation->nama);
                    $init2 = count($parts) > 1 ? strtoupper(substr($parts[1], 0, 1)) : '';
                    $initials = $init1 . $init2;
                    $selStatusLabel = match ($selectedDonation->status) {
                        'terverifikasi' => 'Terverifikasi',
                        'menunggu_verifikasi' => 'Menunggu verifikasi',
                        'bukti_ulang' => 'Bukti ulang',
                        default => 'Belum bayar',
                    };
                    $selMetode = strtoupper($selectedDonation->metode ?? 'qris') === 'QRIS' ? 'QRIS manual' : 'Transfer bank manual';
                    $selRelatedOrder = App\Models\Order::where('nama', $selectedDonation->nama)->first();
                    $selJenisBadge = ($selRelatedOrder && $selRelatedOrder->donasi > 0) ? 'Pesan Kaos + Donasi' : 'Donasi Saja';
                @endphp

                <!-- Selected Donation Inspector Card -->
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                    <!-- Inspector Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-2xl text-stone-900 tracking-tight">{{ $selectedDonation->kode }}</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-xs font-semibold text-stone-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                            {{ $selJenisBadge }}
                        </span>
                    </div>

                    <!-- Donor Profile Row -->
                    <div class="flex items-center gap-3 pt-1">
                        <div class="h-11 w-11 shrink-0 grid place-items-center rounded-full bg-[#faefe8] text-[#b55322] font-serif font-bold text-base">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-stone-900 truncate">{{ $selectedDonation->nama }}</h3>
                            <p class="text-xs text-stone-400 mt-0.5">
                                {{ $selectedDonation->tanggal ? $selectedDonation->tanggal->translatedFormat('d F Y') . ' · 09.15 WITA' : '12 Oktober 2026 · 09.15 WITA' }}
                            </p>
                        </div>
                    </div>

                    <!-- Nominal Display Box -->
                    <div class="pt-2">
                        <p class="font-serif text-3xl sm:text-[34px] font-bold text-[#b55322] leading-none">
                            Rp{{ number_format($selectedDonation->nominal, 0, ',', '.') }}
                        </p>
                        <div class="mt-2.5">
                            @if ($selectedDonation->status === 'terverifikasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Terverifikasi
                                </span>
                            @elseif ($selectedDonation->status === 'menunggu_verifikasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-semibold text-amber-800">
                                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu verifikasi
                                </span>
                            @elseif ($selectedDonation->status === 'bukti_ulang')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 border border-red-200 px-3 py-1 text-xs font-semibold text-red-700">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                    Bukti ulang
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 border border-stone-200 px-3 py-1 text-xs font-semibold text-stone-500">
                                    <span class="h-2 w-2 rounded-full bg-stone-400"></span>
                                    Belum bayar
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details List -->
                    <div class="divide-y divide-stone-100 text-xs pt-1">
                        <div class="space-y-2 py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">WhatsApp</span>
                                <span class="font-semibold text-stone-900">{{ $selectedDonation->nomor ?: '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Email</span>
                                <span class="font-semibold text-stone-900">{{ $selectedDonation->email ?: '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Metode</span>
                                <span class="font-semibold text-stone-900">{{ $selMetode }}</span>
                            </div>
                        </div>

                        <!-- Pesan Donatur Box -->
                        <div class="py-3">
                            <label class="block text-[11px] font-medium text-stone-500 mb-1.5">Pesan donatur</label>
                            <div class="rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs text-stone-700 leading-relaxed">
                                {{ $selectedDonation->catatan ?: '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Status Verified Notice -->
                    <div class="flex items-start gap-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <p class="leading-relaxed">Donasi telah terverifikasi dan resmi dibukukan ke dalam kas pembangunan.</p>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-1">
                        <a href="{{ route('admin.verification', ['selected_id' => $selectedDonation->id, 'selected_tipe' => 'donation']) }}" class="w-full rounded-xl bg-[#f2eee7] hover:bg-[#e8e2d8] py-2.5 text-xs font-bold text-stone-800 shadow-2xs transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>Lihat berkas verifikasi</span>
                        </a>
                        <p class="text-[11px] text-stone-400 text-center mt-2">{{ $selJenisBadge === 'Donasi Saja' ? 'Donasi Saja tidak memiliki pesanan atau status pengiriman.' : 'Pesanan kaos terhubung: ' . ($selRelatedOrder->nomor_pesanan ?? '') }}</p>
                    </div>
                </div>
            @endif

            <!-- Ketentuan Donasi Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                <h3 class="font-serif text-lg font-bold text-stone-900">Ketentuan donasi</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-5 w-5 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-stone-900">Donasi Saja</p>
                            <p class="mt-0.5 text-xs text-stone-500 leading-relaxed">Sukarela, tanpa batas minimum. Tidak memerlukan alamat pengiriman.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="#b55322" class="h-5 w-5 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-stone-900">Pesan Kaos + Donasi</p>
                            <p class="mt-0.5 text-xs text-stone-500 leading-relaxed">Donasi minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}, terpisah dari harga kaos dan ongkir.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
