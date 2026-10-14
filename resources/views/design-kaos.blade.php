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
            <div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-12 lg:py-14">

                <div class="flex flex-col justify-center lg:col-span-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-muted">Koleksi Kaos</p>
                    <h1 class="mt-3 font-display text-3xl font-extrabold uppercase leading-[1.02] tracking-tight sm:text-4xl">
                        {{ $selected->nama }}
                    </h1>
                    <p class="mt-5 w-fit rounded-full bg-primary px-5 py-2 font-display text-sm font-bold text-white">
                        Rp {{ number_format($selected->harga, 0, ',', '.') }}
                    </p>
                    <p class="mt-5 text-sm leading-relaxed text-muted">{{ $selected->deskripsi }}</p>

                    <div class="mt-8 border-y border-line">
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center justify-between py-4 text-xs font-bold uppercase tracking-[0.15em] [&::-webkit-details-marker]:hidden">
                                Bahan &amp; Ukuran
                                <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </summary>
                            <div class="pb-5 text-sm leading-relaxed text-muted">
                                <p>Kaos katun combed 30s — adem, jahitan rapi, sablon fleksibel yang tidak mudah retak.</p>
                                <table class="mt-3 w-full max-w-xs text-xs">
                                    <tbody class="divide-y divide-line">
                                        <tr><td class="py-1.5 font-semibold text-ink">S</td><td class="py-1.5">Lingkar dada 48 cm</td></tr>
                                        <tr><td class="py-1.5 font-semibold text-ink">M</td><td class="py-1.5">Lingkar dada 50 cm</td></tr>
                                        <tr><td class="py-1.5 font-semibold text-ink">L</td><td class="py-1.5">Lingkar dada 52 cm</td></tr>
                                        <tr><td class="py-1.5 font-semibold text-ink">XL</td><td class="py-1.5">Lingkar dada 54 cm</td></tr>
                                        <tr><td class="py-1.5 font-semibold text-ink">XXL</td><td class="py-1.5">Lingkar dada 56 cm</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>
                        <details class="group border-t border-line">
                            <summary class="flex cursor-pointer list-none items-center justify-between py-4 text-xs font-bold uppercase tracking-[0.15em] [&::-webkit-details-marker]:hidden">
                                Cara Perawatan
                                <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </summary>
                            <div class="pb-5 text-sm leading-relaxed text-muted">
                                Cuci dengan air dingin, jangan pakai pemutih. Setrika dari balik sablon dan hindari mesin pengering agar awet.
                            </div>
                        </details>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center lg:col-span-5">
                    <div class="relative w-full max-w-[480px]">
                        @if ($selected->video360Url())
                            {{-- 360° VIDEO PLAYING KHUSUS UNTUK DESAIN YANG MEMILIKI VIDEO 360 --}}
                            <div id="viewer-360" class="relative aspect-square w-full cursor-grab select-none overflow-hidden rounded-3xl border border-line bg-stone-100 shadow-sm active:cursor-grabbing">
                                <video id="viewer-360-video" src="{{ $selected->video360Url() }}" playsinline webkit-playsinline muted loop autoplay
                                    class="pointer-events-none absolute inset-0 h-full w-full object-contain p-6 will-change-transform"></video>
                                <span class="absolute left-3 top-3 z-10 flex items-center gap-1.5 rounded-full bg-stone-900/85 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-xs">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    360&deg; Video Playing
                                </span>
                                <span id="viewer-360-counter" class="absolute right-3 top-3 z-10 rounded-full bg-stone-900/85 px-3 py-1 font-mono text-[10px] font-bold text-white backdrop-blur-xs">0&deg;</span>
                                <span class="pointer-events-none absolute bottom-4 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded-full border border-line bg-white/95 px-3.5 py-1.5 text-[11px] font-semibold text-stone-600 shadow-xs backdrop-blur-xs">
                                    Geser kiri/kanan untuk putar 360&deg;
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-center gap-2">
                                <button type="button" data-putar="-10" class="grid h-10 w-10 place-items-center rounded-full border border-line bg-white text-sm font-bold text-ink shadow-xs transition hover:border-primary hover:text-primary" title="Putar -10°">&minus;10&deg;</button>
                                <button type="button" id="btn-jeda" class="grid h-10 w-10 place-items-center rounded-full border border-line bg-white text-sm font-bold text-ink shadow-xs transition hover:border-primary hover:text-primary" title="Jeda / putar otomatis">&#10074;&#10074;</button>
                                <button type="button" data-putar="reset" class="grid h-10 w-10 place-items-center rounded-full border border-line bg-white text-xs font-bold text-ink shadow-xs transition hover:border-primary hover:text-primary" title="Reset ke 0°">0&deg;</button>
                                <button type="button" data-putar="10" class="grid h-10 w-10 place-items-center rounded-full border border-line bg-white text-sm font-bold text-ink shadow-xs transition hover:border-primary hover:text-primary" title="Putar +10°">+10&deg;</button>
                            </div>
                        @else
                            {{-- DESAIN TANPA VIDEO 360: GAMBAR PAKAIAN STATIS ELEGAN & BERSIH --}}
                            <div class="relative aspect-square w-full select-none overflow-hidden rounded-3xl border border-line bg-stone-100 shadow-sm flex items-center justify-center p-8">
                                <img src="{{ $selected->gambarUrl() }}" alt="{{ $selected->nama }}" draggable="false"
                                    class="max-h-full max-w-full object-contain drop-shadow-md transition duration-300 hover:scale-105">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col justify-center lg:col-span-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-ink">Ukuran</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @foreach ($ukuran as $u)
                            <button type="button" data-size="{{ $u }}"
                                class="size-pill grid h-11 {{ $u === 'XXL' ? 'px-4' : 'w-11' }} place-items-center rounded-full text-sm font-bold transition {{ $u === 'L' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-ink hover:bg-stone-200' }}">
                                {{ $u }}
                            </button>
                        @endforeach
                    </div>
                    <button type="button" id="size-guide" class="mt-3 w-fit text-[11px] font-bold uppercase tracking-[0.15em] text-muted underline underline-offset-4 hover:text-ink">
                        Panduan Ukuran
                    </button>

                    <p class="mt-8 text-[11px] font-bold uppercase tracking-[0.25em] text-ink">Warna</p>
                    <div class="mt-3 flex items-center gap-3">
                        <button type="button" data-warna="Terracotta" aria-label="Terracotta" class="color-swatch h-9 w-9 rounded-full ring-2 ring-primary ring-offset-2 ring-offset-white transition" style="background:#b4531f"></button>
                        <button type="button" data-warna="Hitam" aria-label="Hitam" class="color-swatch h-9 w-9 rounded-full ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#1c1917"></button>
                        <button type="button" data-warna="Putih" aria-label="Putih" class="color-swatch h-9 w-9 rounded-full border border-line ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#f5f5f4"></button>
                        <button type="button" data-warna="Navy" aria-label="Navy" class="color-swatch h-9 w-9 rounded-full ring-offset-2 ring-offset-white transition hover:ring-2 hover:ring-stone-400" style="background:#1e293b"></button>
                    </div>

                    <div class="mt-8 flex flex-col gap-3">
                        <button type="button" id="fav" class="btn w-full border-2 border-primary py-3 text-xs font-bold uppercase tracking-[0.12em] text-primary hover:bg-primary/5">
                            Simpan Favorit
                        </button>
                        <a id="pesan" href="{{ route('checkout.pemesanan', ['desain' => $selected->id, 'ukuran' => 'L']) }}"
                            class="btn btn-primary w-full py-3 text-xs font-bold uppercase tracking-[0.12em]">
                            Pesan Sekarang
                        </a>
                    </div>
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
                                <img src="{{ $d->gambarUrl() }}" alt="{{ $d->nama }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
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
@endsection

