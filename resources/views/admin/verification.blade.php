@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')
@section('breadcrumb', 'Verifikasi Pembayaran')

@section('content')
    @php
        $metodeLabel = function ($m) {
            if (empty($m)) return 'Transfer Bank';
            if (strtolower($m) === 'qris') return 'QRIS Merchant';
            return ucfirst($m);
        };
        $initialsOf = function ($nama) {
            $parts = preg_split('/\s+/', trim($nama ?? ''));
            $ini = '';
            foreach (array_slice(array_filter($parts), 0, 2) as $p) {
                $ini .= mb_strtoupper(mb_substr($p, 0, 1));
            }
            return $ini ?: 'JB';
        };
        $statusBadge = function ($st) {
            return match ($st) {
                'terverifikasi' => 'bg-emerald-50 border border-emerald-200/80 text-emerald-700',
                'menunggu_verifikasi' => 'bg-amber-50 border border-amber-200/80 text-amber-800',
                'bukti_ulang' => 'bg-amber-50 border border-amber-200/80 text-amber-800',
                'ditolak' => 'bg-red-50 border border-red-200/80 text-red-700',
                default => 'bg-stone-100 border border-stone-200 text-stone-500',
            };
        };
        $statusLabel = function ($st) {
            return match ($st) {
                'terverifikasi' => 'Terverifikasi',
                'menunggu_verifikasi' => 'Menunggu',
                'bukti_ulang' => 'Bukti ulang',
                'ditolak' => 'Ditolak',
                default => 'Belum bayar',
            };
        };
    @endphp

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
            <p class="mt-1 text-sm text-stone-500">Periksa bukti pembayaran, cocokkan nominal, dan konfirmasi pesanan secara manual.</p>
        </div>
        <a href="{{ route('admin.verification') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Refresh Data</span>
        </a>
    </div>

    <!-- 4 Metrics Row -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.verification', ['status' => 'menunggu_verifikasi']) }}"
            class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center justify-between gap-3 hover:border-[#b55322]/50 hover:shadow-xs transition group">
            <div class="flex items-center gap-3 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#b55322]/10 text-[#b55322]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.25 3h6m-6 3h6m-6-6h.008v.008h-.008v-.008ZM3.75 16.5v3.75m13.5-3.75v3.75M6 8.25h12a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5Z" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-stone-500">Menunggu Verifikasi</p>
                    <p class="mt-0.5 font-sans text-2xl font-bold text-stone-900 leading-none">{{ $statMenunggu }} <span class="text-xs font-medium text-stone-400">pesanan</span></p>
                    <p class="mt-1 text-xs text-stone-600">Rp{{ number_format($rpMenunggu, 0, ',', '.') }}</p>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-stone-300 shrink-0 group-hover:text-stone-500 transition">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>

        <a href="{{ route('admin.verification', ['status' => 'bukti_ulang']) }}"
            class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center justify-between gap-3 hover:border-[#b55322]/50 hover:shadow-xs transition group">
            <div class="flex items-center gap-3 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-secondary/10 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-stone-500">Perlu Bukti Ulang</p>
                    <p class="mt-0.5 font-sans text-2xl font-bold text-stone-900 leading-none">{{ $statBuktiUlang }} <span class="text-xs font-medium text-stone-400">pesanan</span></p>
                    <p class="mt-1 text-xs text-stone-600">Rp{{ number_format($rpBuktiUlang, 0, ',', '.') }}</p>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-stone-300 shrink-0 group-hover:text-stone-500 transition">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>

        <a href="{{ route('admin.verification', ['status' => 'terverifikasi']) }}"
            class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center justify-between gap-3 hover:border-[#b55322]/50 hover:shadow-xs transition group">
            <div class="flex items-center gap-3 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-secondary text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-stone-500">Terverifikasi</p>
                    <p class="mt-0.5 font-sans text-2xl font-bold text-stone-900 leading-none">{{ $statTerverifikasi }} <span class="text-xs font-medium text-stone-400">pesanan</span></p>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-stone-300 shrink-0 group-hover:text-stone-500 transition">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>

        <a href="{{ route('admin.verification', ['status' => 'ditolak']) }}"
            class="rounded-2xl border border-[#e8e4dc] bg-white p-4 sm:p-5 flex items-center justify-between gap-3 hover:border-red-300 hover:shadow-xs transition group">
            <div class="flex items-center gap-3 min-w-0">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-red-50 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-stone-500">Ditolak / Bermasalah</p>
                    <p class="mt-0.5 font-sans text-2xl font-bold text-red-700 leading-none">{{ $statDitolak + $statBelumBayar }} <span class="text-xs font-medium text-stone-400">pesanan</span></p>
                    <p class="mt-1 text-xs text-stone-600">Rp{{ number_format($rpBelumBayar, 0, ',', '.') }}</p>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-stone-300 shrink-0 group-hover:text-stone-500 transition">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>
    </div>

    <!-- Main Two-Column Master-Detail Layout -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">

        <!-- Left Column (Daftar Pesanan) -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">

            <!-- Filters Bar -->
            <form method="GET" action="{{ route('admin.verification') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[240px] flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, ID pesanan, atau nomor WhatsApp..." class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
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

            <!-- Bulk Delete Form (Hidden) -->
            <form id="bulk-delete-form" method="POST" action="{{ route('admin.verification.bulk_delete') }}" style="display: none;">
                @csrf
                <input type="hidden" name="ids" id="bulk-delete-ids" value="">
            </form>

            <!-- Daftar Pesanan Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <!-- Card Header -->
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Daftar Pesanan</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-stone-400">Menampilkan {{ $transaksi->count() }} dari {{ $transaksi->total() }} pesanan</span>
                        <button type="button" id="bulk-delete-btn" onclick="confirmBulkDelete()" class="hidden rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span id="bulk-delete-count">Hapus (0)</span>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead>
                            <tr class="bg-[#f4efe8] text-[11px] font-semibold text-stone-600">
                                <th class="w-9 rounded-l-xl px-2 py-2.5"><input type="checkbox" id="check-all" class="rounded text-[#b55322] focus:ring-[#b55322] cursor-pointer"></th>
                                <th class="px-2 py-2.5 font-semibold">No.</th>
                                <th class="px-3 py-2.5 font-semibold">Nama Pemesan</th>
                                <th class="px-3 py-2.5 font-semibold">Jenis</th>
                                <th class="px-3 py-2.5 font-semibold">Total</th>
                                <th class="px-3 py-2.5 font-semibold">Pembayaran</th>
                                <th class="px-3 py-2.5 font-semibold">Status</th>
                                <th class="rounded-r-xl px-3 py-2.5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-sm">
                            @forelse ($transaksi as $i => $t)
                                @php
                                    $isOrder = $t instanceof App\Models\Order;
                                    $status = $isOrder ? $t->status_pembayaran : $t->status;
                                    $tipe = $isOrder ? 'order' : 'donation';
                                    $idLabel = $isOrder ? $t->nomor_pesanan : $t->kode;
                                    $tagihan = $isOrder ? $t->totalTagihan() : $t->nominal;
                                    $jenisRow = $isOrder
                                        ? ($t->jenis === 'kaos_donasi' ? ($t->donasi > 0 ? 'Kaos + Donasi' : 'Kaos') : 'Kaos')
                                        : 'Donasi';
                                    $isSelected = $selectedItem && $selectedItem->id === $t->id && $selectedTipe === $tipe;
                                    $hasBukti = !empty($t->bukti_path);
                                    $bisaVerifikasi = in_array($status, ['menunggu_verifikasi', 'bukti_ulang']) && $hasBukti;
                                @endphp
                                <tr class="{{ $isSelected ? 'bg-[#faefe8]' : 'hover:bg-stone-50/60' }} transition">
                                    <td class="px-2 py-3"><input type="checkbox" class="row-checkbox rounded text-[#b55322] focus:ring-[#b55322] cursor-pointer" value="{{ $t->id }}_{{ $tipe }}" data-id="{{ $t->id }}" data-tipe="{{ $tipe }}"></td>
                                    <td class="px-2 py-3 text-xs text-stone-400">{{ $i + 1 }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#eef0e9] text-[11px] font-bold text-stone-700">{{ $initialsOf($t->nama) }}</span>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.verification', array_merge(request()->query(), ['selected_id' => $t->id, 'selected_tipe' => $tipe])) }}" class="block text-xs font-bold text-stone-900 leading-tight truncate hover:text-[#b55322] transition">{{ $t->nama }}</a>
                                                <p class="text-[11px] text-stone-400 mt-0.5">{{ $t->nomor ?: '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-stone-700">{{ $jenisRow }}</td>
                                    <td class="px-3 py-3 text-xs font-semibold text-stone-900 whitespace-nowrap">Rp{{ number_format($tagihan, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">
                                        <p class="text-xs text-stone-800 leading-tight whitespace-nowrap">{{ $metodeLabel($t->metode ?? null) }}</p>
                                        <p class="text-[11px] text-stone-400 mt-0.5 whitespace-nowrap">{{ $t->updated_at ? $t->updated_at->translatedFormat('d M Y') : '—' }}</p>
                                        <p class="text-[11px] text-stone-400 whitespace-nowrap">{{ $t->updated_at ? $t->updated_at->format('H.i') : '' }}</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold whitespace-nowrap {{ $statusBadge($status) }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $status === 'terverifikasi' ? 'bg-emerald-500' : ($status === 'ditolak' ? 'bg-red-500' : ($status === 'belum_bayar' ? 'bg-stone-400' : 'bg-amber-500')) }}"></span>
                                            {{ $statusLabel($status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.verification', array_merge(request()->query(), ['selected_id' => $t->id, 'selected_tipe' => $tipe])) }}"
                                                class="grid h-8 w-8 place-items-center rounded-full border border-[#e8e4dc] bg-white text-stone-500 transition hover:bg-stone-50 hover:text-stone-900 cursor-pointer"
                                                title="Lihat detail" aria-label="Lihat detail">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </a>

                                            @if ($bisaVerifikasi)
                                                <form method="POST" action="{{ route('admin.verification.action', [$t->id, $tipe]) }}">
                                                    @csrf
                                                    <input type="hidden" name="aksi" value="verifikasi">
                                                    <input type="hidden" name="kirim_wa" value="1">
                                                    <button type="submit"
                                                        class="grid h-8 w-8 place-items-center rounded-full bg-secondary text-white transition hover:bg-[#0b3b22] cursor-pointer"
                                                        title="Verifikasi cepat (kirim notifikasi WA)" aria-label="Verifikasi cepat">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif (!$hasBukti && in_array($status, ['menunggu_verifikasi', 'bukti_ulang', 'belum_bayar']))
                                                <span class="grid h-8 w-8 place-items-center rounded-full bg-stone-100 text-stone-300 cursor-not-allowed select-none"
                                                    title="Verifikasi belum aktif: user belum mengirim bukti pembayaran" aria-label="Bukti pembayaran belum diunggah">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </span>
                                            @elseif ($status === 'ditolak')
                                                <form method="POST" action="{{ route('admin.verification.action', [$t->id, $tipe]) }}">
                                                    @csrf
                                                    <input type="hidden" name="aksi" value="bukti_ulang">
                                                    <button type="submit"
                                                        class="grid h-8 w-8 place-items-center rounded-full border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100 cursor-pointer"
                                                        title="Minta bukti ulang" aria-label="Minta bukti ulang">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-4 w-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif ($status === 'terverifikasi')
                                                <span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-500" title="Sudah terverifikasi" aria-label="Sudah terverifikasi">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-3 py-10 text-center text-xs text-stone-400">Tidak ada pesanan yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $transaksi->firstItem() }}&ndash;{{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} pesanan</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($transaksi->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $transaksi->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        @php
                            $pageStart = max(1, $transaksi->currentPage() - 2);
                            $pageEnd = min($transaksi->lastPage(), $pageStart + 4);
                            $pageStart = max(1, min($pageStart, $pageEnd - 4));
                        @endphp
                        @foreach (range($pageStart, $pageEnd) as $p)
                            @if ($p === $transaksi->currentPage())
                                <span class="grid h-7 w-7 place-items-center rounded-lg bg-secondary font-bold text-white shadow-2xs">{{ $p }}</span>
                            @else
                                <a href="{{ $transaksi->url($p) }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">{{ $p }}</a>
                            @endif
                        @endforeach

                        @if ($transaksi->hasMorePages())
                            <a href="{{ $transaksi->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
                        @else
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info Note -->
            <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#e8e4dc] bg-[#f8f6f0] p-4 text-xs text-stone-600">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 text-stone-600 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <span>Jika bukti pembayaran belum sesuai, Anda dapat menghubungi pemesan melalui WhatsApp atau menolak pesanan dengan alasan yang jelas.</span>
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
                    $selJenis = $selIsOrder
                        ? ($selectedItem->jenis === 'kaos_donasi' ? ($selectedItem->donasi > 0 ? 'Kaos + Donasi' : 'Kaos') : 'Kaos')
                        : 'Donasi Saja';

                    $buktiPath = $selectedItem->bukti_path;
                    $buktiUrl = $selectedItem->buktiUrl();
                    $buktiExt = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                    $isImage = in_array($buktiExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                    $isPdf = $buktiExt === 'pdf';

                    // Kelompokkan item per desain + gender untuk chip ukuran
                    $itemGroups = [];
                    if ($selIsOrder) {
                        foreach ($selectedItem->items_list as $it) {
                            $g = $it['gender'] ?? 'pria';
                            $key = $it['design_id'] . '|' . $g;
                            if (!isset($itemGroups[$key])) {
                                $itemGroups[$key] = ['nama' => $it['nama'], 'design_id' => $it['design_id'], 'gender' => $g, 'items' => [], 'total' => 0];
                            }
                            $itemGroups[$key]['items'][] = $it;
                            $itemGroups[$key]['total'] += $it['jumlah'];
                        }
                    }
                @endphp

                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                    <!-- Panel Header -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h2 class="font-serif text-lg font-bold text-stone-900">Detail Pesanan</h2>
                            <p class="mt-0.5 font-sans text-xl font-bold text-stone-900">{{ $selId }}</p>
                            <p class="mt-0.5 text-[11px] text-stone-400">{{ $selectedItem->updated_at ? $selectedItem->updated_at->translatedFormat('d M Y') . ' · ' . $selectedItem->updated_at->format('H.i') : '—' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap {{ $statusBadge($selStatus) }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $selStatus === 'terverifikasi' ? 'bg-emerald-500' : ($selStatus === 'ditolak' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                            {{ $statusLabel($selStatus) }}
                        </span>
                    </div>

                    <!-- Pemesan -->
                    <div class="flex items-center gap-3 rounded-2xl bg-[#f8f6f0] border border-[#ede8df] p-3.5">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#eef0e9] text-xs font-bold text-stone-700">{{ $initialsOf($selNama) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-stone-900 truncate">{{ $selNama }}</p>
                            <p class="text-[11px] text-stone-500 mt-0.5 truncate">{{ $selectedItem->nomor ?: '—' }}</p>
                            <p class="text-[11px] text-stone-400 truncate">{{ $selectedItem->email ?: '—' }}</p>
                        </div>
                        @if ($selectedItem->nomor)
                            @php($selWaUrl = app(\App\Services\FonnteService::class)->getDirectWaLink($selectedItem, $selectedTipe))
                            <a href="{{ $selWaUrl }}" target="_blank" rel="noopener" title="Hubungi via WhatsApp"
                                class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#e8e4dc] bg-white transition hover:bg-stone-50 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    @if ($selIsOrder && count($itemGroups))
                        <!-- Produk yang dipesan -->
                        <div>
                            <h3 class="text-xs font-bold text-stone-900">Produk yang dipesan</h3>
                            @foreach ($itemGroups as $gr)
                                @php($grDesign = \App\Models\Design::find($gr['design_id']))
                                <div class="mt-2.5 flex items-start gap-3 rounded-2xl bg-[#f8f6f0] border border-[#ede8df] p-3">
                                    @if ($grDesign && $grDesign->gambar)
                                        <img src="{{ asset($grDesign->gambar) }}" alt="{{ $gr['nama'] }}" class="h-16 w-16 shrink-0 rounded-xl border border-[#ede8df] bg-white object-contain p-1">
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-stone-900 leading-tight">{{ $gr['nama'] }}</p>
                                        <p class="text-[11px] text-stone-400 mt-0.5">Kaos {{ $gr['gender'] === 'wanita' ? 'Wanita' : 'Unisex' }}</p>
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach ($gr['items'] as $gi)
                                                <span class="rounded-md border border-[#e8e4dc] bg-white px-2 py-0.5 text-[10px] font-bold text-stone-700">{{ $gi['ukuran'] }} &times; {{ $gi['jumlah'] }}</span>
                                            @endforeach
                                        </div>
                                        <p class="mt-1.5 text-[11px] text-stone-500">{{ $gr['total'] }} pcs &middot; Rp{{ number_format(collect($gr['items'])->sum('subtotal'), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ route('admin.orders.show', $selectedItem->id) }}"
                                class="mt-2.5 flex w-full items-center justify-between rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs font-semibold text-stone-700 transition hover:bg-stone-50 cursor-pointer">
                                <span>Lihat detail produk</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-stone-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    @else
                        <!-- Donasi saja -->
                        <div>
                            <h3 class="text-xs font-bold text-stone-900">Donasi yang diberikan</h3>
                            <div class="mt-2.5 flex items-start gap-3 rounded-2xl bg-[#f8f6f0] border border-[#ede8df] p-3">
                                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-[#b55322]/10 text-[#b55322]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-stone-900 leading-tight">Donasi Pembangunan</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">Tanpa pemesanan kaos</p>
                                    <p class="mt-1.5 text-[11px] text-stone-500">{{ $selJenis }} &middot; Rp{{ number_format($selectedItem->nominal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Rincian Pembayaran -->
                    <div>
                        <h3 class="text-xs font-bold text-stone-900">Rincian Pembayaran</h3>
                        <div class="mt-2 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Metode</span>
                                <span class="font-semibold text-stone-800">{{ $metodeLabel($selectedItem->metode ?? null) }}</span>
                            </div>
                            @if ($selIsOrder)
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Subtotal ({{ collect($selectedItem->items_list)->sum('jumlah') }} pcs)</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->subtotal_kaos, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Donasi</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->donasi, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Ongkir</span>
                                    <span class="font-semibold text-stone-800">Rp{{ number_format($selectedItem->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center justify-between rounded-xl bg-[#f8f6f0] px-3.5 py-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Total Pembayaran</span>
                            <span class="font-sans text-xl font-bold text-secondary">Rp{{ number_format($selTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div>
                        <h3 class="text-xs font-bold text-stone-900">Bukti Pembayaran</h3>
                        @if ($buktiPath && $isImage)
                            <div class="mt-2 flex items-center gap-3 rounded-2xl border border-[#ede8df] bg-white p-3">
                                <img src="{{ $buktiUrl }}" alt="Bukti pembayaran {{ $selId }}" class="h-16 w-16 shrink-0 rounded-lg border border-[#ede8df] object-cover cursor-pointer" onclick="openProofLightbox('{{ $buktiUrl }}', '{{ basename($buktiPath) }}')">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-stone-900">{{ basename($buktiPath) }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">{{ $selectedItem->updated_at ? $selectedItem->updated_at->translatedFormat('d M Y') . ' · ' . $selectedItem->updated_at->format('H.i') : '—' }}</p>
                                </div>
                                <a href="{{ $buktiUrl }}" download class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#e8e4dc] bg-white text-stone-500 transition hover:bg-stone-50 hover:text-stone-900 cursor-pointer" title="Unduh bukti" aria-label="Unduh bukti">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </a>
                            </div>
                        @elseif ($buktiPath && $isPdf)
                            <div class="mt-2 flex items-center gap-3 rounded-2xl border border-[#ede8df] bg-white p-3">
                                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-lg bg-red-50 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-stone-900">{{ basename($buktiPath) }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">Dokumen PDF bukti transfer</p>
                                </div>
                                <a href="{{ $buktiUrl }}" target="_blank" rel="noopener" class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#e8e4dc] bg-white text-stone-500 transition hover:bg-stone-50 hover:text-stone-900 cursor-pointer" title="Buka PDF" aria-label="Buka PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" /></svg>
                                </a>
                            </div>
                        @else
                            <div class="mt-2 rounded-2xl border-2 border-dashed border-[#dcd6ca] bg-white/70 p-5 text-center">
                                <p class="text-xs font-bold text-stone-800">Belum ada bukti pembayaran</p>
                                <p class="mt-1 text-[11px] text-stone-500 leading-relaxed">
                                    @if ($selStatus === 'belum_bayar')
                                        Pemesan/donatur belum mengunggah foto bukti transfer.
                                    @else
                                        Bukti transfer belum diunggah atau perlu diminta bukti ulang.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($selStatus === 'terverifikasi')
                        <!-- Status terverifikasi -->
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

                        @if ($selectedItem->nomor)
                            <form id="form-resend-wa" method="POST" action="{{ route('admin.verification.resend_wa', [$selectedItem->id, $selectedTipe]) }}">
                                @csrf
                                <button type="submit" id="btn-resend-wa" class="w-full rounded-xl bg-secondary hover:bg-[#0b3b22] py-2.5 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>Kirim via WhatsApp</span>
                                </button>
                            </form>
                        @endif

                        @if ($selIsOrder)
                            <a href="{{ route('admin.orders.show', $selectedItem->id) }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <span>Kelola Pengiriman di Pesanan Kaos &rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('admin.donations.show', $selectedItem->id) }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <span>Lihat di Buku Donasi &rarr;</span>
                            </a>
                        @endif

                        @if ($selectedItem->catatan_panitia)
                            <div>
                                <h3 class="text-xs font-bold text-stone-900">Catatan pemeriksaan</h3>
                                <div class="mt-2 rounded-xl bg-[#f8f6f0] p-3 text-xs text-stone-700 leading-relaxed">{{ $selectedItem->catatan_panitia }}</div>
                            </div>
                        @endif
                    @else
                        <!-- Form Keputusan Verifikasi -->
                        <form method="POST" action="{{ route('admin.verification.action', [$selectedItem->id, $selectedTipe]) }}" class="space-y-3 pt-1">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-stone-800 mb-1.5">Catatan pemeriksaan</label>
                                <textarea name="catatan_panitia" rows="2" class="w-full rounded-xl border border-[#e8e4dc] p-3 text-xs text-stone-800 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none" placeholder="Nominal sesuai tagihan. Cocokkan dana masuk pada mutasi sebelum verifikasi.">{{ old('catatan_panitia', $selectedItem->catatan_panitia ?? 'Nominal sesuai tagihan. Cocokkan dana masuk pada mutasi sebelum verifikasi.') }}</textarea>
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
                                @if (!empty($selectedItem->bukti_path))
                                    <button type="submit" name="aksi" value="verifikasi" class="w-full rounded-xl bg-secondary hover:bg-[#0b3b22] py-3 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span>Verifikasi Pembayaran</span>
                                    </button>
                                @else
                                    <button type="button" disabled class="w-full rounded-xl bg-stone-200 py-3 text-xs font-bold text-stone-400 cursor-not-allowed flex items-center justify-center gap-2 select-none" title="User belum mengunggah bukti pembayaran">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4 text-stone-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span>Verifikasi Pembayaran (Belum Aktif)</span>
                                    </button>
                                    <div class="flex items-start gap-2 rounded-xl bg-amber-50/90 border border-amber-200/80 p-2.5 text-xs text-amber-800 leading-relaxed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Tombol verifikasi belum aktif karena user belum mengirim bukti pembayaran. Tombol akan aktif setelah user mengunggah bukti pembayaran.</span>
                                    </div>
                                @endif

                                <button type="submit" name="aksi" value="tolak" class="w-full rounded-xl border border-[#e8e4dc] bg-white hover:bg-stone-50 py-2.5 text-xs font-bold text-stone-700 transition flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-stone-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    <span>Tolak {{ $selIsOrder ? 'Pesanan' : 'Donasi' }}</span>
                                </button>

                                <button type="submit" name="aksi" value="bukti_ulang" class="w-full rounded-xl border border-amber-200 bg-amber-50/60 hover:bg-amber-100/70 py-2.5 text-xs font-bold text-amber-800 transition flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-amber-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    <span>Minta Bukti Ulang</span>
                                </button>
                            </div>

                            <div class="flex items-start gap-2 rounded-xl bg-[#fbf7ee] border border-[#f3ecd8] p-3 text-xs text-stone-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-amber-600 shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                <p class="leading-relaxed">Pastikan nominal, tanggal, dan pengirim sesuai dengan data pesanan sebelum melakukan verifikasi.</p>
                            </div>
                        </form>
                    @endif

                    <!-- Delete Action -->
                    <div class="pt-3 border-t border-stone-100">
                        <form method="POST" action="{{ route('admin.verification.destroy', [$selectedItem->id, $selectedTipe]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $selId }} ({{ $selNama }})? Tindakan ini akan menghapus data dan berkas bukti secara permanen.');">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50/60 hover:bg-red-100/80 py-2.5 text-xs font-semibold text-red-700 transition flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>Hapus Data {{ $selIsOrder ? 'Pesanan' : 'Donasi' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 text-center text-xs text-stone-400">
                    Pilih pesanan dari tabel untuk melihat rincian bukti dan melakukan verifikasi.
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
        // Seleksi massal
        const checkAll = document.getElementById('check-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const bulkDeleteCount = document.getElementById('bulk-delete-count');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');
        const bulkDeleteIds = document.getElementById('bulk-delete-ids');

        function updateBulkDeleteUI() {
            const selected = Array.from(rowCheckboxes).filter(cb => cb.checked);
            const count = selected.length;
            bulkDeleteCount.textContent = 'Hapus (' + count + ')';
            bulkDeleteBtn.classList.toggle('hidden', count === 0);
        }

        if (checkAll && rowCheckboxes.length) {
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                rowCheckboxes.forEach(cb => { cb.checked = isChecked; });
                updateBulkDeleteUI();
            });

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkDeleteUI);
            });
        }

        function confirmBulkDelete() {
            const selected = Array.from(rowCheckboxes).filter(cb => cb.checked);
            if (selected.length === 0) return;

            if (!confirm(`Yakin ingin menghapus ${selected.length} data transaksi terpilih? Data dan bukti pembayaran akan dihapus permanen.`)) return;

            const ids = selected.map(cb => cb.value);
            bulkDeleteIds.value = JSON.stringify(ids);
            bulkDeleteForm.submit();
        }

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
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                        btn.classList.remove('bg-secondary', 'hover:bg-[#0b3b22]', 'text-white');
                        btn.classList.add('bg-emerald-600', 'text-white');
                        btn.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>${data.message || 'Pesan berhasil dikirim ke WhatsApp!'}</span>
                        `;
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-emerald-600');
                            btn.classList.add('bg-secondary', 'hover:bg-[#0b3b22]', 'text-white');
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
