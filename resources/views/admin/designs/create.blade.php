@extends('layouts.admin')

@section('title', $design->exists ? 'Edit Desain Kaos' : 'Upload Desain Kaos')
@section('breadcrumb', 'Koleksi Kaos')

@section('content')
    @php
        $initialNama = old('nama', $design->nama ?? '');
        $initialHarga = old('harga', $design->harga ?? 150000);
        $initialDeskripsi = old('deskripsi', $design->deskripsi ?? '');
        $initialAktif = old('aktif', $design->exists ? ($design->aktif ? 1 : 0) : 1);
        $initialGambarUrl = $design->exists ? $design->gambarUrl() : null;
        $initialVideoUrl = $design->exists ? $design->video360Url() : null;
        $allSizes = \App\Models\Design::ALL_UKURAN;
        $initialUkuran = old('ukuran', $design->exists ? $design->pilihanUkuran() : $allSizes);
        if (!is_array($initialUkuran)) {
            $initialUkuran = $allSizes;
        }
    @endphp

    <!-- Top Back & Header -->
    <div class="mb-6">
        <a href="{{ route('admin.designs') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-stone-900 transition mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Koleksi Kaos</span>
        </a>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / KOLEKSI KAOS PEMBANGUNAN</p>
        <h1 class="mt-1 font-serif text-3xl font-bold text-stone-900">{{ $design->exists ? 'Edit Desain Pakaian' : 'Upload Desain Kaos Baru' }}</h1>
        <p class="mt-1 text-xs text-stone-500">{{ $design->exists ? 'Perbarui informasi pakaian, foto desain mockup, video 360° interaktif, dan harga donasi pada etalase.' : 'Unggah foto pakaian baru, video 360° interaktif, dan atur harga donasi untuk Koleksi Kaos Pembangunan.' }}</p>
    </div>

    <!-- Main Grid: Form on Left, Live Preview on Right -->
    <div class="grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left: Upload Form -->
        <div class="lg:col-span-7 xl:col-span-7">
            <form id="design-form" method="POST" action="{{ $design->exists ? route('admin.designs.update', $design->id) : route('admin.designs.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4 shadow-2xs">
                @csrf

                <!-- Nama Kaos / Pakaian -->
                <div>
                    <label for="nama" class="block text-xs font-semibold text-stone-700 mb-1">
                        Nama Desain Kaos <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ $initialNama }}" placeholder="Contoh: Kaos &quot;Bangun Bersama&quot; Edisi 2026" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none" required>
                    @error('nama')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga & Status (2 cols) -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="harga" class="block text-xs font-semibold text-stone-700 mb-1">
                            Harga Donasi Kaos (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400">Rp</span>
                            <input type="number" id="harga" name="harga" value="{{ $initialHarga }}" min="10000" step="5000" class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none font-semibold" required>
                        </div>
                        @error('harga')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="aktif" class="block text-xs font-semibold text-stone-700 mb-1">
                            Status Publikasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="aktif" name="aktif" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none" required>
                            <option value="1" @selected($initialAktif == 1)>Aktif — Tampil di website publik</option>
                            <option value="0" @selected($initialAktif == 0)>Draf — Sembunyikan dari katalog</option>
                        </select>
                        @error('aktif')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi & Filosofi -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-stone-700 mb-1">
                        Deskripsi &amp; Filosofi Pakaian <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan makna desain, bahan pakaian (katun combed 30s), atau edisi kampanye pembangunan..." class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none leading-relaxed" required>{{ $initialDeskripsi }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilihan Ukuran Tersedia (Stok Pakaian) -->
                <div class="rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div>
                            <label class="block text-xs font-bold text-stone-800">
                                Pilihan Ukuran Tersedia <span class="text-stone-400 font-normal">&mdash; Stok Pakaian</span>
                            </label>
                            <p class="text-[11px] text-stone-500 mt-0.5">
                                Klik kotak ukuran untuk mengaktifkan / menonaktifkan jika stok ukuran tertentu habis.
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" id="btn-select-all-sizes" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer shadow-2xs">
                                Aktifkan Semua
                            </button>
                            <button type="button" id="btn-reset-sizes" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-stone-500 hover:text-stone-800 transition cursor-pointer shadow-2xs">
                                Nonaktifkan Semua
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2 sm:gap-2.5 pt-1.5">
                        @foreach ($allSizes as $s)
                            @php
                                $isChecked = in_array($s, $initialUkuran);
                            @endphp
                            <label class="size-toggle-card group relative flex flex-col items-center justify-center p-2.5 rounded-xl border-2 transition-all cursor-pointer {{ $isChecked ? 'border-[#b55322] bg-white shadow-2xs' : 'border-stone-200 bg-stone-100/70 opacity-60 hover:opacity-100' }}" title="Klik untuk {{ $isChecked ? 'menonaktifkan (stok habis)' : 'mengaktifkan (stok tersedia)' }}">
                                <input type="checkbox" name="ukuran[]" value="{{ $s }}" class="sr-only size-checkbox" {{ $isChecked ? 'checked' : '' }} data-size="{{ $s }}">
                                
                                <span class="size-label font-serif text-sm font-bold {{ $isChecked ? 'text-[#b55322]' : 'text-stone-400 line-through' }}">
                                    {{ $s }}
                                </span>
                                
                                <span class="size-status mt-1 text-[9px] font-bold uppercase tracking-wider {{ $isChecked ? 'text-emerald-700' : 'text-stone-400' }}">
                                    {{ $isChecked ? 'Tersedia' : 'Habis' }}
                                </span>

                                <span class="size-badge-check absolute top-1 right-1 grid h-3.5 w-3.5 place-items-center rounded-full text-[8px] font-bold text-white {{ $isChecked ? 'bg-[#b55322]' : 'bg-stone-300' }}">
                                    {{ $isChecked ? '✓' : '✕' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('ukuran')
                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Gambar Pakaian -->
                <div>
                    <label for="gambar" class="block text-xs font-semibold text-stone-700 mb-1">
                        Upload Foto / Mockup Pakaian <span class="{{ $design->exists ? 'text-stone-400 font-normal' : 'text-rose-500' }}">{{ $design->exists ? '— opsional' : '*' }}</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="w-full rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] px-3.5 py-2.5 text-xs text-stone-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#faefe8] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-[#b55322] hover:file:bg-[#f5dfd2] focus:outline-none cursor-pointer" {{ $design->exists ? '' : 'required' }}>
                    </div>
                    <p class="mt-1.5 text-[11px] text-stone-400">Rekomendasi: File PNG transparan, WEBP, JPG atau SVG &middot; Maksimal 10 MB</p>
                    @error('gambar')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror

                    @if ($design->exists && $design->gambar)
                        <div class="mt-3 flex items-center gap-3 rounded-xl border border-[#e8e4dc] bg-[#f5f2ec] p-3 text-xs text-stone-600">
                            <img src="{{ $design->gambarUrl() }}" alt="{{ $design->nama }}" class="h-10 w-10 object-contain rounded-lg border border-[#e8e4dc] bg-white p-1">
                            <div class="min-w-0 flex-1">
                                <span class="font-semibold text-stone-800">Berkas foto saat ini:</span>
                                <span class="truncate block text-stone-500">{{ basename($design->gambar) }}</span>
                            </div>
                            <span class="text-stone-400 font-normal text-[11px]">(Pilih file baru untuk mengganti)</span>
                        </div>
                    @endif
                </div>

                <!-- Upload Video 360° Playing Pakaian -->
                <div class="rounded-xl border border-amber-200/70 bg-amber-50/30 p-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="video_360" class="block text-xs font-bold text-stone-800">
                            Upload Video 360&deg; Playing <span class="text-stone-400 font-normal">&mdash; opsional</span>
                        </label>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-900 text-white px-2.5 py-0.5 text-[10px] font-mono font-bold shadow-2xs">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            360&deg; Video Playing
                        </span>
                    </div>
                    <p class="text-[11px] text-stone-500 mb-2.5">
                        Unggah rekaman putaran pakaian 360 derajat untuk mengaktifkan fitur <strong>360&deg; Video Playing</strong> interaktif di katalog dan studio etalase jemaat.
                    </p>
                    <div class="relative">
                        <input type="file" id="video_360" name="video_360" accept="video/mp4,video/webm,video/quicktime" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-900 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white hover:file:bg-stone-800 focus:outline-none cursor-pointer">
                    </div>
                    <p class="mt-1.5 text-[11px] text-stone-400">Mendukung format video MP4, WebM, atau MOV &middot; Maksimal 50 MB</p>
                    @error('video_360')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror

                    @if ($design->exists && $design->video_360)
                        <div class="mt-3 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/80 p-3 text-xs text-emerald-900">
                            <div class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-600 text-white font-mono text-[11px] font-bold shrink-0 shadow-2xs">
                                360&deg;
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-emerald-950 block">Video 360&deg; aktif di sistem:</span>
                                <span class="truncate block text-emerald-700 text-[11px]">{{ basename($design->video_360) }}</span>
                            </div>
                            @if ($design->video360Url())
                                <a href="{{ $design->video360Url() }}" target="_blank" class="rounded-lg bg-white border border-emerald-300 px-2.5 py-1 text-[11px] font-bold text-emerald-800 hover:bg-emerald-100 transition shrink-0 shadow-2xs">
                                    Buka Video &nearr;
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Submit Buttons -->
                <div class="pt-3 flex flex-wrap items-center gap-3 border-t border-stone-100">
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        {{ $design->exists ? 'Simpan Perubahan' : 'Upload & Simpan Desain' }}
                    </button>
                    <a href="{{ route('admin.designs') }}" class="rounded-xl border border-[#e8e4dc] bg-white px-5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Right: Real-time Live Preview Showcase -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-6 sticky top-20">
            
            <!-- Live Preview Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                <!-- Preview Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Pratinjau Etalase</h2>
                        <p class="text-[11px] text-stone-400">Tampilan live otomatis pada website publik.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        LIVE
                    </span>
                </div>

                <!-- Preview Mode Switcher (Foto vs 360° Video) -->
                <div class="flex items-center gap-1.5 rounded-xl bg-[#f5f2ec] p-1 border border-[#e8e4dc]">
                    <button type="button" id="tab-mode-photo" class="flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-stone-900 shadow-2xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span>Foto Mockup</span>
                    </button>
                    <button type="button" id="tab-mode-video" class="flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>360&deg; Video</span>
                        <span id="video-indicator-badge" class="{{ $initialVideoUrl ? 'inline-block' : 'hidden' }} rounded-full bg-stone-900 text-white font-mono text-[9px] px-1.5 py-0.2">360°</span>
                    </button>
                </div>

                <!-- Product Showcase Box -->
                <div class="overflow-hidden rounded-2xl border border-[#e8e4dc] bg-white shadow-2xs">
                    <!-- Media Showcase Container -->
                    <div id="preview-media-wrapper" class="relative overflow-hidden bg-[#fbf9f6] aspect-square flex items-center justify-center border-b border-[#e8e4dc]">
                        
                        <!-- 1. Photo View -->
                        <div id="view-photo-container" class="h-full w-full flex items-center justify-center p-6">
                            <img id="preview-img" src="{{ $initialGambarUrl ?: '' }}" alt="Pratinjau Kaos" class="{{ $initialGambarUrl ? 'max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300' : 'hidden' }}">

                            <!-- Photo Placeholder -->
                            <div id="preview-placeholder" class="{{ $initialGambarUrl ? 'hidden' : 'flex flex-col items-center justify-center gap-2 text-stone-400 p-4 text-center' }}">
                                <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white border border-[#e8e4dc] text-stone-400 shadow-2xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 text-stone-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-stone-700">Foto pakaian belum dipilih</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">Unggah gambar pakaian untuk pratinjau live</p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Video 360 View -->
                        <div id="view-video-container" class="hidden h-full w-full flex items-center justify-center bg-stone-950/5 relative overflow-hidden">
                            <!-- Actual Video Player -->
                            <video id="preview-video" src="{{ $initialVideoUrl ?: '' }}" playsinline webkit-playsinline muted loop autoplay class="{{ $initialVideoUrl ? 'h-full w-full object-contain p-4' : 'hidden' }}"></video>

                            <!-- Video Placeholder when no video -->
                            <div id="preview-video-placeholder" class="{{ $initialVideoUrl ? 'hidden' : 'flex flex-col items-center justify-center gap-2.5 text-stone-400 p-6 text-center' }}">
                                <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white border border-[#e8e4dc] text-stone-700 shadow-2xs">
                                    <svg class="h-7 w-7 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="9" stroke-dasharray="3 3"/>
                                        <polygon points="10 8 16 12 10 16 10 8" fill="currentColor"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-stone-800">Video 360&deg; Belum Dipilih</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5 max-w-[220px]">Pilih file video MP4/WebM pada form di samping untuk menguji putaran 360&deg; secara langsung.</p>
                                </div>
                            </div>

                            <!-- Floating Badges for 360 Video Playing -->
                            <span id="preview-video-badge" class="{{ $initialVideoUrl ? 'flex' : 'hidden' }} absolute left-3 bottom-3 items-center gap-1.5 rounded-full bg-stone-900/90 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-xs shadow-2xs z-10">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>360&deg; Video Playing</span>
                            </span>
                            <span id="preview-video-counter" class="{{ $initialVideoUrl ? 'block' : 'hidden' }} absolute right-3 bottom-3 rounded-full bg-stone-900/90 px-3 py-1 font-mono text-[10px] font-bold text-white backdrop-blur-xs shadow-2xs z-10">
                                0&deg;
                            </span>
                        </div>

                        <!-- Badge Top Left: Koleksi Kaos -->
                        <span class="absolute top-3 left-3 rounded-full bg-stone-900/80 backdrop-blur-xs px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white z-10">
                            Koleksi Kaos 2026
                        </span>

                        <!-- Badge Status Top Right -->
                        <span id="preview-status-badge" class="absolute top-3 right-3 rounded-full px-2.5 py-0.5 text-[10px] font-bold z-10 {{ $initialAktif == 1 ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">
                            {{ $initialAktif == 1 ? '• Aktif di Website' : '• Draf' }}
                        </span>
                    </div>

                    <!-- Showcase Body -->
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#b55322]">Koleksi Kaos Pembangunan</p>
                                <h3 id="preview-nama" class="font-serif text-lg font-bold text-stone-900 leading-snug break-words mt-0.5">
                                    {{ $initialNama ?: 'Nama Desain Kaos' }}
                                </h3>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] text-stone-400 block font-medium">Harga Donasi</span>
                                <span id="preview-harga" class="font-serif text-lg font-bold text-[#b55322]">
                                    Rp {{ number_format($initialHarga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <p id="preview-deskripsi" class="text-xs text-stone-500 leading-relaxed break-words whitespace-pre-line border-t border-stone-100 pt-2.5">
                            {{ $initialDeskripsi ?: 'Deskripsi dan filosofi desain kaos tampil di sini.' }}
                        </p>

                        <!-- Available Sizes Preview -->
                        <div class="border-t border-stone-100 pt-2.5">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Pilihan Ukuran:</span>
                                <span class="text-[10px] text-stone-400 italic">Klik untuk aktif / nonaktif</span>
                            </div>
                            <div id="preview-sizes-container" class="flex items-center gap-1.5 text-xs flex-wrap">
                                @foreach ($allSizes as $size)
                                    @php
                                        $isAvail = in_array($size, $initialUkuran);
                                    @endphp
                                    <button type="button"
                                        class="preview-size-btn grid h-7 w-7 place-items-center rounded-lg border text-[11px] font-bold transition cursor-pointer {{ $isAvail ? 'border-[#e8e4dc] bg-[#fbf9f6] text-stone-700 hover:border-[#b55322]' : 'border-stone-200 bg-stone-100 text-stone-300 line-through opacity-50' }}"
                                        data-size="{{ $size }}"
                                        title="{{ $size }} — {{ $isAvail ? 'Tersedia (Klik untuk ubah ke Habis)' : 'Habis (Klik untuk ubah ke Tersedia)' }}">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- CTA Mock Button -->
                        <div class="pt-2">
                            <div class="w-full rounded-xl bg-[#b55322]/10 border border-[#b55322]/20 py-2.5 text-center text-xs font-bold text-[#b55322]">
                                Pesan Kaos &amp; Donasi Sekarang &rarr;
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checklist Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-3">
                <h3 class="font-serif text-base font-bold text-stone-900">Checklist Publikasi Desain</h3>
                
                <ul class="space-y-2.5 text-xs">
                    <!-- Check 1: Nama -->
                    <li id="chk-nama" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Nama desain kaos diisi</span>
                    </li>

                    <!-- Check 2: Harga -->
                    <li id="chk-harga" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Harga donasi minimal Rp 10.000</span>
                    </li>

                    <!-- Check 3: Deskripsi -->
                    <li id="chk-deskripsi" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Deskripsi &amp; bahan pakaian dijelaskan</span>
                    </li>

                    <!-- Check 4: Ukuran -->
                    <li id="chk-ukuran" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Pilihan ukuran stok diatur</span>
                    </li>

                    <!-- Check 4: Gambar -->
                    <li id="chk-gambar" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Foto mockup pakaian diunggah</span>
                    </li>

                    <!-- Check 5: Video 360 -->
                    <li id="chk-video" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Video 360&deg; playing diunggah (opsional)</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <!-- Live Reactive Vanilla JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputNama = document.getElementById('nama');
            const inputHarga = document.getElementById('harga');
            const inputDeskripsi = document.getElementById('deskripsi');
            const selectAktif = document.getElementById('aktif');
            const inputGambar = document.getElementById('gambar');
            const inputVideo = document.getElementById('video_360');

            const prevNama = document.getElementById('preview-nama');
            const prevHarga = document.getElementById('preview-harga');
            const prevDeskripsi = document.getElementById('preview-deskripsi');
            const prevStatusBadge = document.getElementById('preview-status-badge');
            
            // Photo View Elements
            const viewPhotoContainer = document.getElementById('view-photo-container');
            const prevImg = document.getElementById('preview-img');
            const prevPlaceholder = document.getElementById('preview-placeholder');

            // Video View Elements
            const viewVideoContainer = document.getElementById('view-video-container');
            const prevVideo = document.getElementById('preview-video');
            const prevVideoPlaceholder = document.getElementById('preview-video-placeholder');
            const prevVideoBadge = document.getElementById('preview-video-badge');
            const prevVideoCounter = document.getElementById('preview-video-counter');
            const videoIndicatorBadge = document.getElementById('video-indicator-badge');

            // Tab Buttons
            const tabModePhoto = document.getElementById('tab-mode-photo');
            const tabModeVideo = document.getElementById('tab-mode-video');

            // Checklist Elements
            const chkNama = document.getElementById('chk-nama');
            const chkHarga = document.getElementById('chk-harga');
            const chkDeskripsi = document.getElementById('chk-deskripsi');
            const chkGambar = document.getElementById('chk-gambar');
            const chkVideo = document.getElementById('chk-video');

            let currentVideoBlobUrl = null;

            function formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }

            function setChecklist(itemEl, isCompleted) {
                const icon = itemEl.querySelector('.chk-icon');
                const text = itemEl.querySelector('.chk-text');
                if (isCompleted) {
                    itemEl.classList.remove('text-stone-400');
                    itemEl.classList.add('text-stone-700');
                    icon.className = 'chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700';
                    text.classList.add('font-medium');
                } else {
                    itemEl.classList.add('text-stone-400');
                    itemEl.classList.remove('text-stone-700');
                    icon.className = 'chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400';
                    text.classList.remove('font-medium');
                }
            }

            // Tab Switching (Photo vs Video)
            function switchTab(mode) {
                if (mode === 'video') {
                    tabModeVideo.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-stone-900 shadow-2xs cursor-pointer';
                    tabModePhoto.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer';
                    viewPhotoContainer.classList.add('hidden');
                    viewVideoContainer.classList.remove('hidden');
                    if (prevVideo.src) {
                        prevVideo.play().catch(function(){});
                    }
                } else {
                    tabModePhoto.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-stone-900 shadow-2xs cursor-pointer';
                    tabModeVideo.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer';
                    viewVideoContainer.classList.add('hidden');
                    viewPhotoContainer.classList.remove('hidden');
                }
            }

            tabModePhoto.addEventListener('click', function() { switchTab('photo'); });
            tabModeVideo.addEventListener('click', function() { switchTab('video'); });

            // Sync Nama
            function syncNama() {
                const val = inputNama.value.trim();
                prevNama.textContent = val || 'Nama Desain Kaos';
                setChecklist(chkNama, val.length > 0);
            }

            // Sync Harga
            function syncHarga() {
                const val = parseInt(inputHarga.value, 10);
                prevHarga.textContent = formatRupiah(val);
                setChecklist(chkHarga, !isNaN(val) && val >= 10000);
            }

            // Sync Deskripsi
            function syncDeskripsi() {
                const val = inputDeskripsi.value.trim();
                prevDeskripsi.textContent = val || 'Deskripsi dan filosofi desain kaos tampil di sini.';
                setChecklist(chkDeskripsi, val.length > 0);
            }

            // Sync Aktif Status
            function syncAktif() {
                const val = selectAktif.value;
                if (val === '1') {
                    prevStatusBadge.className = 'absolute top-3 right-3 rounded-full px-2.5 py-0.5 text-[10px] font-bold z-10 bg-emerald-50 border border-emerald-200 text-emerald-700';
                    prevStatusBadge.textContent = '• Aktif di Website';
                } else {
                    prevStatusBadge.className = 'absolute top-3 right-3 rounded-full px-2.5 py-0.5 text-[10px] font-bold z-10 bg-stone-200 text-stone-600';
                    prevStatusBadge.textContent = '• Draf';
                }
            }

            // Sync Gambar
            function syncGambar() {
                const file = inputGambar.files && inputGambar.files[0];
                if (file) {
                    const fileUrl = URL.createObjectURL(file);
                    prevImg.src = fileUrl;
                    prevImg.classList.remove('hidden');
                    prevPlaceholder.classList.add('hidden');
                    setChecklist(chkGambar, true);
                    switchTab('photo');
                } else {
                    @if ($design->exists && $design->gambar)
                        setChecklist(chkGambar, true);
                    @else
                        prevImg.classList.add('hidden');
                        prevImg.src = '';
                        prevPlaceholder.classList.remove('hidden');
                        setChecklist(chkGambar, false);
                    @endif
                }
            }

            // Sync Video 360
            function syncVideo() {
                const file = inputVideo.files && inputVideo.files[0];
                if (file) {
                    if (currentVideoBlobUrl) {
                        URL.revokeObjectURL(currentVideoBlobUrl);
                    }
                    currentVideoBlobUrl = URL.createObjectURL(file);
                    prevVideo.src = currentVideoBlobUrl;
                    prevVideo.classList.remove('hidden');
                    prevVideoPlaceholder.classList.add('hidden');
                    prevVideoBadge.classList.remove('hidden');
                    prevVideoBadge.classList.add('flex');
                    prevVideoCounter.classList.remove('hidden');
                    videoIndicatorBadge.classList.remove('hidden');
                    setChecklist(chkVideo, true);
                    switchTab('video');
                    prevVideo.play().catch(function(){});
                } else {
                    @if ($design->exists && $design->video_360)
                        setChecklist(chkVideo, true);
                        videoIndicatorBadge.classList.remove('hidden');
                    @else
                        if (!prevVideo.src) {
                            prevVideo.classList.add('hidden');
                            prevVideoPlaceholder.classList.remove('hidden');
                            prevVideoBadge.classList.add('hidden');
                            prevVideoCounter.classList.add('hidden');
                            videoIndicatorBadge.classList.add('hidden');
                            setChecklist(chkVideo, false);
                        }
                    @endif
                }
            }

            // Update angle display from video playback
            prevVideo.addEventListener('timeupdate', function () {
                const dur = prevVideo.duration || 2;
                const ct = prevVideo.currentTime || 0;
                const deg = Math.round((ct / dur) * 360) % 360;
                prevVideoCounter.textContent = deg + '°';
            });

            // Bind Events
            inputNama.addEventListener('input', syncNama);
            inputHarga.addEventListener('input', syncHarga);
            inputDeskripsi.addEventListener('input', syncDeskripsi);
            selectAktif.addEventListener('change', syncAktif);
            inputGambar.addEventListener('change', syncGambar);
            inputVideo.addEventListener('change', syncVideo);

            // Size Selection Logic
            const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
            const previewSizeBtns = document.querySelectorAll('.preview-size-btn');
            const btnSelectAllSizes = document.getElementById('btn-select-all-sizes');
            const btnResetSizes = document.getElementById('btn-reset-sizes');
            const chkUkuran = document.getElementById('chk-ukuran');

            function updateSizeCard(checkbox) {
                const card = checkbox.closest('.size-toggle-card');
                const label = card ? card.querySelector('.size-label') : null;
                const status = card ? card.querySelector('.size-status') : null;
                const checkBadge = card ? card.querySelector('.size-badge-check') : null;
                const size = checkbox.value;
                const isChecked = checkbox.checked;

                if (isChecked) {
                    if (card) {
                        card.classList.add('border-[#b55322]', 'bg-white', 'shadow-2xs');
                        card.classList.remove('border-stone-200', 'bg-stone-100/70', 'opacity-60');
                        card.title = 'Klik untuk menonaktifkan (stok habis)';
                    }
                    if (label) {
                        label.classList.add('text-[#b55322]');
                        label.classList.remove('text-stone-400', 'line-through');
                    }
                    if (status) {
                        status.textContent = 'Tersedia';
                        status.classList.add('text-emerald-700');
                        status.classList.remove('text-stone-400');
                    }
                    if (checkBadge) {
                        checkBadge.textContent = '✓';
                        checkBadge.classList.add('bg-[#b55322]');
                        checkBadge.classList.remove('bg-stone-300');
                    }
                } else {
                    if (card) {
                        card.classList.remove('border-[#b55322]', 'bg-white', 'shadow-2xs');
                        card.classList.add('border-stone-200', 'bg-stone-100/70', 'opacity-60');
                        card.title = 'Klik untuk mengaktifkan (stok tersedia)';
                    }
                    if (label) {
                        label.classList.remove('text-[#b55322]');
                        label.classList.add('text-stone-400', 'line-through');
                    }
                    if (status) {
                        status.textContent = 'Habis';
                        status.classList.remove('text-emerald-700');
                        status.classList.add('text-stone-400');
                    }
                    if (checkBadge) {
                        checkBadge.textContent = '✕';
                        checkBadge.classList.remove('bg-[#b55322]');
                        checkBadge.classList.add('bg-stone-300');
                    }
                }

                // Sync to preview buttons
                previewSizeBtns.forEach(btn => {
                    if (btn.dataset.size === size) {
                        if (isChecked) {
                            btn.className = 'preview-size-btn grid h-7 w-7 place-items-center rounded-lg border text-[11px] font-bold transition cursor-pointer border-[#e8e4dc] bg-[#fbf9f6] text-stone-700 hover:border-[#b55322]';
                            btn.title = size + ' — Tersedia (Klik untuk ubah ke Habis)';
                        } else {
                            btn.className = 'preview-size-btn grid h-7 w-7 place-items-center rounded-lg border text-[11px] font-bold transition cursor-pointer border-stone-200 bg-stone-100 text-stone-300 line-through opacity-50';
                            btn.title = size + ' — Habis (Klik untuk ubah ke Tersedia)';
                        }
                    }
                });

                // Update checklist: at least 1 size is selected
                let anyChecked = false;
                sizeCheckboxes.forEach(cb => { if (cb.checked) anyChecked = true; });
                if (chkUkuran) setChecklist(chkUkuran, anyChecked);
            }

            sizeCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    updateSizeCard(this);
                });
            });

            previewSizeBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const size = this.dataset.size;
                    const targetCb = document.querySelector('.size-checkbox[value="' + size + '"]');
                    if (targetCb) {
                        targetCb.checked = !targetCb.checked;
                        updateSizeCard(targetCb);
                    }
                });
            });

            if (btnSelectAllSizes) {
                btnSelectAllSizes.addEventListener('click', function () {
                    sizeCheckboxes.forEach(cb => {
                        cb.checked = true;
                        updateSizeCard(cb);
                    });
                });
            }

            if (btnResetSizes) {
                btnResetSizes.addEventListener('click', function () {
                    sizeCheckboxes.forEach(cb => {
                        cb.checked = false;
                        updateSizeCard(cb);
                    });
                });
            }

            // Initial Trigger
            syncNama();
            syncHarga();
            syncDeskripsi();
            syncAktif();
            sizeCheckboxes.forEach(cb => updateSizeCard(cb));

            @if ($design->exists && $design->gambar)
                setChecklist(chkGambar, true);
            @else
                setChecklist(chkGambar, false);
            @endif

            @if ($design->exists && $design->video_360)
                setChecklist(chkVideo, true);
                // If design has 360 video, start in video mode or have it ready
                prevVideo.play().catch(function(){});
            @else
                setChecklist(chkVideo, false);
            @endif
        });
    </script>
@endsection
