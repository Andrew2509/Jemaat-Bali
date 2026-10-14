@props([
    'productName' => 'Produk Kaos',
    'imagePath' => asset('images/products/flame-shirt'),
    'videoMp4' => asset('images/products/flame-shirt/product_360.mp4'),
    'videoWebm' => asset('images/products/flame-shirt/product_360.webm'),
    'frameCount' => 36,
    'frameInterval' => 10,
    'initialAngle' => 0,
    'sensitivity' => 1.0,
    'showHint' => true,
    'showControls' => true,
    'fallbackImage' => null,
    'id' => 'product-360-viewer-' . uniqid(),
])

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'product-360-viewer-container w-full select-none']) }}
    data-product-name="{{ $productName }}"
    data-image-path="{{ $imagePath }}"
    data-video-mp4="{{ $videoMp4 }}"
    data-video-webm="{{ $videoWebm }}"
    data-frame-count="{{ $frameCount }}"
    data-frame-interval="{{ $frameInterval }}"
    data-initial-angle="{{ $initialAngle }}"
    data-sensitivity="{{ $sensitivity }}"
    data-show-hint="{{ $showHint ? 'true' : 'false' }}"
    data-show-controls="{{ $showControls ? 'true' : 'false' }}"
    @if ($fallbackImage) data-fallback-image="{{ $fallbackImage }}" @endif>
    {{-- Komponen Product360Viewer JavaScript akan menginisialisasi video viewer dan kontrol interaktif di sini --}}
</div>

@pushOnce('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.product-360-viewer-container').forEach(function (el) {
            if (el.dataset.initialized) return;
            el.dataset.initialized = 'true';

            if (typeof window.Product360Viewer === 'function') {
                new window.Product360Viewer(el, {
                    productName: el.dataset.productName || 'Produk Kaos',
                    imagePath: el.dataset.imagePath || '',
                    videoMp4: el.dataset.videoMp4 || '',
                    videoWebm: el.dataset.videoWebm || '',
                    fallbackImage: el.dataset.fallbackImage || '',
                    initialAngle: parseInt(el.dataset.initialAngle || '0', 10),
                    sensitivity: parseFloat(el.dataset.sensitivity || '1.0'),
                    showHint: el.dataset.showHint !== 'false',
                    showControls: el.dataset.showControls !== 'false',
                });
            }
        });
    });
</script>
@endPushOnce
