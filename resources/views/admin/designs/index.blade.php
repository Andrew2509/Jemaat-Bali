@extends('layouts.admin')

@section('title', 'Koleksi Kaos Pembangunan')
@section('breadcrumb', 'Koleksi Kaos')

@section('content')
    <!-- Toast / Notification -->
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI FUNDRAISER</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Koleksi Kaos Pembangunan</h1>
            <p class="mt-1 text-sm text-stone-500">Kelola katalog desain pakaian, harga donasi kaos, dan unggah desain baru untuk website publik.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('design') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs transition">
                <span>Lihat di Website</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </a>
            <a href="{{ route('admin.designs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2.5 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Upload Desain Kaos</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs & Stats -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8e4dc]">
        <nav class="flex gap-8 -mb-px">
            <a href="{{ route('admin.designs') }}" class="border-b-2 border-[#b55322] pb-3 text-xs font-bold text-[#b55322]">Semua Koleksi</a>
            <a href="{{ route('admin.designs', ['status' => 'aktif']) }}" class="border-b-2 {{ request('status') === 'aktif' ? 'border-[#b55322] text-[#b55322] font-bold' : 'border-transparent text-stone-500 font-semibold hover:text-stone-900' }} pb-3 text-xs transition">Aktif di Website ({{ $totalAktif }})</a>
            <a href="{{ route('admin.designs', ['status' => 'nonaktif']) }}" class="border-b-2 {{ request('status') === 'nonaktif' ? 'border-[#b55322] text-[#b55322] font-bold' : 'border-transparent text-stone-500 font-semibold hover:text-stone-900' }} pb-3 text-xs transition">Draf / Nonaktif ({{ $totalNonaktif }})</a>
        </nav>
        <p class="text-xs font-medium text-stone-400 pb-3">{{ $totalDesigns }} desain &middot; {{ $totalAktif }} aktif di website</p>
    </div>

    <!-- Filter Bar -->
    <div class="mt-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('admin.designs') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[260px] flex-1 max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama desain kaos atau deskripsi..." class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif di Website</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif / Draf</option>
            </select>

            @if(request()->anyFilled(['q', 'status']))
                <a href="{{ route('admin.designs') }}" class="text-xs font-semibold text-stone-400 hover:text-stone-700">Reset</a>
            @endif
        </form>
    </div>

    <!-- Main Grid: Master Table on Left, Inspector on Right -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left: Designs Master Table -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Katalog Pakaian</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        {{ $designs->total() }} pakaian
                    </span>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[560px]">
                        <div class="col-span-6">Desain / Nama Pakaian</div>
                        <div class="col-span-3">Harga Donasi</div>
                        <div class="col-span-3 text-right">Status &amp; Aksi</div>
                    </div>

                    <!-- Rows -->
                    <div class="mt-1 divide-y divide-stone-100 min-w-[560px]">
                        @forelse ($designs as $d)
                            @php
                                $isSelected = $selectedDesign && $selectedDesign->id === $d->id;
                            @endphp
                            <div class="grid grid-cols-12 gap-2 items-center px-4 py-3 rounded-xl transition {{ $isSelected ? 'bg-[#faefe8] border border-[#f0dcd0]' : 'hover:bg-stone-50/80 border border-transparent' }}">
                                <!-- Desain / Thumbnail / Nama -->
                                <a href="{{ route('admin.designs', array_merge(request()->query(), ['selected_id' => $d->id])) }}" class="col-span-6 flex items-center gap-3 min-w-0 pr-2 cursor-pointer no-underline">
                                    <div class="h-12 w-12 rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] p-1 shrink-0 grid place-items-center overflow-hidden">
                                        <img src="{{ $d->gambarUrl() }}" alt="{{ $d->nama }}" class="h-full w-full object-contain">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-xs font-bold text-stone-900 leading-snug truncate">{{ $d->nama }}</p>
                                            @if ($d->video_360)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-stone-900 text-white font-mono text-[9px] font-bold px-1.5 py-0.2 shrink-0 shadow-2xs" title="Video 360° Tersedia">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                    360&deg;
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-stone-400 mt-0.5 truncate">{{ Str::limit($d->deskripsi, 38) }}</p>
                                    </div>
                                </a>

                                <!-- Harga Donasi -->
                                <div class="col-span-3">
                                    <p class="text-xs font-bold text-stone-900">Rp {{ number_format($d->harga, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-stone-400 mt-0.5">{{ $d->orders_count }} pesanan</p>
                                </div>

                                <!-- Status & Aksi -->
                                <div class="col-span-3 flex items-center justify-end gap-2">
                                    @if ($d->aktif)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 whitespace-nowrap">
                                            <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                            Draf
                                        </span>
                                    @endif

                                    <a href="{{ route('admin.designs.edit', $d->id) }}" class="rounded-lg border border-[#e8e4dc] bg-white p-1.5 text-stone-500 hover:text-stone-900 hover:bg-stone-50 transition" title="Edit Desain">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-xs text-stone-400">Belum ada desain kaos di koleksi.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $designs->count() }} dari {{ $designs->total() }} desain</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($designs->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $designs->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        <span class="grid h-7 w-7 place-items-center rounded-lg border border-[#f0dcd0] bg-[#faefe8] font-bold text-[#b55322] shadow-2xs">{{ $designs->currentPage() }}</span>

                        @if ($designs->hasMorePages())
                            <a href="{{ $designs->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
                        @else
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panduan Katalog Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-2">
                <h3 class="font-serif text-lg font-bold text-stone-900">Panduan Koleksi Kaos Pembangunan</h3>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Setiap kaos yang ditambahkan akan otomatis muncul di etalase <span class="font-semibold text-stone-800">Koleksi Kaos Pembangunan</span> pada halaman utama dan halaman <span class="font-semibold text-stone-800">Design Kaos</span>. Anda juga dapat mengunggah <span class="font-semibold text-stone-800">Video 360&deg; Playing</span> agar pengunjung website dapat memutar pakaian 360 derajat secara interaktif.
                </p>
            </div>
        </div>

        <!-- Right: Inspector Panel -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-20">
            @if ($selectedDesign)
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4 shadow-2xs">
                    <!-- Inspector Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl font-bold text-stone-900">Detail Pakaian</h2>
                        @if ($selectedDesign->aktif)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Aktif di Website
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-xs font-semibold text-stone-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                Nonaktif / Draf
                            </span>
                        @endif
                    </div>

                    @if ($selectedDesign->video_360 && $selectedDesign->video360Url())
                        <!-- Media Tabs Switcher -->
                        <div class="flex items-center gap-1.5 rounded-xl bg-[#f5f2ec] p-1 border border-[#e8e4dc]">
                            <button type="button" id="insp-btn-photo" onclick="showInspectorMedia('photo')" class="flex-1 rounded-lg py-1 px-2.5 text-xs font-bold bg-white text-stone-900 shadow-2xs cursor-pointer">
                                📷 Foto Mockup
                            </button>
                            <button type="button" id="insp-btn-video" onclick="showInspectorMedia('video')" class="flex-1 rounded-lg py-1 px-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>360&deg; Video</span>
                            </button>
                        </div>
                    @endif

                    <!-- Image Showcase Box -->
                    <div class="relative overflow-hidden rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] aspect-square flex items-center justify-center">
                        <div id="insp-media-photo" class="h-full w-full flex items-center justify-center p-6">
                            <img src="{{ $selectedDesign->gambarUrl() }}" alt="{{ $selectedDesign->nama }}" class="max-h-full max-w-full object-contain drop-shadow-md">
                        </div>
                        @if ($selectedDesign->video_360 && $selectedDesign->video360Url())
                            <div id="insp-media-video" class="hidden h-full w-full flex items-center justify-center bg-stone-950/5 relative overflow-hidden">
                                <video id="insp-video-player" src="{{ $selectedDesign->video360Url() }}" playsinline webkit-playsinline muted loop autoplay controls class="h-full w-full object-contain p-2"></video>
                                <span class="absolute top-3 left-3 rounded-full bg-stone-900/90 text-white font-mono text-[9px] font-bold px-2.5 py-0.5 flex items-center gap-1 backdrop-blur-xs z-10 shadow-2xs">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    360&deg; Video Playing
                                </span>
                            </div>
                        @endif
                        <span class="absolute top-3 right-3 rounded-full bg-white/90 backdrop-blur-xs border border-[#e8e4dc] px-2.5 py-0.5 text-[10px] font-bold text-stone-600 shadow-2xs z-10">
                            ID: #{{ $selectedDesign->id }}
                        </span>
                    </div>

                    <!-- Metadata Details -->
                    <div class="space-y-3 pt-1">
                        <div>
                            <p class="text-sm font-bold text-stone-900">{{ $selectedDesign->nama }}</p>
                            <p class="font-serif text-lg font-bold text-[#b55322] mt-0.5">Rp {{ number_format($selectedDesign->harga, 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-xl bg-[#f5f2ec] p-3 text-xs text-stone-600 leading-relaxed">
                            <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Filosofi &amp; Deskripsi:</p>
                            {{ $selectedDesign->deskripsi }}
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div class="rounded-xl border border-[#e8e4dc] p-2.5 text-center">
                                <span class="text-stone-400 block text-[10px] uppercase font-bold">Total Pesanan</span>
                                <span class="text-sm font-bold text-stone-900">{{ $selectedDesign->orders_count }} pcs</span>
                            </div>
                            <div class="rounded-xl border border-[#e8e4dc] p-2.5 text-center">
                                <span class="text-stone-400 block text-[10px] uppercase font-bold">Fitur 360&deg;</span>
                                <span class="text-sm font-bold {{ $selectedDesign->video_360 ? 'text-emerald-700' : 'text-stone-400' }}">
                                    {{ $selectedDesign->video_360 ? 'Video 360° Aktif' : 'Belum Ada Video' }}
                                </span>
                            </div>
                        </div>

                        {{-- Pilihan Ukuran Stok --}}
                        <div class="rounded-xl border border-[#e8e4dc] p-3 text-xs bg-[#fbf9f6]">
                            <span class="text-stone-400 block text-[10px] uppercase font-bold tracking-wider mb-1.5">Pilihan Ukuran Stok:</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach (\App\Models\Design::ALL_UKURAN as $s)
                                    @php $isReady = $selectedDesign->isUkuranTersedia($s); @endphp
                                    <span class="grid h-6 w-6 place-items-center rounded-md border text-[10px] font-bold {{ $isReady ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-stone-200 bg-stone-100 text-stone-300 line-through opacity-50' }}" title="{{ $s }} — {{ $isReady ? 'Tersedia' : 'Habis' }}">
                                        {{ $s }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-2 space-y-2">
                            <a href="{{ route('admin.designs.edit', $selectedDesign->id) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-2.5 text-xs font-bold text-white shadow-2xs transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span>Edit Desain &amp; Upload Video</span>
                            </a>

                            <div class="grid grid-cols-2 gap-2">
                                <form method="POST" action="{{ route('admin.designs.toggle', $selectedDesign->id) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-white py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs transition cursor-pointer">
                                        <span>{{ $selectedDesign->aktif ? 'Sembunyikan' : 'Aktifkan' }}</span>
                                    </button>
                                </form>

                                <a href="{{ route('design') }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-white py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs transition">
                                    <span>Lihat Live</span>
                                    <span class="text-xs">&nearr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <script>
        function showInspectorMedia(mode) {
            const photoEl = document.getElementById('insp-media-photo');
            const videoEl = document.getElementById('insp-media-video');
            const btnPhoto = document.getElementById('insp-btn-photo');
            const btnVideo = document.getElementById('insp-btn-video');
            const videoPlayer = document.getElementById('insp-video-player');

            if (!photoEl || !videoEl) return;

            if (mode === 'video') {
                photoEl.classList.add('hidden');
                videoEl.classList.remove('hidden');
                if (btnVideo) btnVideo.className = 'flex-1 rounded-lg py-1 px-2.5 text-xs font-bold bg-white text-stone-900 shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer';
                if (btnPhoto) btnPhoto.className = 'flex-1 rounded-lg py-1 px-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition cursor-pointer';
                if (videoPlayer) videoPlayer.play().catch(function(){});
            } else {
                videoEl.classList.add('hidden');
                photoEl.classList.remove('hidden');
                if (btnPhoto) btnPhoto.className = 'flex-1 rounded-lg py-1 px-2.5 text-xs font-bold bg-white text-stone-900 shadow-2xs cursor-pointer';
                if (btnVideo) btnVideo.className = 'flex-1 rounded-lg py-1 px-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer';
                if (videoPlayer) videoPlayer.pause();
            }
        }
    </script>
@endsection
