@extends('layouts.app')

@section('title', ($programInfo->nama_program ?? 'Jemaat Bali') . ' — Donasi & Kaos Fundraiser')
@section('description', $programInfo->deskripsi ?? 'Situs penggalangan dana pembangunan Jemaat Bali: video, proposal, progres, desain kaos, dan donasi.')

@section('content')
    {{-- ================= HERO VIDEO DIAM (FIXED / TIDAK IKUT SCROLL) ================= --}}
    <div id="hero-video-wrapper" class="fixed top-0 left-0 right-0 z-0 h-[440px] sm:h-[520px] lg:h-[600px] w-full overflow-hidden transition-opacity duration-200 {{ $heroVideoType === 'gdrive' ? 'pointer-events-auto' : 'pointer-events-none' }}" aria-hidden="true">
        @if ($heroVideoType === 'gdrive' && $heroVideoEmbedUrl)
            <div class="relative h-full w-full bg-stone-900">
                <iframe src="{{ $heroVideoEmbedUrl }}"
                    class="h-full w-full object-cover border-0"
                    allow="autoplay; encrypted-media; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @else
            <video id="hero-bg-video" autoplay muted loop playsinline preload="auto" poster="{{ $heroPosterUrl }}"
                class="h-full w-full object-cover object-center">
                <source src="{{ $heroVideoUrl }}" type="video/mp4">
                @if ($heroVideoType === 'default')
                    <source src="{{ asset('videos/hero-bg.webm') }}" type="video/webm">
                @endif
            </video>
        @endif
    </div>

    {{-- Spacer transparan seukuran hero --}}
    <div id="hero-spacer" class="relative w-full h-[340px] sm:h-[420px] lg:h-[480px] pointer-events-none" aria-hidden="true"></div>

    {{-- Semua konten di bawah hero menutupi video saat di-scroll --}}
    <div class="relative z-10 w-full bg-canvas">

    {{-- ================= FLOATING SEARCH / QUICK-ACTION BAR ================= --}}
    <div id="quick-action-bar" class="relative z-20 -translate-y-1/2 px-4 sm:px-6">
        <div class="mx-auto max-w-6xl rounded-3xl lg:rounded-full bg-white p-3 sm:p-4 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.12)] border border-stone-200/80">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-center lg:gap-0 lg:divide-x lg:divide-stone-100">
                {{-- 1. Lokasi --}}
                <a href="{{ route('progres') }}" class="group flex items-center gap-3.5 px-4 py-2 hover:bg-stone-50 rounded-2xl lg:rounded-l-full transition">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-700 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-stone-900">Lokasi Proyek</span>
                        <span class="block text-xs text-muted">Denpasar, Bali</span>
                    </div>
                </a>

                {{-- 2. Update Progres --}}
                <a href="{{ route('progres') }}" class="group flex items-center gap-3.5 px-4 py-2 hover:bg-stone-50 rounded-2xl transition">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-700 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-stone-900">Update Progres</span>
                        <span class="block text-xs text-muted">{{ $progressTerbaru->count() ? $progressTerbaru->count() + 4 : 0 }}+ Entri Mingguan</span>
                    </div>
                </a>

                {{-- 3. Kaos Fundraiser --}}
                <a href="#koleksi-kaos" class="group flex items-center gap-3.5 px-4 py-2 hover:bg-stone-50 rounded-2xl transition">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-700 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-stone-900">Kaos Fundraiser</span>
                        <span class="block text-xs text-muted">{{ $designs->count() }} Pilihan Desain</span>
                    </div>
                </a>

                {{-- 4. Proposal Dokumen --}}
                <a href="{{ route('proposal') }}" class="group flex items-center gap-3.5 px-4 py-2 hover:bg-stone-50 rounded-2xl transition">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-stone-100 text-stone-700 group-hover:bg-primary/10 group-hover:text-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-stone-900">Proposal Resmi</span>
                        <span class="block text-xs text-muted">Transparan & Lengkap</span>
                    </div>
                </a>

                {{-- 5. Main Action Button (Search button style in reference) --}}
                <div class="px-2 py-1">
                    <a href="{{ route('shop') }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-[#1c2e24] px-6 py-3.5 text-sm font-semibold text-white shadow-md hover:bg-[#15231b] transition">
                        <span>Donasi Sekarang</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 4 FEATURE PILLARS ================= --}}
    <section class="mx-auto max-w-6xl px-4 pt-2 sm:pt-4 pb-8 sm:px-6">
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- 1 --}}
            <div class="flex items-start gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-stone-100 text-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A1.167 1.167 0 0 0 19.333 9H4.667A1.167 1.167 0 0 0 3.5 10.333V21" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-sm font-bold text-ink">100% Kas Pembangunan</h3>
                    <p class="mt-0.5 text-xs text-muted leading-relaxed">Seluruh donasi & laba penjualan kaos dialokasikan langsung.</p>
                </div>
            </div>

            {{-- 2 --}}
            <div class="flex items-start gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-stone-100 text-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-sm font-bold text-ink">Transparansi Terbuka</h3>
                    <p class="mt-0.5 text-xs text-muted leading-relaxed">Foto dan video rekaman lapangan diunggah berkala tiap minggu.</p>
                </div>
            </div>

            {{-- 3 --}}
            <div class="flex items-start gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-stone-100 text-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-sm font-bold text-ink">Bahan Kaos Pilihan</h3>
                    <p class="mt-0.5 text-xs text-muted leading-relaxed">Katun combed 24s/30s nyaman dan sablon awet untuk dipakai ibadah.</p>
                </div>
            </div>

            {{-- 4 --}}
            <div class="flex items-start gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-stone-100 text-stone-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-sm font-bold text-ink">Panitia Siap Melayani</h3>
                    <p class="mt-0.5 text-xs text-muted leading-relaxed">Konfirmasi donasi dan pengiriman dihubungi langsung oleh pengurus.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PROPOSAL (Canva player interaktif atau Sampul PDF) ================= --}}
    <section class="border-b border-line bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="grid items-center gap-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 class="font-display text-2xl font-extrabold sm:text-3xl">Proposal pembangunan</h2>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        @if ($isCanva && $canvaEmbedUrl)
                            Presentasi resmi rencana kerja dan anggaran pembangunan. Telusuri langsung slide proposal di samping atau buka dalam mode layar penuh.
                        @else
                            Dokumen resmi rencana kerja dan anggaran. Klik sampul untuk membaca selengkapnya.
                        @endif
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row lg:flex-col gap-3">
                        <a href="{{ route('proposal') }}" class="btn btn-primary inline-flex items-center justify-center gap-2">
                            <span>{{ $isCanva && $canvaEmbedUrl ? 'Buka Proposal Layar Penuh' : 'Buka Halaman Proposal' }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    @if ($isCanva && $canvaEmbedUrl)
                        {{-- Tampilan Canva Presentasi Langsung: Menampilkan Sampul Depan & Slide Interaktif --}}
                        <div class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-stone-200 bg-stone-950 shadow-2xl">
                            {{-- Iframe Canva Player --}}
                            <div class="relative w-full aspect-[16/9] min-h-[300px] sm:min-h-[420px] md:min-h-[460px] bg-stone-950">
                                <iframe id="home-canva-frame"
                                    src="{{ $canvaEmbedUrl }}"
                                    title="{{ $programInfo->judul_proposal ?? 'Proposal Pembangunan' }}"
                                    loading="lazy"
                                    allow="autoplay; fullscreen"
                                    allowfullscreen="allowfullscreen"
                                    class="absolute inset-0 h-full w-full border-0">
                                </iframe>
                            </div>
                        </div>
                        <div class="mt-3.5 flex flex-wrap items-center justify-between gap-3 text-xs text-stone-500">
                            <p class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-purple-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" />
                                </svg>
                                <span>Klik atau geser pada tampilan di atas untuk menelusuri halaman proposal.</span>
                            </p>
                        </div>
                    @else
                        {{-- Mode Buku PDF (Sampul PDF.js atau Fallback Card) --}}
                        <div class="mx-auto max-w-2xl">
                            <a href="{{ route('proposal') }}" title="Buka halaman Proposal"
                                class="group relative block overflow-hidden rounded-lg border border-stone-200 bg-white shadow-[0_20px_50px_-15px_rgba(0,0,0,0.18)] transition hover:shadow-xl">
                                {{-- Kepala halaman seperti mode 1 halaman --}}
                                <div class="flex items-center justify-between px-5 pt-5 sm:px-8 sm:pt-6">
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#b05525] text-sm font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                                        <span class="text-sm font-extrabold tracking-tight text-stone-900 sm:text-base">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                                    </div>
                                    <span class="text-xs font-medium text-stone-500 sm:text-sm">Hal. 1</span>
                                </div>
                                <div class="px-5 py-4 sm:px-8">
                                    <div class="relative aspect-[3/4] w-full overflow-hidden rounded-sm">
                                        {{-- Sampul statis (tampil duluan / fallback) --}}
                                        <div id="proposal-cover-fallback" class="absolute inset-0 flex flex-col justify-between bg-stone-100 p-6 text-left sm:p-8">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#b05525] text-sm font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                                                    <span class="font-extrabold tracking-tight text-stone-900">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                                                </div>
                                                <p class="mt-8 inline-block rounded bg-[#b05525]/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-[#b05525]">
                                                    Program Resmi
                                                </p>
                                                <h3 class="mt-3 font-display text-4xl font-extrabold leading-tight text-stone-900 sm:text-5xl">Proposal<br><span class="font-serif italic font-normal text-[#b05525]">Pembangunan</span></h3>
                                                <p class="mt-3 max-w-md text-sm leading-relaxed text-stone-500">{{ $programInfo->deskripsi ?? 'Rencana fisik, anggaran kebutuhan, & tahapan pembangunan rumah jemaat.' }}</p>
                                            </div>
                                            <div class="border-t border-stone-200 pt-4 text-xs text-stone-500">
                                                <p class="font-semibold text-stone-800">Panitia {{ $programInfo->nama_program ?? 'Pembangunan' }}</p>
                                                <p>{{ $programInfo->nama_program ?? 'Jemaat Bali' }} — {{ date('Y') }}</p>
                                            </div>
                                        </div>
                                        {{-- Sampul asli (halaman 1 PDF, dimuat via JS) --}}
                                        <canvas id="proposal-cover-canvas" class="absolute inset-0 hidden h-full w-full"></canvas>
                                        {{-- Overlay hover --}}
                                        <div class="absolute inset-0 grid place-items-center bg-stone-900/0 transition group-hover:bg-stone-900/40">
                                            <span class="translate-y-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-stone-900 opacity-0 shadow-lg transition group-hover:translate-y-0 group-hover:opacity-100">
                                                Buka Proposal &rarr;
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between px-5 pb-5 text-xs font-medium text-stone-500 sm:px-8 sm:pb-6">
                                    <span>Proposal Pembangunan</span>
                                    <span>1</span>
                                </div>
                            </a>
                            <p id="proposal-cover-pages" class="mt-3 text-center text-xs text-stone-500">
                                Klik sampul untuk membaca proposal lengkap
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($adaFile && !($isCanva && $canvaEmbedUrl))
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const coverUrl = @json($url ?? '');
                    const coverCanvas = document.getElementById('proposal-cover-canvas');
                    const coverFallback = document.getElementById('proposal-cover-fallback');
                    const coverPages = document.getElementById('proposal-cover-pages');
                    if (!coverUrl || !coverCanvas || !window.pdfjsLib) return;
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    window.pdfjsLib.getDocument(coverUrl).promise.then(function (pdf) {
                        if (coverPages) coverPages.textContent = pdf.numPages + ' halaman • klik sampul untuk membaca';
                        return pdf.getPage(1);
                    }).then(function (page) {
                        const base = page.getViewport({ scale: 1.0 });
                        const viewport = page.getViewport({ scale: 620 / base.width });
                        const scale = window.devicePixelRatio || 1;
                        coverCanvas.width = Math.floor(viewport.width * scale);
                        coverCanvas.height = Math.floor(viewport.height * scale);
                        return page.render({
                            canvasContext: coverCanvas.getContext('2d'),
                            transform: scale !== 1 ? [scale, 0, 0, scale, 0, 0] : null,
                            viewport: viewport
                        }).promise;
                    }).then(function () {
                        coverCanvas.classList.remove('hidden');
                        coverFallback?.classList.add('hidden');
                    }).catch(function () { /* tetap tampilkan sampul statis */ });
                });
            </script>
        @endpush
    @endif

    {{-- ================= PROGRES PEMBANGUNAN (SLIDER ELEGAN ALA REFERENSI GLOBE EXPRESS) ================= --}}
    <section id="progres-highlight" class="relative min-h-[620px] sm:min-h-[700px] lg:min-h-[780px] overflow-hidden bg-stone-950 text-white flex flex-col justify-between py-6 sm:py-10 px-4 sm:px-8 lg:px-14">
        {{-- Garis aksen kuning khas referensi di tepi atas --}}
        <div class="absolute inset-x-0 top-0 h-[3px] bg-white/10 z-20">
            <div class="h-full w-48 sm:w-72 bg-amber-400"></div>
        </div>
        {{-- Latar foto per slide (full bleed dengan fade transition) --}}
        @forelse ($progressTerbaru as $i => $p)
            @php
                $thumb = $p->thumbnailUrl() ?: ($p->tipe === 'foto' ? $p->mediaUrl() : null);
            @endphp
            @if ($thumb)
                <div class="progres-bg absolute inset-0 transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
                    style="background-image: url('{{ $thumb }}'); background-size: cover; background-position: center;">
                </div>
            @elseif ($p->tipe === 'video' && $p->mediaUrl())
                <div class="progres-bg absolute inset-0 transition-opacity duration-700 ease-in-out overflow-hidden {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
                    <video src="{{ $p->mediaUrl() }}" autoplay muted loop playsinline class="h-full w-full object-cover"></video>
                </div>
            @else
                <div class="progres-bg absolute inset-0 transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
                    style="background-image: url('{{ asset('images/hero-poster.jpg') }}'); background-size: cover; background-position: center;">
                </div>
            @endif
        @empty
            <div class="absolute inset-0 bg-[linear-gradient(135deg,#3a2415,#1c1917)]"></div>
        @endforelse

        {{-- Vignette & Overlay gelap sinematik (kiri gelap agar teks terbaca, bawah gelap untuk kontrol) --}}
        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/55 to-black/25 pointer-events-none z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-black/35 pointer-events-none z-10"></div>

        {{-- Header Bar Section --}}
        <div class="relative z-20 flex items-center justify-between pt-6 sm:pt-8 pb-4 sm:pb-6">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-white/90">Dokumentasi Progres Lapangan</span>
            </div>
            <a href="{{ route('progres') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-white/70 transition hover:text-amber-400">
                <span>Semua Progres</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Konten Utama (Kiri Teks Hero Aktif, Kanan Kartu-Kartu Geser) --}}
        <div class="relative z-20 my-auto py-4 sm:py-6">
            <div class="grid items-center gap-8 lg:gap-12 lg:grid-cols-12">
                {{-- Kiri: Teks Hero Slide Aktif --}}
                <div class="lg:col-span-5 xl:col-span-5 lg:pr-6">
                    @forelse ($progressTerbaru as $i => $p)
                        <div class="progres-info transition-all duration-500 {{ $i === 0 ? '' : 'hidden' }}">
                            {{-- Strip garis & kategori --}}
                            <div class="flex items-center gap-2.5">
                                <span class="h-[2px] w-6 bg-white/80"></span>
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-white/90">
                                    {{ $p->tanggal->translatedFormat('d F Y') }} &middot; {{ strtoupper($p->tipe) }}
                                </p>
                            </div>

                            {{-- Judul Proporsional Huruf Kapital --}}
                            <h2 class="mt-3 font-display text-2xl sm:text-3xl md:text-4xl lg:text-3xl xl:text-4xl font-extrabold uppercase tracking-tight leading-tight text-white drop-shadow-md max-w-lg">
                                {{ $p->judul }}
                            </h2>

                            {{-- Deskripsi singkat --}}
                            <p class="mt-3 line-clamp-3 max-w-md text-xs sm:text-sm leading-relaxed text-white/80">
                                {{ $p->deskripsi }}
                            </p>

                            {{-- Tombol Aksi: Putar Video / Lihat Dokumentasi --}}
                            <div class="mt-6 flex items-center gap-3.5">
                                @if ($p->tipe === 'video' && ($p->embedUrl() || $p->mediaUrl()))
                                    <button type="button"
                                        class="btn-play-progres grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-amber-400 text-stone-950 shadow-md transition hover:scale-105 hover:bg-amber-300"
                                        data-embed="{{ $p->embedUrl() }}"
                                        data-file="{{ $p->mediaUrl() }}"
                                        data-title="{{ $p->judul }}"
                                        title="Putar Video: {{ $p->judul }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 ml-0.5">
                                            <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('progres') }}" title="Buka Detail Progres"
                                        class="grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-amber-400 text-stone-950 shadow-md transition hover:scale-105 hover:bg-amber-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                            <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('progres') }}"
                                    class="inline-flex items-center rounded-full border border-white/50 bg-white/5 backdrop-blur-sm px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:border-white hover:bg-white hover:text-stone-900">
                                    <span>Lihat Dokumentasi &rarr;</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="h-[2px] w-6 bg-white/80"></span>
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">Progres Pembangunan</p>
                            </div>
                            <h2 class="mt-4 font-display text-4xl sm:text-6xl font-black uppercase text-white">Belum Ada Update</h2>
                            <p class="mt-4 max-w-md text-sm text-white/70">Foto dan video progres lapangan akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Kanan: Barisan Kartu Vertikal Melayang --}}
                <div class="lg:col-span-7 xl:col-span-7">
                    <div class="overflow-hidden">
                        <div id="progres-track" class="flex gap-4 sm:gap-5 transition-transform duration-500 ease-out">
                            @foreach ($progressTerbaru as $i => $p)
                                @php
                                    $cardThumb = $p->thumbnailUrl() ?: ($p->tipe === 'foto' ? $p->mediaUrl() : null);
                                @endphp
                                <button type="button" data-card="{{ $i }}"
                                    class="progres-card group relative w-40 sm:w-48 md:w-56 shrink-0 aspect-[9/13] overflow-hidden rounded-2xl sm:rounded-3xl text-left transition-all duration-300 {{ $i === 0 ? 'opacity-100 ring-2 ring-amber-400 shadow-2xl scale-[1.02]' : 'opacity-65 hover:opacity-95 hover:scale-[1.01]' }}">
                                    {{-- Foto / Thumbnail Kartu --}}
                                    @if ($cardThumb)
                                        <img src="{{ $cardThumb }}" alt="{{ $p->judul }}"
                                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                    @elseif ($p->tipe === 'video' && $p->mediaUrl())
                                        <video src="{{ $p->mediaUrl() }}#t=0.1" preload="metadata" muted playsinline
                                            class="absolute inset-0 h-full w-full object-cover pointer-events-none transition-transform duration-700 group-hover:scale-105"></video>
                                    @else
                                        <img src="{{ asset('images/hero-poster.jpg') }}" alt="{{ $p->judul }}"
                                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                    @endif

                                    {{-- Gradient Gelap Bawah Kartu --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>

                                    @if ($p->tipe === 'video')
                                        <span class="absolute top-3 right-3 grid h-7 w-7 place-items-center rounded-full bg-black/60 text-white backdrop-blur-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 ml-0.5">
                                                <path d="M6.3 2.841A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.269l9.344-5.89a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
                                            </svg>
                                        </span>
                                    @endif

                                    {{-- Teks Bawah Kartu --}}
                                    <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-px w-3 bg-white/70"></span>
                                            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-white/80">
                                                {{ $p->tanggal->translatedFormat('d M Y') }} &middot; {{ ucfirst($p->tipe) }}
                                            </p>
                                        </div>
                                        <p class="mt-1 font-display text-sm sm:text-base font-black uppercase leading-tight text-white drop-shadow line-clamp-2">
                                            {{ $p->judul }}
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bawah: Bar Navigasi (Tombol < >, Progress Bar, Angka 01) --}}
        @if ($progressTerbaru->count())
            <div class="relative z-20 pt-4 border-t border-white/10 sm:border-t-0">
                <div class="grid items-center gap-6 lg:grid-cols-12">
                    <div class="hidden lg:block lg:col-span-5"></div>
                    <div class="lg:col-span-7 flex flex-wrap items-center justify-between gap-4 sm:gap-6">
                        <div class="flex items-center gap-4 sm:gap-6">
                            {{-- Tombol Lingkaran < dan > --}}
                            <div class="flex items-center gap-2.5">
                                <button type="button" id="progres-prev" aria-label="Sebelumnya"
                                    class="grid h-11 w-11 place-items-center rounded-full border border-white/40 text-white transition hover:border-white hover:bg-white hover:text-stone-950">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                    </svg>
                                </button>
                                <button type="button" id="progres-next" aria-label="Berikutnya"
                                    class="grid h-11 w-11 place-items-center rounded-full border border-white/40 text-white transition hover:border-white hover:bg-white hover:text-stone-950">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Garis Progress Tipis Emas/Kuning --}}
                            <div class="h-[2px] w-32 sm:w-48 md:w-64 overflow-hidden rounded-full bg-white/20">
                                <div id="progres-bar" class="h-full bg-amber-400 transition-all duration-500" style="width:{{ 100 / $progressTerbaru->count() }}%"></div>
                            </div>
                        </div>

                        {{-- Angka Besar Kanan Bawah ala "04" di Referensi --}}
                        <div class="font-display text-4xl sm:text-5xl lg:text-6xl font-black text-white/90 tabular-nums tracking-tight">
                            <span id="progres-now">01</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- Modal Pemutar Video Progres Lapangan --}}
    <div id="modal-progres-video" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/85 p-4 backdrop-blur-md transition-opacity" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl bg-stone-950 shadow-2xl border border-white/10">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-3.5">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    <h4 id="modal-video-title" class="font-display text-sm font-bold text-white truncate max-w-md">Video Progres</h4>
                </div>
                <button type="button" id="btn-close-progres-video" class="rounded-lg p-1 text-white/70 hover:bg-white/10 hover:text-white transition" aria-label="Tutup video">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modal-video-container" class="relative aspect-video w-full bg-black">
                {{-- Diisi secara dinamis dengan video atau iframe embed --}}
            </div>
        </div>
    </div>

    @if ($progressTerbaru->count())
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const track = document.getElementById('progres-track');
                    const cards = Array.from(document.querySelectorAll('.progres-card'));
                    const bgs = Array.from(document.querySelectorAll('.progres-bg'));
                    const infos = Array.from(document.querySelectorAll('.progres-info'));
                    const bar = document.getElementById('progres-bar');
                    const now = document.getElementById('progres-now');
                    const prev = document.getElementById('progres-prev');
                    const next = document.getElementById('progres-next');
                    const total = cards.length;

                    // Modal Video Elements
                    const modalVideo = document.getElementById('modal-progres-video');
                    const modalTitle = document.getElementById('modal-video-title');
                    const modalContainer = document.getElementById('modal-video-container');
                    const btnCloseVideo = document.getElementById('btn-close-progres-video');

                    function openVideoModal(title, embedUrl, fileUrl) {
                        if (!modalVideo || !modalContainer) return;
                        if (modalTitle) modalTitle.textContent = title || 'Video Progres Lapangan';
                        if (fileUrl) {
                            modalContainer.innerHTML = '<video src="' + fileUrl + '" controls autoplay playsinline class="h-full w-full object-contain bg-black"></video>';
                        } else if (embedUrl) {
                            modalContainer.innerHTML = '<iframe src="' + embedUrl + '" class="h-full w-full border-0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                        }
                        modalVideo.classList.remove('hidden');
                        modalVideo.classList.add('flex');
                        document.body.classList.add('overflow-hidden');
                    }

                    function closeVideoModal() {
                        if (!modalVideo || !modalContainer) return;
                        modalVideo.classList.add('hidden');
                        modalVideo.classList.remove('flex');
                        modalContainer.innerHTML = '';
                        document.body.classList.remove('overflow-hidden');
                    }

                    document.querySelectorAll('.btn-play-progres').forEach(function (btn) {
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const embed = btn.getAttribute('data-embed');
                            const file = btn.getAttribute('data-file');
                            const title = btn.getAttribute('data-title');
                            openVideoModal(title, embed, file);
                        });
                    });

                    btnCloseVideo?.addEventListener('click', closeVideoModal);
                    modalVideo?.addEventListener('click', function (e) {
                        if (e.target === modalVideo) closeVideoModal();
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && modalVideo && !modalVideo.classList.contains('hidden')) {
                            closeVideoModal();
                        }
                    });

                    if (!track || total === 0) return;

                    let idx = 0;
                    let maxIdx = 0;
                    let timer = null;
                    const pad = (n) => String(n).padStart(2, '0');

                    function apply() {
                        bgs.forEach((el, i) => {
                            el.classList.toggle('opacity-100', i === idx);
                            el.classList.toggle('opacity-0', i !== idx);
                        });
                        infos.forEach((el, i) => el.classList.toggle('hidden', i !== idx));
                        cards.forEach((el, i) => {
                            const active = i === idx;
                            el.classList.toggle('opacity-100', active);
                            el.classList.toggle('ring-2', active);
                            el.classList.toggle('ring-amber-400', active);
                            el.classList.toggle('shadow-2xl', active);
                            el.classList.toggle('scale-[1.02]', active);
                            el.classList.toggle('opacity-65', !active);
                        });
                        const start = Math.min(idx, maxIdx);
                        const step = cards[0].offsetWidth + (window.innerWidth < 640 ? 16 : 20);
                        track.style.transform = 'translateX(' + (-start * step) + 'px)';
                        if (bar) bar.style.width = ((idx + 1) / total * 100) + '%';
                        if (now) now.textContent = pad(idx + 1);
                    }

                    function layout() {
                        const step = cards[0].offsetWidth + (window.innerWidth < 640 ? 16 : 20);
                        const vw = track.parentElement.clientWidth;
                        maxIdx = Math.max(0, total - Math.max(1, Math.floor(vw / step)));
                        if (idx > maxIdx && maxIdx > 0) idx = maxIdx;
                        apply();
                    }

                    function restart() {
                        if (timer) clearInterval(timer);
                        if (total > 1) timer = setInterval(() => { idx = (idx + 1) % total; apply(); }, 7000);
                    }

                    prev?.addEventListener('click', () => { idx = (idx - 1 + total) % total; apply(); restart(); });
                    next?.addEventListener('click', () => { idx = (idx + 1) % total; apply(); restart(); });
                    cards.forEach((el, i) => el.addEventListener('click', () => { idx = i; apply(); restart(); }));

                    window.addEventListener('resize', layout);
                    layout();
                    restart();
                });
            </script>
        @endpush
    @endif

    {{-- ================= KOLEKSI KAOS FUNDRAISER (FASHION SHOWCASE CODED LAYOUT) ================= --}}
    @php
        // Desain unggulan: pilih yang berkas gambarnya benar-benar ada
        // (home kini tampil statis berupa gambar pakaian sesuai permintaan)
        $adaGambar = fn ($d) => $d && (file_exists(public_path('designs/clean-' . basename($d->gambar)))
            || file_exists(public_path($d->gambar)));
        $defaultDesign = $designs->first($adaGambar)
            ?? $designs->firstWhere('id', 1)
            ?? $designs->first();
    @endphp

    <section id="koleksi-kaos" class="relative overflow-hidden border-b border-stone-300/80 bg-[#edebe6] py-14 sm:py-20">
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            {{-- Header Pengantar --}}
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end sm:mb-10">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-900/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-widest text-stone-800">
                            ✦ Official Merch & Fundraiser
                        </span>
                        <span class="rounded-full bg-amber-500/15 border border-amber-500/30 px-2.5 py-0.5 text-[10px] font-bold text-amber-900">
                            100% Donasi Pembangunan
                        </span>
                    </div>
                    <h2 class="mt-2 font-display text-3xl font-black uppercase tracking-tight text-stone-900 sm:text-4xl">
                        Koleksi Kaos Pembangunan
                    </h2>
                    <p class="mt-1 text-xs text-stone-600 sm:text-sm">
                        Desain streetwear eksklusif jemaat • Pratinjau produk siap pesan — putar 360° di halaman katalog
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('design') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-stone-800 bg-stone-900 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-xs transition hover:bg-stone-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>Studio 360° Penuh &rarr;</span>
                    </a>
                </div>
            </div>

            {{-- ================= FASHION SHOWCASE CARD (PERSIS SEPERTI GAMBAR REFERENSI) ================= --}}
            <div class="relative mx-auto max-w-5xl overflow-hidden rounded-[2rem] sm:rounded-[2.5rem] border border-stone-800/20 bg-stone-900 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)]">
                {{-- Garis Frame Putih Tipis Inset (Sesuai Referensi Gambar) --}}
                <div class="pointer-events-none absolute inset-3 sm:inset-5 z-20 rounded-2xl sm:rounded-3xl border border-white/25"></div>

                {{-- Kontainer Split: Kiri Olive Green, Kanan Greyish Fog Atmosphere --}}
                <div class="relative grid grid-cols-1 md:grid-cols-12 min-h-[500px] sm:min-h-[540px] lg:min-h-[580px]">
                    {{-- 1. BAGIAN KIRI: SOLID OLIVE GREEN DENGAN TYPOGRAPHY & BADGE --}}
                    <div class="relative z-10 md:col-span-6 bg-[#4c5443] p-5 sm:p-7 lg:p-8 flex flex-col justify-between overflow-hidden">
                        {{-- Watermark Outlined Vertikal di Sisi Kiri (Sesuai Gambar "REGEN JACKET") --}}
                        <div class="pointer-events-none absolute left-3 top-8 bottom-8 select-none flex items-center justify-center opacity-30">
                            <span class="font-display text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-[0.2em] whitespace-nowrap"
                                style="-webkit-text-stroke: 1.5px rgba(255,255,255,0.4); color: transparent; writing-mode: vertical-rl; transform: rotate(180deg);">
                                KAOS JEMAAT
                            </span>
                        </div>

                        {{-- Konten Teks Kiri Atas: Hanya Label Seri & Judul Utama --}}
                        <div class="relative z-10 pl-8 sm:pl-10 max-w-xs sm:max-w-sm">
                            {{-- Label Seri (Sesuai "Iva™ | Trendy") --}}
                            <p class="font-mono text-[11px] sm:text-xs font-semibold tracking-wider text-white/75">
                                Edisi 2026 | Official Fundraiser
                            </p>

                            {{-- Judul Utama --}}
                            <h3 id="showcase-title" class="mt-1 font-display text-2xl sm:text-3xl font-black uppercase tracking-tight text-white leading-tight">
                                {{ preg_replace('/["\'“”]/', '', $defaultDesign->nama ?? 'Kaos Pembangunan') }}
                            </h3>
                        </div>

                        {{-- Konten Teks Kiri Bawah: Tabel Ukuran Tepat di Atas Teks Isi / Deskripsi --}}
                        <div class="relative z-10 pl-8 sm:pl-10 mt-4 max-w-xs sm:max-w-sm">
                            {{-- Tabel Panduan Ukuran (Size Chart) --}}
                            @php
                                $homeSizeChart = $defaultDesign ? $defaultDesign->getSizeChart() : ($programInfo ? $programInfo->getSizeChart() : \App\Models\ProgramInfo::defaultSizeChart());
                                $chartCols = $homeSizeChart['columns'] ?? ['Size', 'S', 'M', 'L', 'XL', 'XXL'];
                                $chartRows = $homeSizeChart['rows'] ?? [
                                    ['Lebar', '47', '50', '53', '56', '59'],
                                    ['Panjang', '67', '70', '73', '75', '77'],
                                ];
                                $chartTitle = $homeSizeChart['title'] ?? 'Tabel Ukuran (cm)';
                                $chartNote = $homeSizeChart['note'] ?? 'Toleransi ±1 cm';
                            @endphp
                            <div class="mb-2 sm:mb-2.5">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-white/90 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-amber-300">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                                        </svg>
                                        {{ $chartTitle }}
                                    </span>
                                    @if ($chartNote)
                                        <span class="text-[9px] font-semibold text-white/60">{{ $chartNote }}</span>
                                    @endif
                                </div>
                                <div class="overflow-x-auto rounded-xl border border-white/20 bg-black/30 backdrop-blur-xs shadow-xs">
                                    <table class="w-full text-center text-[10px] sm:text-[11px] text-white border-collapse">
                                        <thead>
                                            <tr class="border-b border-white/15 bg-black/35 font-black uppercase tracking-wider text-white/80">
                                                @foreach ($chartCols as $cIdx => $col)
                                                    <th class="py-1 px-1.5 {{ $cIdx === 0 ? 'text-left pl-2.5' : '' }} {{ $col === 'L' ? 'text-amber-300' : '' }}">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/10 font-mono font-semibold">
                                            @foreach ($chartRows as $row)
                                                <tr class="hover:bg-white/5 transition">
                                                    @foreach ($chartCols as $cIdx => $col)
                                                        @php $val = $row[$cIdx] ?? ''; @endphp
                                                        <td class="py-1 px-1.5 {{ $cIdx === 0 ? 'text-left pl-2.5 font-sans font-bold text-white/70' : 'text-white/90' }} {{ $col === 'L' ? 'font-bold text-amber-300' : '' }}">{{ $val }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Keterangan Bahan / Teks Isi Deskripsi --}}
                            <p id="showcase-desc" class="text-[11px] sm:text-xs text-white/80 leading-relaxed">
                                {{ $defaultDesign->deskripsi ?? 'Bahan 100% Combed Cotton adem, sablon premium tahan cuci.' }}
                            </p>
                        </div>
                    </div>

                    {{-- 2. BAGIAN KANAN: SOFT FOG / ATMOSPHERIC BACKDROP DENGAN GALLERY THUMBNAIL --}}
                    <div class="relative md:col-span-6 p-5 sm:p-7 lg:p-8 flex flex-col justify-between items-end overflow-hidden"
                        style="background: radial-gradient(circle at 75% 25%, #f6f5f1 0%, #e6e5df 50%, #cbcec5 100%);">
                        {{-- Logo Brand di Pojok Kanan Atas (Sesuai "LOOMIE FASHION") --}}
                        <div class="relative z-20">
                            <div class="border-y-2 border-stone-800 px-4 py-1 text-center">
                                <span class="block font-display text-xs sm:text-sm font-black tracking-[0.25em] text-stone-900 leading-tight uppercase">{{ $programInfo->nama_program ?? 'JEMAAT BALI' }}</span>
                                <span class="block font-mono text-[8px] sm:text-[9px] font-bold tracking-[0.3em] text-stone-600">OFFICIAL FUNDRAISER</span>
                            </div>
                        </div>

                        {{-- Galeri Thumbnail Kapsul Vertikal (Sesuai Bentuk Kapsul Melengkung di Gambar) --}}
                        <div class="relative z-20 my-auto py-3">
                            <div class="flex flex-col gap-2 sm:gap-2.5 rounded-[2.2rem] sm:rounded-[2.5rem] bg-stone-400/25 p-2 sm:p-2.5 border border-white/60 shadow-lg backdrop-blur-md w-24 sm:w-28 md:w-32">
                                @foreach ($designs as $idx => $design)
                                    @php
                                        $cleanImg = file_exists(public_path('designs/clean-' . basename($design->gambar)))
                                            ? 'designs/clean-' . basename($design->gambar)
                                            : $design->gambar;
                                        $isActive = $defaultDesign && ($design->id === $defaultDesign->id);
                                    @endphp
                                    @if (!file_exists(public_path($cleanImg)))
                                        {{-- Desain tanpa berkas gambar valid dilewati agar tidak menampilkan gambar rusak --}}
                                        @continue
                                    @endif
                                    <button type="button"
                                        class="design-thumb group relative flex flex-col items-center justify-center overflow-hidden rounded-2xl bg-white/95 p-1.5 shadow-xs transition-all duration-200 hover:scale-105 cursor-pointer {{ $isActive ? 'active ring-2 ring-stone-900 border-stone-900 scale-105' : 'border border-stone-200/60 opacity-80 hover:opacity-100' }}"
                                        data-id="{{ $design->id }}"
                                        data-nama="{{ preg_replace('/["\'“”]/', '', $design->nama) }}"
                                        data-harga="{{ $design->harga }}"
                                        data-deskripsi="{{ $design->deskripsi }}"
                                        data-img="{{ asset($cleanImg) }}"
                                        data-sizes="{{ json_encode($design->pilihanUkuran()) }}"
                                        title="{{ $design->nama }} — Rp {{ number_format($design->harga, 0, ',', '.') }}">
                                        <div class="relative h-14 w-full sm:h-16 flex items-center justify-center p-1">
                                            <img src="{{ asset($cleanImg) }}" alt="{{ $design->nama }}"
                                                class="h-full w-full object-contain drop-shadow-xs transition group-hover:scale-105">
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 3. HERO GARMENT DISPLAY (DI TENGAH-TENGAH PERBATASAN WARNA, UKURAN BESAR & SEIMBANG) --}}
                    <div class="pointer-events-none absolute inset-0 z-15 flex items-center justify-center">
                        <div id="hero-static-stage" class="pointer-events-none flex items-center justify-center md:translate-x-6 lg:translate-x-8">
                            {{-- Gambar Kaos Hero Transparan Dinamis (Diperbesar Sesuai Permintaan) --}}
                            <div class="relative w-72 sm:w-84 md:w-[26rem] lg:w-[29rem] transition-transform duration-300 flex items-center justify-center">
                                @php
                                    $defaultCleanImg = file_exists(public_path('designs/clean-' . basename($defaultDesign->gambar ?? '')))
                                        ? 'designs/clean-' . basename($defaultDesign->gambar)
                                        : ($defaultDesign->gambar ?? 'designs/kaos-bangun-bersama.svg');
                                @endphp
                                <img id="hero-shirt-img"
                                    src="{{ asset($defaultCleanImg) }}"
                                    alt="{{ $defaultDesign->nama ?? 'Kaos Pembangunan' }}"
                                    class="w-full h-auto max-h-[400px] sm:max-h-[440px] lg:max-h-[470px] object-contain drop-shadow-[0_25px_35px_rgba(0,0,0,0.4)] transition-all duration-300">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= LIVE INTERACTIVE ORDERING BAR ================= --}}
                <div class="border-t border-stone-800/30 bg-white/95 p-4 sm:p-6 backdrop-blur-md">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        {{-- Kiri: Detail Desain & Harga yang Aktif --}}
                        <div class="flex items-center gap-3.5">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-stone-900 text-white font-bold text-xs shadow-sm">
                                <span id="bar-design-id">#{{ $defaultDesign->id ?? 1 }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400">Desain Terpilih</span>
                                <h4 id="bar-design-name" class="font-display text-sm sm:text-base font-extrabold text-stone-900 leading-tight">
                                    {{ $defaultDesign->nama ?? 'Kaos Bangun Bersama' }}
                                </h4>
                                <p id="bar-design-sub" class="text-xs font-semibold text-stone-500">
                                    Rp {{ number_format($defaultDesign->harga ?? 150000, 0, ',', '.') }} • 100% Cotton 24s/30s
                                </p>
                            </div>
                        </div>

                        {{-- Tengah: Pemilih Ukuran & Jumlah --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-bold uppercase tracking-wider text-stone-600 mr-1">Size:</span>
                                @php
                                    $defaultSizes = $defaultDesign ? $defaultDesign->pilihanUkuran() : ['S', 'M', 'L', 'XL', 'XXL'];
                                    $initialActiveSize = in_array('L', $defaultSizes) ? 'L' : ($defaultSizes[0] ?? 'L');
                                @endphp
                                @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                    @php
                                        $isAvail = in_array($sz, $defaultSizes);
                                        $isSel = $sz === $initialActiveSize;
                                    @endphp
                                    <button type="button" data-size="{{ $sz }}"
                                        {{ $isAvail ? '' : 'disabled' }}
                                        title="{{ $sz }} — {{ $isAvail ? 'Tersedia' : 'Habis' }}"
                                        class="size-choice-btn grid h-9 w-9 place-items-center rounded-xl border text-xs font-black transition {{ !$isAvail ? 'border-stone-200 bg-stone-100 text-stone-300 line-through opacity-50 cursor-not-allowed' : ($isSel ? 'border-stone-900 bg-stone-900 text-white shadow-xs' : 'border-stone-300 bg-white text-stone-700 hover:border-stone-900') }}">
                                        {{ $sz }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="flex items-center rounded-xl border border-stone-300 bg-stone-50 px-2 py-1">
                                <button type="button" id="btn-qty-minus" aria-label="Kurangi"
                                    class="grid h-7 w-7 place-items-center rounded-lg text-sm font-black text-stone-600 transition hover:bg-stone-200 hover:text-stone-900">−</button>
                                <span id="display-qty" class="font-mono text-xs font-black text-stone-900 px-2">1</span>
                                <button type="button" id="btn-qty-plus" aria-label="Tambah"
                                    class="grid h-7 w-7 place-items-center rounded-lg text-sm font-black text-stone-600 transition hover:bg-stone-200 hover:text-stone-900">+</button>
                            </div>
                        </div>

                        {{-- Kanan: Total & Tombol Order Now --}}
                        <div class="flex items-center justify-between gap-4 border-t border-stone-200 pt-3 md:border-t-0 md:pt-0">
                            <div class="text-left md:text-right">
                                <span class="block text-[10px] font-semibold text-stone-500 uppercase tracking-wider">Total Donasi</span>
                                <span id="display-total-price" class="font-display text-lg sm:text-xl font-black text-stone-900">
                                    Rp {{ number_format($defaultDesign->harga ?? 150000, 0, ',', '.') }}
                                </span>
                            </div>

                            <a id="btn-order-checkout"
                                href="{{ $defaultDesign ? route('checkout.pemesanan', ['desain' => $defaultDesign->id, 'ukuran' => 'L', 'jumlah' => 1]) : route('checkout.pemesanan') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-xs font-black uppercase tracking-widest text-white shadow-md transition hover:bg-primary-dark hover:scale-[1.02] active:scale-95 sm:text-sm">
                                <span>Order Now</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const thumbs = document.querySelectorAll('.design-thumb');
                const heroImg = document.getElementById('hero-shirt-img');
                const showcaseTitle = document.getElementById('showcase-title');
                const showcaseDesc = document.getElementById('showcase-desc');
                const barDesignId = document.getElementById('bar-design-id');
                const barDesignName = document.getElementById('bar-design-name');
                const barDesignSub = document.getElementById('bar-design-sub');
                const displayTotalPrice = document.getElementById('display-total-price');
                const displayQty = document.getElementById('display-qty');
                const btnQtyMinus = document.getElementById('btn-qty-minus');
                const btnQtyPlus = document.getElementById('btn-qty-plus');
                const btnOrderCheckout = document.getElementById('btn-order-checkout');
                const sizeBtns = document.querySelectorAll('.size-choice-btn');

                if (!thumbs.length || !btnOrderCheckout) return;

                let activeId = '{{ $defaultDesign->id ?? 1 }}';
                let activeName = @json(preg_replace('/["\'“”]/', '', $defaultDesign->nama ?? 'Kaos Flame Streetwear'));
                let activePrice = {{ $defaultDesign->harga ?? 185000 }};
                let activeDesc = @json($defaultDesign->deskripsi ?? '');
                @php
                    $homeInitSizes = $defaultDesign ? $defaultDesign->pilihanUkuran() : ['S', 'M', 'L', 'XL', 'XXL'];
                @endphp
                let activeSizes = @json($homeInitSizes);
                let activeSize = activeSizes.includes('L') ? 'L' : (activeSizes[0] || 'L');
                let activeQty = 1;

                function formatRupiah(num) {
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                }

                function syncUI() {
                    if (showcaseTitle) showcaseTitle.textContent = activeName;
                    if (showcaseDesc) showcaseDesc.textContent = activeDesc;
                    if (barDesignId) barDesignId.textContent = '#' + activeId;
                    if (barDesignName) barDesignName.textContent = activeName;
                    if (barDesignSub) barDesignSub.textContent = formatRupiah(activePrice) + ' • 100% Cotton 24s/30s';
                    if (displayQty) displayQty.textContent = String(activeQty);
                    if (displayTotalPrice) displayTotalPrice.textContent = formatRupiah(activePrice * activeQty);

                    // Update link href ke form pemesanan
                    btnOrderCheckout.href = '{{ route("checkout.pemesanan") }}?desain=' + encodeURIComponent(activeId) +
                        '&ukuran=' + encodeURIComponent(activeSize) +
                        '&jumlah=' + encodeURIComponent(activeQty);

                    // Update tombol ukuran
                    sizeBtns.forEach(btn => {
                        const s = btn.dataset.size;
                        const isAvail = activeSizes.includes(s);
                        btn.disabled = !isAvail;
                        if (!isAvail) {
                            btn.className = 'size-choice-btn grid h-9 w-9 place-items-center rounded-xl border border-stone-200 bg-stone-100 text-xs font-black text-stone-300 line-through opacity-50 cursor-not-allowed transition';
                            btn.title = s + ' — Habis';
                        } else if (s === activeSize) {
                            btn.className = 'size-choice-btn grid h-9 w-9 place-items-center rounded-xl border border-stone-900 bg-stone-900 text-xs font-black text-white shadow-xs transition cursor-pointer';
                            btn.title = s + ' — Terpilih';
                        } else {
                            btn.className = 'size-choice-btn grid h-9 w-9 place-items-center rounded-xl border border-stone-300 bg-white text-xs font-black text-stone-700 hover:border-stone-900 transition cursor-pointer';
                            btn.title = s + ' — Tersedia';
                        }
                    });
                }

                // Klik thumbnail: ganti gambar kaos secara halus (100% statis sesuai permintaan)
                thumbs.forEach(thumb => {
                    thumb.addEventListener('click', function () {
                        thumbs.forEach(t => {
                            t.classList.remove('active', 'ring-2', 'ring-stone-900', 'border-stone-900', 'scale-105');
                            t.classList.add('border-stone-200/60', 'opacity-80');
                        });
                        this.classList.add('active', 'ring-2', 'ring-stone-900', 'border-stone-900', 'scale-105');
                        this.classList.remove('border-stone-200/60', 'opacity-80');

                        activeId = this.dataset.id;
                        activeName = this.dataset.nama;
                        activePrice = Number(this.dataset.harga || 150000);
                        activeDesc = this.dataset.deskripsi;

                        if (this.dataset.sizes) {
                            try {
                                activeSizes = JSON.parse(this.dataset.sizes);
                            } catch (e) {
                                activeSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                            }
                            if (!activeSizes.includes(activeSize) && activeSizes.length > 0) {
                                activeSize = activeSizes[0];
                            }
                        }

                        if (heroImg && this.dataset.img) {
                            heroImg.style.opacity = '0';
                            heroImg.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                heroImg.src = this.dataset.img;
                                heroImg.style.opacity = '1';
                                heroImg.style.transform = 'scale(1)';
                            }, 120);
                        }

                        syncUI();
                    });
                });

                // Pilih Ukuran
                sizeBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        if (this.disabled) return;
                        activeSize = this.dataset.size;
                        syncUI();
                    });
                });

                // Qty Stepper
                btnQtyMinus?.addEventListener('click', function () {
                    if (activeQty > 1) {
                        activeQty--;
                        syncUI();
                    }
                });

                btnQtyPlus?.addEventListener('click', function () {
                    if (activeQty < 99) {
                        activeQty++;
                        syncUI();
                    }
                });

                // Klik Order: Munculkan modal pilihan "Beli Kaos Saja" atau "Kaos + Donasi"
                btnOrderCheckout?.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (window.OrderChoiceModal) {
                        window.OrderChoiceModal.open({
                            name: activeName,
                            size: activeSize,
                            qty: activeQty,
                            price: activePrice,
                            designId: activeId,
                            imgUrl: heroImg ? heroImg.src : null,
                            baseUrl: '{{ route("checkout.pemesanan") }}'
                        });
                    } else {
                        window.location.href = this.href;
                    }
                });

                syncUI();
            });
        </script>
    @endpush

    {{-- CTA --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="relative overflow-hidden rounded-3xl border border-stone-200 bg-white p-8 text-center shadow-sm sm:p-12">
            <span class="absolute left-0 top-0 h-10 w-10 rounded-br-2xl border-b-4 border-r-4 border-primary"></span>
            <span class="absolute right-0 top-0 h-10 w-10 rounded-bl-2xl border-b-4 border-l-4 border-primary"></span>
            <span class="absolute bottom-0 left-0 h-10 w-10 rounded-tr-2xl border-r-4 border-t-4 border-primary"></span>
            <span class="absolute bottom-0 right-0 h-10 w-10 rounded-tl-2xl border-l-4 border-t-4 border-primary"></span>
            <h2 class="font-display text-2xl font-extrabold text-primary sm:text-3xl">Siap ikut membangun?</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-muted sm:text-base">
                Isi formulir donasi untuk mendukung pembangunan. Nama, nomor, email, dan nominal donasi
                sudah cukup — panitia yang menghubungi Anda.
            </p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('shop') }}" class="btn btn-primary !px-6 !py-3.5">Isi Formulir Donasi</a>
                <a href="{{ route('proposal') }}" class="btn btn-outline !px-6 !py-3.5">Lihat Proposal</a>
            </div>
        </div>
    </section>
    </div>

    {{-- Modal Pilihan: Beli Kaos Saja vs Kaos + Donasi --}}
    @include('components.order-choice-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Video hero: pastikan autoplay jalan (muted + playsinline sudah di tag video)
            const bgVideo = document.getElementById('hero-bg-video');
            if (bgVideo) {
                const p = bgVideo.play();
                if (p && p.catch) p.catch(function () { /* autoplay ditahan browser, abaikan */ });
            }

            // Sembunyikan video ketika user scroll melewati area hero
            // agar video fixed TIDAK PERNAH muncul di bagian bawah halaman / footer
            const heroSpacer = document.getElementById('hero-spacer');
            const heroVideo = document.getElementById('hero-video-wrapper');
            if (heroSpacer && heroVideo && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            heroVideo.style.visibility = 'visible';
                            heroVideo.style.opacity = '1';
                        } else {
                            heroVideo.style.visibility = 'hidden';
                            heroVideo.style.opacity = '0';
                        }
                    });
                }, { threshold: 0 });
                observer.observe(heroSpacer);
            }
        });
    </script>
@endpush