@push('scripts')
    <script>
        (function () {
            var viewer = document.getElementById('viewer-360');
            var video = document.getElementById('viewer-360-video');
            var counter = document.getElementById('viewer-360-counter');
            var jedaBtn = document.getElementById('btn-jeda');

            // Kontrol 360° Video: Hanya berjalan bila pakaian memiliki berkas video 360°
            if (viewer && video) {
                var otomatis = true;
                var menyeret = false;
                var awalX = 0;
                var awalTime = 0;

                video.addEventListener('timeupdate', function () {
                    if (!menyeret && video.duration) {
                        var deg = Math.round((video.currentTime / video.duration) * 360) % 360;
                        if (counter) counter.textContent = deg + '°';
                    }
                });

                function setOtomatis(nilai) {
                    otomatis = nilai;
                    if (jedaBtn) {
                        jedaBtn.innerHTML = nilai ? '&#10074;&#10074;' : '&#9654;';
                        jedaBtn.title = nilai ? 'Jeda putar otomatis' : 'Lanjutkan putar otomatis';
                    }
                    if (nilai) video.play().catch(function(){});
                    else video.pause();
                }

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
                    if (otomatis) video.play().catch(function(){});
                });
                viewer.addEventListener('pointercancel', function () {
                    menyeret = false;
                    if (otomatis) video.play().catch(function(){});
                });

                document.querySelectorAll('[data-putar]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var aksi = btn.getAttribute('data-putar');
                        if (!video.duration) return;
                        var dur = video.duration;
                        if (aksi === 'reset') {
                            video.currentTime = 0;
                        } else {
                            var newTime = (video.currentTime + (Number(aksi) / 360) * dur);
                            while (newTime < 0) newTime += dur;
                            while (newTime >= dur) newTime -= dur;
                            video.currentTime = newTime;
                        }
                    });
                });

                if (jedaBtn) {
                    jedaBtn.addEventListener('click', function () { setOtomatis(!otomatis); });
                }
            }

            document.getElementById('size-guide')?.addEventListener('click', function () {
                var det = document.querySelector('details');
                if (det) {
                    det.open = true;
                    det.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            var sizeWrap = document.querySelector('.size-pill')?.parentElement;
            var pesanLink = document.getElementById('pesan');
            if (sizeWrap && pesanLink) {
                sizeWrap.addEventListener('click', function (e) {
                    var btn = e.target.closest('.size-pill');
                    if (!btn) return;
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
            var selectedId = @json($selected?->id);
            if (favBtn && selectedId) {
                function favIds() {
                    try { return JSON.parse(localStorage.getItem('jb-favorit') || '[]'); } catch (err) { return []; }
                }
                function syncFav() {
                    var on = favIds().indexOf(selectedId) !== -1;
                    favBtn.textContent = on ? 'Tersimpan ✓' : 'Simpan Favorit';
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
