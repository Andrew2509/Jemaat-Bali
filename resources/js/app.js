/**
 * Base bundle: hanya berisi bootstrap ringan yang dijalankan di semua halaman.
 * Modul berat (QRIS renderer, Product360Viewer) dimuat secara lazy hanya bila
 * elemennya benar-benar ada di halaman, sehingga Total Blocking Time turun.
 */

function bootQrisRenderer() {
    if (!document.querySelector('[data-qris-code]')) {
        return;
    }

    import('./components/QrisRenderer').then(({ initQrisRenderer }) => {
        initQrisRenderer();
    });
}

function bootProduct360() {
    if (!document.querySelector('.product-360-viewer-container')) {
        return;
    }

    import('./components/Product360Viewer').then(({ Product360Viewer }) => {
        window.Product360Viewer = Product360Viewer;
        document.dispatchEvent(new CustomEvent('product-360-ready'));
    });
}

function bootKaosSelector() {
    if (!document.querySelector('[data-kaos-selector]')) {
        return;
    }

    import('./components/KaosSelector').then(({ initKaosSelector }) => {
        initKaosSelector();
    });
}

function boot() {
    bootQrisRenderer();
    bootProduct360();
    bootKaosSelector();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
