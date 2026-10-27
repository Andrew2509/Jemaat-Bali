@extends('layouts.admin')

@section('title', 'Detail Donasi')
@section('breadcrumb', 'Donasi')

@section('content')
    @php
        $statusLabel = match ($donation->status) {
            'terverifikasi' => 'Terverifikasi',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'bukti_ulang' => 'Bukti ulang',
            default => 'Ditolak',
        };
        $badge = match ($donation->status) {
            'terverifikasi' => 'bg-emerald-100 text-emerald-700',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
            'bukti_ulang' => 'bg-red-100 text-red-700',
            default => 'bg-stone-100 text-stone-600',
        };
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.donations') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-muted transition hover:text-primary">
            <span>&larr;</span> Kembali ke daftar donatur
        </a>
        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Jemaat Bali / Administrasi Program</p>
        <h1 class="mt-1 font-serif text-3xl font-bold">Detail Donasi</h1>
        <p class="mt-1 text-sm text-muted">{{ $donation->kode }} &middot; {{ $donation->nama }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:order-2 lg:col-span-2">
            <div class="card p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-serif text-lg font-bold">{{ $donation->kode }}</p>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badge }}">{{ $statusLabel }}</span>
                </div>

                <div class="mt-5 flex items-center gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-primary/10 font-serif text-lg font-bold text-primary">{{ strtoupper(substr($donation->nama, 0, 2)) }}</span>
                    <div>
                        <p class="font-serif text-xl font-bold">{{ $donation->nama }}</p>
                        <p class="text-sm text-muted">{{ $donation->tanggal?->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl bg-canvas p-5 text-center">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted">Nominal donasi</p>
                    <p class="mt-2 font-serif text-4xl font-bold text-primary">Rp{{ number_format($donation->nominal, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs font-semibold text-muted">{{ $statusLabel }}</p>
                </div>

                <div class="mt-5 divide-y divide-line text-sm">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">WhatsApp</span>
                        <span class="font-semibold">{{ $donation->nomor }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Email</span>
                        <span class="font-semibold">{{ $donation->email }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Metode</span>
                        <span class="font-semibold">{{ $donation->metode ?? 'Transfer manual' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <span class="text-muted">Bukti terkirim</span>
                        <span class="text-right font-semibold">
                            @if ($donation->bukti_path)
                                {{ $donation->updated_at->format('d M Y, H:i') }}
                                <span class="block text-xs font-normal text-muted">{{ $donation->updated_at->diffForHumans() }}</span>
                            @else
                                &mdash;
                            @endif
                        </span>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="label">Pesan donatur</p>
                    <div class="rounded-xl bg-canvas p-4 text-sm leading-relaxed text-ink">{{ $donation->catatan ?? 'Tidak ada pesan dari donatur.' }}</div>
                </div>

                @if ($donation->status !== 'terverifikasi')
                    <p class="mt-5 flex items-start gap-2 rounded-xl bg-amber-50 p-3 text-xs leading-relaxed text-amber-900">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-4 w-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                        Bukti sudah dikirim, tetapi donasi belum terverifikasi dan belum dihitung sebagai dana pembangunan.
                    </p>
                @endif

                @if ($donation->status === 'terverifikasi')
                    <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            </span>
                            <div>
                                <p class="font-serif text-lg font-bold text-emerald-900">Donasi terverifikasi</p>
                                <p class="mt-1 text-sm text-emerald-800">Diverifikasi pada {{ $donation->verified_at?->format('d M Y, H:i') }} oleh {{ $donation->verifier?->nama ?? $donation->verifier?->name ?? 'Panitia' }}. Nominal ini sudah dihitung sebagai dana pembangunan.</p>
                                @if ($donation->catatan_panitia)
                                    <p class="mt-2 rounded-lg bg-white/70 p-2.5 text-xs text-emerald-900">Catatan panitia: {{ $donation->catatan_panitia }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.donations.action', $donation) }}" class="mt-5 rounded-2xl border border-line bg-white p-5">
                        @csrf
                        <h2 class="font-serif text-lg font-bold">Keputusan verifikasi</h2>
                        <p class="mt-1 text-xs text-muted">Cocokkan nominal dan tanggal pada bukti dengan mutasi rekening panitia sebelum mengambil keputusan.</p>

                        <div class="mt-4">
                            <label for="catatan_panitia" class="label">Catatan pemeriksaan <span class="font-normal text-muted">— opsional</span></label>
                            <textarea name="catatan_panitia" id="catatan_panitia" rows="3" class="input" placeholder="Nominal sesuai donasi. Cocokkan dana masuk pada mutasi sebelum diverifikasi.">{{ old('catatan_panitia', $donation->catatan_panitia) }}</textarea>
                            @error('catatan_panitia')<p class="error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-4 flex flex-wrap gap-3">
                            @if (!empty($donation->bukti_path))
                                <button type="submit" name="aksi" value="verifikasi" class="btn btn-primary">Periksa pembayaran</button>
                            @else
                                <button type="button" disabled class="btn bg-stone-200 text-stone-400 cursor-not-allowed select-none" title="Donatur belum mengunggah bukti pembayaran">Periksa pembayaran (Belum aktif)</button>
                            @endif
                            <button type="submit" name="aksi" value="tolak" class="btn btn-outline !border-red-300 !text-red-600 hover:!border-red-500 hover:!bg-red-50" onclick="return confirm('Tolak donasi ini?')">Tolak donasi</button>
                            <button type="submit" name="aksi" value="bukti_ulang" class="btn btn-outline" onclick="return confirm('Minta donatur mengunggah ulang bukti?')">Minta bukti ulang</button>
                        </div>
                        @if (empty($donation->bukti_path))
                            <p class="mt-3 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                                <span><strong>Catatan:</strong> Tombol verifikasi pembayaran belum diaktifkan karena donatur belum mengirim/mengunggah bukti pembayaran. Tombol akan aktif secara otomatis setelah bukti diunggah.</span>
                            </p>
                        @endif
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-6 lg:order-1 lg:col-span-1">
            <div class="card p-5">
                <h2 class="font-serif text-lg font-bold">Ketentuan donasi</h2>
                <div class="mt-4 space-y-4">
                    <div class="flex gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold">Donasi Saja</p>
                            <p class="mt-1 text-xs leading-relaxed text-muted">Sukarela, tanpa batas minimum. Tidak memerlukan alamat pengiriman.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" /></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold">Pesan Kaos</p>
                            <p class="mt-1 text-xs leading-relaxed text-muted">Pesanan kaos lewat katalog, tanpa kolom donasi. Ongkir dikonfirmasi terpisah.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-canvas p-4 text-xs leading-relaxed text-muted">
                Donasi Saja tidak memiliki pesanan atau status pengiriman.
            </div>

            <div class="rounded-xl bg-canvas p-4 text-xs leading-relaxed text-muted">
                Pembayaran donasi diverifikasi manual panitia. Donasi baru dihitung sebagai dana pembangunan setelah berstatus terverifikasi.
            </div>
        </div>
    </div>
@endsection
