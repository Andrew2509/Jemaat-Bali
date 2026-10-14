@extends('layouts.admin')

@section('title', $progress->exists ? 'Edit Progres' : 'Tambah Progres')
@section('breadcrumb', 'Pengelolaan Konten')

@section('content')
    @php
        $previewStatus = old('status', $progress->status ?? 'terbit');
        $previewTanggal = old('tanggal', $progress->tanggal ? $progress->tanggal->format('Y-m-d') : date('Y-m-d'));
        $previewJudul = old('judul', $progress->judul ?? '');
        $previewTahap = old('tahap', $progress->tahap ?? 'Fondasi');
        $previewDeskripsi = old('deskripsi', $progress->deskripsi ?? '');
        $previewTeksAlt = old('teks_alt', $progress->teks_alt ?? '');
        $previewVideoUrl = old('video_url', $progress->video_url ?? '');
        $initialMediaUrl = $progress->exists ? $progress->mediaUrl() : null;
        $initialMediaTipe = $progress->exists ? $progress->tipe : null;
        $initialEmbedUrl = $progress->exists && $progress->video_url ? $progress->embedUrl() : null;
        $initialPlatform = $progress->exists ? $progress->videoPlatform() : null;
    @endphp

    <!-- Top Back & Header -->
    <div class="mb-6">
        <a href="{{ route('admin.content') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-stone-900 transition mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke daftar</span>
        </a>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI PROGRAM</p>
        <h1 class="mt-1 font-serif text-3xl font-bold text-stone-900">{{ $progress->exists ? 'Edit Progres' : 'Tambah Progres' }}</h1>
        <p class="mt-1 text-xs text-stone-500">{{ $progress->exists ? 'Perbarui dokumentasi pembangunan yang sudah tercatat.' : 'Catat progres pembangunan terbaru beserta media foto atau video tautan (YouTube, Instagram, TikTok).' }}</p>
    </div>

    <!-- Main Grid: Form on Left, Live Preview on Right -->
    <div class="grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left: Form -->
        <div class="lg:col-span-7 xl:col-span-7">
            <form id="progress-form" method="POST" action="{{ $progress->exists ? route('admin.content.update', $progress->id) : route('admin.content.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4 shadow-2xs">
                @csrf

                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-xs font-semibold text-stone-700 mb-1">
                        Judul Progres <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ $previewJudul }}" placeholder="Contoh: Pemasangan fondasi bangunan utama" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none" required>
                    @error('judul')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-stone-700 mb-1">
                        Keterangan / Deskripsi <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Ringkasan kegiatan pembangunan pada tahap ini..." class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none leading-relaxed" required>{{ $previewDeskripsi }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal & Tahap -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="tanggal" class="block text-xs font-semibold text-stone-700 mb-1">
                            Tanggal Dokumentasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ $previewTanggal }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                        @error('tanggal')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tahap" class="block text-xs font-semibold text-stone-700 mb-1">
                            Tahap Pembangunan <span class="text-stone-400 font-normal">— opsional</span>
                        </label>
                        <input type="text" id="tahap" name="tahap" value="{{ $previewTahap }}" placeholder="Contoh: Fondasi, Lantai dasar, dll" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                        @error('tahap')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status & Teks Alt -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="status" class="block text-xs font-semibold text-stone-700 mb-1">
                            Status Publikasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none" required>
                            <option value="terbit" @selected($previewStatus === 'terbit')>Terbit — terlihat di website</option>
                            <option value="draf" @selected($previewStatus === 'draf')>Draf — tersimpan di admin</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="teks_alt" class="block text-xs font-semibold text-stone-700 mb-1">
                            Teks Alternatif Foto <span class="text-stone-400 font-normal">— opsional</span>
                        </label>
                        <input type="text" id="teks_alt" name="teks_alt" value="{{ $previewTeksAlt }}" placeholder="Deskripsi gambar untuk aksesibilitas" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
                        @error('teks_alt')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- BAGIAN MEDIA: PILIHAN LINK (YouTube, IG, TikTok) ATAU UPLOAD FILE -->
                <div class="rounded-2xl border border-[#e8e4dc] bg-[#faf8f5] p-4.5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-200/70 pb-3">
                        <div>
                            <p class="text-xs font-bold text-stone-900">Media Dokumentasi</p>
                            <p class="text-[11px] text-stone-500 mt-0.5">Bisa gunakan link video eksternal atau upload file langsung</p>
                        </div>
                        
                        <!-- Segmented Switcher -->
                        <div class="inline-flex rounded-xl bg-stone-200/80 p-0.5 text-xs font-semibold shadow-inner">
                            <button type="button" id="tab-btn-link" onclick="switchMediaTab('link')"
                                class="rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold {{ $previewVideoUrl || (!$initialMediaUrl && !$previewVideoUrl) ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                                <span>🔗 Link Video (YouTube / IG / TikTok)</span>
                            </button>
                            <button type="button" id="tab-btn-file" onclick="switchMediaTab('file')"
                                class="rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold {{ $initialMediaUrl && !$previewVideoUrl ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                                <span>📁 Upload File</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: TAUTAN LINK (YOUTUBE / INSTAGRAM / TIKTOK) -->
                    <div id="media-tab-link" class="{{ $previewVideoUrl || (!$initialMediaUrl && !$previewVideoUrl) ? '' : 'hidden' }} space-y-3">
                        <div>
                            <label for="video_url" class="block text-xs font-semibold text-stone-700 mb-1">
                                Tautan / URL Video <span class="text-[#b55322]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                    </svg>
                                </span>
                                <input type="text" id="video_url" name="video_url" value="{{ $previewVideoUrl }}"
                                    placeholder="https://www.youtube.com/... atau https://www.instagram.com/reel/... atau https://www.tiktok.com/..."
                                    class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none transition">
                            </div>
                            @error('video_url')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Platform Badges & Guide -->
                        <div class="flex flex-wrap items-center gap-2 pt-0.5">
                            <span class="text-[11px] text-stone-500 font-medium">Format platform didukung:</span>
                            
                            <!-- YouTube badge -->
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 border border-rose-200/80 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                <svg class="w-3.5 h-3.5 fill-rose-600" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                <span>YouTube (Video / Shorts)</span>
                            </span>

                            <!-- Instagram badge -->
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-50 border border-purple-200/80 px-2.5 py-1 text-[11px] font-bold text-purple-700">
                                <svg class="w-3.5 h-3.5 fill-purple-600" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                <span>Instagram (Reels / Post)</span>
                            </span>

                            <!-- TikTok badge -->
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-stone-900 border border-stone-800 px-2.5 py-1 text-[11px] font-bold text-white">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                <span>TikTok</span>
                            </span>
                        </div>

                        <!-- Live Detection Alert Box -->
                        <div id="link-detected-status" class="hidden rounded-xl bg-emerald-50 border border-emerald-200/90 p-3 text-xs text-emerald-900 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span id="link-detected-text" class="font-bold"></span>
                            </div>
                            <span class="text-[10px] bg-emerald-200/60 text-emerald-800 px-2 py-0.5 rounded-md font-semibold">Pratinjau Otomatis</span>
                        </div>

                            <!-- Action Box: Konversi Otomatis ke MP4 Server -->
                        <div class="rounded-xl border border-[#faefe8] bg-[#fdfaf7] p-3.5 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-[#faefe8] text-[#b55322] font-bold text-xs">⚡</span>
                                    <span class="text-xs font-bold text-stone-900">Konversi Otomatis ke MP4 Server</span>
                                </div>
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">100% Video Bersih</span>
                            </div>
                            <p class="text-[11px] text-stone-500 leading-relaxed">
                                Unduh langsung dari link menjadi berkas video MP4 di server Jemaat Bali. Diputar menggunakan tag <code class="bg-stone-200/60 px-1 py-0.5 rounded text-[10px] font-mono text-stone-700">&lt;video&gt;</code> murni tanpa watermark, tanpa logo platform, dan tanpa nama channel.
                            </p>

                            <!-- Pilihan Kompresi Ukuran Video -->
                            <div class="flex flex-wrap items-center gap-3 py-1 border-y border-stone-200/60">
                                <div class="flex items-center gap-2">
                                    <label for="video_quality" class="text-xs font-semibold text-stone-700 whitespace-nowrap flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#b55322]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        <span>Kompresi Ukuran:</span>
                                    </label>
                                    <select id="video_quality" name="video_quality" class="rounded-xl border border-[#e8e4dc] bg-white px-3 py-1.5 text-xs text-stone-800 font-medium focus:border-[#b55322] focus:outline-none transition cursor-pointer shadow-2xs">
                                        <option value="720p" selected>🌟 720p Hemat Web (Cepat & Jernih - Rekomendasi)</option>
                                        <option value="480p">🍃 480p Super Ringan (Sangat Kecil & Hemat Kuota)</option>
                                        <option value="1080p">🎬 1080p Kualitas Penuh (Ukuran Lebih Besar)</option>
                                    </select>
                                </div>
                                <span class="text-[11px] text-stone-500">Kapasitas otomatis diperkecil agar server hemat dan cepat di HP.</span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <button type="button" id="btn-convert-now" class="inline-flex items-center gap-2 rounded-xl bg-[#292524] hover:bg-stone-950 px-3.5 py-2 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                    <svg id="btn-convert-icon" class="h-3.5 w-3.5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z"/>
                                        <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z"/>
                                    </svg>
                                    <span id="btn-convert-spinner" class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                                    <span id="btn-convert-text">Unduh &amp; Simpan ke Server Sekarang</span>
                                </button>
                                
                                <label class="inline-flex items-center gap-2 text-xs font-medium text-stone-700 cursor-pointer select-none">
                                    <input type="checkbox" name="konversi_ke_mp4" value="1" checked class="h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]">
                                    <span>Otomatis unduh ke server saat klik Simpan</span>
                                </label>
                            </div>

                            <input type="hidden" id="downloaded_media_path" name="downloaded_media_path" value="">

                            <!-- Conversion Status Feedback Message -->
                            <div id="convert-status-box" class="hidden rounded-lg p-2.5 text-xs"></div>
                        </div>
                    </div>

                    <!-- TAB 2: UPLOAD FILE LOKAL (FOTO ATAU VIDEO MP4) -->
                    <div id="media-tab-file" class="{{ $initialMediaUrl && !$previewVideoUrl ? '' : 'hidden' }} space-y-2.5">
                        <label for="media" class="block text-xs font-semibold text-stone-700 mb-1">
                            Pilih File dari Komputer / HP (Foto JPG, PNG, WEBP atau Video MP4)
                        </label>
                        <div class="relative">
                            <input type="file" id="media" name="media" accept="image/jpeg,image/png,image/webp,video/mp4" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#faefe8] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-[#b55322] hover:file:bg-[#f5dfd2] focus:outline-none cursor-pointer">
                        </div>
                        <p class="text-[11px] text-stone-400">Maksimal ukuran file: 100 MB (Foto JPG/PNG/WEBP atau Video MP4)</p>
                        @error('media')
                            <p class="text-xs text-rose-500">{{ $message }}</p>
                        @enderror

                        @if ($progress->exists && $progress->mediaUrl())
                            <div class="mt-3 flex items-center gap-3 rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs text-stone-600">
                                <span class="font-semibold text-stone-800">Media file saat ini tersimpan:</span>
                                <span class="truncate text-stone-500">{{ basename($progress->media_path) }}</span>
                                <span class="text-stone-400 font-normal">(Pilih file baru untuk mengganti)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-3 flex flex-wrap items-center gap-3 border-t border-stone-100">
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        {{ $progress->exists ? 'Simpan perubahan' : 'Simpan progres' }}
                    </button>
                    <a href="{{ route('admin.content') }}" class="rounded-xl border border-[#e8e4dc] bg-white px-5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">
                        Batal
                    </a>

                    @if ($progress->exists)
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus progres ini secara permanen? Berkas video/foto juga akan dihapus.')) document.getElementById('delete-progress-form').submit();" class="ml-auto rounded-xl border border-rose-200/90 bg-rose-50 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition cursor-pointer flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Hapus Progres</span>
                        </button>
                    @endif
                </div>
            </form>

            @if ($progress->exists)
                <form id="delete-progress-form" method="POST" action="{{ route('admin.content.destroy', $progress->id) }}" class="hidden">
                    @csrf
                </form>
            @endif
        </div>

        <!-- Right: Live Preview Panel & Checklist -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-6 sticky top-20">
            
            <!-- Live Preview Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                <!-- Preview Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Pratinjau</h2>
                        <p class="text-[11px] text-stone-400">Tampilan live otomatis saat mengetik link atau judul.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        LIVE
                    </span>
                </div>

                <!-- Card Preview Container -->
                <div class="overflow-hidden rounded-xl border border-[#e8e4dc] bg-white shadow-2xs">
                    <!-- Media Preview Area (16:9) -->
                    <div id="preview-media-wrapper" class="relative overflow-hidden bg-[#f5f2ec] aspect-16/9 flex items-center justify-center border-b border-[#e8e4dc]">
                        <!-- Dynamic Image -->
                        <img id="preview-img" src="{{ $initialMediaUrl ?: '' }}" alt="Pratinjau" class="{{ $initialMediaUrl && $initialMediaTipe === 'foto' && !$previewVideoUrl ? 'h-full w-full object-cover' : 'hidden' }}">
                        
                        <!-- Dynamic Video File MP4 -->
                        <video id="preview-video" src="{{ $initialMediaUrl ?: '' }}" controls class="{{ $initialMediaUrl && $initialMediaTipe === 'video' && !$previewVideoUrl ? 'h-full w-full object-cover bg-stone-900' : 'hidden' }}"></video>

                        <!-- Dynamic Embed Iframe (YouTube / Instagram / TikTok) -->
                        @php
                            $iframeStyle = 'position:absolute; inset:0; width:100%; height:100%; border:0;';
                            if ($initialPlatform === 'youtube') {
                                $iframeStyle = 'position:absolute; left:0; width:100%; height:calc(100% + 140px); margin-top:-65px; border:0;';
                            } elseif ($initialPlatform === 'instagram') {
                                $iframeStyle = 'position:absolute; left:0; width:100%; height:calc(100% + 180px); margin-top:-65px; border:0;';
                            } elseif ($initialPlatform === 'tiktok') {
                                $iframeStyle = 'position:absolute; left:0; width:100%; height:calc(100% + 95px); margin-top:-48px; border:0;';
                            }
                        @endphp
                        <iframe id="preview-iframe" src="{{ $initialEmbedUrl ?: '' }}" style="{{ $iframeStyle }}" class="{{ $initialEmbedUrl ? '' : 'hidden' }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                        <!-- Fallback Placeholder -->
                        <div id="preview-placeholder" class="{{ ($initialMediaUrl || $initialEmbedUrl) ? 'hidden' : 'flex flex-col items-center justify-center gap-2 text-stone-400 p-4 text-center' }}">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white border border-[#e8e4dc] text-stone-400 shadow-2xs">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-stone-600">Media belum dipilih</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">Tempel link YouTube/IG/TikTok atau upload file foto/video</p>
                            </div>
                        </div>

                        <!-- Pill Badge on top of media -->
                        <div class="absolute bottom-2.5 left-2.5 z-10 pointer-events-none">
                            <span id="preview-media-type-badge" class="{{ ($initialMediaUrl || $initialEmbedUrl) ? 'inline-flex' : 'hidden' }} items-center gap-1.5 rounded-md bg-stone-900/80 backdrop-blur-xs px-2.5 py-1 text-[10px] font-semibold text-white shadow-sm">
                                <span id="preview-media-type-icon">
                                    @if ($initialPlatform === 'youtube') 🔴 @elseif($initialPlatform === 'instagram') 🟣 @elseif($initialPlatform === 'tiktok') ⚫ @else 📷 @endif
                                </span>
                                <span id="preview-media-type-text">
                                    {{ $progress->exists ? $progress->videoPlatformLabel() : 'Foto' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2">
                        <!-- Judul & Status Pill -->
                        <div class="flex items-start justify-between gap-3">
                            <p id="preview-judul" class="text-sm font-bold text-stone-900 leading-snug break-words">
                                {{ $previewJudul ?: 'Judul progres' }}
                            </p>
                            <span id="preview-status-pill" class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $previewStatus === 'terbit' ? 'bg-[#f2eee7] text-stone-700' : 'bg-stone-100 text-stone-500' }}">
                                <span id="preview-status-dot" class="h-1.5 w-1.5 rounded-full {{ $previewStatus === 'terbit' ? 'bg-stone-500' : 'bg-stone-400' }}"></span>
                                <span id="preview-status-text">{{ $previewStatus === 'terbit' ? 'Terbit' : 'Draf' }}</span>
                            </span>
                        </div>

                        <!-- Tanggal & Tahap -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs text-stone-400">
                            <span id="preview-tanggal">{{ $previewTanggal ? \Illuminate\Support\Carbon::parse($previewTanggal)->translatedFormat('d F Y') : 'Tanggal belum diisi' }}</span>
                            <span id="preview-tahap-separator">&middot;</span>
                            <span id="preview-tahap" class="text-stone-600 font-medium">{{ $previewTahap ?: 'Tahap belum ditentukan' }}</span>
                        </div>

                        <!-- Deskripsi -->
                        <p id="preview-deskripsi" class="text-xs text-stone-500 leading-relaxed break-words whitespace-pre-line pt-1">
                            {{ $previewDeskripsi ?: 'Deskripsi progres tampil di sini.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Checklist Card (Updates live) -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-3">
                <h3 class="font-serif text-base font-bold text-stone-900">Checklist sebelum terbit</h3>
                
                <ul class="space-y-2.5 text-xs">
                    <!-- Check 1: Judul -->
                    <li id="chk-judul" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Judul progres diisi</span>
                    </li>

                    <!-- Check 2: Tanggal -->
                    <li id="chk-tanggal" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Tanggal dokumentasi ditentukan</span>
                    </li>

                    <!-- Check 3: Keterangan -->
                    <li id="chk-deskripsi" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Keterangan / deskripsi lengkap</span>
                    </li>

                    <!-- Check 4: Media -->
                    <li id="chk-media" class="flex items-center gap-2.5 text-stone-400 transition">
                        <span class="chk-icon grid h-5 w-5 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="chk-text">Media foto / video dokumentasi (link / file)</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <!-- Live Reactive Vanilla JavaScript -->
    <script>
        // Tab switcher
        function switchMediaTab(tab) {
            const tabBtnLink = document.getElementById('tab-btn-link');
            const tabBtnFile = document.getElementById('tab-btn-file');
            const tabContentLink = document.getElementById('media-tab-link');
            const tabContentFile = document.getElementById('media-tab-file');

            if (tab === 'link') {
                tabBtnLink.className = 'rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold bg-white text-stone-900 shadow-2xs';
                tabBtnFile.className = 'rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold text-stone-600 hover:text-stone-900';
                tabContentLink.classList.remove('hidden');
                tabContentFile.classList.add('hidden');
                document.getElementById('video_url')?.focus();
            } else {
                tabBtnFile.className = 'rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold bg-white text-stone-900 shadow-2xs';
                tabBtnLink.className = 'rounded-lg px-3 py-1.5 text-xs transition cursor-pointer font-bold text-stone-600 hover:text-stone-900';
                tabContentFile.classList.remove('hidden');
                tabContentLink.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const inputJudul = document.getElementById('judul');
            const inputDeskripsi = document.getElementById('deskripsi');
            const inputTanggal = document.getElementById('tanggal');
            const inputTahap = document.getElementById('tahap');
            const selectStatus = document.getElementById('status');
            const inputMedia = document.getElementById('media');
            const inputVideoUrl = document.getElementById('video_url');

            const prevJudul = document.getElementById('preview-judul');
            const prevDeskripsi = document.getElementById('preview-deskripsi');
            const prevTanggal = document.getElementById('preview-tanggal');
            const prevTahap = document.getElementById('preview-tahap');
            const prevStatusPill = document.getElementById('preview-status-pill');
            const prevStatusDot = document.getElementById('preview-status-dot');
            const prevStatusText = document.getElementById('preview-status-text');
            const prevImg = document.getElementById('preview-img');
            const prevVideo = document.getElementById('preview-video');
            const prevIframe = document.getElementById('preview-iframe');
            const prevPlaceholder = document.getElementById('preview-placeholder');
            const prevMediaBadge = document.getElementById('preview-media-type-badge');
            const prevMediaIcon = document.getElementById('preview-media-type-icon');
            const prevMediaText = document.getElementById('preview-media-type-text');

            const statusDetectedBox = document.getElementById('link-detected-status');
            const statusDetectedText = document.getElementById('link-detected-text');

            const chkJudul = document.getElementById('chk-judul');
            const chkTanggal = document.getElementById('chk-tanggal');
            const chkDeskripsi = document.getElementById('chk-deskripsi');
            const chkMedia = document.getElementById('chk-media');

            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            function formatIdDate(val) {
                if (!val) return 'Tanggal belum diisi';
                const parts = val.split('-');
                if (parts.length === 3) {
                    const y = parts[0];
                    const m = parseInt(parts[1], 10) - 1;
                    const d = parseInt(parts[2], 10);
                    if (m >= 0 && m < 12) {
                        return `${d} ${months[m]} ${y}`;
                    }
                }
                return val;
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

            // Parser URL Video (YouTube, Instagram, TikTok)
            function parseVideoUrl(url) {
                if (!url) return null;
                url = url.trim();

                // 1. YouTube
                const ytMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
                if (ytMatch && ytMatch[1]) {
                    return {
                        platform: 'youtube',
                        id: ytMatch[1],
                        label: 'YouTube Video',
                        icon: '🔴',
                        embed: `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?autoplay=1&controls=0&rel=0&modestbranding=1&playsinline=1&iv_load_policy=3&disablekb=1&fs=0`,
                    };
                }

                // 2. Instagram (Reels / Post / TV)
                const igMatch = url.match(/(?:instagram\.com|instagr\.am)\/(?:p|reel|tv)\/([a-zA-Z0-9_-]+)/i);
                if (igMatch && igMatch[1]) {
                    return {
                        platform: 'instagram',
                        id: igMatch[1],
                        label: 'Instagram (Reels / Post)',
                        icon: '🟣',
                        embed: `https://www.instagram.com/reel/${igMatch[1]}/embed/`,
                    };
                }

                // 3. TikTok
                const ttMatch = url.match(/(?:tiktok\.com\/@[^\/]+\/video\/|tiktok\.com\/v\/)(\d+)/i);
                if (ttMatch && ttMatch[1]) {
                    return {
                        platform: 'tiktok',
                        id: ttMatch[1],
                        label: 'TikTok Video',
                        icon: '⚫',
                        embed: `https://www.tiktok.com/player/v1/${ttMatch[1]}?autoplay=1&music_info=0&description=0`,
                    };
                }

                // Fallback valid URL
                if (url.startsWith('http://') || url.startsWith('https://')) {
                    return {
                        platform: 'other',
                        id: null,
                        label: 'Link Video Web',
                        icon: '🎬',
                        embed: url,
                    };
                }

                return null;
            }

            // Sync Judul
            function syncJudul() {
                const val = inputJudul.value.trim();
                prevJudul.textContent = val || 'Judul progres';
                setChecklist(chkJudul, val.length > 0);
            }

            // Sync Deskripsi
            function syncDeskripsi() {
                const val = inputDeskripsi.value.trim();
                prevDeskripsi.textContent = val || 'Deskripsi progres tampil di sini.';
                setChecklist(chkDeskripsi, val.length > 0);
            }

            // Sync Tanggal
            function syncTanggal() {
                const val = inputTanggal.value;
                prevTanggal.textContent = formatIdDate(val);
                setChecklist(chkTanggal, !!val);
            }

            // Sync Tahap
            function syncTahap() {
                const val = inputTahap.value.trim();
                prevTahap.textContent = val || 'Tahap belum ditentukan';
            }

            // Sync Status
            function syncStatus() {
                const val = selectStatus.value;
                if (val === 'terbit') {
                    prevStatusPill.className = 'shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-[#f2eee7] text-stone-700';
                    prevStatusDot.className = 'h-1.5 w-1.5 rounded-full bg-stone-500';
                    prevStatusText.textContent = 'Terbit';
                } else {
                    prevStatusPill.className = 'shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-stone-100 text-stone-500';
                    prevStatusDot.className = 'h-1.5 w-1.5 rounded-full bg-stone-400';
                    prevStatusText.textContent = 'Draf';
                }
            }

            // Sync Video Link Input
            function syncVideoLink() {
                const val = (inputVideoUrl.value || '').trim();
                const parsed = parseVideoUrl(val);

                if (parsed) {
                    // Update Alert Box
                    statusDetectedBox.classList.remove('hidden');
                    statusDetectedText.textContent = `Terdeteksi: ${parsed.label}`;

                    // Update Right Live Preview with Iframe
                    prevImg.classList.add('hidden');
                    prevVideo.classList.add('hidden');
                    prevPlaceholder.classList.add('hidden');

                    if (prevIframe.src !== parsed.embed) {
                        prevIframe.src = parsed.embed;
                    }
                    if (parsed.platform === 'youtube') {
                        prevIframe.style.cssText = 'position:absolute; left:0; width:100%; height:calc(100% + 140px); margin-top:-65px; border:0;';
                    } else if (parsed.platform === 'instagram') {
                        prevIframe.style.cssText = 'position:absolute; left:0; width:100%; height:calc(100% + 180px); margin-top:-65px; border:0;';
                    } else if (parsed.platform === 'tiktok') {
                        prevIframe.style.cssText = 'position:absolute; left:0; width:100%; height:calc(100% + 95px); margin-top:-48px; border:0;';
                    } else {
                        prevIframe.style.cssText = 'position:absolute; inset:0; width:100%; height:100%; border:0;';
                    }
                    prevIframe.classList.remove('hidden');

                    // Update Badge
                    prevMediaBadge.classList.remove('hidden');
                    prevMediaBadge.classList.add('inline-flex');
                    prevMediaIcon.textContent = parsed.icon;
                    prevMediaText.textContent = parsed.label;

                    setChecklist(chkMedia, true);
                    return true;
                } else {
                    statusDetectedBox.classList.add('hidden');
                    // Check if file upload exists
                    if (inputMedia && inputMedia.files && inputMedia.files[0]) {
                        return syncMediaFile();
                    }
                    
                    @if ($progress->exists && $progress->mediaUrl())
                        // Restore existing file
                        prevIframe.classList.add('hidden');
                        prevIframe.src = '';
                        @if ($progress->tipe === 'video')
                            prevVideo.classList.remove('hidden');
                        @else
                            prevImg.classList.remove('hidden');
                        @endif
                        prevPlaceholder.classList.add('hidden');
                        setChecklist(chkMedia, true);
                        return true;
                    @else
                        prevIframe.classList.add('hidden');
                        prevIframe.src = '';
                        prevImg.classList.add('hidden');
                        prevVideo.classList.add('hidden');
                        prevPlaceholder.classList.remove('hidden');
                        prevMediaBadge.classList.add('hidden');
                        setChecklist(chkMedia, false);
                        return false;
                    @endif
                }
            }

            // Sync Media File Upload
            function syncMediaFile() {
                const file = inputMedia.files && inputMedia.files[0];
                if (file) {
                    const fileUrl = URL.createObjectURL(file);
                    const isVideo = file.type.startsWith('video/');

                    prevIframe.classList.add('hidden');
                    prevIframe.src = '';

                    if (isVideo) {
                        prevImg.classList.add('hidden');
                        prevImg.src = '';
                        prevVideo.src = fileUrl;
                        prevVideo.classList.remove('hidden');
                        prevMediaIcon.textContent = '🎥';
                        prevMediaText.textContent = 'Video MP4';
                    } else {
                        prevVideo.classList.add('hidden');
                        prevVideo.src = '';
                        prevImg.src = fileUrl;
                        prevImg.classList.remove('hidden');
                        prevMediaIcon.textContent = '📷';
                        prevMediaText.textContent = 'Foto';
                    }
                    prevPlaceholder.classList.add('hidden');
                    prevMediaBadge.classList.remove('hidden');
                    prevMediaBadge.classList.add('inline-flex');
                    setChecklist(chkMedia, true);
                    return true;
                } else {
                    // Fall back to link if available
                    if (inputVideoUrl.value.trim()) {
                        return syncVideoLink();
                    }

                    @if ($progress->exists && $progress->mediaUrl())
                        setChecklist(chkMedia, true);
                    @else
                        prevImg.classList.add('hidden');
                        prevVideo.classList.add('hidden');
                        prevIframe.classList.add('hidden');
                        prevPlaceholder.classList.remove('hidden');
                        prevMediaBadge.classList.add('hidden');
                        setChecklist(chkMedia, false);
                    @endif
                    return false;
                }
            }

            // Bind Event Listeners
            inputJudul.addEventListener('input', syncJudul);
            inputDeskripsi.addEventListener('input', syncDeskripsi);
            inputTanggal.addEventListener('input', syncTanggal);
            inputTanggal.addEventListener('change', syncTanggal);
            inputTahap.addEventListener('input', syncTahap);
            selectStatus.addEventListener('change', syncStatus);
            inputMedia.addEventListener('change', syncMediaFile);
            inputVideoUrl.addEventListener('input', syncVideoLink);
            inputVideoUrl.addEventListener('paste', () => setTimeout(syncVideoLink, 50));

            // Button Convert Now Handler
            const btnConvertNow = document.getElementById('btn-convert-now');
            const btnConvertSpinner = document.getElementById('btn-convert-spinner');
            const btnConvertIcon = document.getElementById('btn-convert-icon');
            const btnConvertText = document.getElementById('btn-convert-text');
            const convertStatusBox = document.getElementById('convert-status-box');
            const inputDownloadedMediaPath = document.getElementById('downloaded_media_path');

            if (btnConvertNow) {
                btnConvertNow.addEventListener('click', async function () {
                    const url = inputVideoUrl.value.trim();
                    if (!url) {
                        alert('Silakan tempelkan tautan video terlebih dahulu pada kolom di atas.');
                        inputVideoUrl.focus();
                        return;
                    }

                    btnConvertNow.disabled = true;
                    btnConvertSpinner.classList.remove('hidden');
                    btnConvertIcon.classList.add('hidden');
                    btnConvertText.textContent = 'Sedang mengunduh & kompresi...';
                    convertStatusBox.className = 'rounded-lg p-2.5 text-xs bg-amber-50 text-amber-900 border border-amber-200 block';
                    convertStatusBox.innerHTML = '<span class="font-bold">⏳ Sedang memproses:</span> Mengunduh video dari tautan dan mengompresi ukuran berkas MP4 di server...';

                    try {
                        const token = document.querySelector('input[name="_token"]').value;
                        const selectQuality = document.getElementById('video_quality');
                        const quality = selectQuality ? selectQuality.value : '720p';

                        const response = await fetch('{{ route('admin.content.convert-video') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ 
                                video_url: url,
                                video_quality: quality
                            })
                        });

                        const res = await response.json();

                        if (response.ok && res.success) {
                            inputDownloadedMediaPath.value = res.media_path;
                            convertStatusBox.className = 'rounded-lg p-2.5 text-xs bg-emerald-50 text-emerald-900 border border-emerald-200 block';
                            
                            const sizeBadge = res.size_human ? ' &bull; Ukuran berkas: <span class="font-bold underline">' + res.size_human + '</span>' : '';
                            convertStatusBox.innerHTML = '<strong>✅ Berhasil:</strong> ' + res.message + '<br><span class="text-[11px] text-emerald-700">Tersimpan di server: ' + res.media_path + sizeBadge + '</span>';

                            // Switch live preview to direct MP4 video player!
                            prevIframe.classList.add('hidden');
                            prevIframe.src = '';
                            prevImg.classList.add('hidden');
                            prevImg.src = '';

                            prevVideo.src = res.media_url;
                            prevVideo.classList.remove('hidden');
                            prevPlaceholder.classList.add('hidden');

                            prevMediaBadge.classList.remove('hidden');
                            prevMediaBadge.classList.add('inline-flex');
                            prevMediaIcon.textContent = '🎥';
                            prevMediaText.textContent = 'Video MP4 Bersih (Server)';
                            setChecklist(chkMedia, true);
                        } else {
                            convertStatusBox.className = 'rounded-lg p-2.5 text-xs bg-rose-50 text-rose-900 border border-rose-200 block';
                            convertStatusBox.innerHTML = '<strong>❌ Gagal:</strong> ' + (res.message || 'Gagal mengunduh video.');
                        }
                    } catch (err) {
                        convertStatusBox.className = 'rounded-lg p-2.5 text-xs bg-rose-50 text-rose-900 border border-rose-200 block';
                        convertStatusBox.innerHTML = '<strong>❌ Error:</strong> Terjadi kendala saat mengunduh video ke server.';
                    } finally {
                        btnConvertNow.disabled = false;
                        btnConvertSpinner.classList.add('hidden');
                        btnConvertIcon.classList.remove('hidden');
                        btnConvertText.textContent = 'Unduh & Simpan ke Server Sekarang';
                    }
                });
            }

            // Initial trigger
            syncJudul();
            syncDeskripsi();
            syncTanggal();
            syncTahap();
            syncStatus();

            if (inputVideoUrl.value.trim()) {
                syncVideoLink();
            } else if (inputMedia.files && inputMedia.files[0]) {
                syncMediaFile();
            } else {
                @if ($progress->exists && ($progress->mediaUrl() || $progress->video_url))
                    setChecklist(chkMedia, true);
                @else
                    setChecklist(chkMedia, false);
                @endif
            }
        });
    </script>
@endsection
