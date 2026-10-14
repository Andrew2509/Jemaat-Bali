@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI PROGRAM</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Dashboard</h1>
            <p class="mt-1 text-sm text-stone-500">Selamat pagi, Panitia. Mari pastikan setiap dukungan dikelola dengan baik.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <span>Juli&ndash;Oktober 2026</span>
        </div>
    </div>

    <!-- 4 Metrics Row -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Card 1 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Total donasi terverifikasi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($donasiTerverifikasi, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">6 donasi · termasuk donasi dari kaos</p>
        </div>

        <!-- Card 2 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Dana bersih kaos</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">Rp{{ number_format($danaBersihKaos, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-stone-400">Omzet Rp{{ number_format($omzetKaos, 0, ',', '.') }} &minus; biaya Rp{{ number_format($biayaProduksi, 0, ',', '.') }}</p>
        </div>

        <!-- Card 3 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Pembayaran perlu verifikasi</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $perluVerifikasi }} transaksi</p>
            <p class="mt-2 text-xs text-stone-400">Rp{{ number_format($rpPerluVerifikasi, 0, ',', '.') }} · bukti sudah dikirim</p>
        </div>

        <!-- Card 4 -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Pesanan belum dikirim</p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="#b55322" class="h-4.5 w-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-9v9l9 5.25" />
                </svg>
            </div>
            <p class="mt-3 font-serif text-[26px] font-bold text-stone-900 leading-none">{{ $pesananBelumDikirim }} pesanan</p>
            <p class="mt-2 text-xs text-stone-400">1 siap kirim · 1 masih diproduksi</p>
        </div>
    </div>

    <!-- Middle Section: Priority Payments (left) & Building Fund Card (right) -->
    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <!-- Pembayaran prioritas -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 xl:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-xl font-bold text-stone-900">Pembayaran prioritas</h2>
                        <p class="mt-0.5 text-xs text-stone-500">2 bukti menunggu pemeriksaan dan 1 permintaan bukti ulang.</p>
                    </div>
                    <a href="{{ route('admin.verification') }}" class="text-xs font-semibold text-[#b55322] hover:underline inline-flex items-center gap-1">
                        <span>Lihat semua</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <!-- Table Header Bar -->
                    <div class="grid grid-cols-12 gap-3 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-500 min-w-[620px]">
                        <div class="col-span-4">Pemesan / dukungan</div>
                        <div class="col-span-3">Total tagihan</div>
                        <div class="col-span-2">Bukti terkirim</div>
                        <div class="col-span-3 pl-1">Pembayaran</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="divide-y divide-stone-100 min-w-[620px]">
                        @forelse ($daftarPrioritas->take(3) as $item)
                            <div class="grid grid-cols-12 gap-3 items-center px-4 py-3.5 hover:bg-stone-50/50 transition">
                                <div class="col-span-4">
                                    <p class="text-sm font-bold text-stone-900 leading-snug">{{ $item['nama'] }}</p>
                                    <p class="text-xs text-stone-400 mt-0.5">{{ $item['sub'] }}</p>
                                </div>
                                <div class="col-span-3">
                                    <p class="text-sm font-normal text-stone-900">Rp{{ number_format($item['total'], 0, ',', '.') }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-semibold text-stone-800">{{ $item['tanggal_jam'] }}</p>
                                    <p class="text-xs text-stone-400 mt-0.5">{{ $item['keterangan_waktu'] }}</p>
                                </div>
                                <div class="col-span-3 pl-1">
                                    @if ($item['status'] === 'bukti_ulang')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fdeeee] px-2.5 py-1 text-xs font-semibold text-[#c2410c] whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#c2410c]"></span>
                                            Bukti ulang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#faefe8] px-2.5 py-1 text-xs font-semibold text-[#b55322] whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#b55322]"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-stone-400">Tidak ada bukti menunggu verifikasi.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Caution Note -->
            <div class="mt-5 flex items-center gap-2.5 rounded-xl bg-[#f4efe8] p-3.5 text-xs text-stone-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-600 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Bukti terkirim bukan berarti lunas. Cocokkan nominal dan mutasi sebelum memverifikasi.</span>
            </div>
        </div>

        <!-- Right Side: Dark Olive "UNTUK PEMBANGUNAN" Card -->
        <div class="rounded-2xl bg-[#444d41] p-6 text-white flex flex-col justify-between shadow-2xs">
            <div>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/60">UNTUK PEMBANGUNAN</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5 text-white/60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M3 21h18M3 10.5h18" />
                    </svg>
                </div>

                <p class="mt-4 font-serif text-3xl lg:text-[34px] font-bold text-white tracking-tight leading-none">Rp{{ number_format($totalPembangunan, 0, ',', '.') }}</p>
                <p class="mt-3 text-xs leading-relaxed text-white/70">Donasi terverifikasi + dana bersih kaos. Tidak termasuk ongkir dan pembayaran tertunda.</p>

                <div class="my-5 border-t border-white/15"></div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Pengeluaran pembangunan</span>
                        <span class="font-semibold text-white">Rp{{ number_format($pengeluaranPembangunan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Saldo pembangunan</span>
                        <span class="font-bold text-white">Rp{{ number_format($saldoPembangunan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.reports') }}" class="mt-6 inline-flex items-center gap-1.5 text-xs font-semibold text-white hover:underline">
                <span>Buka laporan dana</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Bottom Section: Latest Activities (left) & Action Required (right) -->
    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <!-- Aktivitas terbaru -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-xl font-bold text-stone-900">Aktivitas terbaru</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Real-time
                </span>
            </div>

            <div class="mt-4 divide-y divide-stone-100">
                @forelse ($aktivitas as $a)
                    <div class="group flex items-center justify-between gap-3.5 py-3.5 first:pt-1 last:pb-1">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-12 shrink-0 text-xs font-mono text-stone-400" title="{{ $a['tanggal_lengkap'] ?? '' }}">{{ $a['waktu'] }}</span>
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#ede8df] text-stone-600 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                @if ($a['tipe'] === 'doc')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @elseif ($a['tipe'] === 'box')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-9v9l9 5.25" />
                                    </svg>
                                @elseif ($a['tipe'] === 'check')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                @if (!empty($a['url']))
                                    <a href="{{ $a['url'] }}" class="text-sm font-bold text-stone-900 leading-tight hover:text-primary transition-colors block truncate">{{ $a['judul'] }}</a>
                                @else
                                    <p class="text-sm font-bold text-stone-900 leading-tight truncate">{{ $a['judul'] }}</p>
                                @endif
                                <p class="text-xs text-stone-400 mt-0.5 leading-tight truncate">{{ $a['sub'] }}</p>
                            </div>
                        </div>
                        @if (!empty($a['url']))
                            <a href="{{ $a['url'] }}" class="hidden sm:inline-flex shrink-0 text-xs font-semibold text-stone-400 hover:text-primary transition-colors">
                                Lihat &rarr;
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-stone-400">
                        Belum ada aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Perlu ditindaklanjuti -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-6 flex flex-col justify-between">
            <div>
                <h2 class="font-serif text-xl font-bold text-stone-900">Perlu ditindaklanjuti</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-sm font-bold text-stone-800">Bukti ulang</p>
                            <p class="text-sm font-bold text-stone-900">{{ $buktiUlang }} transaksi</p>
                        </div>
                        <p class="text-xs text-stone-400 mt-1 leading-relaxed">Putu Lestari · nominal pada bukti belum terbaca. Tunggu unggahan pengganti.</p>
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-sm font-bold text-stone-800">Belum bayar</p>
                            <p class="text-sm font-bold text-stone-900">{{ $belumBayar }} pesanan</p>
                        </div>
                        <p class="text-xs text-stone-400 mt-1 leading-relaxed">Ben Pranata · total final Rp550.000 telah dikonfirmasi, belum ada bukti.</p>
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between">
                            <p class="text-sm font-bold text-stone-800">Donasi tertunda</p>
                            <p class="font-serif text-base font-bold text-[#b55322]">Rp{{ number_format($donasiTertunda, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-xs text-stone-400 mt-1 leading-relaxed">4 donasi belum terverifikasi; belum masuk dana pembangunan.</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.verification') }}" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#b55322] px-4 py-3 text-xs font-bold text-white transition hover:bg-[#9c4316] shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
                <span>Buka antrean verifikasi</span>
            </a>
        </div>
    </div>
@endsection
