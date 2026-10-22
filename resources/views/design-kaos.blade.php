 @extends('layouts.app')

@section('title', 'Design Kaos — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Katalog desain kaos yang mendukung program ' . ($programInfo->nama_program ?? 'Jemaat Bali') . '. Putar kaos 360° dengan geser kursor atau jari.')

@section('content')
    @php
        $ukuran = $ukuran ?? ['S', 'M', 'L', 'XL', 'XXL'];
        $terkait = $designs->where('id', '!=', $selected?->id)->values();
    @endphp

    @if ($designs->isEmpty() || !$selected)
        <section class="mx-auto max-w-3xl px-4 py-24 sm:px-6">
            <div class="card p-8 text-center text-sm text-muted">
                Belum ada desain yang dipublikasikan. Tambahkan lewat seeder: <code>php artisan migrate --seed</code>
            </div>
        </section>
    @else
        <section class="border-b border-line bg-white">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:py-12">

                {{-- ================= KIRI: GALERI PRODUK ================= --}}
                <div class="lg:col-span-7">
                    <div class="flex gap-3 sm:gap-4">
                        {{-- Strip Thumbnail Vertikal --}}
                        <div id="gal-thumbs" class="flex w-16 shrink-0 flex-col gap-2 sm:w-20">
                            <button type="button" data-gal="foto" class="gal-thumb relative aspect-square w-full shrink-0 overflow-hidden rounded-xl border-2 border-primary bg-stone-100 opacity-100 transition hover:opacity-100 cursor-pointer">
                                <img src="{{ $selected->gambarUrl() }}" alt="Foto {{ $selected->nama }}" class="h-full w-full object-cover" draggable="false">
                            </button>
                            @if ($selected->video360Url())
                                <button type="button" data-gal="360" class="gal-thumb relative aspect-square w-full shrink-0 overflow-hidden rounded-xl border-2 border-line bg-stone-100 opacity-70 transition hover:opacity-100 cursor-pointer">
                                    <img src="{{ $selected->isVideo360Gif() ? $selected->video360Url() : $selected->gambarUrl() }}" alt="Video 360 {{ $selected->nama }}" class="h-full w-full object-cover" draggable="false">
                                    <span class="absolute inset-0 grid place-items-center bg-stone-900/35">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-white/95 shadow-2xs">
                                            <svg class="h-3.5 w-3.5 translate-x-px text-stone-800" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                            @endif
                        </div>

                        {{-- Media Utama --}}
                        <div class="relative flex-1 overflow-hidden rounded-2xl border border-line bg-stone-100 aspect-square">
                            {{-- Panel Foto --}}
                            <div id="gal-panel-foto" class="absolute inset-0 flex items-center justify-center p-6 sm:p-10">
                                <img src="{{ $selected->gambarUrl() }}" alt="{{ $selected->nama }}" draggable="false" decoding="async"
                                    class="max-h-full max-w-full object-contain drop-shadow-md transition duration-300 hover:scale-105">
                            </div>

                            {{-- Panel 360° --}}
                            @if ($selected->video360Url())
                                <div id="gal-panel-360" class="absolute inset-0 hidden">
                                    @if ($selected->isVideo360Gif())
                                        <div id="viewer-360" class="absolute inset-0 flex select-none items-center justify-center">
                                            <img id="viewer-360-gif" src="{{ $selected->video360Url() }}" alt="GIF 360 {{ $selected->nama }}" draggable="false"
                                                class="max-h-full max-w-full object-contain p-6 sm:p-10">
                                        </div>
                                    @else
                                        <div id="viewer-360" class="absolute inset-0 cursor-grab select-none active:cursor-grabbing">
                                            <video id="viewer-360-video" src="{{ $selected->video360Url() }}" playsinline webkit-playsinline muted loop preload="metadata"
                                                class="h-full w-full object-contain p-6 will-change-transform sm:p-10"></video>
                                            <span id="viewer-360-counter" class="absolute bottom-3 right-3 rounded-full bg-stone-900/80 px-2.5 py-1 font-mono text-[11px] font-bold text-white backdrop-blur-xs">0&deg;</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <span id="gal-counter" class="absolute right-3 top-3 z-10 rounded-full bg-stone-900/70 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur-xs">
                                1/{{ $selected->video360Url() ? 2 : 1 }}
                            </span>
                        </div>
                    </div>

                </div>

                {{-- ================= KANAN: INFO PRODUK ================= --}}
                <div class="flex flex-col lg:col-span-5">
                    <span class="w-fit rounded-md bg-primary px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">
                        Koleksi Kaos Pembangunan
                    </span>

                    <h1 class="mt-3 font-display text-3xl font-extrabold uppercase leading-[1.05] tracking-tight sm:text-4xl">
                        {{ $selected->nama }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                        <span class="font-bold text-ink">5.0</span>
                        <span class="tracking-tight text-primary">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        <span class="h-4 w-px bg-line"></span>
                        <span class="text-muted">{{ number_format($selected->orders_count) }} pesanan</span>
                    </div>

                    <p class="mt-5 font-display text-4xl font-extrabold tracking-tight text-primary sm:text-5xl">
                        Rp {{ number_format($selected->harga, 0, ',', '.') }}
                    </p>

                    {{-- Baris Keunggulan --}}
                    <div class="mt-6 grid grid-cols-3 divide-x divide-line border-y border-line py-4">
                        <div class="flex flex-col items-center gap-1.5 px-2 text-center">
                            <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <span class="text-xs font-bold text-ink">Denpasar, Bali</span>
                            <span class="text-[10px] leading-tight text-muted">Dirancang lokal</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 px-2 text-center">
                            <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                            </svg>
                            <span class="text-xs font-bold text-ink">Kualitas Premium</span>
                            <span class="text-[10px] leading-tight text-muted">Dijahit rapi</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 px-2 text-center">
                            <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h.375c.621 0 1.125-.504 1.125-1.125V11.25c0-1.44-.72-2.72-1.92-3.49l-1.23-.76a1.125 1.125 0 0 0-1.24.18L13.5 7.5M3.375 18.75h4.125M3.375 14.25h4.125m4.5 0h4.125M16.5 7.5h.008v.008H16.5V7.5Zm-3 0h.008v.008h-.008V7.5Zm-3 0h.008v.008h-.008V7.5Z" />
                            </svg>
                            <span class="text-xs font-bold text-ink">Kirim Se-Indonesia</span>
                            <span class="text-[10px] leading-tight text-muted">Aman &amp; terpercaya</span>
                        </div>
                    </div>

                    {{-- Ukuran --}}
                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-ink">Ukuran</p>
                            <button type="button" data-open-size-chart class="text-xs font-semibold text-primary underline underline-offset-2 transition hover:text-primary-dark cursor-pointer">
                                Panduan Ukuran
                            </button>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @php
                                $availableSizes = $selected ? $selected->pilihanUkuran() : ['S', 'M', 'L', 'XL', 'XXL'];
                                $defaultSelectedSize = in_array('L', $availableSizes) ? 'L' : ($availableSizes[0] ?? 'L');
                            @endphp
                            @foreach ($ukuran as $u)
                                @php
                                    $isAvailable = in_array($u, $availableSizes);
                                    $isSelected = $u === $defaultSelectedSize;
                                @endphp
                                <button type="button" data-size="{{ $u }}"
                                    {{ $isAvailable ? '' : 'disabled' }}
                                    title="{{ $u }} — {{ $isAvailable ? 'Tersedia' : 'Habis' }}"
                                    class="size-pill grid h-11 {{ $u === 'XXL' ? 'px-4' : 'w-11' }} place-items-center rounded-full text-sm font-bold transition {{ !$isAvailable ? 'bg-stone-100 text-stone-300 line-through opacity-40 cursor-not-allowed' : ($isSelected ? 'bg-stone-900 text-white cursor-pointer' : 'bg-stone-100 text-ink hover:bg-stone-200 cursor-pointer') }}">
                                    {{ $u }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Warna --}}
                    <div class="mt-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-ink">Warna</p>
                        <div class="mt-3 flex items-center gap-3">
                            <button type="button" data-warna="Terracotta" aria-label="Terracotta" class="color-swatch h-9 w-9 rounded-full ring-2 ring-primary ring-offset-2 ring-offset-white transition" style="background:#b4531f"></button>
                            <button type="button" data-warna="Hitam" aria-label="Hitam" class="color-swatch h-9 w-9 rounded-full ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#1c1917"></button>
                            <button type="button" data-warna="Putih" aria-label="Putih" class="color-swatch h-9 w-9 rounded-full border border-line ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#f5f5f4"></button>
                            <button type="button" data-warna="Navy" aria-label="Navy" class="color-swatch h-9 w-9 rounded-full ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#1e293b"></button>
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button type="button" id="fav" class="btn flex flex-1 items-center justify-center gap-2 border-2 border-primary py-3 text-xs font-bold uppercase tracking-[0.12em] text-primary hover:bg-primary/5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span id="fav-label">Simpan Favorit</span>
                        </button>
                        <a id="pesan" href="{{ route('checkout.pemesanan', ['desain' => $selected->id, 'ukuran' => $defaultSelectedSize]) }}"
                            class="btn btn-primary flex flex-[1.4] items-center justify-center gap-2 py-3 text-xs font-bold uppercase tracking-[0.12em]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                            </svg>
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="border-t border-line">
                <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
                    <h2 class="text-[11px] font-bold uppercase tracking-[0.25em] text-ink">Deskripsi Produk</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-muted">{{ $selected->deskripsi }}</p>
                </div>
            </div>

            <div class="border-t border-line">
                <p class="mx-auto max-w-6xl px-4 py-5 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-muted sm:px-6">
                    Dirancang di Denpasar, Bali &nbsp;/&nbsp; Dijahit rapi &middot; Dikirim ke seluruh Indonesia
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
            <h2 class="text-center font-display text-2xl font-extrabold uppercase tracking-tight sm:text-3xl">Anda Mungkin Suka</h2>

            @if ($terkait->isEmpty())
                <p class="mt-6 text-center text-sm text-muted">Belum ada desain lain di koleksi ini.</p>
            @else
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($terkait as $d)
                        <a href="{{ route('design', ['desain' => $d->id]) }}" class="card group flex flex-col overflow-hidden !p-0 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                                <img src="{{ $d->gambarUrl() }}" alt="{{ $d->nama }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-display text-sm font-bold uppercase leading-snug">{{ $d->nama }}</span>
                                    <span class="shrink-0 font-display text-sm font-bold">Rp {{ number_format($d->harga, 0, ',', '.') }}</span>
                                </div>
                                <span class="mt-2 flex items-center gap-1.5 text-xs text-muted">
                                    <span class="tracking-tight text-primary">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                    {{ $d->orders_count }} pesanan
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-12 flex flex-col items-start justify-between gap-5 rounded-2xl border border-line bg-white p-6 sm:flex-row sm:items-center">
                <div>
                    <h3 class="font-serif text-lg font-bold">Tidak sedang butuh kaos?</h3>
                    <p class="mt-1 text-sm text-muted">Anda tetap bisa membantu lewat donasi nominal bebas.</p>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-primary">Ke Formulir Donasi</a>
            </div>
        </section>
    @endif

    {{-- Modal Pilihan: Beli Kaos Saja vs Kaos + Donasi --}}
    @include('components.order-choice-modal')
    {{-- Modal Panduan Ukuran Kaos --}}
    @include('components.size-chart-modal')
@endsection

@push('scripts')
    <script>
        (function () {
            var viewer = document.getElementById('viewer-360');
            var video = document.getElementById('viewer-360-video');
            var counter = document.getElementById('viewer-360-counter');

            // ================= Galeri: Foto vs 360° =================
            var galThumbs = document.querySelectorAll('[data-gal]');
            var galPanelFoto = document.getElementById('gal-panel-foto');
            var galPanel360 = document.getElementById('gal-panel-360');
            var galCounter = document.getElementById('gal-counter');
            var galTotal = document.querySelectorAll('.gal-thumb').length;

            function showGal(mode) {
                var is360 = mode === '360' && galPanel360;
                galThumbs.forEach(function (t) {
                    var on = t.dataset.gal === (is360 ? '360' : 'foto');
                    t.classList.toggle('border-primary', on);
                    t.classList.toggle('border-line', !on);
                    t.classList.toggle('opacity-100', on);
                    t.classList.toggle('opacity-70', !on);
                });
                if (galPanelFoto) galPanelFoto.classList.toggle('hidden', is360);
                if (galPanel360) galPanel360.classList.toggle('hidden', !is360);
                if (galCounter) galCounter.textContent = (is360 ? 2 : 1) + '/' + galTotal;
                if (video) {
                    if (is360) video.play().catch(function(){});
                    else video.pause();
                }
            }

            galThumbs.forEach(function (t) {
                t.addEventListener('click', function () { showGal(t.dataset.gal); });
            });
            showGal('foto');

            // Kontrol 360° Video: Hanya berjalan bila pakaian memiliki berkas video 360°
            if (viewer && video) {
                var menyeret = false;
                var awalX = 0;
                var awalTime = 0;

                video.addEventListener('timeupdate', function () {
                    if (!menyeret && video.duration) {
                        var deg = Math.round((video.currentTime / video.duration) * 360) % 360;
                        if (counter) counter.textContent = deg + '°';
                    }
                });

                viewer.addEventListener('pointerdown', function (e) {
                    menyeret = true;
                    awalX = e.clientX;
                    awalTime = video.currentTime;
                    video.pause();
                    viewer.setPointerCapture(e.pointerId);
                });

                viewer.addEventListener('pointermove', function (e) {
                    if (!menyeret || !video.duration) return;
                    var delta = (e.clientX - awalX);
                    var dur = video.duration;
                    var w = viewer.clientWidth || 400;
                    var newTime = awalTime + (delta / w) * dur;
                    while (newTime < 0) newTime += dur;
                    while (newTime >= dur) newTime -= dur;
                    video.currentTime = newTime;
                    var deg = Math.round((newTime / dur) * 360) % 360;
                    if (counter) counter.textContent = deg + '°';
                });

                viewer.addEventListener('pointerup', function () {
                    menyeret = false;
                    video.play().catch(function(){});
                });
                viewer.addEventListener('pointercancel', function () {
                    menyeret = false;
                    video.play().catch(function(){});
                });
            }

            var sizeWrap = document.querySelector('.size-pill')?.parentElement;
            var pesanLink = document.getElementById('pesan');
            if (sizeWrap && pesanLink) {
                sizeWrap.addEventListener('click', function (e) {
                    var btn = e.target.closest('.size-pill');
                    if (!btn || btn.disabled) return;
                    sizeWrap.querySelectorAll('.size-pill').forEach(function (p) {
                        p.classList.remove('bg-stone-900', 'text-white');
                        p.classList.add('bg-stone-100', 'text-ink');
                    });
                    btn.classList.remove('bg-stone-100', 'text-ink');
                    btn.classList.add('bg-stone-900', 'text-white');
                    var url = new URL(pesanLink.href);
                    url.searchParams.set('ukuran', btn.dataset.size);
                    pesanLink.href = url.toString();
                });

                pesanLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    var activeSize = document.querySelector('.size-pill.bg-stone-900')?.dataset.size || 'L';
                    if (window.OrderChoiceModal) {
                        window.OrderChoiceModal.open({
                            name: @json($selected->nama ?? 'Kaos Jemaat'),
                            size: activeSize,
                            qty: 1,
                            price: {{ (int) ($selected->harga ?? 150000) }},
                            designId: {{ $selected->id ?? 1 }},
                            imgUrl: '{{ $selected ? $selected->gambarUrl() : "" }}',
                            baseUrl: '{{ route("checkout.pemesanan") }}'
                        });
                    } else {
                        window.location.href = this.href;
                    }
                });
            }

            document.querySelectorAll('.color-swatch').forEach(function (sw) {
                sw.addEventListener('click', function () {
                    document.querySelectorAll('.color-swatch').forEach(function (s) {
                        s.classList.remove('ring-2', 'ring-primary', 'ring-stone-400');
                    });
                    sw.classList.add('ring-2', 'ring-primary');
                });
            });

            var favBtn = document.getElementById('fav');
            var favLabel = document.getElementById('fav-label');
            var selectedId = @json($selected?->id);
            if (favBtn && selectedId) {
                function favIds() {
                    try { return JSON.parse(localStorage.getItem('jb-favorit') || '[]'); } catch (err) { return []; }
                }
                function syncFav() {
                    var on = favIds().indexOf(selectedId) !== -1;
                    if (favLabel) favLabel.textContent = on ? 'Tersimpan ✓' : 'Simpan Favorit';
                    favBtn.classList.toggle('bg-primary/10', on);
                }
                favBtn.addEventListener('click', function () {
                    var ids = favIds();
                    var i = ids.indexOf(selectedId);
                    if (i === -1) ids.push(selectedId); else ids.splice(i, 1);
                    localStorage.setItem('jb-favorit', JSON.stringify(ids));
                    syncFav();
                });
                syncFav();
            }
        })();
    </script>
@endpush
