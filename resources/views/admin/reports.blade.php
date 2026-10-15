@extends('layouts.admin')

@section('title', 'Laporan Dana')
@section('breadcrumb', 'Laporan Dana')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI PROGRAM</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Laporan Dana</h1>
            <p class="mt-1 text-sm text-stone-500">Pisahkan penerimaan dan biaya agar dana pembangunan tetap transparan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white px-3.5 py-2 text-xs font-semibold shadow-2xs transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Dana Pengeluaran</span>
            </a>
            <a href="{{ route('admin.reports.export', ['format' => 'xlsx', 'from' => $from, 'to' => $to, 'jenis' => request('jenis')]) }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition" title="Unduh Laporan Keuangan format Excel (.xlsx) rapi & siap pakai">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-1.125c0-.621-.504-1.125-1.125-1.125H12M3.375 4.5h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 0 1 2.25 16.125V5.625C2.25 5.004 2.754 4.5 3.375 4.5Z" />
                </svg>
                <span>Excel (.xlsx)</span>
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>PDF</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar Row -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports') }}" id="reportFilterForm" class="flex flex-wrap items-center gap-3 relative">
            <!-- Calendar Range Dropdown Trigger -->
            <div class="relative inline-block text-left" id="datePickerContainer">
                <button type="button" id="datePickerBtn" class="inline-flex items-center gap-2.5 rounded-xl border {{ (request('from') || request('to')) ? 'border-[#b55322] bg-[#fbf6f3] text-[#b55322]' : 'border-[#e8e4dc] bg-white text-stone-700' }} px-3.5 py-2.5 text-xs font-semibold shadow-2xs hover:border-[#b55322] transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 {{ (request('from') || request('to')) ? 'text-[#b55322]' : 'text-stone-500' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span id="dateRangeLabel">
                        {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" id="dateChevron" class="h-3.5 w-3.5 text-stone-400 transition-transform duration-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Popover Modal: Luas, Mewah, dan Proporsional -->
                <div id="datePickerPopover" class="hidden absolute left-0 top-full mt-2.5 z-50 w-[94vw] sm:w-[680px] md:w-[720px] max-w-[720px] rounded-3xl border border-[#e8e4dc] bg-white p-6 shadow-2xl transition-all duration-200">
                    <!-- Popover Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-stone-100">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-8 w-8 place-items-center rounded-xl bg-[#b55322]/10 text-[#b55322]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="font-serif font-bold text-stone-900 text-base leading-tight">Pilih Periode Laporan Keuangan</h3>
                                    <p class="text-xs text-stone-400 mt-0.5">Tentukan rentang tanggal transaksi atau pilih template cepat di bawah</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="closeDatePickerBtn" class="text-stone-400 hover:text-stone-700 p-1.5 rounded-xl hover:bg-stone-100 transition cursor-pointer" title="Tutup">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Popover Main Grid: 2 Kolom Luas & Proporsional -->
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-12 gap-6 items-start">
                        <!-- Kolom Kiri: Preset Cepat Vertikal -->
                        <div class="sm:col-span-5 flex flex-col sm:border-r sm:border-stone-100 sm:pr-5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2.5">Preset Rentang Cepat</p>
                            
                            <div class="space-y-2">
                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="2026-07-01" data-to="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">Semua Periode</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">01 Jul &ndash; Sekarang</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>

                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="{{ now()->startOfMonth()->format('Y-m-d') }}" data-to="{{ now()->endOfMonth()->format('Y-m-d') }}">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">Bulan Ini ({{ now()->translatedFormat('F') }})</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">01 {{ now()->translatedFormat('M') }} &ndash; {{ now()->endOfMonth()->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>

                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}" data-to="{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">Bulan Lalu ({{ now()->subMonth()->translatedFormat('F') }})</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">01 {{ now()->subMonth()->translatedFormat('M') }} &ndash; {{ now()->subMonth()->endOfMonth()->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>

                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="2026-07-01" data-to="2026-09-30">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">Kuartal 3 (Jul &ndash; Sep)</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">01 Jul &ndash; 30 Sep 2026</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>

                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="{{ now()->subDays(6)->format('Y-m-d') }}" data-to="{{ now()->format('Y-m-d') }}">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">7 Hari Terakhir</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">{{ now()->subDays(6)->translatedFormat('d M') }} &ndash; {{ now()->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>

                                <button type="button" class="preset-btn w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-left hover:border-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer flex items-center justify-between group"
                                    data-from="{{ now()->subDays(29)->format('Y-m-d') }}" data-to="{{ now()->format('Y-m-d') }}">
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#b55322] transition">30 Hari Terakhir</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">{{ now()->subDays(29)->translatedFormat('d M') }} &ndash; {{ now()->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="text-stone-300 group-hover:text-[#b55322] text-xs font-bold transition">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Input Tanggal & Kalender Visual Interaktif -->
                        <div class="sm:col-span-7 flex flex-col justify-between space-y-4">
                            <!-- Input Tanggal Kustom Luas -->
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">Rentang Tanggal Kustom</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="filter_from" class="block text-[11px] font-semibold text-stone-600 mb-1">Dari Tanggal</label>
                                        <input type="date" name="from" id="filter_from" value="{{ $from }}"
                                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label for="filter_to" class="block text-[11px] font-semibold text-stone-600 mb-1">Sampai Tanggal</label>
                                        <input type="date" name="to" id="filter_to" value="{{ $to }}"
                                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                                    </div>
                                </div>
                            </div>

                            <!-- Visual Interactive Calendar Grid -->
                            <div class="rounded-2xl border border-[#e8e4dc] bg-[#faf8f5] p-3.5">
                                <!-- Calendar Month Navigator -->
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200">
                                    <button type="button" id="calPrevMonthBtn" class="p-1 rounded-lg hover:bg-stone-200 text-stone-600 transition cursor-pointer" title="Bulan Sebelumnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                    <span id="calMonthYearTitle" class="font-serif font-bold text-xs text-stone-900 tracking-wide">
                                        Oktober 2026
                                    </span>
                                    <button type="button" id="calNextMonthBtn" class="p-1 rounded-lg hover:bg-stone-200 text-stone-600 transition cursor-pointer" title="Bulan Berikutnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Weekday Headers -->
                                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-stone-400 mb-1.5">
                                    <span>Min</span>
                                    <span>Sen</span>
                                    <span>Sel</span>
                                    <span>Rab</span>
                                    <span>Kam</span>
                                    <span>Jum</span>
                                    <span>Sab</span>
                                </div>

                                <!-- Days Grid Container (diisi oleh JS) -->
                                <div id="calDaysGrid" class="grid grid-cols-7 gap-1 text-center text-xs">
                                </div>
                            </div>

                            <!-- Live Selected Summary Text -->
                            <div class="flex items-center gap-2 rounded-xl bg-[#f4efe8] px-3.5 py-2 text-[11px] text-stone-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-[#b55322] shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span id="calSelectedSummary" class="font-medium truncate">
                                    Rentang terpilih: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Popover Footer -->
                    <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between">
                        <a href="{{ route('admin.reports') }}" class="text-xs font-semibold text-stone-500 hover:text-[#b55322] transition">
                            Reset ke Semua Periode
                        </a>
                        <div class="flex items-center gap-2.5">
                            <button type="button" id="cancelDatePickerBtn" class="px-4 py-2 rounded-xl border border-stone-200 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] text-xs font-bold text-white shadow-xs transition cursor-pointer flex items-center gap-1.5">
                                <span>Terapkan Filter</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jenis Dukungan Select Filter -->
            <select name="jenis" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs font-semibold text-stone-700 shadow-2xs focus:border-[#b55322] focus:outline-none cursor-pointer">
                <option value="" {{ request('jenis') == '' ? 'selected' : '' }}>Semua jenis dukungan</option>
                <option value="donasi" {{ request('jenis') == 'donasi' ? 'selected' : '' }}>Donasi Saja</option>
                <option value="kaos" {{ request('jenis') == 'kaos' ? 'selected' : '' }}>Pesan Kaos</option>
            </select>

            <!-- Reset Filter Badge (jika filter aktif) -->
            @if(request()->has('from') || request()->has('to') || request()->filled('jenis'))
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-dashed border-[#b55322]/40 bg-[#fbf6f3] px-3 py-2 text-xs font-semibold text-[#b55322] hover:bg-[#f6eee8] transition" title="Hapus semua filter tanggal dan jenis">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                    <span>Reset Filter</span>
                </a>
            @endif
        </form>

        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
            <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
            Hanya pembayaran terverifikasi
        </span>
    </div>

    <!-- Metrics Row: Dana Pembangunan -->
    <div class="grid gap-4 sm:grid-cols-3">
        <!-- Card 1: Dana Pembangunan (Total) -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Dana Pembangunan</p>
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A2.25 2.25 0 0 0 17.25 8H6.75A2.25 2.25 0 0 0 4.5 10.333V21" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($danaPembangunan, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">Donasi + omzet kaos terverifikasi</p>
        </div>

        <!-- Card 2: Pengeluaran -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Pengeluaran</p>
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-rose-50 text-rose-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($belanjaPembangunan, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">{{ $expenses->count() }} catatan pengeluaran</p>
        </div>

        <!-- Card 3: Saldo Pembangunan -->
        <div class="rounded-2xl bg-[#444d41] p-5 text-white flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-white/70">Saldo pembangunan saat ini</p>
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-white/10 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75a.75.75 0 0 1 .75.75v.75m0-9.75h18a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25H3.75m18-9a2.25 2.25 0 0 0-2.25-2.25H3.75" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-white leading-none">Rp{{ number_format($saldoPembangunan, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-white/60">Dana terkumpul dikurangi belanja</p>
        </div>
    </div>

    <!-- Middle Grid: Penerimaan per bulan (left) & Dana untuk pembangunan (right) -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        <!-- Left: Penerimaan per bulan -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 lg:col-span-8 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Dana Pembangunan per bulan</h2>
                        <div class="mt-2 flex items-center gap-4 text-xs">
                            <span class="inline-flex items-center gap-1.5 text-stone-600 font-medium">
                                <span class="h-2 w-2 rounded-xs bg-[#444d41]"></span>
                                Dana Pembangunan
                            </span>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-stone-400">DALAM RUPIAH</span>
                        </div>
                    </div>
                    <span class="text-xs text-stone-400">{{ \Carbon\Carbon::parse($from)->translatedFormat('d M') }} &ndash; {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}</span>
                </div>

                <!-- Monthly Bar Chart with exact Y-Axis grid lines -->
                <div class="mt-6 relative h-48">
                    <!-- Background Grid Lines & Y-Axis Labels -->
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none text-[10px] text-stone-400">
                        <!-- 6 jt -->
                        <div class="flex items-center gap-3 w-full">
                            <span class="w-6 text-right shrink-0">6 jt</span>
                            <div class="flex-1 border-t border-stone-100"></div>
                        </div>
                        <!-- 4 jt -->
                        <div class="flex items-center gap-3 w-full">
                            <span class="w-6 text-right shrink-0">4 jt</span>
                            <div class="flex-1 border-t border-stone-100"></div>
                        </div>
                        <!-- 2 jt -->
                        <div class="flex items-center gap-3 w-full">
                            <span class="w-6 text-right shrink-0">2 jt</span>
                            <div class="flex-1 border-t border-stone-100"></div>
                        </div>
                        <!-- 0 -->
                        <div class="flex items-center gap-3 w-full">
                            <span class="w-6 text-right shrink-0">0</span>
                            <div class="flex-1 border-t border-stone-200"></div>
                        </div>
                    </div>

                    <!-- Bars Area (positioned with left offset for Y-axis) -->
                    <div class="absolute inset-0 left-9 flex items-end justify-around pb-0.5">
                        @foreach($months as $key => $m)
                            @php
                                $danaVal = $danaPembangunanPerBulan[$key] ?? 0;
                                $maxRef = 6000000;
                                $hDana = $danaVal > 0 ? max(6, min(140, round(($danaVal / $maxRef) * 140))) : 0;
                                $labelDana = $danaVal >= 1000000
                                    ? number_format($danaVal / 1000000, 2, ',', '.') . ' jt'
                                    : ($danaVal > 0 ? number_format($danaVal / 1000, 0, ',', '.') . ' rb' : '0');
                            @endphp
                            <div class="flex flex-col items-center flex-1 max-w-[100px]">
                                <div class="flex items-end justify-center h-36">
                                    <div class="relative flex flex-col items-center justify-end h-full">
                                        @if($danaVal > 0)
                                            <span class="text-[9px] font-bold text-stone-700 mb-1 whitespace-nowrap">{{ $labelDana }}</span>
                                            <div class="w-8 sm:w-9 rounded-t-xs bg-[#444d41] transition-all duration-300" style="height: {{ $hDana }}px;" title="Dana Pembangunan: Rp{{ number_format($danaVal, 0, ',', '.') }}"></div>
                                        @else
                                            <div class="w-8 sm:w-9 rounded-t-xs bg-stone-100" style="height: 4px;" title="Dana Pembangunan: Rp0"></div>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[11px] font-medium text-stone-500 mt-2 text-center">{{ $m['short'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Monthly Numbers Table Bar -->
                <div class="mt-8 overflow-x-auto">
                    <div class="flex items-center rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[500px]">
                        <div class="w-36 shrink-0">Jenis</div>
                        @foreach($months as $key => $m)
                            <div class="flex-1 text-center font-bold">{{ $m['short'] }}</div>
                        @endforeach
                    </div>

                    <div class="divide-y divide-stone-100 min-w-[500px] text-xs">
                        <div class="flex items-center px-4 py-3">
                            <div class="w-36 shrink-0 font-bold text-stone-900 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-xs bg-[#444d41]"></span>
                                Dana Pembangunan
                            </div>
                            @foreach($months as $key => $m)
                                <div class="flex-1 text-center font-semibold text-stone-700">
                                    {{ number_format($danaPembangunanPerBulan[$key] ?? 0, 0, ',', '.') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Dana Pembangunan -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 lg:col-span-4 flex flex-col justify-between space-y-5">
            <div>
                <h2 class="font-serif text-lg font-bold text-stone-900">Dana Pembangunan</h2>
                <p class="mt-3 font-serif text-3xl font-bold text-[#b55322] leading-none">Rp{{ number_format($danaPembangunan, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-stone-500 leading-relaxed">Donasi + omzet kaos terverifikasi, sebelum belanja pembangunan.</p>

                <div class="mt-5 space-y-2.5 text-xs border-t border-stone-100 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Belanja pembangunan</span>
                        <span class="font-semibold text-stone-900">&minus; Rp{{ number_format($belanjaPembangunan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Box Saldo Pembangunan -->
                <div class="mt-5 rounded-xl bg-[#f5f2ec] p-4">
                    <p class="text-[11px] font-semibold text-stone-500">Saldo pembangunan</p>
                    <p class="mt-1 font-serif text-2xl font-bold text-stone-900 leading-tight">Rp{{ number_format($saldoPembangunan, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Caution note -->
            <div class="flex items-start gap-2 rounded-xl bg-[#fbf7ee] border border-[#f3ecd8] p-3 text-xs text-stone-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500 shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <p class="leading-relaxed">Ongkir Rp80.000 dibukukan sebagai pengiriman prabayar. Tidak dihitung sebagai dana pembangunan.</p>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Rincian Penerimaan (left) & Rincian Pengeluaran (right) -->
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <!-- Rincian penerimaan -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6">
            <h2 class="font-serif text-lg font-bold text-stone-900">Rincian penerimaan</h2>

            <div class="mt-4 overflow-x-auto">
                <div class="grid grid-cols-12 gap-3 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600">
                    <div class="col-span-8">Kategori</div>
                    <div class="col-span-4 text-right">Jumlah</div>
                </div>

                <div class="divide-y divide-stone-100 text-xs">
                    <div class="grid grid-cols-12 gap-3 items-center px-4 py-3.5">
                        <div class="col-span-8">
                            <p class="font-bold text-stone-900">Donasi Saja</p>
                            <p class="text-stone-400 mt-0.5">{{ $donasiSajaTerverifikasi->count() }} donasi terverifikasi</p>
                        </div>
                        <div class="col-span-4 text-right font-semibold text-stone-900">
                            Rp{{ number_format($donasiSajaTerverifikasi->sum('nominal'), 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-3 items-center px-4 py-3.5">
                        <div class="col-span-8">
                            <p class="font-bold text-stone-900">Donasi dari kaos</p>
                            <p class="text-stone-400 mt-0.5">{{ $donasiDariKaosTerverifikasi->count() }} donasi terverifikasi</p>
                        </div>
                        <div class="col-span-4 text-right font-semibold text-stone-900">
                            Rp{{ number_format($donasiDariKaosTerverifikasi->sum('nominal'), 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-3 items-center px-4 py-3.5">
                        <div class="col-span-8">
                            <p class="font-bold text-stone-900">Omzet kaos</p>
                            <p class="text-stone-400 mt-0.5">{{ $orders->sum('jumlah') }} kaos terverifikasi</p>
                        </div>
                        <div class="col-span-4 text-right font-semibold text-stone-900">
                            Rp{{ number_format($omzetKaos, 0, ',', '.') }}
                        </div>
                    </div>

                </div>

                <div class="mt-3 flex items-center justify-between border-t border-stone-200 px-4 pt-3.5 text-xs">
                    <span class="font-bold text-stone-700">Total dana pembangunan</span>
                    <span class="font-bold text-[#b55322] text-sm">Rp{{ number_format($danaPembangunan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Rincian pengeluaran -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Rincian pengeluaran</h2>
                    <p class="text-xs text-stone-400 mt-0.5">Daftar biaya pembangunan & operasional program</p>
                </div>
                <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-[#faf8f5] hover:bg-[#f4efe8] px-3 py-1.5 text-xs font-semibold text-stone-700 transition">
                    <span>Kelola Pengeluaran</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <div class="grid grid-cols-12 gap-3 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600">
                    <div class="col-span-8">Kategori</div>
                    <div class="col-span-4 text-right">Jumlah</div>
                </div>

                <div class="divide-y divide-stone-100 text-xs">
                    @forelse($expenses as $e)
                        <div class="grid grid-cols-12 gap-3 items-center px-4 py-3.5">
                            <div class="col-span-8">
                                <p class="font-bold text-stone-900">{{ $e->nama }}</p>
                                <p class="text-stone-400 mt-0.5">{{ $e->kategori }} &middot; {{ $e->tanggal ? $e->tanggal->translatedFormat('d M') : '-' }}</p>
                            </div>
                            <div class="col-span-4 text-right font-semibold text-stone-900">
                                Rp{{ number_format($e->jumlah, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-xs text-stone-400">
                            Tidak ada pengeluaran pada rentang tanggal ini.
                        </div>
                    @endforelse
                </div>

                <div class="mt-3 flex items-center justify-between border-t border-stone-200 px-4 pt-3.5 text-xs">
                    <span class="font-bold text-stone-700">Total pengeluaran</span>
                    <span class="font-bold text-[#b55322] text-sm">Rp{{ number_format($pengeluaran, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Callout Banner -->
    <div class="mt-6 flex items-center gap-3 rounded-2xl bg-[#f4efe8] p-4 text-xs text-stone-600">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 text-stone-500 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <p class="leading-relaxed">Saldo Pembangunan Rp{{ number_format($saldoPembangunan, 0, ',', '.') }} = Dana Pembangunan Rp{{ number_format($danaPembangunan, 0, ',', '.') }} &minus; Pengeluaran Rp{{ number_format($belanjaPembangunan, 0, ',', '.') }}. Donasi tertunda dan pesanan belum terverifikasi tidak masuk laporan ini.</p>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('datePickerBtn');
    const popover = document.getElementById('datePickerPopover');
    const chevron = document.getElementById('dateChevron');
    const closeBtn = document.getElementById('closeDatePickerBtn');
    const cancelBtn = document.getElementById('cancelDatePickerBtn');
    const form = document.getElementById('reportFilterForm');
    const inputFrom = document.getElementById('filter_from');
    const inputTo = document.getElementById('filter_to');
    const calMonthYearTitle = document.getElementById('calMonthYearTitle');
    const calDaysGrid = document.getElementById('calDaysGrid');
    const calPrevMonthBtn = document.getElementById('calPrevMonthBtn');
    const calNextMonthBtn = document.getElementById('calNextMonthBtn');
    const calSelectedSummary = document.getElementById('calSelectedSummary');

    const monthNamesIndo = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    let selFrom = inputFrom ? inputFrom.value : '2026-07-01';
    let selTo = inputTo ? inputTo.value : '2026-10-31';

    // Inisialisasi tampilan bulan berdasarkan tanggal 'to'
    let initialDate = new Date(selTo || Date.now());
    if (isNaN(initialDate.getTime())) initialDate = new Date();
    let curYear = initialDate.getFullYear();
    let curMonth = initialDate.getMonth();
    let isSelectingRange = false;

    function formatIndoDateStr(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const y = parts[0];
        const m = parseInt(parts[1], 10) - 1;
        const d = parts[2];
        const mName = monthNamesIndo[m] ? monthNamesIndo[m].substring(0, 3) : '';
        return `${d} ${mName} ${y}`;
    }

    function updateSummary() {
        if (calSelectedSummary) {
            calSelectedSummary.textContent = `Rentang terpilih: ${formatIndoDateStr(selFrom)} – ${formatIndoDateStr(selTo)}`;
        }
    }

    function renderCalendar() {
        if (!calDaysGrid || !calMonthYearTitle) return;

        calMonthYearTitle.textContent = `${monthNamesIndo[curMonth]} ${curYear}`;
        calDaysGrid.innerHTML = '';

        const firstDayIndex = new Date(curYear, curMonth, 1).getDay(); // 0 = Min, 1 = Sen, etc.
        const totalDays = new Date(curYear, curMonth + 1, 0).getDate();
        const prevMonthTotalDays = new Date(curYear, curMonth, 0).getDate();

        // Hari dari bulan sebelumnya (muted)
        for (let i = firstDayIndex - 1; i >= 0; i--) {
            const dayNum = prevMonthTotalDays - i;
            const cell = document.createElement('div');
            cell.className = 'py-1.5 text-stone-300 font-medium cursor-not-allowed select-none text-[11px]';
            cell.textContent = dayNum;
            calDaysGrid.appendChild(cell);
        }

        // Hari pada bulan aktif
        for (let d = 1; d <= totalDays; d++) {
            const mStr = String(curMonth + 1).padStart(2, '0');
            const dStr = String(d).padStart(2, '0');
            const dateStr = `${curYear}-${mStr}-${dStr}`;

            const btnDay = document.createElement('button');
            btnDay.type = 'button';
            btnDay.setAttribute('data-date', dateStr);
            btnDay.textContent = d;

            const isStart = dateStr === selFrom;
            const isEnd = dateStr === selTo;
            const inRange = selFrom && selTo && dateStr > selFrom && dateStr < selTo;

            let baseClass = 'py-1.5 font-semibold text-xs transition select-none cursor-pointer ';

            if (isStart && isEnd) {
                baseClass += 'bg-[#b55322] text-white rounded-xl shadow-2xs font-bold';
            } else if (isStart) {
                baseClass += 'bg-[#b55322] text-white rounded-l-xl font-bold';
            } else if (isEnd) {
                baseClass += 'bg-[#b55322] text-white rounded-r-xl font-bold';
            } else if (inRange) {
                baseClass += 'bg-[#fbeee6] text-[#9d4419] font-medium';
            } else {
                baseClass += 'text-stone-700 hover:bg-[#b55322]/10 hover:text-[#b55322] rounded-xl';
            }

            btnDay.className = baseClass;

            btnDay.addEventListener('click', function(e) {
                e.preventDefault();
                const clickedDate = this.getAttribute('data-date');

                if (!isSelectingRange || clickedDate < selFrom) {
                    selFrom = clickedDate;
                    selTo = clickedDate;
                    isSelectingRange = true;
                } else {
                    selTo = clickedDate;
                    isSelectingRange = false;
                }

                if (inputFrom) inputFrom.value = selFrom;
                if (inputTo) inputTo.value = selTo;

                updateSummary();
                renderCalendar();
            });

            calDaysGrid.appendChild(btnDay);
        }
    }

    if (calPrevMonthBtn) {
        calPrevMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            curMonth--;
            if (curMonth < 0) {
                curMonth = 11;
                curYear--;
            }
            renderCalendar();
        });
    }

    if (calNextMonthBtn) {
        calNextMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            curMonth++;
            if (curMonth > 11) {
                curMonth = 0;
                curYear++;
            }
            renderCalendar();
        });
    }

    function togglePopover(show) {
        if (!popover) return;
        const isHidden = popover.classList.contains('hidden');
        const willShow = (show !== undefined) ? show : isHidden;
        if (willShow) {
            popover.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
            renderCalendar();
            updateSummary();
        } else {
            popover.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            togglePopover();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            togglePopover(false);
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            togglePopover(false);
        });
    }

    // Klik di luar popover menutup popover
    document.addEventListener('click', function(e) {
        if (popover && !popover.contains(e.target) && btn && !btn.contains(e.target)) {
            togglePopover(false);
        }
    });

    // Tutup saat tombol Escape ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            togglePopover(false);
        }
    });

    // Preset tombol cepat: set input dan kirim form seketika
    document.querySelectorAll('.preset-btn').forEach(function(preset) {
        preset.addEventListener('click', function(e) {
            e.preventDefault();
            const fromVal = this.getAttribute('data-from');
            const toVal = this.getAttribute('data-to');
            if (inputFrom && inputTo && form) {
                inputFrom.value = fromVal;
                inputTo.value = toVal;
                selFrom = fromVal;
                selTo = toVal;
                form.submit();
            }
        });
    });

    // Sinkronisasi jika user mengetik di input manual
    if (inputFrom) {
        inputFrom.addEventListener('change', function() {
            selFrom = this.value;
            if (selFrom > selTo) selTo = selFrom;
            updateSummary();
            renderCalendar();
        });
    }

    if (inputTo) {
        inputTo.addEventListener('change', function() {
            selTo = this.value;
            if (selTo < selFrom) selFrom = selTo;
            updateSummary();
            renderCalendar();
        });
    }

    // Render awal kalender
    renderCalendar();
});
</script>
@endpush
