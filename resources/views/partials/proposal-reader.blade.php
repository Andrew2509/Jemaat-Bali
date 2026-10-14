{{-- Pembaca proposal mode buku digital. Dipakai di halaman Proposal & Home. Butuh $adaFile dan $url. --}}
    @if ($adaFile)
        <div class="bg-[#e9e7e2]">
            <div class="mx-auto max-w-6xl px-3 py-4 sm:px-6 sm:py-6">

                {{-- Bar kontrol atas: kiri prev + pil Hal, kanan zoom + next --}}
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="btn-prev-page" title="Halaman sebelumnya" aria-label="Halaman sebelumnya"
                            class="grid h-10 w-10 place-items-center rounded-full border border-stone-200 bg-white text-stone-900 shadow-sm transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-30 sm:h-11 sm:w-11">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <span class="rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-semibold tabular-nums text-stone-900 shadow-sm">
                            Hal. <span id="current-page-label">1–2</span><span id="total-pages-label" class="hidden">…</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2.5">
                        <button type="button" id="btn-spread" title="Tampilan 2 halaman"
                            class="grid h-10 w-10 place-items-center rounded-full border border-stone-200 bg-white text-stone-900 shadow-sm transition hover:scale-105 sm:h-11 sm:w-11 md:hidden">
                            <svg id="icon-spread-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                            <svg id="icon-spread-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="hidden h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </button>
                        <button type="button" id="btn-zoom-out" title="Perkecil"
                            class="grid h-10 w-10 place-items-center rounded-full bg-white text-xl leading-none text-stone-900 shadow transition hover:scale-105 sm:h-11 sm:w-11">−</button>
                        <span id="zoom-label" class="rounded-full border border-stone-200 bg-white px-4 py-2 font-mono text-sm font-semibold text-stone-900 shadow-sm">100%</span>
                        <button type="button" id="btn-zoom-in" title="Perbesar"
                            class="grid h-10 w-10 place-items-center rounded-full bg-white text-xl leading-none text-stone-900 shadow transition hover:scale-105 sm:h-11 sm:w-11">+</button>
                        <button type="button" id="btn-next-page" title="Halaman berikutnya" aria-label="Halaman berikutnya"
                            class="grid h-10 w-10 place-items-center rounded-full border border-stone-200 bg-white text-stone-900 shadow-sm transition hover:scale-105 disabled:cursor-not-allowed disabled:opacity-30 sm:h-11 sm:w-11">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Panggung buku --}}
                <div id="book-stage" class="relative mt-4 flex justify-center">
                    <div id="pdf-loading" class="flex flex-col items-center gap-3 py-24">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-stone-300 border-t-stone-800"></div>
                        <p class="text-xs font-semibold text-stone-600">Memuat halaman dokumen…</p>
                    </div>

                    <div id="book" class="relative hidden w-full max-w-5xl">
                        <div id="book-inner" class="relative flex items-stretch justify-center">
                            {{-- Halaman kiri --}}
                            <div id="page-left-wrap"
                                class="book-page relative flex w-full max-w-[560px] cursor-pointer flex-col overflow-hidden rounded-l-lg rounded-r-none bg-white">
                                <div class="flex items-center justify-between px-5 pt-5 sm:px-8 sm:pt-6">
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#b05525] text-sm font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                                        <span class="text-sm font-extrabold tracking-tight text-stone-900 sm:text-base">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                                    </div>
                                    <span class="text-xs font-medium text-stone-500 sm:text-sm">Hal. <span id="label-num-left">1</span></span>
                                </div>
                                <div class="flex-1 px-5 py-4 sm:px-8">
                                    <canvas id="canvas-page-left" class="block h-auto w-full rounded-sm"></canvas>
                                </div>
                                <div class="mt-auto flex items-center justify-between px-5 pb-5 text-xs font-medium text-stone-500 sm:px-8 sm:pb-6">
                                    <span>{{ $programInfo->judul_proposal ?? 'Proposal Pembangunan' }}</span>
                                    <span id="footer-num-left">1</span>
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-black/10 to-transparent"></div>
                            </div>

                            {{-- Halaman kanan (desktop saja) --}}
                            <div id="page-right-wrap"
                                class="book-page relative hidden w-full max-w-[560px] cursor-pointer flex-col overflow-hidden rounded-l-none rounded-r-lg bg-white md:flex">
                                <div class="flex items-center justify-between px-5 pt-5 sm:px-8 sm:pt-6">
                                    <span class="text-xs font-medium text-stone-500 sm:text-sm">Hal. <span id="label-num-right">2</span></span>
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#b05525] text-sm font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                                        <span class="text-sm font-extrabold tracking-tight text-stone-900 sm:text-base">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 px-5 py-4 sm:px-8">
                                    <canvas id="canvas-page-right" class="block h-auto w-full rounded-sm"></canvas>
                                </div>
                                <div class="mt-auto flex items-center justify-between px-5 pb-5 text-xs font-medium text-stone-500 sm:px-8 sm:pb-6">
                                    <span>{{ $programInfo->judul_proposal ?? 'Proposal Pembangunan' }}</span>
                                    <span id="footer-num-right">2</span>
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 left-0 w-6 bg-gradient-to-r from-black/10 to-transparent"></div>
                            </div>

                            {{-- Lipatan tengah --}}
                            <div id="book-spine" class="pointer-events-none absolute inset-y-0 left-1/2 z-10 hidden w-[2px] -translate-x-1/2 bg-stone-300/80 md:block"></div>
                            <div id="book-spine-soft" class="pointer-events-none absolute inset-y-0 left-1/2 z-10 hidden w-8 -translate-x-1/2 bg-gradient-to-r from-black/15 via-black/25 to-black/15 blur-[1px] md:block"></div>

                            {{-- Daun halaman yang berputar saat flip --}}
                            <div id="flip-layer">
                                <div id="flip-shadow"></div>
                                <div id="flip-leaf">
                                    <div class="flip-face" id="flip-front">
                                        <img id="flip-front-img" alt="">
                                        <div class="flip-shade" id="flip-front-shade"></div>
                                    </div>
                                    <div class="flip-face" id="flip-back">
                                        <img id="flip-back-img" alt="">
                                        <div class="flip-shade"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="bg-[#e9e7e2]">
            <div class="mx-auto max-w-6xl px-3 py-10 sm:px-6">
                <div class="mx-auto max-w-xl rounded-2xl border-2 border-dashed border-stone-300 bg-white p-10 text-center shadow-sm">
                    <h3 class="font-display text-xl font-bold text-stone-900">File Proposal Belum Tersedia</h3>
                    <p class="mt-2 text-sm text-stone-600">Dokumen belum diunggah. Silakan unggah berkas PDF proposal untuk ditampilkan sebagai buku digital.</p>
                    <button type="button" id="btn-empty-upload"
                        class="mt-6 inline-flex items-center gap-2 rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-800">
                        Pilih &amp; Unggah File PDF
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal unggah (pengurus, tidak ditonjolkan di UI baca) --}}
    <div id="upload-modal" class="form-font fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-xs" role="dialog" aria-modal="true">
        <div class="w-full max-w-lg rounded-3xl border border-stone-200 bg-white p-6 shadow-2xl sm:p-8">
            <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                <h3 class="font-serif text-lg font-bold text-stone-900">Unggah Berkas Proposal PDF</h3>
                <button type="button" id="close-upload-modal" class="grid h-8 w-8 place-items-center rounded-full text-stone-400 transition hover:bg-stone-100 hover:text-stone-700">✕</button>
            </div>
            <form action="{{ route('proposal.upload') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                <div id="dropzone" class="relative cursor-pointer rounded-2xl border-2 border-dashed border-stone-300 bg-stone-50/50 p-8 text-center transition hover:border-stone-500">
                    <input type="file" name="file" id="pdf-file-input" accept=".pdf,application/pdf" required class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                    <div id="dropzone-prompt">
                        <p class="text-sm font-semibold text-stone-800">Klik untuk memilih file PDF atau tarik ke sini</p>
                        <p class="mt-1 text-xs text-stone-500">Format .PDF hingga maksimal 25 MB</p>
                    </div>
                    <div id="file-selected-info" class="hidden text-left">
                        <div class="flex items-center gap-3 rounded-xl border border-stone-200 bg-white p-3">
                            <div class="grid h-10 w-10 place-items-center rounded-lg bg-red-100 text-xs font-bold text-red-600">PDF</div>
                            <div class="min-w-0 flex-1">
                                <p id="selected-filename" class="truncate text-xs font-bold text-stone-900">file.pdf</p>
                                <p id="selected-filesize" class="text-[11px] text-stone-500">0 KB</p>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600">Siap diunggah</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-4">
                    <button type="button" id="cancel-upload" class="rounded-full px-5 py-2.5 text-sm font-semibold text-stone-600 transition hover:bg-stone-100">Batal</button>
                    <button type="submit" class="rounded-full bg-stone-900 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800">Unggah &amp; Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="mx-auto max-w-6xl px-3 sm:px-6">
            <div class="mx-auto mt-4 max-w-2xl rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-bold">Gagal mengunggah file:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

