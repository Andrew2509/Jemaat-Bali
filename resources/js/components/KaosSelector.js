// resources/js/components/KaosSelector.js
window.KaosSelector = window.KaosSelector || {};

window.KaosSelector.init = function (spec) {
    const {
        componentId,
        designs,
        initialDraft,
        selectedId,
        mode,
        config,
    } = spec;

    const cmpId = componentId;
    const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    const num = function (n) { return Number(n || 0).toLocaleString('id-ID'); };

    const productCardsEl = document.getElementById(`${cmpId}-product-cards`);
    const productDetailEl = document.getElementById(`${cmpId}-product-detail`);
    const heroSpacer = document.getElementById('hero-spacer');
    const heroVideoWrapper = document.getElementById('hero-video-wrapper');

    if (!productCardsEl || !productDetailEl) return;

    // Inisialisasi tinggi hero berdasarkan tinggi spacer (agar tidak ketutup konten)
    function syncHeroHeight() {
        if (heroSpacer) {
            heroVideoWrapper.style.height = heroSpacer.offsetHeight + 'px';
        }
    };

    syncHeroHeight();
    window.addEventListener('resize', syncHeroHeight);

    // Video hero: pastikan autoplay jalan (muted + playsinline sudah di tag video)
    const bgVideo = document.getElementById('hero-bg-video');
    if (bgVideo) {
        const p = bgVideo.play();
        if (p && p.catch) p.catch(function () { /* autoplay ditahan browser, abaikan */ });
    }

    // Sembunyikan video ketika user scroll melewati area hero
    // agar video fixed TIDAK PERNAH muncul di bagian bawah halaman / footer
    if (heroSpacer && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    heroVideoWrapper.style.visibility = 'visible';
                    heroVideoWrapper.style.opacity = '1';
                } else {
                    heroVideoWrapper.style.visibility = 'hidden';
                    heroVideoWrapper.style.opacity = '0';
                }
            });
        }, { threshold: 0 });
        observer.observe(heroSpacer);
    }
};