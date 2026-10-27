@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('breadcrumb', 'Pesanan Kaos / Detail')

@section('content')
    @php
        $bayarLabel = match ($order->status_pembayaran) {
            'terverifikasi' => 'Terverifikasi',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'bukti_ulang' => 'Bukti ulang',
            'ditolak' => 'Ditolak',
            default => 'Belum bayar',
        };
        $bayarBadge = match ($order->status_pembayaran) {
            'terverifikasi' => 'bg-emerald-50 border border-emerald-200/80 text-emerald-700',
            'menunggu_verifikasi' => 'bg-amber-50 border border-amber-200/80 text-amber-800',
            'bukti_ulang' => 'bg-red-50 border border-red-200/80 text-red-700',
            'ditolak' => 'bg-stone-100 border border-stone-200 text-stone-600',
            default => 'bg-stone-100 border border-stone-200 text-stone-500',
        };
        $kirimLabel = match ($order->status_pengiriman) {
            'siap_kirim' => 'Siap kirim',
            'produksi' => 'Produksi',
            'terkirim' => 'Terkirim',
            default => 'Belum diproduksi',
        };
        $kirimBadge = match ($order->status_pengiriman) {
            'siap_kirim' => 'bg-amber-50 border border-amber-200/80 text-amber-800',
            'produksi' => 'bg-blue-50 border border-blue-200/80 text-blue-700',
            'terkirim' => 'bg-emerald-50 border border-emerald-200/80 text-emerald-700',
            default => 'bg-stone-100 border border-stone-200 text-stone-600',
        };

        // Tahapan timeline: Dibuat -> Dikonfirmasi -> Diproses -> Dikirim -> Selesai
        $tahapDiproses = in_array($order->status_pengiriman, ['produksi', 'siap_kirim', 'terkirim']);
        $tahapDikirim = $order->status_pengiriman === 'terkirim' || $order->dikirim_at !== null;
        $tahapSelesai = $order->status_pengiriman === 'terkirim';

        $punyaKaos = count($order->items_list) > 0;
        $punyaDonasi = (int) $order->donasi > 0;
        $semuaJumlah = collect($order->items_list)->sum('jumlah');
    @endphp

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>

    @if (session('success'))
        <div class="mb-6 no-print flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
            <p class="mt-1 text-sm font-medium text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6 no-print">
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 transition hover:text-[#b55322]">
            <span>&larr;</span> Kembali ke daftar pesanan
        </a>
        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.2em] text-[#b55322]">Jemaat Bali / Administrasi Program</p>
        <h1 class="mt-1 font-serif text-3xl font-bold text-stone-900 tracking-tight">Detail Pesanan</h1>
    </div>

    <!-- 4 Info Cards -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
            <p class="text-xs font-medium text-stone-500">No. Pesanan</p>
            <p class="mt-2 font-serif text-xl font-bold text-stone-900">{{ $order->nomor_pesanan }}</p>
            <p class="mt-1 text-xs text-stone-400">{{ $order->created_at->translatedFormat('d M Y, H.i') }} &middot; {{ $order->jenisLabel() }}</p>
        </div>
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
            <p class="text-xs font-medium text-stone-500">Nama Pemesan</p>
            <p class="mt-2 font-serif text-xl font-bold text-stone-900 truncate">{{ $order->nama }}</p>
            <p class="mt-1 text-xs text-stone-400">WA: {{ $order->nomor ?: '—' }}</p>
        </div>
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
            <p class="text-xs font-medium text-stone-500">Status Pembayaran</p>
            <div class="mt-2.5">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-semibold {{ $bayarBadge }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $order->status_pembayaran === 'terverifikasi' ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                    {{ $bayarLabel }}
                </span>
            </div>
            <p class="mt-2 text-xs text-stone-400">{{ $order->metode ?? 'Transfer manual' }}</p>
        </div>
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
            <p class="text-xs font-medium text-stone-500">Status Pesanan</p>
            <div class="mt-2.5">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-semibold {{ $kirimBadge }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                    {{ $kirimLabel }}
                </span>
            </div>
            <p class="mt-2 text-xs text-stone-400">{{ $order->resi ? 'Resi: ' . $order->resi : 'Belum ada nomor resi' }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3 items-start">
        <!-- Left: Rincian + Riwayat -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Rincian Pesanan -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Rincian Pesanan</h2>
                    @if ($punyaKaos && $punyaDonasi)
                        <div class="no-print inline-flex rounded-xl bg-[#f4efe8] p-1 text-xs font-semibold" data-order-tabs>
                            <button type="button" data-tab="semua" class="rounded-lg bg-white px-3.5 py-1.5 text-stone-900 shadow-2xs cursor-pointer transition">Semua</button>
                            <button type="button" data-tab="kaos" class="rounded-lg px-3.5 py-1.5 text-stone-500 cursor-pointer transition hover:text-stone-700">Kaos</button>
                            <button type="button" data-tab="donasi" class="rounded-lg px-3.5 py-1.5 text-stone-500 cursor-pointer transition hover:text-stone-700">Donasi</button>
                        </div>
                    @endif
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left">
                        <thead>
                            <tr class="bg-[#f4efe8] text-[11px] font-semibold text-stone-600">
                                <th class="w-8 rounded-l-xl px-3 py-2.5 font-semibold">No</th>
                                <th class="px-3 py-2.5 font-semibold">Produk</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Jumlah &amp; Ukuran</th>
                                <th class="px-3 py-2.5 text-right font-semibold">Harga Satuan</th>
                                <th class="rounded-r-xl px-3 py-2.5 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-sm" data-order-rows>
                            @forelse ($order->items_list as $i => $item)
                                @php($itemDesign = \App\Models\Design::find($item['design_id']))
                                <tr data-tipe="kaos">
                                    <td class="px-3 py-3 align-top text-xs text-stone-400">{{ $i + 1 }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($itemDesign && $itemDesign->gambar)
                                                <img src="{{ asset($itemDesign->gambar) }}" alt="{{ $item['nama'] }}" class="h-12 w-12 shrink-0 rounded-lg border border-[#ede8df] bg-white object-contain p-1">
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-stone-900 leading-tight">{{ $item['nama'] }}</p>
                                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                    <span class="rounded-full bg-[#0f5132]/10 px-2 py-0.5 text-[10px] font-bold text-[#0f5132]">Kaos</span>
                                                    <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-bold text-stone-600">{{ ucfirst($item['gender'] ?? 'pria') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center align-top">
                                        <span class="inline-block rounded-md border border-[#e8e4dc] bg-[#f8f6f0] px-2.5 py-1 text-[11px] font-bold text-stone-800">{{ $item['ukuran'] }} &times; {{ $item['jumlah'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-right align-top text-xs text-stone-600">Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-right align-top text-xs font-bold text-stone-900">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                @unless ($punyaDonasi)
                                    <tr><td colspan="5" class="px-3 py-8 text-center text-xs text-stone-400">Tidak ada item kaos.</td></tr>
                                @endunless
                            @endforelse
                            @if ($punyaDonasi)
                                <tr data-tipe="donasi">
                                    <td class="px-3 py-3 align-top text-xs text-stone-400">{{ ($semuaJumlah > 0 ? 2 : 1) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg border border-[#ede8df] bg-[#f8f6f0] text-[#b55322]">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-stone-900 leading-tight">Donasi {{ $order->isDonasiOnly() ? 'Saja' : 'Pembangunan' }}</p>
                                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                    <span class="rounded-full bg-[#b55322]/10 px-2 py-0.5 text-[10px] font-bold text-[#b55322]">Donasi</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center align-top">
                                        <span class="inline-block rounded-md border border-[#e8e4dc] bg-[#f8f6f0] px-2.5 py-1 text-[11px] font-bold text-stone-800">1 &times;</span>
                                    </td>
                                    <td class="px-3 py-3 text-right align-top text-xs text-stone-600">Rp{{ number_format($order->donasi, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-right align-top text-xs font-bold text-stone-900">Rp{{ number_format($order->donasi, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Total rows -->
                <div class="mt-4 space-y-2 border-t border-stone-100 pt-4 text-sm">
                    @if ($punyaKaos)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-stone-500">Subtotal kaos ({{ $semuaJumlah }} pcs)</span>
                            <span class="font-semibold text-stone-900">Rp{{ number_format($order->subtotal_kaos, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($punyaDonasi)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-stone-500">Donasi</span>
                            <span class="font-semibold text-stone-900">Rp{{ number_format($order->donasi, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-stone-500">Ongkos kirim</span>
                        <span class="font-semibold text-stone-900">
                            @if ($order->ongkir > 0)
                                Rp{{ number_format($order->ongkir, 0, ',', '.') }}
                            @else
                                Belum dihitung
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-[#f8f6f0] px-4 py-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-stone-500">Total</span>
                        <span class="font-serif text-2xl font-bold text-stone-900">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pesanan -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <h2 class="font-serif text-lg font-bold text-stone-900">Riwayat Pesanan</h2>
                <ol class="relative mt-5 space-y-6 border-l-2 border-[#e8e4dc] pl-6">
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full bg-[#b55322] ring-4 ring-white"></span>
                        <p class="text-sm font-bold text-stone-900">Pesanan Dibuat</p>
                        <p class="mt-0.5 text-xs text-stone-400">{{ $order->created_at->translatedFormat('d M Y, H.i') }} &middot; Pesanan tercatat dalam sistem.</p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $order->status_pembayaran === 'terverifikasi' ? 'bg-emerald-500' : 'bg-amber-400' }} ring-4 ring-white"></span>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-bold text-stone-900">Pembayaran Dikonfirmasi</p>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $bayarBadge }}">{{ $bayarLabel }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-stone-400">
                            @if ($order->verified_at)
                                Diverifikasi {{ $order->verified_at->translatedFormat('d M Y, H.i') }} oleh {{ $order->verifier?->nama ?? $order->verifier?->name ?? 'Panitia' }}.
                            @else
                                Menunggu pemeriksaan bukti transfer oleh panitia.
                            @endif
                        </p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $tahapDiproses ? 'bg-[#b55322]' : 'bg-stone-300' }} ring-4 ring-white"></span>
                        <p class="text-sm font-bold {{ $tahapDiproses ? 'text-stone-900' : 'text-stone-400' }}">Diproses</p>
                        <p class="mt-0.5 text-xs text-stone-400">{{ $tahapDiproses ? 'Pesanan masuk tahap produksi / persiapan pengiriman.' : 'Belum masuk tahap produksi.' }}</p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $tahapDikirim ? 'bg-emerald-500' : 'bg-stone-300' }} ring-4 ring-white"></span>
                        <p class="text-sm font-bold {{ $tahapDikirim ? 'text-stone-900' : 'text-stone-400' }}">Dikirim</p>
                        <p class="mt-0.5 text-xs text-stone-400">
                            @if ($order->dikirim_at)
                                Dikirim {{ $order->dikirim_at->translatedFormat('d M Y, H.i') }}{{ $order->resi ? ' · Resi ' . $order->resi : '' }}.
                            @else
                                Belum ada pembaruan pengiriman.
                            @endif
                        </p>
                    </li>
                    <li class="relative">
                        <span class="absolute -left-[31px] top-0.5 grid h-4 w-4 place-items-center rounded-full {{ $tahapSelesai ? 'bg-emerald-500' : 'bg-stone-300' }} ring-4 ring-white"></span>
                        <p class="text-sm font-bold {{ $tahapSelesai ? 'text-stone-900' : 'text-stone-400' }}">Selesai</p>
                        <p class="mt-0.5 text-xs text-stone-400">{{ $tahapSelesai ? 'Pesanan tiba di penerima.' : 'Menunggu konfirmasi diterima.' }}</p>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Ringkasan Pesanan -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
                <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan Pesanan</h2>
                <div class="mt-3 rounded-2xl bg-[#f8f6f0] p-5 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Total Pembayaran</p>
                    <p class="mt-2 font-serif text-3xl font-bold text-[#b55322]">Rp{{ number_format($order->totalTagihan(), 0, ',', '.') }}</p>
                </div>
                <div class="mt-4 divide-y divide-stone-100 text-sm">
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-stone-500">Metode Pembayaran</span>
                        <span class="font-semibold text-stone-900">{{ $order->metodeLabel() }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-stone-500">Status</span>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $bayarBadge }}">{{ $bayarLabel }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-stone-500">Dicatat pada</span>
                        <span class="font-semibold text-stone-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-2.5">
                        <span class="shrink-0 text-stone-500">Alamat</span>
                        <span class="max-w-[60%] text-right font-semibold leading-relaxed text-stone-900">{{ $order->isDonasiOnly() ? '—' : $order->alamatLengkap() }}</span>
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
                <h2 class="font-serif text-lg font-bold text-stone-900">Bukti Pembayaran</h2>
                @if ($order->bukti_path)
                    <a href="{{ $order->buktiUrl() }}" target="_blank" rel="noopener" class="mt-3 flex items-center gap-3 rounded-xl border border-[#ede8df] bg-white p-3 transition hover:border-[#b55322]/50">
                        <img src="{{ $order->buktiUrl() }}" alt="Bukti pembayaran {{ $order->nomor_pesanan }}" class="h-16 w-16 shrink-0 rounded-lg border border-[#ede8df] object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-xs font-bold text-stone-900">{{ basename($order->bukti_path) }}</span>
                            <span class="text-[11px] text-stone-400">Bukti transfer pemesan</span>
                        </span>
                        <span class="shrink-0 text-xs font-bold text-[#b55322]">Lihat Detail &rarr;</span>
                    </a>
                @else
                    <p class="mt-3 rounded-xl bg-[#f8f6f0] p-4 text-xs text-stone-500">Belum ada bukti pembayaran yang diunggah.</p>
                @endif
            </div>

            <!-- Aksi Panitia -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 no-print">
                <h2 class="font-serif text-lg font-bold text-stone-900">Aksi Panitia</h2>

                @if ($order->status_pengiriman === 'belum_diproduksi')
                    <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="status_pengiriman" value="siap_kirim">
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#b55322] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#9c4319] cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Konfirmasi Pesanan
                        </button>
                        <p class="mt-2 text-center text-[11px] text-stone-400">Menandai pesanan siap masuk proses produksi &amp; kirim.</p>
                    </form>
                @else
                    <p class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 px-3 py-2.5 text-xs font-semibold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Pesanan sudah dikonfirmasi ({{ $kirimLabel }})
                    </p>
                @endif

                @if (!$order->canBeDeleted())
                    <p class="mt-4 rounded-xl bg-stone-50 border border-stone-200 p-3 text-xs text-stone-600 text-center">Pesanan ini sudah dikirim dan tidak dapat dihapus.</p>
                @endif

                @if ($order->nomor)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                        class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-4 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-[#25D366]"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                        Kirim Notifikasi WA
                    </a>
                @endif

                <button type="button" onclick="window.print()"
                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-4 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-stone-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.17-.57-.985-1.227l.234-2.523m11.94 0a40.705 40.705 0 0 0-11.94 0m11.94 0a2.25 2.25 0 1 0 2.236-2.366 2.25 2.25 0 0 0-2.236 2.366Zm-11.94 0a2.25 2.25 0 1 1-2.236-2.366 2.25 2.25 0 0 1 2.236 2.366Z" /></svg>
                    Cetak Invoice
                </button>
            </div>

            <!-- Perbarui Pengiriman -->
            <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 no-print">
                @csrf
                <h2 class="font-serif text-lg font-bold text-stone-900">Perbarui Pengiriman</h2>
                <p class="mt-1 text-xs text-stone-400">Atur tahap produksi, siap kirim, atau sudah dikirim beserta nomor resinya.</p>

                <div class="mt-4">
                    <label for="status_pengiriman" class="text-xs font-semibold text-stone-600">Status pengiriman</label>
                    <select name="status_pengiriman" id="status_pengiriman" class="mt-1.5 w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none">
                        <option value="belum_diproduksi" @selected(old('status_pengiriman', $order->status_pengiriman) === 'belum_diproduksi')>Belum diproduksi</option>
                        <option value="produksi" @selected(old('status_pengiriman', $order->status_pengiriman) === 'produksi')>Produksi</option>
                        <option value="siap_kirim" @selected(old('status_pengiriman', $order->status_pengiriman) === 'siap_kirim')>Siap kirim</option>
                        <option value="terkirim" @selected(old('status_pengiriman', $order->status_pengiriman) === 'terkirim')>Terkirim</option>
                    </select>
                    @error('status_pengiriman')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mt-3">
                    <label for="resi" class="text-xs font-semibold text-stone-600">Nomor resi <span class="font-normal text-stone-400">— opsional</span></label>
                    <input type="text" name="resi" id="resi" value="{{ old('resi', $order->resi) }}" placeholder="Nomor resi pengiriman" class="mt-1.5 w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none">
                    @error('resi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="mt-4 w-full rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-stone-800 cursor-pointer">Simpan Status</button>
            </form>

            <!-- Catatan -->
            @if ($order->catatan || $order->catatan_panitia)
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Catatan</h2>
                    @if ($order->catatan)
                        <div class="mt-3 rounded-xl bg-[#f8f6f0] p-4 text-sm leading-relaxed text-stone-700">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Dari pemesan</p>
                            <p class="mt-1">{{ $order->catatan }}</p>
                        </div>
                    @endif
                    @if ($order->catatan_panitia)
                        <div class="mt-3 rounded-xl bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Dari panitia</p>
                            <p class="mt-1">{{ $order->catatan_panitia }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tabsWrap = document.querySelector('[data-order-tabs]');
                if (!tabsWrap) return;
                var rows = document.querySelectorAll('[data-order-rows] tr[data-tipe]');
                tabsWrap.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-tab]');
                    if (!btn) return;
                    var tab = btn.dataset.tab;
                    tabsWrap.querySelectorAll('[data-tab]').forEach(function (b) {
                        var active = b === btn;
                        b.classList.toggle('bg-white', active);
                        b.classList.toggle('text-stone-900', active);
                        b.classList.toggle('shadow-2xs', active);
                        b.classList.toggle('text-stone-500', !active);
                    });
                    rows.forEach(function (r) {
                        r.style.display = (tab === 'semua' || r.dataset.tipe === tab) ? '' : 'none';
                    });
                });
            });
        </script>
    @endpush
@endsection