@push('scripts')
    <style>
        .book-page { transform-origin: center; }
        .book-turn-next { animation: turnNext .45s cubic-bezier(.2, .7, .3, 1); }
        .book-turn-prev { animation: turnPrev .45s cubic-bezier(.2, .7, .3, 1); }
        /* Lapisan flip seperti buku fisik */
        #book-inner { perspective: 2200px; }
        #flip-layer { position: absolute; inset: 0; z-index: 30; pointer-events: none; display: none; perspective: 2200px; }
        #flip-layer.active { display: block; }
        #flip-leaf { position: absolute; top: 0; bottom: 0; transform-style: preserve-3d; background: #fff; box-shadow: 0 18px 45px -18px rgba(0,0,0,.55); }
        #flip-leaf.flip-next { left: 50%; right: 0; transform-origin: left center; border-radius: 0 8px 8px 0; }
        #flip-leaf.flip-prev { left: 0; right: 50%; transform-origin: right center; border-radius: 8px 0 0 8px; }
        #flip-leaf.flip-single { left: 0; right: 0; transform-origin: left center; border-radius: 8px; }
        .flip-face { position: absolute; inset: 0; overflow: hidden; background: #fff; backface-visibility: hidden; -webkit-backface-visibility: hidden; }
        .flip-face img { width: 100%; height: 100%; object-fit: fill; display: block; }
        #flip-back { transform: rotateY(180deg); }
        .flip-shade { position: absolute; inset: 0; pointer-events: none; background: linear-gradient(to right, rgba(0,0,0,.28), rgba(0,0,0,0) 35%); }
        #flip-back .flip-shade { background: linear-gradient(to left, rgba(0,0,0,.28), rgba(0,0,0,0) 35%); }
        #flip-shadow { position: absolute; top: 4%; bottom: 4%; left: 50%; width: 12%; transform: translateX(-50%); background: radial-gradient(ellipse at center, rgba(0,0,0,.35) 0%, rgba(0,0,0,0) 70%); opacity: 0; }
        @keyframes turnNext {
            0% { transform: perspective(1400px) rotateY(-7deg) translateX(10px); opacity: .55; }
            100% { transform: perspective(1400px) rotateY(0deg) translateX(0); opacity: 1; }
        }
        @keyframes turnPrev {
            0% { transform: perspective(1400px) rotateY(7deg) translateX(-10px); opacity: .55; }
            100% { transform: perspective(1400px) rotateY(0deg) translateX(0); opacity: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
            .book-turn-next, .book-turn-prev { animation: none; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.pdfjsLib) {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            const pdfUrl = @json($url ?? '');
            const hasFile = @json($adaFile ?? false);

            const uploadModal = document.getElementById('upload-modal');
            const emptyUploadBtn = document.getElementById('btn-empty-upload');
            const closeUploadBtn = document.getElementById('close-upload-modal');
            const cancelUploadBtn = document.getElementById('cancel-upload');
            const fileInput = document.getElementById('pdf-file-input');
            function openModal() { if (!uploadModal) return; uploadModal.classList.remove('hidden'); uploadModal.classList.add('flex'); }
            function closeModal() { if (!uploadModal) return; uploadModal.classList.add('hidden'); uploadModal.classList.remove('flex'); }
            emptyUploadBtn?.addEventListener('click', openModal);
            closeUploadBtn?.addEventListener('click', closeModal);
            cancelUploadBtn?.addEventListener('click', closeModal);
            uploadModal?.addEventListener('click', function (e) { if (e.target === uploadModal) closeModal(); });
            fileInput?.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    document.getElementById('selected-filename').textContent = this.files[0].name;
                    document.getElementById('selected-filesize').textContent = (this.files[0].size / 1024).toFixed(1) + ' KB';
                    document.getElementById('dropzone-prompt').classList.add('hidden');
                    document.getElementById('file-selected-info').classList.remove('hidden');
                }
            });

            if (!hasFile || !pdfUrl || !window.pdfjsLib) return;

            let pdfDoc = null;
            let currentPage = 1;
            let totalPages = 1;
            let currentZoom = 1.0;
            let isRendering = false;
            const mqDesktop = window.matchMedia('(min-width: 768px)');
            // Desktop selalu 2 halaman. Di HP default 1 halaman (bisa beralih ke 2 halaman via tombol).
            let forceSpread = null;
            try {
                const saved = localStorage.getItem('proposal-spread');
                if (saved === 'spread') forceSpread = true;
                else if (saved === 'single') forceSpread = false;
            } catch (err) {}
            const isSpread = () => (mqDesktop.matches ? true : (forceSpread === true)) && totalPages > 1;
            function pageContentWidth(spread) {
                const inner = document.getElementById('book-inner');
                const w = (inner && inner.clientWidth) || document.getElementById('book-stage')?.clientWidth || 640;
                if (!spread) return Math.min(620, w - 32);
                return Math.max(110, Math.floor(w / 2) - 56);
            }

            const loadingEl = document.getElementById('pdf-loading');
            const bookEl = document.getElementById('book');
            const pageLabel = document.getElementById('current-page-label');
            const totalLabel = document.getElementById('total-pages-label');
            const prevBtn = document.getElementById('btn-prev-page');
            const nextBtn = document.getElementById('btn-next-page');
            const zoomInBtn = document.getElementById('btn-zoom-in');
            const zoomOutBtn = document.getElementById('btn-zoom-out');
            const zoomLabel = document.getElementById('zoom-label');
            const canvasLeft = document.getElementById('canvas-page-left');
            const canvasRight = document.getElementById('canvas-page-right');
            const wrapLeft = document.getElementById('page-left-wrap');
            const wrapRight = document.getElementById('page-right-wrap');
            const labelNumLeft = document.getElementById('label-num-left');
            const labelNumRight = document.getElementById('label-num-right');
            const footerNumLeft = document.getElementById('footer-num-left');
            const footerNumRight = document.getElementById('footer-num-right');
            const spine = document.getElementById('book-spine');
            const spineSoft = document.getElementById('book-spine-soft');

            function renderPageToCanvas(pageNum, canvas, targetWidth) {
                if (!pdfDoc || pageNum > totalPages || pageNum < 1 || !canvas) return Promise.resolve();
                return pdfDoc.getPage(pageNum).then(function (page) {
                    const base = page.getViewport({ scale: 1.0 });
                    const scale = (targetWidth / base.width) * currentZoom;
                    const viewport = page.getViewport({ scale: scale });
                    const outputScale = window.devicePixelRatio || 1;
                    canvas.width = Math.floor(viewport.width * outputScale);
                    canvas.height = Math.floor(viewport.height * outputScale);
                    canvas.style.width = Math.floor(viewport.width) + 'px';
                    canvas.style.height = Math.floor(viewport.height) + 'px';
                    const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;
                    return page.render({ canvasContext: canvas.getContext('2d'), transform: transform, viewport: viewport }).promise;
                });
            }

            function playFlip(direction) {
                [wrapLeft, wrapRight].forEach(function (el) {
                    if (!el) return;
                    el.classList.remove('book-turn-next', 'book-turn-prev');
                    void el.offsetWidth;
                    el.classList.add(direction === 'next' ? 'book-turn-next' : 'book-turn-prev');
                });
            }

            function setNumbers(leftNum, rightNum) {
                if (labelNumLeft) labelNumLeft.textContent = leftNum;
                if (footerNumLeft) footerNumLeft.textContent = leftNum;
                if (rightNum) {
                    if (labelNumRight) labelNumRight.textContent = rightNum;
                    if (footerNumRight) footerNumRight.textContent = rightNum;
                    pageLabel.textContent = leftNum + '–' + rightNum;
                } else {
                    pageLabel.textContent = String(leftNum);
                }
            }

            function renderSpread(direction) {
                if (!pdfDoc || isRendering) return;
                isRendering = true;
                const spread = isSpread() && totalPages > 1;

                if (spread) {
                    wrapRight.classList.remove('hidden');
                    wrapRight.style.display = '';
                    spine?.classList.remove('hidden');
                    spineSoft?.classList.remove('hidden');
                    wrapLeft.classList.add('rounded-l-lg', 'rounded-r-none');
                    wrapLeft.classList.remove('rounded-lg');
                } else {
                    wrapRight.classList.add('hidden');
                    wrapRight.style.display = 'none';
                    spine?.classList.add('hidden');
                    spineSoft?.classList.add('hidden');
                    wrapLeft.classList.remove('rounded-l-lg', 'rounded-r-none');
                    wrapLeft.classList.add('rounded-lg');
                }

                const jobs = [];
                if (!spread) {
                    const w = pageContentWidth(false);
                    jobs.push(renderPageToCanvas(currentPage, canvasLeft, w));
                    setNumbers(currentPage, null);
                } else {
                    const leftNum = currentPage;
                    const rightNum = currentPage + 1 <= totalPages ? currentPage + 1 : null;
                    jobs.push(renderPageToCanvas(leftNum, canvasLeft, pageContentWidth(true)));
                    setNumbers(leftNum, rightNum);
                    if (rightNum) {
                        wrapRight.style.visibility = 'visible';
                        jobs.push(renderPageToCanvas(rightNum, canvasRight, pageContentWidth(true)));
                    } else {
                        wrapRight.style.visibility = 'hidden';
                    }
                }

                Promise.all(jobs).then(function () {
                    if (totalLabel) totalLabel.textContent = totalPages;
                    const atStart = currentPage <= 1;
                    const atEnd = spread ? (currentPage + 1 >= totalPages) : (currentPage >= totalPages);
                    prevBtn.disabled = atStart;
                    nextBtn.disabled = atEnd;
                    zoomLabel.textContent = Math.round(currentZoom * 100) + '%';
                    if (direction) playFlip(direction);
                    isRendering = false;
                }).catch(function () { isRendering = false; });
            }

            let isFlipping = false;
            const flipLayer = document.getElementById('flip-layer');
            const flipLeaf = document.getElementById('flip-leaf');
            const flipFrontImg = document.getElementById('flip-front-img');
            const flipBackImg = document.getElementById('flip-back-img');
            const flipShadow = document.getElementById('flip-shadow');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function snapshotOf(canvas) {
                try {
                    if (canvas && canvas.width > 0) return canvas.toDataURL('image/png');
                } catch (err) { /* abaikan, pakai fallback */ }
                return null;
            }

            function renderPageToDataURL(pageNum, targetWidth) {
                if (!pdfDoc || pageNum > totalPages || pageNum < 1) return Promise.resolve(null);
                const tmp = document.createElement('canvas');
                return renderPageToCanvas(pageNum, tmp, targetWidth).then(function () {
                    try { return tmp.toDataURL('image/png'); } catch (err) { return null; }
                }).catch(function () { return null; });
            }

            // Animasi daun buku: front = halaman lama, back = halaman baru
            function runBookFlip(mode, frontURL, backURL) {
                return new Promise(function (resolve) {
                    if (reduceMotion || !frontURL || !backURL || !flipLayer || !flipLeaf) { resolve(false); return; }
                    flipFrontImg.src = frontURL;
                    flipBackImg.src = backURL;
                    flipLeaf.classList.remove('flip-next', 'flip-prev', 'flip-single');
                    const isSingle = mode === 'single-next' || mode === 'single-prev';
                    flipLeaf.classList.add(isSingle ? 'flip-single' : (mode === 'prev' ? 'flip-prev' : 'flip-next'));
                    flipLayer.classList.add('active');

                    const isPrev = mode === 'prev' || mode === 'single-prev';
                    const from = 'rotateY(0deg)';
                    const to = isPrev ? 'rotateY(180deg)' : 'rotateY(-180deg)';
                    const leafAnim = flipLeaf.animate(
                        [{ transform: from }, { transform: to }],
                        { duration: 750, easing: 'cubic-bezier(.35,.55,.25,1)', fill: 'forwards' }
                    );
                    const shadeAnim = flipShadow ? flipShadow.animate(
                        [{ opacity: 0 }, { opacity: 1, offset: 0.5 }, { opacity: 0 }],
                        { duration: 750, easing: 'ease-in-out', fill: 'forwards' }
                    ) : null;
                    leafAnim.onfinish = function () {
                        setTimeout(function () {
                            flipLayer.classList.remove('active');
                            flipLeaf.getAnimations().forEach(function (a) { try { a.cancel(); } catch (e) {} });
                            resolve(true);
                        }, 30);
                    };
                    leafAnim.oncancel = function () { resolve(false); };
                });
            }

            function fallbackNav(target, dir) {
                currentPage = target;
                renderSpread(dir);
                isFlipping = false;
            }

            // Mode 1 halaman (HP): geser biasa, tanpa efek buka buku
            function paintCanvasFromURL(canvas, url) {
                return new Promise(function (resolve) {
                    const img = new Image();
                    img.onload = function () {
                        try {
                            canvas.width = img.naturalWidth;
                            canvas.height = img.naturalHeight;
                            canvas.style.width = '100%';
                            canvas.style.height = 'auto';
                            canvas.getContext('2d').drawImage(img, 0, 0);
                            resolve(true);
                        } catch (err) { resolve(false); }
                    };
                    img.onerror = function () { resolve(false); };
                    img.src = url;
                });
            }

            function syncSingleChrome() {
                setNumbers(currentPage, null);
                if (totalLabel) totalLabel.textContent = totalPages;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
                zoomLabel.textContent = Math.round(currentZoom * 100) + '%';
            }

            function runSingleSlide(dir, oldURL, newURL, target) {
                if (reduceMotion || !oldURL || !newURL || !wrapLeft) {
                    currentPage = target;
                    renderSpread();
                    isFlipping = false;
                    return;
                }
                const outX = dir === 'next' ? '-56px' : '56px';
                const inX = dir === 'next' ? '56px' : '-56px';
                const out = wrapLeft.animate(
                    [{ transform: 'translateX(0)', opacity: 1 }, { transform: 'translateX(' + outX + ')', opacity: 0 }],
                    { duration: 170, easing: 'ease-in', fill: 'forwards' }
                );
                out.onfinish = function () {
                    paintCanvasFromURL(canvasLeft, newURL).then(function (ok) {
                        if (!ok) {
                            try { out.cancel(); } catch (e) {}
                            currentPage = target;
                            renderSpread();
                            isFlipping = false;
                            return;
                        }
                        currentPage = target;
                        syncSingleChrome();
                        try { out.cancel(); } catch (e) {}
                        const inn = wrapLeft.animate(
                            [{ transform: 'translateX(' + inX + ')', opacity: 0 }, { transform: 'translateX(0)', opacity: 1 }],
                            { duration: 230, easing: 'ease-out' }
                        );
                        inn.onfinish = function () { isFlipping = false; };
                        inn.oncancel = function () { isFlipping = false; };
                    });
                };
                out.oncancel = function () { isFlipping = false; };
            }

            function goToPrev() {
                const spread = isSpread() && totalPages > 1;
                const step = spread ? 2 : 1;
                if (currentPage <= 1 || isFlipping || isRendering) return;
                const target = Math.max(1, currentPage - step);
                if (reduceMotion) { currentPage = target; renderSpread('prev'); return; }

                isFlipping = true;
                prevBtn.disabled = true;
                nextBtn.disabled = true;

                if (!spread) {
                    const frontURL = snapshotOf(canvasLeft);
                    const w = pageContentWidth(false);
                    renderPageToDataURL(target, w).then(function (backURL) {
                        runSingleSlide('prev', frontURL, backURL, target);
                    });
                } else {
                    // Lembar dibalikkan kembali ke kanan: depan = hal. kiri lama, belakang = hal. kanan tujuan
                    const frontURL = snapshotOf(canvasLeft);
                    renderPageToDataURL(target + 1, pageContentWidth(true)).then(function (targetRightURL) {
                        if (!frontURL || !targetRightURL) { fallbackNav(target, 'prev'); return; }
                        runBookFlip('prev', frontURL, targetRightURL).then(function () {
                            currentPage = target;
                            renderSpread();
                            isFlipping = false;
                        });
                    });
                }
            }
            function goToNext() {
                const spread = isSpread() && totalPages > 1;
                const step = spread ? 2 : 1;
                if (currentPage >= totalPages || isFlipping || isRendering) return;
                if (spread && currentPage + 1 >= totalPages && currentPage === totalPages) return;
                const target = Math.min(totalPages, currentPage + step);
                if (reduceMotion) { currentPage = target; renderSpread('next'); return; }

                isFlipping = true;
                prevBtn.disabled = true;
                nextBtn.disabled = true;

                if (!spread) {
                    const frontURL = snapshotOf(canvasLeft);
                    const w = pageContentWidth(false);
                    renderPageToDataURL(target, w).then(function (backURL) {
                        runSingleSlide('next', frontURL, backURL, target);
                    });
                } else {
                    // Lembar 2/3 dibalik ke kiri: depan = hal. kanan lama, belakang = hal. kiri baru
                    const frontURL = snapshotOf(canvasRight) || snapshotOf(canvasLeft);
                    renderPageToDataURL(target, pageContentWidth(true)).then(function (backURL) {
                        if (!frontURL || !backURL) { fallbackNav(target, 'next'); return; }
                        runBookFlip('next', frontURL, backURL).then(function () {
                            currentPage = target;
                            renderSpread();
                            isFlipping = false;
                        });
                    });
                }
            }

            prevBtn?.addEventListener('click', goToPrev);
            nextBtn?.addEventListener('click', goToNext);
            wrapLeft?.addEventListener('click', function (e) {
                if (!isSpread()) {
                    const r = wrapLeft.getBoundingClientRect();
                    const x = (e.clientX - r.left) / r.width;
                    if (x < 0.25) goToPrev(); else goToNext();
                }
            });
            wrapRight?.addEventListener('click', goToNext);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') goToPrev();
                if (e.key === 'ArrowRight') goToNext();
            });

            zoomInBtn?.addEventListener('click', function () {
                if (currentZoom < 1.8) { currentZoom = Math.round((currentZoom + 0.15) * 100) / 100; renderSpread(); }
            });
            zoomOutBtn?.addEventListener('click', function () {
                if (currentZoom > 0.7) { currentZoom = Math.round((currentZoom - 0.15) * 100) / 100; renderSpread(); }
            });

            // Tombol paksa 1 halaman / 2 halaman (mis. tampilkan 2 halaman di HP)
            const spreadBtn = document.getElementById('btn-spread');
            const iconSpread1 = document.getElementById('icon-spread-1');
            const iconSpread2 = document.getElementById('icon-spread-2');
            function syncSpreadIcon() {
                const spread = isSpread();
                iconSpread1?.classList.toggle('hidden', spread);
                iconSpread2?.classList.toggle('hidden', !spread);
                spreadBtn?.setAttribute('title', spread ? 'Tampilan 1 halaman' : 'Tampilan 2 halaman');
            }
            spreadBtn?.addEventListener('click', function () {
                if (isFlipping || isRendering || mqDesktop.matches) return;
                forceSpread = !isSpread();
                try { localStorage.setItem('proposal-spread', forceSpread ? 'spread' : 'single'); } catch (err) {}
                if (forceSpread && currentPage % 2 === 0) currentPage = Math.max(1, currentPage - 1);
                syncSpreadIcon();
                renderSpread();
            });

            if (mqDesktop.addEventListener) {
                mqDesktop.addEventListener('change', function () {
                    if (mqDesktop.matches && currentPage % 2 === 0) currentPage = Math.max(1, currentPage - 1);
                    syncSpreadIcon();
                    renderSpread();
                });
            }
            let resizeT;
            window.addEventListener('resize', function () {
                clearTimeout(resizeT);
                resizeT = setTimeout(function () { renderSpread(); }, 200);
            });

            window.pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
                pdfDoc = pdf;
                totalPages = pdf.numPages;
                loadingEl?.classList.add('hidden');
                bookEl?.classList.remove('hidden');
                syncSpreadIcon();
                renderSpread();
            }).catch(function (err) {
                console.warn('Gagal memuat PDF:', err);
                if (loadingEl) loadingEl.innerHTML = '<p class="text-sm font-semibold text-red-700">Dokumen tidak dapat dimuat. Coba muat ulang halaman.</p>';
            });
        });
    </script>
@endpush
