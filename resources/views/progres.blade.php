 @extends('layouts.app')

@section('title', 'Progres Pembangunan — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Dokumentasi lapangan dalam bentuk foto dan video. Diperbarui setiap ada perkembangan, agar setiap donatur dapat melihat langsung bagaimana pembangunan berjalan.')

@section('content')
    @push('preload')
        <link rel="preload" as="image" type="image/webp" fetchpriority="high" sizes="100vw"
            href="{{ asset('images/hero-poster.webp') }}"
            imagesrcset="{{ asset('images/hero-poster-768.webp') }} 768w, {{ asset('images/hero-poster.webp') }} 1376w">
    @endpush

    {{-- ================= HERO ================= --}}
    <section class="relative overflow-hidden bg-stone-900">
        <picture>
            <source srcset="{{ asset('images/hero-poster-768.webp') }} 768w, {{ asset('images/hero-poster.webp') }} 1376w"
                type="image/webp" sizes="100vw">
            <img src="{{ asset('images/hero-poster.jpg') }}" alt="" aria-hidden="true" width="1376" height="768"
                class="absolute inset-0 h-full w-full object-cover opacity-40" fetchpriority="high" decoding="async">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-br from-stone-950/85 via-stone-900/70 to-stone-950/90"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-20">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-[0.25em] text-white/70">Transparansi</span>
                <span class="h-px w-14 bg-white/40"></span>
            </div>

            <h1 class="mt-4 max-w-2xl font-display text-4xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-5xl lg:text-6xl">
                Progres Pembangunan
            </h1>

            <p class="mt-5 max-w-xl text-sm leading-relaxed text-white/75 sm:text-base">
                Dokumentasi lapangan dalam bentuk foto dan video. Diperbarui setiap ada perkembangan,
                agar setiap donatur dapat melihat langsung bagaimana pembangunan berjalan.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/5 px-4 py-3 backdrop-blur-xs">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                    </span>
                    <span class="text-sm font-bold uppercase tracking-wide text-white">{{ $total }} <span class="font-medium text-white/60">Entri</span></span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/5 px-4 py-3 backdrop-blur-xs">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </span>
                    <span class="text-sm font-bold uppercase tracking-wide text-white">{{ $fotoCount }} <span class="font-medium text-white/60">Foto</span></span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/5 px-4 py-3 backdrop-blur-xs">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </span>
                    <span class="text-sm font-bold uppercase tracking-wide text-white">{{ $videoCount }} <span class="font-medium text-white/60">Video</span></span>
                </div>
            </div>

            <p class="pointer-events-none mt-10 max-w-xs text-right font-serif text-3xl italic leading-tight text-white/50 sm:absolute sm:right-6 sm:top-1/2 sm:mt-0 sm:max-w-none sm:-translate-y-1/2 sm:text-4xl lg:text-5xl">
                Bersama Membangun Rumah Tuhan
            </p>
        </div>
    </section>

    @if ($total === 0)
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="card p-10 text-center text-sm text-muted">Belum ada progres yang diunggah.</div>
        </section>
    @else
        {{-- ================= GALERI ================= --}}
        <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-16">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.25em] text-muted">Galeri</span>
                    <h2 class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Dokumentasi Terbaru</h2>
                </div>

                <div class="flex flex-wrap gap-2" id="gal-filter">
                    @php
                        $filters = [
                            ['semua', 'Semua'],
                            ['pembangunan', 'Pembangunan'],
                            ['kegiatan', 'Kegiatan'],
                            ['video', 'Video'],
                        ];
                    @endphp
                    @foreach ($filters as [$key, $label])
                        <button type="button" data-filter="{{ $key }}"
                            class="gal-pill rounded-full border px-4 py-2 text-sm font-semibold transition {{ $key === 'semua' ? 'border-transparent bg-[#414b36] text-white shadow-xs' : 'border-line bg-white text-ink hover:border-stone-400' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2" id="gal-grid">
                @foreach ($entries as $entry)
                    @php
                        $isVideo = $entry->tipe === 'video';
                        $thumb = $isVideo ? $entry->thumbnailUrl() : $entry->mediaUrl();
                        $kategori = $entry->kategori ?? 'pembangunan';
                    @endphp
                    <article class="gal-card group overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_2px_rgb(0_0_0/0.05)] transition hover:shadow-lg"
                        data-kategori="{{ $kategori }}" data-tipe="{{ $entry->tipe }}">
                        <div class="relative aspect-[16/10] w-full overflow-hidden bg-stone-100"
                            @if ($isVideo) data-embed="{{ $entry->embedUrl() }}" data-file="{{ $entry->mediaUrl() }}" data-platform="{{ $entry->videoPlatform() }}" @endif>
                            @if ($thumb)
                                <img src="{{ $thumb }}" alt="{{ $entry->teks_alt ?: $entry->judul }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                            @elseif ($isVideo && $entry->mediaUrl())
                                <video src="{{ $entry->mediaUrl() }}#t=0.1" preload="metadata" class="absolute inset-0 h-full w-full object-cover pointer-events-none"></video>
                            @else
                                <div class="grid h-full w-full place-items-center text-sm text-muted">Tidak ada media</div>
                            @endif

                            <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-stone-900/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-xs">
                                @if ($isVideo)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" /></svg>
                                    Video
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                    Foto
                                @endif
                            </span>

                            @if ($isVideo)
                                <button type="button" class="video-poster group/play absolute inset-0 z-10 grid w-full cursor-pointer place-items-center" aria-label="Putar video {{ $entry->judul }}">
                                    <span class="grid h-14 w-14 place-items-center rounded-full bg-white text-stone-900 shadow-xl transition-all duration-200 group-hover/play:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-1 h-6 w-6"><path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </button>
                            @endif
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-3">
                                <time class="text-[11px] font-bold uppercase tracking-widest text-muted" datetime="{{ $entry->tanggal->toDateString() }}">
                                    {{ $entry->tanggal->translatedFormat('d F Y') }}
                                </time>
                                <span class="h-px flex-1 bg-line"></span>
                            </div>
                            <div class="mt-2.5 flex items-start justify-between gap-4">
                                <h3 class="font-display text-lg font-bold leading-snug text-ink">{{ $entry->judul }}</h3>
                                <span class="mt-1 grid h-9 w-9 shrink-0 place-items-center rounded-full border border-line text-stone-500 transition group-hover:border-stone-900 group-hover:bg-stone-900 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                </span>
                            </div>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $entry->deskripsi }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <p id="gal-empty" class="hidden card mt-8 p-10 text-center text-sm text-muted">Tidak ada dokumentasi pada kategori ini.</p>
        </section>

        {{-- ================= CTA ================= --}}
        <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
            <div class="flex flex-col items-start gap-8 rounded-3xl bg-[#414b36] p-8 sm:flex-row sm:items-center sm:gap-10 sm:p-10">
                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white/10 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </span>
                <div class="flex-1">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/60">Dukung Pembangunan</span>
                    <h3 class="mt-2 font-display text-2xl font-bold text-white sm:text-3xl">Mari Jadi Bagian dari Cerita Ini</h3>
                    <p class="mt-2 max-w-lg text-sm leading-relaxed text-white/70">
                        Setiap doa, dukungan, dan donasi Anda sangat berarti untuk mewujudkan rumah Tuhan di Bali.
                    </p>
                </div>
                <a href="{{ route('shop') }}" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-bold text-stone-900 shadow-sm transition hover:bg-stone-100">
                    Donasi Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            // Putar video
            document.querySelectorAll('.video-poster').forEach(function (poster) {
                poster.addEventListener('click', function () {
                    var box = poster.parentElement;
                    var fileUrl = box.getAttribute('data-file');
                    var embedUrl = box.getAttribute('data-embed');

                    if (fileUrl) {
                        box.innerHTML = '<video style="position:absolute; inset:0; width:100%; height:100%; object-fit:contain; background:#000;" src="' + fileUrl + '" controls autoplay playsinline></video>';
                    } else if (embedUrl) {
                        box.innerHTML = '<iframe style="position:absolute; inset:0; width:100%; height:100%; border:0;" src="' + embedUrl + '" title="Video progres" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                    }
                });
            });

            // Filter galeri
            var pills = document.querySelectorAll('.gal-pill');
            var cards = document.querySelectorAll('.gal-card');
            var empty = document.getElementById('gal-empty');

            pills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    var key = pill.getAttribute('data-filter');

                    pills.forEach(function (p) {
                        var active = p === pill;
                        p.className = 'gal-pill rounded-full border px-4 py-2 text-sm font-semibold transition ' +
                            (active ? 'border-transparent bg-[#414b36] text-white shadow-xs' : 'border-line bg-white text-ink hover:border-stone-400');
                    });

                    var visible = 0;
                    cards.forEach(function (card) {
                        var show = key === 'semua'
                            || (key === 'video' && card.getAttribute('data-tipe') === 'video')
                            || card.getAttribute('data-kategori') === key;
                        card.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });
                    if (empty) empty.classList.toggle('hidden', visible !== 0);
                });
            });
        })();
    </script>
@endpush
