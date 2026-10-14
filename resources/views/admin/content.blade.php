@extends('layouts.admin')

@section('title', 'Pengelolaan Konten')
@section('breadcrumb', 'Pengelolaan Konten')

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
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Pengelolaan Konten</h1>
            <p class="mt-1 text-sm text-stone-500">Rawat dokumentasi pembangunan, proposal, serta informasi program dan kontak.</p>
        </div>
        <a href="{{ route('admin.content.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2.5 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah progres</span>
        </a>
    </div>

    <!-- Navigation Tabs & Count Row -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8e4dc]">
        <nav class="flex gap-8 -mb-px">
            <a href="{{ route('admin.content') }}" class="border-b-2 border-[#b55322] pb-3 text-xs font-bold text-[#b55322]">Progres pembangunan</a>
            <a href="#proposal-section" class="border-b-2 border-transparent pb-3 text-xs font-semibold text-stone-500 hover:text-stone-900 transition">Proposal PDF</a>
            <a href="#program-section" class="border-b-2 border-transparent pb-3 text-xs font-semibold text-stone-500 hover:text-stone-900 transition">Program &amp; kontak</a>
        </nav>
        <p class="text-xs font-medium text-stone-400 pb-3">{{ $progresses->total() }} entri &middot; {{ $totalTerbit }} terbit &middot; {{ $totalDraf }} draf</p>
    </div>

    <!-- Filter Bar Row -->
    <div class="mt-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('admin.content') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[260px] flex-1 max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul dokumentasi" class="w-full rounded-xl border border-[#e8e4dc] bg-white pl-10 pr-3.5 py-2.5 text-xs text-stone-900 placeholder:text-stone-400 focus:border-[#b55322] focus:outline-none">
            </div>

            <select name="tipe" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                <option value="">Foto &amp; video</option>
                <option value="foto" @selected(request('tipe') === 'foto')>Foto</option>
                <option value="video" @selected(request('tipe') === 'video')>Video</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                <option value="">Semua status</option>
                <option value="terbit" @selected(request('status') === 'terbit')>Terbit</option>
                <option value="draf" @selected(request('status') === 'draf')>Draf</option>
            </select>

            @if(request()->anyFilled(['q', 'tipe', 'status']))
                <a href="{{ route('admin.content') }}" class="text-xs font-semibold text-stone-400 hover:text-stone-700">Reset</a>
            @endif
        </form>
    </div>

    <!-- Main Master-Detail Split Grid -->
    <div class="mt-6 grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left Column: Progres Pembangunan Master Table & Guidance Cards -->
        <div class="space-y-6 lg:col-span-7 xl:col-span-8">
            
            <!-- Progres Table Card -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Progres pembangunan</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        {{ $totalFoto }} foto &middot; {{ $totalVideo }} video
                    </span>
                </div>

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <!-- Table Header Bar -->
                    <div class="grid grid-cols-12 gap-2 rounded-xl bg-[#f4efe8] px-4 py-2.5 text-[11px] font-semibold text-stone-600 min-w-[580px]">
                        <div class="col-span-6">Media / judul / tahap</div>
                        <div class="col-span-2">Tanggal</div>
                        <div class="col-span-2">Publikasi</div>
                        <div class="col-span-2 text-right pr-1">Aksi</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="mt-1 divide-y divide-stone-100 min-w-[580px]">
                        @forelse ($progresses as $p)
                            @php
                                $isSelected = $selectedProgress && $selectedProgress->id === $p->id;
                            @endphp
                            <div class="grid grid-cols-12 gap-2 items-center px-4 py-3 rounded-xl transition {{ $isSelected ? 'bg-[#faefe8] border border-[#f0dcd0]' : 'hover:bg-stone-50/80 border border-transparent' }}">
                                
                                <!-- Media / Judul / Tahap (Klik untuk pratinjau) -->
                                <a href="{{ route('admin.content', array_merge(request()->query(), ['selected_id' => $p->id])) }}" class="col-span-6 flex items-center gap-3 min-w-0 pr-2 cursor-pointer no-underline text-inherit">
                                    @if ($p->tipe === 'foto' && $p->mediaUrl())
                                        <img src="{{ $p->mediaUrl() }}" alt="{{ $p->teks_alt ?: $p->judul }}" class="h-11 w-15 rounded-lg border border-[#e8e4dc] object-cover shrink-0 bg-[#f5f2ec]">
                                    @elseif ($p->thumbnailUrl())
                                        <div class="relative h-11 w-15 shrink-0 rounded-lg overflow-hidden border border-[#e8e4dc]">
                                            <img src="{{ $p->thumbnailUrl() }}" alt="{{ $p->judul }}" class="h-full w-full object-cover">
                                            <span class="absolute inset-0 grid place-items-center bg-black/30 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                                    <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    @else
                                        <div class="h-11 w-15 shrink-0 grid place-items-center rounded-lg bg-[#243342] text-white/80">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-stone-900 leading-snug truncate">{{ $p->judul }}</p>
                                        <p class="text-[11px] text-stone-400 mt-0.5 capitalize flex items-center gap-1.5 flex-wrap">
                                            @if($p->tipe === 'video')
                                                <span class="inline-flex items-center gap-1 rounded bg-[#faefe8] px-1.5 py-0.2 text-[10px] font-bold text-[#b55322]">
                                                    {{ $p->videoPlatformLabel() }}
                                                </span>
                                            @else
                                                <span>Foto</span>
                                            @endif
                                            &middot; <span>{{ $p->tahap ?: 'Perencanaan' }}</span>
                                        </p>
                                    </div>
                                </a>

                                <!-- Tanggal -->
                                <a href="{{ route('admin.content', array_merge(request()->query(), ['selected_id' => $p->id])) }}" class="col-span-2 text-xs text-stone-600 no-underline">
                                    {{ $p->tanggal ? str_replace(['Agt', 'Aug'], 'Agu', $p->tanggal->translatedFormat('d M Y')) : '—' }}
                                </a>

                                <!-- Publikasi Pill Badge -->
                                <a href="{{ route('admin.content', array_merge(request()->query(), ['selected_id' => $p->id])) }}" class="col-span-2 no-underline">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 whitespace-nowrap">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $p->status === 'terbit' ? 'bg-stone-500' : 'bg-stone-400' }}"></span>
                                        {{ $p->status === 'terbit' ? 'Terbit' : 'Draf' }}
                                    </span>
                                </a>

                                <!-- Aksi (Sunting & Hapus) -->
                                <div class="col-span-2 flex items-center justify-end gap-1.5">
                                    <!-- Sunting Link -->
                                    <a href="{{ route('admin.content.edit', $p->id) }}" title="Sunting Progres" class="grid h-8 w-8 place-items-center rounded-lg border border-[#e8e4dc] bg-white text-stone-600 hover:border-[#b55322] hover:text-[#b55322] transition shadow-2xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>

                                    <!-- Hapus Form -->
                                    <form method="POST" action="{{ route('admin.content.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus progres &quot;{{ addslashes($p->judul) }}&quot;? Berkas video/foto yang tersimpan juga akan dihapus permanen.');" class="inline-block m-0 p-0">
                                        @csrf
                                        <button type="submit" title="Hapus Progres" class="grid h-8 w-8 place-items-center rounded-lg border border-rose-200/90 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 transition shadow-2xs cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-xs text-stone-400">Tidak ada entri yang cocok dengan pencarian.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                    <p class="text-xs text-stone-400">Menampilkan {{ $progresses->count() }} dari {{ $progresses->total() }} entri</p>
                    <div class="flex items-center gap-1 text-xs">
                        @if ($progresses->onFirstPage())
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&lsaquo;</span>
                        @else
                            <a href="{{ $progresses->previousPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&lsaquo;</a>
                        @endif

                        <span class="grid h-7 w-7 place-items-center rounded-lg border border-[#f0dcd0] bg-[#faefe8] font-bold text-[#b55322] shadow-2xs">{{ $progresses->currentPage() }}</span>

                        @if ($progresses->hasMorePages())
                            <a href="{{ $progresses->nextPageUrl() }}" class="grid h-7 w-7 place-items-center rounded-lg text-stone-600 hover:bg-stone-100">&rsaquo;</a>
                        @else
                            <span class="grid h-7 w-7 place-items-center rounded-lg text-stone-300 cursor-not-allowed">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Callout Note -->
            <div class="flex items-start gap-2.5 rounded-2xl bg-[#f4efe8] p-4 text-xs text-stone-600 leading-relaxed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 text-stone-500 shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p>Media menggunakan referensi website. Status publikasi pada konsep admin ini adalah data contoh; website referensi tidak diubah.</p>
            </div>

            <!-- Card: Sebelum Menerbitkan -->
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
                <h3 class="font-serif text-lg font-bold text-stone-900">Sebelum menerbitkan</h3>
                <p class="mt-2 text-xs leading-relaxed text-stone-500">Pastikan tanggal dan tahap sesuai dokumentasi. Hindari menampilkan data pribadi donatur atau bukti pembayaran pada website publik.</p>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        Foto: JPG / PNG
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4efe8] px-3 py-1 text-xs font-semibold text-stone-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        Video: MP4 / tautan
                    </span>
                </div>
            </div>

        </div>

        <!-- Right Column: Inspector Panel "Sunting entri" -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-20">
            @if ($selectedProgress)
                <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 space-y-4">
                    <!-- Inspector Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl font-bold text-stone-900">Sunting entri</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-xs font-semibold text-stone-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                            {{ $selectedProgress->status === 'terbit' ? 'Terbit' : 'Draf' }}
                        </span>
                    </div>

                    <!-- Media Preview Box -->
                    <div class="space-y-2 pt-1">
                        <div class="overflow-hidden rounded-xl border border-[#e8e4dc] bg-[#f5f2ec] aspect-16/9 flex items-center justify-center">
                            @if ($selectedProgress->tipe === 'foto' && $selectedProgress->mediaUrl())
                                <img src="{{ $selectedProgress->mediaUrl() }}" alt="{{ $selectedProgress->teks_alt ?: $selectedProgress->judul }}" class="h-full w-full object-cover">
                            @elseif ($selectedProgress->embedUrl())
                                @if ($selectedProgress->videoPlatform() === 'youtube')
                                    <div class="relative w-full h-full overflow-hidden bg-black flex items-center justify-center">
                                        <iframe src="{{ $selectedProgress->embedUrl() }}" style="position:absolute; left:0; width:100%; height:calc(100% + 140px); margin-top:-65px; border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                @elseif ($selectedProgress->videoPlatform() === 'instagram')
                                    <div class="relative w-full h-full overflow-hidden bg-black flex items-center justify-center">
                                        <iframe src="{{ $selectedProgress->embedUrl() }}" style="position:absolute; left:0; width:100%; height:calc(100% + 180px); margin-top:-65px; border:0;" allowfullscreen></iframe>
                                    </div>
                                @elseif ($selectedProgress->videoPlatform() === 'tiktok')
                                    <div class="relative w-full h-full overflow-hidden bg-black flex items-center justify-center">
                                        <iframe src="{{ $selectedProgress->embedUrl() }}" style="position:absolute; left:0; width:100%; height:calc(100% + 95px); margin-top:-48px; border:0;" allowfullscreen></iframe>
                                    </div>
                                @elseif ($selectedProgress->videoPlatform() === 'gdrive')
                                    <iframe src="{{ $selectedProgress->embedUrl() }}" class="h-full w-full border-0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                @else
                                    <iframe src="{{ $selectedProgress->embedUrl() }}" class="h-full w-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @endif
                            @elseif ($selectedProgress->mediaUrl())
                                <video src="{{ $selectedProgress->mediaUrl() }}" controls class="h-full w-full object-cover"></video>
                            @else
                                <div class="grid place-items-center text-stone-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-stone-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                    <span class="text-xs font-medium text-stone-500 mt-1">Pratinjau Media</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <span class="text-stone-500 font-medium">
                                @if ($selectedProgress->tipe === 'video')
                                    <span class="text-[#b55322] font-semibold">{{ $selectedProgress->videoPlatformLabel() }}</span> &middot; {{ $selectedProgress->video_url ? 'tautan video' : 'berkas mp4' }}
                                @else
                                    Foto &middot; berkas gambar
                                @endif
                            </span>
                            <a href="{{ route('admin.content.edit', $selectedProgress->id) }}" class="font-semibold text-[#b55322] hover:text-[#9d4419] cursor-pointer inline-flex items-center gap-1">
                                <span>Sunting lengkap</span>
                                <span class="text-xs">&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form method="POST" action="{{ route('admin.content.update', $selectedProgress->id) }}" enctype="multipart/form-data" class="space-y-3.5 pt-1">
                        @csrf
                        <input type="file" id="media-file-input" name="media" class="hidden" onchange="this.form.submit()">

                        <!-- Judul Progres -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Judul progres</label>
                            <input type="text" name="judul" value="{{ old('judul', $selectedProgress->judul) }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                        </div>

                        <!-- Tanggal & Tahap (2 cols) -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-stone-700 mb-1">Tanggal dokumentasi</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', $selectedProgress->tanggal ? $selectedProgress->tanggal->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-stone-700 mb-1">Tahap pembangunan</label>
                                <select name="tahap" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                                    <option value="Fondasi" @selected(old('tahap', $selectedProgress->tahap) === 'Fondasi')>Fondasi</option>
                                    <option value="Lantai dasar" @selected(old('tahap', $selectedProgress->tahap) === 'Lantai dasar')>Lantai dasar</option>
                                    <option value="Persiapan" @selected(old('tahap', $selectedProgress->tahap) === 'Persiapan')>Persiapan</option>
                                    <option value="Perencanaan" @selected(old('tahap', $selectedProgress->tahap) === 'Perencanaan')>Perencanaan</option>
                                    <option value="Dinding & atap" @selected(old('tahap', $selectedProgress->tahap) === 'Dinding & atap')>Dinding &amp; atap</option>
                                    <option value="Finishing" @selected(old('tahap', $selectedProgress->tahap) === 'Finishing')>Finishing</option>
                                </select>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Keterangan</label>
                            <textarea name="deskripsi" rows="3" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none leading-relaxed" required>{{ old('deskripsi', $selectedProgress->deskripsi) }}</textarea>
                        </div>

                        <!-- Tautan Video (YouTube, Instagram, TikTok) -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Tautan Video (YouTube / Instagram / TikTok)</label>
                            <input type="url" name="video_url" value="{{ old('video_url', $selectedProgress->video_url) }}" placeholder="https://www.youtube.com/... atau tiktok / ig" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none">
                            <p class="mt-1 text-[10px] text-stone-400">Isi tautan video bila ingin menampilkan video dari medsos.</p>
                        </div>

                        <!-- Teks Alternatif Foto -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Teks alternatif foto</label>
                            <input type="text" name="teks_alt" value="{{ old('teks_alt', $selectedProgress->teks_alt) }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none">
                        </div>

                        <!-- Status Publikasi -->
                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Status publikasi</label>
                            <select name="status" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-700 focus:border-[#b55322] focus:outline-none">
                                <option value="terbit" @selected(old('status', $selectedProgress->status) === 'terbit')>Terbit &mdash; terlihat di website</option>
                                <option value="draf" @selected(old('status', $selectedProgress->status) === 'draf')>Draf &mdash; tersimpan di admin</option>
                            </select>
                        </div>

                        <!-- Footer Meta -->
                        <div class="pt-2 text-[11px] text-stone-400 space-y-0.5">
                            <p>Terakhir disunting: 12 Okt 2026, 07:45 WITA</p>
                            <p>Oleh Super Admin &middot; Data contoh</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 grid grid-cols-2 gap-3">
                            <a href="{{ route('progres') }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#e8e4dc] bg-white py-2.5 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>Pratinjau</span>
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                Simpan perubahan
                            </button>
                        </div>
                    </form>

                    <div class="pt-2 text-right">
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus progres &quot;{{ addslashes($selectedProgress->judul) }}&quot;? Berkas video/foto terkait juga akan dihapus.')) document.getElementById('panel-delete-form-{{ $selectedProgress->id }}').submit();" class="text-xs text-rose-500 hover:text-rose-700 hover:underline font-semibold cursor-pointer inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Hapus entri progres ini</span>
                        </button>
                        <form id="panel-delete-form-{{ $selectedProgress->id }}" method="POST" action="{{ route('admin.content.destroy', $selectedProgress->id) }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- Bottom Row: Proposal PDF (left) & Informasi Program & Kontak (right) -->
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        
        <!-- Proposal PDF Card -->
        <section id="proposal-section" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-stone-900">Proposal PDF</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2eee7] px-2.5 py-0.5 text-xs font-semibold text-stone-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                        Aktif
                    </span>
                </div>

                <!-- PDF File Box matching mockup -->
                <div class="mt-4 flex items-center gap-3.5 rounded-xl bg-[#f5f2ec] border border-[#ede8df] p-4">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#faefe8] text-[#b55322]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-stone-900">Proposal Pembangunan &mdash; Jemaat Bali</p>
                        <p class="text-xs text-stone-400 mt-0.5">proposal-pembangunan-2026.pdf</p>
                        <p class="text-[11px] text-stone-400 mt-0.5">PDF &middot; 2 halaman &middot; dokumen contoh</p>
                    </div>
                </div>

                <!-- Metadata Key-Value List -->
                <div class="mt-4 space-y-2 border-t border-stone-100 pt-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-stone-400">Versi aktif</span>
                        <span class="font-semibold text-stone-800">2026 - v1.0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-stone-400">Diperbarui</span>
                        <span class="font-semibold text-stone-800">18 Juli 2026</span>
                    </div>
                </div>

                <!-- Judul di website -->
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Judul di website</label>
                    <input type="text" form="info-form" name="judul_proposal" value="{{ old('judul_proposal', $programInfo->judul_proposal ?? 'Proposal pembangunan') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none">
                </div>

                <p class="mt-3 text-[11px] text-stone-400 leading-relaxed">
                    Unggah PDF baru untuk mengganti versi aktif. Periksa isi dan persetujuan panitia sebelum dipublikasikan.
                </p>
            </div>

            <!-- PDF Action Buttons -->
            <div class="mt-5 flex flex-wrap items-center gap-2.5 pt-2">
                <a href="{{ route('proposal') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    <span>Lihat PDF</span>
                </a>
                <form method="POST" action="{{ route('admin.content.proposal') }}" enctype="multipart/form-data" class="inline">
                    @csrf
                    <input type="file" id="pdf-upload-input" name="proposal" accept=".pdf,application/pdf" class="hidden" onchange="this.form.submit()">
                    <label for="pdf-upload-input" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Ganti PDF</span>
                    </label>
                </form>
            </div>
        </section>

        <!-- Informasi Program & Kontak Card -->
        <section id="program-section" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6">
            <h2 class="font-serif text-lg font-bold text-stone-900">Informasi program &amp; kontak</h2>

            <form id="info-form" method="POST" action="{{ route('admin.content.info') }}" class="mt-4 space-y-3.5">
                @csrf
                <input type="hidden" id="info-form-judul-proposal" name="judul_proposal" value="{{ old('judul_proposal', $programInfo->judul_proposal ?? 'Proposal pembangunan') }}">
                <div>
                    <label for="nama_program" class="block text-xs font-semibold text-stone-700 mb-1">Nama program</label>
                    <input type="text" id="nama_program" name="nama_program" value="{{ old('nama_program', $programInfo->nama_program ?? 'Pembangunan Jemaat Bali') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-stone-700 mb-1">Deskripsi program</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none leading-relaxed" required>{{ old('deskripsi', $programInfo->deskripsi ?? 'Penggalangan dana pembangunan bersama. Transparan: setiap progres kami unggah di sini.') }}</textarea>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="whatsapp" class="block text-xs font-semibold text-stone-700 mb-1">WhatsApp panitia</label>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $programInfo->whatsapp ?? '0812-0000-0000') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-stone-700 mb-1">Email panitia</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $programInfo->email ?? 'admin@jemaatbali.or.id') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 rounded-xl bg-[#f5f2ec] border border-[#ede8df] p-3 text-xs text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500 shrink-0 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="leading-relaxed">Kontak contoh dari referensi, belum terverifikasi. Atur kontak resmi sebelum digunakan.</p>
                </div>

                <button type="submit" class="w-full rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-3 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center cursor-pointer">
                    Simpan informasi
                </button>
            </form>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('info-form');
            var proposalInput = document.querySelector('input[name="judul_proposal"][form="info-form"]');
            var hiddenInput = document.getElementById('info-form-judul-proposal');

            if (form && proposalInput && hiddenInput) {
                form.addEventListener('submit', function () {
                    hiddenInput.value = proposalInput.value;
                });
                proposalInput.addEventListener('input', function () {
                    hiddenInput.value = proposalInput.value;
                });
            }
        });
    </script>
@endsection
