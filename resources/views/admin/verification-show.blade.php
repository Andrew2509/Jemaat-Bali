@extends('layouts.admin')

@section('title', 'Detail Verifikasi')
@section('breadcrumb', 'Verifikasi Pembayaran')

@section('content')
    @php
        $isOrder = $tipe === 'order';
        $status = $isOrder ? $item->status_pembayaran : $item->status;
        $statusLabel = match ($status) {
            'terverifikasi' => 'Terverifikasi',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'bukti_ulang' => 'Bukti ulang',
            'ditolak' => 'Ditolak',
            default => 'Belum bayar',
        };
        $badge = match ($status) {
            'terverifikasi' => 'bg-emerald-100 text-emerald-700',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
            'bukti_ulang' => 'bg-red-100 text-red-700',
            'ditolak' => 'bg-stone-100 text-stone-600',
            default => 'border border-stone-300 bg-white text-stone-500',
        };
        $tagihan = $isOrder ? $item->totalTagihan() : $item->nominal;
        $dibayar = $tagihan;
        $selisih = $tagihan - $dibayar;
        $idLabel = $isOrder ? $item->nomor_pesanan : $item->kode;
        $jenisLabel = $isOrder
            ? $item->jenisLabel()
            : 'Donasi Saja';
    @endphp

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

    <div class="mb-6">
        <a href="{{ route('admin.verification') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
            <span>&larr;</span> Kembali ke antrean
        </a>
        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Jemaat Bali / Administrasi Program</p>
        <h1 class="mt-1 font-serif text-3xl font-bold">Detail Verifikasi</h1>
        <p class="mt-1 text-sm text-muted">{{ $idLabel }} &middot; {{ $jenisLabel }} &middot; {{ $item->nama }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-serif text-lg font-bold">Rincian transaksi</h2>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badge }}">{{ $statusLabel }}</span>
                </div>

                <div class="mt-4 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Ditagih</span>
                        <span class="font-serif text-lg font-bold">Rp{{ number_format($tagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span>
                            <span class="block font-semibold">Dibayar pada bukti</span>
                            <span class="text-xs text-muted">{{ $item->bukti_path ? 'Terbaca dari gambar bukti transfer' : 'Mengikuti nominal tagihan karena belum ada bukti' }}</span>
                        </span>
                        <span class="font-semibold">Rp{{ number_format($dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Selisih nominal</span>
                        <span class="font-bold text-emerald-700">Rp{{ number_format($selisih, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-canvas p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted">Rincian tagihan</p>
                    <div class="mt-2 space-y-2 text-sm">
                        @if ($isOrder)
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-muted">Subtotal kaos</span>
                                <span class="font-semibold">Rp{{ number_format($item->subtotal_kaos, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-muted">Nominal donasi</span>
                                <span class="font-semibold">Rp{{ number_format($item->donasi, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-muted">Ongkir</span>
                                <span class="font-semibold">Rp{{ number_format($item->ongkir, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-muted">Nominal</span>
                                <span class="font-semibold">Rp{{ number_format($item->nominal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Nama</span>
                        <span class="font-semibold">{{ $item->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">WhatsApp</span>
                        <span class="font-semibold">{{ $item->nomor }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Email</span>
                        <span class="font-semibold">{{ $item->email }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Bukti terkirim</span>
                        <span class="text-right font-semibold">
                            @if ($item->bukti_path)
                                {{ $item->updated_at->format('d M Y, H:i') }}
                                <span class="block text-xs font-normal text-muted">{{ $item->updated_at->diffForHumans() }}</span>
                            @else
                                &mdash;
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Catatan pemeriksaan</span>
                        <span class="max-w-[60%] text-right font-semibold">{{ $item->catatan_panitia ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="font-serif text-lg font-bold">Daftar pemeriksaan panitia</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="font-serif text-2xl font-bold text-primary">01</p>
                        <p class="mt-2 text-sm font-semibold">Cocokkan nama pemesan dan ID transaksi</p>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Pastikan nama dan {{ $isOrder ? 'nomor pesanan' : 'kode donasi' }} pada bukti sama dengan data yang terdaftar.</p>
                    </div>
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="font-serif text-2xl font-bold text-primary">02</p>
                        <p class="mt-2 text-sm font-semibold">Periksa nominal, tanggal, dan penerima pada bukti</p>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Nominal harus sesuai tagihan, tanggal transfer wajar, dan rekening tujuan milik panitia.</p>
                    </div>
                    <div class="rounded-xl bg-canvas p-4">
                        <p class="font-serif text-2xl font-bold text-primary">03</p>
                        <p class="mt-2 text-sm font-semibold">Pastikan dana masuk di mutasi rekening panitia</p>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Verifikasi hanya diberikan setelah mutasi bank menunjukkan dana yang masuk.</p>
                    </div>
                </div>
            </div>

            @if ($status === 'terverifikasi')
                <div class="card border-emerald-200 bg-emerald-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-serif text-lg font-bold text-emerald-900">Pembayaran terverifikasi</p>
                            <p class="mt-1 text-sm text-emerald-800">Diverifikasi oleh {{ $item->verifier?->nama ?? $item->verifier?->name ?? 'Panitia' }} pada {{ $item->verified_at?->format('d M Y, H:i') }}. Pembayaran ini sudah dihitung sebagai penerimaan panitia.</p>
                            @if ($item->catatan_panitia)
                                <p class="mt-2 rounded-lg bg-white/70 p-2.5 text-xs text-emerald-900">Catatan panitia: {{ $item->catatan_panitia }}</p>
                            @endif

                            @if ($item->nomor)
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <form method="POST" action="{{ route('admin.verification.resend_wa', ['id' => $item->id, 'tipe' => $tipe]) }}" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.classList.add('opacity-75', 'cursor-not-allowed'); btn.innerHTML = '<span>Mengirim pesan WhatsApp via Fonnte...</span>';">
                                        @csrf
                                        <button type="submit" class="btn btn-outline !border-emerald-300 !text-emerald-800 hover:!bg-emerald-100 !px-4 !py-2 text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>Kirim via Fonnte ({{ $item->nomor }})</span>
                                        </button>
                                    </form>

                                    @php
                                        $directWaUrl = app(\App\Services\FonnteService::class)->getDirectWaLink($item, $tipe);
                                    @endphp
                                    <a href="{{ $directWaUrl }}" target="_blank" rel="noopener" class="btn btn-outline !border-stone-300 !text-stone-700 hover:!bg-stone-100 !px-4 !py-2 text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        <span>Buka WhatsApp Web Langsung (wa.me) &nearr;</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('admin.verification.action', ['tipe' => $tipe, 'id' => $item->id]) }}" class="card p-5">
                    @csrf
                    <h2 class="font-serif text-lg font-bold">Keputusan verifikasi</h2>
                    <p class="mt-1 text-xs text-muted">Tentukan status pembayaran setelah memeriksa bukti dan mutasi rekening panitia.</p>

                    <div class="mt-4">
                        <label for="catatan_panitia" class="label">Catatan pemeriksaan <span class="font-normal text-muted">— opsional</span></label>
                        <textarea name="catatan_panitia" id="catatan_panitia" rows="3" class="input" placeholder="Nominal sesuai tagihan. Cocokkan dana masuk pada mutasi sebelum diverifikasi.">{{ old('catatan_panitia', $item->catatan_panitia) }}</textarea>
                        @error('catatan_panitia')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <p class="mt-4 flex items-start gap-2 rounded-xl bg-canvas p-3 text-xs text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-4 w-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                        Belum lunas. Nominal pada bukti belum menjadi penerimaan dana sebelum diverifikasi.
                    </p>

                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 text-xs">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="kirim_wa" value="1" class="mt-0.5 rounded text-[#25D366] focus:ring-[#25D366]" checked>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-emerald-950 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    Kirim otomatis rincian pesanan ke WhatsApp pemesan ({{ $item->nomor ?: '—' }})
                                </span>
                            </div>
                        </label>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <button type="submit" name="aksi" value="verifikasi" class="btn btn-primary">Verifikasi pembayaran &amp; kirim pesanan ke WA</button>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-outline !border-red-300 !text-red-600 hover:!border-red-500 hover:!bg-red-50" onclick="return confirm('Tolak pembayaran transaksi ini?')">Tolak pembayaran</button>
                        <button type="submit" name="aksi" value="bukti_ulang" class="btn btn-outline" onclick="return confirm('Minta donatur mengunggah ulang bukti?')">Minta bukti ulang</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-serif text-lg font-bold">{{ $idLabel }}</p>
                        <p class="mt-0.5 text-xs text-muted">{{ $jenisLabel }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badge }}">{{ $statusLabel }}</span>
                </div>

                @if ($item->bukti_path)
                    <div class="mt-4 overflow-hidden rounded-2xl border border-line bg-white shadow-xs">
                        <div class="p-3 bg-stone-100 flex items-center justify-center">
                            <img src="{{ $item->buktiUrl() }}" alt="Bukti transfer {{ $idLabel }}" class="max-h-80 w-auto rounded-lg object-contain shadow-xs">
                        </div>
                        <a href="{{ $item->buktiUrl() }}" target="_blank" rel="noopener" class="flex items-center gap-3 border-t border-line p-3.5 transition hover:bg-stone-50">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-amber-100 text-amber-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold">{{ basename($item->bukti_path) }}</span>
                                <span class="text-xs text-muted">Bukti transfer terunggah &middot; Klik untuk buka ukuran penuh</span>
                            </span>
                            <span class="shrink-0 text-xs font-bold text-primary">Buka Tab Baru &rarr;</span>
                        </a>
                    </div>
                @else
                    <div class="mt-4 flex items-center gap-3 rounded-xl border border-dashed border-line bg-white p-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3M3.75 12a8.25 8.25 0 0 1 1.5-4.875M20.25 12a8.25 8.25 0 0 0-1.5-4.875M3.75 12H6m12 0h2.25M3.75 12v6.75A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25V12" /></svg>
                        </span>
                        <span>
                            <span class="block text-sm font-bold">Belum ada bukti transfer</span>
                            <span class="text-xs text-muted">Donatur / pemesan belum mengunggah bukti pembayaran.</span>
                        </span>
                    </div>
                @endif

                <div class="mt-4 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-muted">Nama</span>
                        <span class="font-semibold">{{ $item->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-muted">Metode</span>
                        <span class="font-semibold">{{ $item->metode ?? 'Transfer manual' }}</span>
                    </div>
                    @if ($isOrder)
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <span class="text-muted">Desain kaos</span>
                            <span class="font-semibold">{{ $item->design?->nama ?? 'Kaos Jemaat Bali' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <span class="text-muted">Status pengiriman</span>
                            <span class="font-semibold">{{ ucwords(str_replace('_', ' ', $item->status_pengiriman)) }}</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <span class="text-muted">Tanggal donasi</span>
                            <span class="font-semibold">{{ $item->tanggal?->translatedFormat('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <span class="text-muted">Dicatat pada</span>
                        <span class="font-semibold">{{ $item->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-canvas p-4 text-xs leading-relaxed text-muted">
                Pembayaran dicatat manual oleh panitia. Status, catatan, dan waktu verifikasi tersimpan pada transaksi ini setelah keputusan diambil.
            </div>
        </div>
    </div>
@endsection
