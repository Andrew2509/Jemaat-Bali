/**
 * Product360Viewer.js
 * 
 * Komponen Reusable 360° Interactive Product Viewer
 * Menggunakan elemen <video> dengan scrubbing durasi (maju / mundur) untuk memutar produk.
 * Memuat video ke Blob URL in-memory (hanya ~270 KB) agar scrubbing instan bebas hambatan jaringan.
 * 
 * Fitur:
 * - Scrubbing video halus (gerakan ke kanan memajukan durasi, gerakan ke kiri memundurkan)
 * - Circular loop (0s s/d duration, 0° s/d 360°)
 * - Pointer Events terpadu (mouse desktop & touchscreen swipe mobile)
 * - Touch-action 'pan-y' agar scroll vertikal halaman mobile tidak terganggu
 * - Kontrol tombol: -10°, +10°, Reset 0°, Auto Spin (Play/Pause)
 * - Keyboard navigation (ArrowLeft, ArrowRight, Home, Space)
 * - High performance, hardware-accelerated decoding via GPU
 */

export class Product360Viewer {
    /**
     * @param {HTMLElement|string} container - Elemen container atau selector CSS
     * @param {Object} options - Konfigurasi viewer
     * @param {string} options.productName - Nama produk
     * @param {string} [options.videoMp4] - URL file MP4 360
     * @param {string} [options.videoWebm] - URL file WebM 360
     * @param {string} [options.imagePath] - Path folder aset gambar (fallback)
     * @param {string} [options.fallbackImage] - URL gambar fallback statis
     * @param {number} [options.initialAngle=0] - Sudut awal (0-360)
     * @param {number} [options.sensitivity=1.0] - Sensitivitas scrubbing
     * @param {boolean} [options.showHint=true] - Tampilkan petunjuk putar
     * @param {boolean} [options.showControls=true] - Tampilkan tombol kontrol
     */
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        if (!this.container) {
            console.error('[Product360Viewer] Container element tidak ditemukan:', container);
            return;
        }

        this.options = Object.assign({
            productName: 'Produk Kaos',
            videoMp4: '',
            videoWebm: '',
            imagePath: '',
            fallbackImage: '',
            initialAngle: 0,
            sensitivity: 1.0,
            showHint: true,
            showControls: true,
            autoplayIntro: true,
            onAngleChange: null,
        }, options);

        // Auto-detect path video standar jika belum disediakan
        if (!this.options.videoMp4 && this.options.imagePath) {
            this.options.videoMp4 = this.options.imagePath.replace(/\/+$/, '') + '/product_360.mp4';
            this.options.videoWebm = this.options.imagePath.replace(/\/+$/, '') + '/product_360.webm';
        }

        // State internal
        this.duration = 2.0;
        this.currentAngle = this.options.initialAngle || 0;
        this.isDragging = false;
        this.startX = 0;
        this.startTime = 0;
        this.lastX = 0;
        this.activePointerId = null;
        this.isAutoSpinning = false;
        this.hasUserInteracted = false;
        this.isDestroyed = false;
        this.pendingRaf = null;
        this.blobUrl = null;

