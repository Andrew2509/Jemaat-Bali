 @extends('layouts.app')

@section('title', 'Progres Pembangunan — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Dokumentasi lapangan dalam bentuk foto dan video. Diperbarui setiap ada perkembangan, agar setiap donatur dapat melihat langsung bagaimana sumbangannya dipakai.')

@section('content')
    <section class="border-b border-line bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <p class="text-sm font-bold uppercase tracking-wider text-primary">Transparansi</p>
            <h1 class="mt-2 font-display text-3xl font-extrabold sm:text-4xl">Progres Pembangunan</h1>
            <p class="mt-3 max-w-2xl text-base leading-relaxed text-muted">
                Dokumentasi lapangan dalam bentuk foto dan video. Diperbarui setiap ada perkembangan,
                agar setiap donatur dapat melihat langsung bagaimana sumbangannya dipakai.
            </p>
            <p class="mt-4 text-sm font-semibold text-ink">
                {{ $total }} ENTRI &middot; {{ $fotos->count() }} FOTO &middot; {{ $videos->count() }} VIDEO
            </p>
        </div>
    </section>

    @if ($total === 0)
        <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="card p-8 text-center text-sm text-muted">Belum ada progres yang diunggah.</div>
        </section>
    @else
        <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <h2 class="font-display text-xl font-bold">Foto Lapangan</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($fotos as $foto)
                    <article class="card overflow-hidden !p-0">
                        <div class="relative aspect-[4/3] w-full bg-stone-100">
                            @if ($foto->mediaUrl())
                                <img src="{{ $foto->mediaUrl() }}" alt="{{ $foto->teks_alt ?: $foto->judul }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="grid h-full w-full place-items-center text-sm text-muted">Tidak ada gambar</div>
                            @endif
                            @if ($foto->tahap)
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/80 to-transparent px-4 pb-3 pt-10">
                                    <p class="text-xs font-bold uppercase tracking-widest text-white">{{ $foto->tahap }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <time class="text-xs font-semibold uppercase tracking-widest text-muted" datetime="{{ $foto->tanggal->toDateString() }}">
                                {{ $foto->tanggal->translatedFormat('d F Y') }}
                            </time>
                            <h3 class="mt-1.5 font-serif text-base font-bold leading-snug">{{ $foto->judul }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $foto->deskripsi }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @if ($videos->isNotEmpty())
            <section class="border-t border-line bg-white">
                <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                    <h2 class="font-display text-xl font-bold">Video Lapangan</h2>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        @foreach ($videos as $video)
                            <article class="card overflow-hidden !p-0">
                                <div class="relative aspect-video w-full bg-stone-900" 
                                     data-embed="{{ $video->embedUrl() }}" 
                                     data-file="{{ $video->mediaUrl() }}"
                                     data-platform="{{ $video->videoPlatform() }}">
                                    @if ($video->thumbnailUrl())
                                        <img src="{{ $video->thumbnailUrl() }}" alt="{{ $video->judul }}" class="absolute inset-0 h-full w-full object-cover">
                                        <div class="absolute inset-0 bg-stone-950/30 backdrop-blur-[0.5px]"></div>
                                    @elseif ($video->mediaUrl())
                                        <video src="{{ $video->mediaUrl() }}#t=0.1" preload="metadata" class="absolute inset-0 h-full w-full object-cover pointer-events-none"></video>
                                        <div class="absolute inset-0 bg-stone-950/20"></div>
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-stone-900 via-stone-850 to-stone-950 flex items-center justify-center">
                                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                                        </div>
                                    @endif
                                    <button type="button" class="video-poster group absolute inset-0 grid w-full cursor-pointer place-items-center z-10" aria-label="Putar video {{ $video->judul }}">
                                        <span class="grid h-14 w-14 place-items-center rounded-full bg-white text-stone-900 shadow-xl transition-all duration-200 group-hover:scale-110 group-hover:bg-[#b55322] group-hover:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-6 w-6">
                                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center gap-2">
                                        <time class="text-xs font-semibold uppercase tracking-widest text-muted" datetime="{{ $video->tanggal->toDateString() }}">
                                            {{ $video->tanggal->translatedFormat('d F Y') }}
                                        </time>
                                        <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-primary">Video</span>
                                    </div>
                                    <h3 class="mt-1.5 font-serif text-base font-bold leading-snug">{{ $video->judul }}</h3>
                                    <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $video->deskripsi }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="card flex flex-col items-start justify-between gap-5 p-6 sm:flex-row sm:items-center">
                <div>
                    <h3 class="font-serif text-lg font-bold">Ada yang ingin ditanyakan?</h3>
                    <p class="mt-1 text-sm text-muted">Hubungi panitia, atau langsung ikut mendukung program.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('proposal') }}" class="btn btn-outline">Proposal</a>
                    <a href="{{ route('design') }}" class="btn btn-outline">Pesan Kaos</a>
                    <a href="{{ route('shop') }}" class="btn btn-primary">Donasi</a>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.video-poster').forEach(function (poster) {
                poster.addEventListener('click', function () {
                    var box = poster.parentElement;
                    var fileUrl = box.getAttribute('data-file');
                    var embedUrl = box.getAttribute('data-embed');
                    var platform = box.getAttribute('data-platform');
                    
                    if (fileUrl) {
                        box.innerHTML = '<video style="position:absolute; inset:0; width:100%; height:100%; object-fit:contain; background:#000;" src="' + fileUrl + '" controls autoplay playsinline></video>';
                    } else if (embedUrl) {
                        if (platform === 'youtube') {
                            box.innerHTML = 
                                '<div style="position:relative; width:100%; height:100%; overflow:hidden; background:#000; display:flex; align-items:center; justify-content:center;">' +
                                    '<iframe style="position:absolute; left:0; width:100%; margin-top:-65px; height:calc(100% + 140px); border:0;" src="' + embedUrl + '" title="Video progres" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                                '</div>';
                        } else if (platform === 'instagram') {
                            box.innerHTML = 
                                '<div style="position:relative; width:100%; height:100%; overflow:hidden; background:#000; display:flex; align-items:center; justify-content:center;">' +
                                    '<iframe style="width:100%; height:calc(100% + 180px); margin-top:-65px; border:0;" src="' + embedUrl + '" title="Video progres" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                                '</div>';
                        } else if (platform === 'tiktok') {
                            box.innerHTML = 
                                '<div style="position:relative; width:100%; height:100%; overflow:hidden; background:#000; display:flex; align-items:center; justify-content:center;">' +
                                    '<iframe style="width:100%; height:calc(100% + 90px); margin-top:-48px; border:0;" src="' + embedUrl + '" title="Video progres" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                                '</div>';
                        } else if (platform === 'gdrive') {
                            box.innerHTML = '<iframe style="position:absolute; inset:0; width:100%; height:100%; border:0;" src="' + embedUrl + '" title="Video progres Google Drive" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                        } else {
                            box.innerHTML = '<iframe style="position:absolute; inset:0; width:100%; height:100%; border:0;" src="' + embedUrl + '" title="Video progres" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        }
                    }
                });
            });
        })();
    </script>
@endpush
