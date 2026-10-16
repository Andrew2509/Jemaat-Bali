import './bootstrap';
import { Product360Viewer } from './components/Product360Viewer';
import { initQrisRenderer } from './components/QrisRenderer';

// Daftarkan secara global ke window untuk kemudahan pemanggilan di Blade views
window.Product360Viewer = Product360Viewer;
window.initQrisRenderer = initQrisRenderer;

function bootQrisRenderer() {
    initQrisRenderer();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootQrisRenderer);
} else {
    bootQrisRenderer();
}