        // Inisialisasi DOM dan event
        this._initDOM();
        this._bindEvents();
        this._loadVideoMedia();
    }

    /**
     * Membangun struktur DOM Video Viewer
     * @private
     */
    _initDOM() {
        this.container.innerHTML = '';
        this.container.classList.add('product-360-viewer', 'relative', 'w-full', 'select-none');

        // Card Wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'group relative flex aspect-square w-full items-center justify-center overflow-hidden rounded-2xl border border-line bg-white shadow-xs transition-shadow duration-300 hover:shadow-md';
        this.wrapper.style.cursor = 'grab';
        this.wrapper.style.touchAction = 'pan-y'; // Mencegah konflik scroll vertikal pada mobile
        this.wrapper.setAttribute('tabindex', '0');
        this.wrapper.setAttribute('role', 'region');
        this.wrapper.setAttribute('aria-label', `Pratinjau Video 360 derajat produk ${this.options.productName}`);

        // Elemen Video
        this.video = document.createElement('video');
        this.video.className = 'product-360-video h-full w-full object-contain pointer-events-none select-none';
        this.video.setAttribute('playsinline', '');
        this.video.setAttribute('webkit-playsinline', '');
        this.video.setAttribute('muted', '');
        this.video.muted = true;
        this.video.loop = true;
        this.video.preload = 'auto';
        this.video.setAttribute('disablePictureInPicture', '');

        this.wrapper.appendChild(this.video);

        // Badge 360° Mode (Kiri Atas)
        this.badge360 = document.createElement('div');
        this.badge360.className = 'absolute top-3 left-3 z-10 flex items-center gap-1.5 rounded-full border border-stone-800/10 bg-white/90 px-2.5 py-1 text-[11px] font-bold text-stone-800 shadow-xs backdrop-blur-xs transition-opacity duration-300';
        this.badge360.innerHTML = `
            <svg class="h-3.5 w-3.5 text-primary animate-spin-slow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-1.19"/>
            </svg>
            <span>360° VIDEO</span>
        `;
        this.wrapper.appendChild(this.badge360);

        // Badge Sudut Rotasi (Kanan Atas)
        this.angleBadge = document.createElement('div');
        this.angleBadge.className = 'absolute top-3 right-3 z-10 rounded-full border border-stone-800/10 bg-stone-900/80 px-2.5 py-0.5 font-mono text-[11px] font-bold text-white shadow-xs backdrop-blur-xs';
        this.angleBadge.textContent = `${this.currentAngle}°`;
        this.wrapper.appendChild(this.angleBadge);

        // Petunjuk Geser / Hint (Bawah Tengah Video)
        if (this.options.showHint) {
            this.hint = document.createElement('div');
            this.hint.className = 'product-360-hint pointer-events-none absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 items-center gap-2 rounded-full border border-stone-800/10 bg-white/95 px-3 py-1.5 text-xs font-semibold text-stone-700 shadow-md backdrop-blur-xs transition-all duration-300';
            this.hint.innerHTML = `
                <svg class="h-4 w-4 text-primary animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m9 18-6-6 6-6M15 6l6 6-6 6"/>
                </svg>
                <span>Geser kiri/kanan untuk putar 360°</span>
            `;
            this.wrapper.appendChild(this.hint);
        }

        // Loading Spinner Bar
        this.loadingBar = document.createElement('div');
        this.loadingBar.className = 'absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/80 backdrop-blur-xs transition-opacity duration-300';
        this.loadingBar.innerHTML = `
            <div class="h-8 w-8 animate-spin rounded-full border-3 border-stone-200 border-t-primary"></div>
            <span class="mt-3 text-xs font-medium text-stone-500">Memuat Video 360°...</span>
        `;
        this.wrapper.appendChild(this.loadingBar);

        this.container.appendChild(this.wrapper);

        // Tombol Kontrol Bawah (Reset, Step, Auto Spin)
        if (this.options.showControls) {
            this.controlsWrap = document.createElement('div');
            this.controlsWrap.className = 'mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-medium';
            this.controlsWrap.innerHTML = `
                <button type="button" class="btn-step-prev flex items-center gap-1 rounded-lg border border-line bg-white px-2.5 py-1.5 text-stone-700 transition hover:bg-stone-50 hover:text-stone-900 active:scale-95 shadow-2xs">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                    <span>-10°</span>
                </button>
                <button type="button" class="btn-auto-spin flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-1.5 text-stone-800 transition hover:bg-stone-50 active:scale-95 shadow-2xs font-semibold">
                    <svg class="icon-play h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    <svg class="icon-pause hidden h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    <span class="label-spin">Auto Spin</span>
                </button>
                <button type="button" class="btn-reset rounded-lg border border-line bg-white px-2.5 py-1.5 text-stone-600 transition hover:bg-stone-50 hover:text-stone-900 active:scale-95 shadow-2xs">
                    Reset 0°
                </button>
                <button type="button" class="btn-step-next flex items-center gap-1 rounded-lg border border-line bg-white px-2.5 py-1.5 text-stone-700 transition hover:bg-stone-50 hover:text-stone-900 active:scale-95 shadow-2xs">
                    <span>+10°</span>
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            `;
            this.container.appendChild(this.controlsWrap);
        }
    }

    /**
     * Memuat video ke Blob URL in-memory
     * @private
     */
    async _loadVideoMedia() {
        const videoSrc = this.options.videoMp4 || this.options.videoWebm;
        if (!videoSrc) {
            this._showErrorFallback();
            return;
        }

        try {
            const resp = await fetch(videoSrc);
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const blob = await resp.blob();

            if (this.blobUrl) {
                URL.revokeObjectURL(this.blobUrl);
            }
            this.blobUrl = URL.createObjectURL(blob);
            this.video.src = this.blobUrl;
            this.video.load();
        } catch (err) {
            console.warn('[Product360Viewer] Gagal memuat Blob URL, menggunakan direct src:', err);
            this.video.src = videoSrc;
            this.video.load();
        }
    }

    /**
     * Menghubungkan seluruh event handler
     * @private
     */
    _bindEvents() {
        // Video Metadata & Readiness
        this.video.addEventListener('loadedmetadata', () => {
            if (this.video.duration && !isNaN(this.video.duration)) {
                this.duration = this.video.duration;
            }
            this._hideLoading();
            this._applyAngle(this.options.initialAngle || 0);

            if (this.options.autoplayIntro && !this.hasUserInteracted && !this.isAutoSpinning) {
                this._startAutoSpin();
            }
        });

        this.video.addEventListener('canplay', () => {
            this._hideLoading();
            if (this.options.autoplayIntro && !this.hasUserInteracted && !this.isAutoSpinning) {
                this._startAutoSpin();
            }
        });

        this.video.addEventListener('error', (e) => {
            console.error('[Product360Viewer] Video error:', e);
            this._showErrorFallback();
        });

        // Update badge dan sudut saat video diputar (Auto Spin)
        this.video.addEventListener('timeupdate', () => {
            if (!this.isDragging) {
                const dur = this.duration || this.video.duration || 2.0;
                const angle = Math.round(((this.video.currentTime / dur) * 360) % 360);
                this._updateAngleBadge(angle);
            }
        });

        // Pointer Events (Mouse, Touch, Stylus)
        this.wrapper.addEventListener('pointerdown', this._handlePointerDown.bind(this));
        this.wrapper.addEventListener('pointermove', this._handlePointerMove.bind(this));
        window.addEventListener('pointermove', this._handlePointerMove.bind(this));
        this.wrapper.addEventListener('pointerup', this._handlePointerUp.bind(this));
        window.addEventListener('pointerup', this._handlePointerUp.bind(this));
        this.wrapper.addEventListener('pointercancel', this._handlePointerUp.bind(this));
        window.addEventListener('pointercancel', this._handlePointerUp.bind(this));

        // Touch Events explicitly for mobile swipe support
        this.wrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length !== 1) return;
            this._stopAutoSpin();
            this.isDragging = true;
            this.startX = e.touches[0].clientX;
            this.startTime = this.video.currentTime || 0;
            this._dismissHint();
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (!this.isDragging || e.touches.length !== 1) return;
            const deltaX = e.touches[0].clientX - this.startX;
            const width = this.wrapper.clientWidth || 400;
            const dur = this.duration || this.video.duration || 2.0;
            const timeDelta = (deltaX / width) * dur * (this.options.sensitivity || 1.0);
            let nextTime = this.startTime + timeDelta;
            while (nextTime < 0) nextTime += dur;
            while (nextTime >= dur) nextTime -= dur;
            try {
                this.video.currentTime = nextTime;
            } catch (_) {}
            const angle = Math.round(((nextTime / dur) * 360) % 360);
            this._updateAngleBadge(angle);
        }, { passive: true });

        window.addEventListener('touchend', () => {
            this.isDragging = false;
        }, { passive: true });

        // Mencegah drag bawaan browser
        this.wrapper.addEventListener('dragstart', (e) => e.preventDefault());

        // Keyboard navigation
        this.wrapper.addEventListener('keydown', (e) => {
            const stepDuration = (this.duration || 2.0) / 36;
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this._scrubRelative(-stepDuration);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this._scrubRelative(stepDuration);
            } else if (e.key === 'Home') {
                e.preventDefault();
                this.reset();
            } else if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                this.toggleAutoSpin();
            }
        });

        // Tombol Kontrol Bawah
        if (this.controlsWrap) {
            const btnPrev = this.controlsWrap.querySelector('.btn-step-prev');
            const btnNext = this.controlsWrap.querySelector('.btn-step-next');
            const btnReset = this.controlsWrap.querySelector('.btn-reset');
            const btnSpin = this.controlsWrap.querySelector('.btn-auto-spin');

            const stepDur = () => (this.duration || 2.0) / 36;

            if (btnPrev) {
                btnPrev.addEventListener('click', () => {
                    this._stopAutoSpin();
                    this._scrubRelative(-stepDur());
                });
            }

            if (btnNext) {
                btnNext.addEventListener('click', () => {
                    this._stopAutoSpin();
                    this._scrubRelative(stepDur());
                });
            }

            if (btnReset) {
                btnReset.addEventListener('click', () => {
                    this._stopAutoSpin();
                    this.reset();
                });
            }

            if (btnSpin) {
                btnSpin.addEventListener('click', () => {
                    this.toggleAutoSpin();
                });
            }
        }
    }

    /**
     * Handle Pointer Down
     * @private
     */
    _handlePointerDown(e) {
        if (e.pointerType === 'mouse' && e.button !== 0) return;

        this._stopAutoSpin();
        this.isDragging = true;
        this.activePointerId = e.pointerId;
        this.startX = e.clientX;
        this.lastX = e.clientX;
        this.startTime = this.video.currentTime || 0;

        this.wrapper.style.cursor = 'grabbing';

        if (typeof this.wrapper.setPointerCapture === 'function') {
            try {
                this.wrapper.setPointerCapture(e.pointerId);
            } catch (_) {}
        }

        this._dismissHint();
    }

    /**
     * Handle Pointer Move (Scrubbing Video Durasi)
     * Menggeser ke kanan -> memajukan durasi video (maju)
     * Menggeser ke kiri -> memundurkan durasi video (mundur)
     * @private
     */
    _handlePointerMove(e) {
        if (!this.isDragging) return;
        if (this.activePointerId !== null && e.pointerId !== this.activePointerId) return;

        const deltaX = e.clientX - this.startX;
        const width = this.wrapper.clientWidth || 400;
        const dur = this.duration || this.video.duration || 2.0;

        // 1 kali swipe penuh selebar kontainer = 1 putaran penuh (durasi video)
        // deltaX > 0 (geser ke kanan) -> durasi maju; deltaX < 0 (geser ke kiri) -> durasi mundur
        const timeDelta = (deltaX / width) * dur * (this.options.sensitivity || 1.0);
        let nextTime = this.startTime + timeDelta;

        // Circular time wrapping
        while (nextTime < 0) nextTime += dur;
        while (nextTime >= dur) nextTime -= dur;

        // Set currentTime langsung (sangat cepat pada blob memory)
        try {
            this.video.currentTime = nextTime;
        } catch (_) {}

        const angle = Math.round(((nextTime / dur) * 360) % 360);
        this._updateAngleBadge(angle);

        this.lastX = e.clientX;
    }

    /**
     * Handle Pointer Up
     * @private
     */
    _handlePointerUp(e) {
        if (!this.isDragging) return;
        if (this.activePointerId !== null && e.pointerId !== this.activePointerId) return;

        this.isDragging = false;
        this.wrapper.style.cursor = 'grab';

        if (this.activePointerId !== null && typeof this.wrapper.releasePointerCapture === 'function') {
            try {
                this.wrapper.releasePointerCapture(this.activePointerId);
            } catch (_) {}
        }
        this.activePointerId = null;
    }

    /**
     * Memajukan atau memundurkan durasi video secara relatif
     * @param {number} deltaSeconds
     * @private
     */
    _scrubRelative(deltaSeconds) {
        const dur = this.duration || this.video.duration || 2.0;
        let nextTime = (this.video.currentTime || 0) + deltaSeconds;

        while (nextTime < 0) nextTime += dur;
        while (nextTime >= dur) nextTime -= dur;

        try {
            this.video.currentTime = nextTime;
        } catch (_) {}

        const angle = Math.round(((nextTime / dur) * 360) % 360);
        this._updateAngleBadge(angle);
    }

    /**
     * Menerapkan sudut rotasi tertentu (0 - 360) ke video
     * @param {number} angle 
     * @private
     */
    _applyAngle(angle) {
        const dur = this.duration || this.video.duration || 2.0;
        const safeAngle = ((angle % 360) + 360) % 360;
        const targetTime = (safeAngle / 360) * dur;

        try {
            this.video.currentTime = targetTime;
        } catch (_) {}

        this._updateAngleBadge(safeAngle);
    }

    /**
     * Memperbarui tampilan badge sudut rotasi
     * @param {number} angle 
     * @private
     */
    _updateAngleBadge(angle) {
        this.currentAngle = angle;
        if (this.angleBadge) {
            this.angleBadge.textContent = `${angle}°`;
        }
        if (typeof this.options.onAngleChange === 'function') {
            this.options.onAngleChange(angle);
        }
    }

    /**
     * Toggle Auto Spin (Play / Pause Video)
     */
    toggleAutoSpin() {
        if (this.isAutoSpinning) {
            this._stopAutoSpin();
        } else {
            this._startAutoSpin();
        }
    }

    /**
     * Mulai Auto Spin
     * @private
     */
    _startAutoSpin() {
        this.isAutoSpinning = true;

        if (this.controlsWrap) {
            this.controlsWrap.querySelector('.icon-play')?.classList.add('hidden');
            this.controlsWrap.querySelector('.icon-pause')?.classList.remove('hidden');
            const lbl = this.controlsWrap.querySelector('.label-spin');
            if (lbl) lbl.textContent = 'Jeda';
        }

        if (this.badge360) {
            this.badge360.innerHTML = `
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-bold text-stone-900">360° VIDEO PLAYING</span>
            `;
        }

        const playPromise = this.video.play();
        if (playPromise !== undefined) {
            playPromise.catch((err) => {
                console.warn('[Product360Viewer] Auto-spin interrupted:', err);
                this._stopAutoSpin();
            });
        }
    }

    /**
     * Berhenti Auto Spin
     * @private
     */
    _stopAutoSpin() {
        this.isAutoSpinning = false;
        this.video.pause();

        if (this.controlsWrap) {
            this.controlsWrap.querySelector('.icon-play')?.classList.remove('hidden');
            this.controlsWrap.querySelector('.icon-pause')?.classList.add('hidden');
            const lbl = this.controlsWrap.querySelector('.label-spin');
            if (lbl) lbl.textContent = 'Auto Spin';
        }

        if (this.badge360) {
            this.badge360.innerHTML = `
                <svg class="h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-1.19"/>
                </svg>
                <span>360° VIDEO</span>
            `;
        }
    }

    /**
     * Reset produk ke sudut tampak depan (0° / 0 detik)
     */
    reset() {
        this._stopAutoSpin();
        this._applyAngle(0);
    }

    /**
     * Menghilangkan hint petunjuk setelah user berinteraksi
     * @private
     */
    _dismissHint() {
        if (this.hasUserInteracted) return;
        this.hasUserInteracted = true;
        if (this.hint) {
            this.hint.style.opacity = '0';
            setTimeout(() => {
                this.hint?.remove();
                this.hint = null;
            }, 300);
        }
    }

    /**
     * Sembunyikan loading bar saat video siap
     * @private
     */
    _hideLoading() {
        if (this.loadingBar) {
            this.loadingBar.style.opacity = '0';
            setTimeout(() => {
                this.loadingBar?.remove();
                this.loadingBar = null;
            }, 300);
        }
    }

    /**
     * Fallback gambar jika video bermasalah
     * @private
     */
    _showErrorFallback() {
        this._hideLoading();
        if (this.options.fallbackImage) {
            const fallbackImg = document.createElement('img');
            fallbackImg.src = this.options.fallbackImage;
            fallbackImg.alt = this.options.productName;
            fallbackImg.className = 'h-full w-full object-contain';
            this.wrapper.innerHTML = '';
            this.wrapper.appendChild(fallbackImg);
        }
    }

    /**
     * Perbarui video produk ketika memilih varian atau produk lain
     * @param {Object} newConfig 
     */
    updateProduct(newConfig) {
        this._stopAutoSpin();
        this.options = Object.assign(this.options, newConfig);

        if (!this.options.videoMp4 && this.options.imagePath) {
            this.options.videoMp4 = this.options.imagePath.replace(/\/+$/, '') + '/product_360.mp4';
            this.options.videoWebm = this.options.imagePath.replace(/\/+$/, '') + '/product_360.webm';
        }

        this._loadVideoMedia();
    }

    /**
     * Bersihkan resource saat komponen di-unmount
     */
    destroy() {
        this.isDestroyed = true;
        this._stopAutoSpin();
        if (this.pendingRaf) {
            cancelAnimationFrame(this.pendingRaf);
        }
        if (this.blobUrl) {
            URL.revokeObjectURL(this.blobUrl);
            this.blobUrl = null;
        }
        this.container.innerHTML = '';
    }
}
