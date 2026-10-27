{{-- ====================================================== --}}
{{-- KOMPONEN: Pilih Produk Kaos (Reusable) --}}
{{-- ====================================================== --}}
{{-- 
  Props:
  - $designs: Collection of Design models
  - $selectedId: int|null
  - $draft: array (normalized: [design_id][gender][size] = qty)
  - $mode: 'kaos_saja' | 'kaos_donasi'
  - $formAction: string
  - $showSidebar: bool
  - $sizeGuideRoute: string|null
--}}
@php
    $componentId = 'ks-' . uniqid('ks_');
    $jsonDesigns = $component->jsonDesigns();
    $jsonDraft = $component->jsonInitialDraft();
    $jsonSelectedId = $component->jsonSelectedId();
    $jsonMode = $component->jsonMode();
    $jsonConfig = $component->jsonConfig();
    $formAction = $component->formAction;
    $showSidebar = $component->showSidebar;
    $sizeGuideRoute = $component->sizeGuideRoute;
@endphp

{{-- ====================================================== --}}
{{-- INDIKATOR LANKAH (hanya untuk mode wizard) --}}
{{-- ====================================================== --}}
@if ($component->mode === 'kaos_donasi')
    <ol id="{{ $componentId }}-wizard-steps" class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-2xl border border-line bg-white px-4 py-2.5" role="navigation" aria-label="Langkah pemesanan">
        @foreach ([1 => 'Pilih Kaos', 2 => 'Data & Alamat', 3 => 'Donasi', 4 => 'Pembayaran'] as $n => $label)
            <li data-wz-ind="{{ $n }}" class="flex cursor-pointer items-center gap-1.5">
                <span data-wz-num class="grid h-6 w-6 place-items-center rounded-full text-[11px] font-extrabold">{{ $n }}</span>
                <span data-wz-label class="text-[11px] font-semibold uppercase tracking-wide">{{ $label }}</span>
            </li>
            @if ($n < 4)
                <li class="mx-1 h-px w-5 bg-line sm:w-8" aria-hidden="true"></li>
            @endif
        @endforeach
    </ol>
@endif

{{-- ====================================================== --}}
{{-- LANGKAH 1: PILIH PRODUK KAOS --}}
{{-- ====================================================== --}}
<section id="{{ $componentId }}-wz-step-1" class="space-y-6 {{ $component->mode === 'kaos_donasi' ? '' : '' }}" data-kaos-selector>
    <div class="card p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-secondary font-display text-sm font-extrabold text-white">1</span>
            <div>
                <h2 class="font-serif text-lg font-bold text-stone-900">Pilih Produk Kaos</h2>
                <p class="text-xs text-muted">Pilih warna, tentukan jumlah per ukuran, dan tambahkan ke pesanan.</p>
            </div>
        </div>

        @error('items')
            <div class="mt-4 rounded-xl border border-error/30 bg-error/5 p-3 text-sm font-medium text-error">{{ $message }}</div>
        @enderror
        <p id="{{ $componentId }}-step1-error" class="mt-4 hidden rounded-xl border border-error/30 bg-error/5 p-3 text-sm font-medium text-error">
            Pilih minimal satu kaos terlebih dahulu sebelum melanjutkan.
        </p>

        {{-- Kartu produk --}}
        <div id="{{ $componentId }}-product-cards" class="mt-5 grid grid-cols-3 gap-2.5 sm:grid-cols-5" role="list"></div>

        {{-- Detail produk terpilih + stepper ukuran --}}
        <div id="{{ $componentId }}-product-detail" class="mt-5" role="region" aria-label="Detail produk terpilih"></div>

        {{-- Produk lain (akordeon) --}}
        <div id="{{ $componentId }}-other-products" class="mt-5 space-y-3"></div>

        {{-- Tombol pemicu modal ukuran (tersembunyi, di-klik via delegasi) --}}
        <button type="button" id="{{ $componentId }}-size-guide-trigger" data-open-size-chart class="hidden" aria-hidden="true" tabindex="-1"></button>

        {{-- Strip kepercayaan --}}
        <div class="mt-6 grid gap-4 rounded-2xl border border-line bg-canvas/60 p-4 sm:grid-cols-3 sm:divide-x sm:divide-line">
            <div class="flex items-start gap-2.5 sm:pr-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60 60 0 0 1 19.5 0M.375 14.5h23.25m-22.875 8.375H23.25m-19.875-8.5h18.375a1.125 1.125 0 0 0 1.114-1.283l-.862-6.03a1.125 1.125 0 0 0-.962-.85l-13.5-1.5a1.125 1.125 0 0 0-1.204.846L4.875 8.25" />
                </svg>
                <div>
                    <p class="text-xs font-bold text-ink">Pengiriman ke seluruh Indonesia</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Estimasi 3&ndash;7 hari (tergantung lokasi).</p>
                </div>
            </div>
            <div class="flex items-start gap-2.5 sm:px-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.96 11.96 0 0 1 3.598 6 12 12 0 0 0 12 21.75 12 12 0 0 0 20.402 6 11.96 11.96 0 0 1 12 2.714Z" />
                </svg>
                <div>
                    <p class="text-xs font-bold text-ink">Pembayaran Aman</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Transfer bank, QRIS, atau e-wallet.</p>
                </div>
            </div>
            <div class="flex items-start gap-2.5 sm:pl-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-secondary" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                </svg>
                <div>
                    <p class="text-xs font-bold text-ink">Setiap Pembelian Berdampak</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-muted">Dukungan untuk pembangunan {{ $programInfo->nama_program ?? 'GMAHK Haleluya, Bali' }}.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================== --}}
{{-- LANKAH 2 & 3: Hanya untuk mode kaos_donasi (di-render di parent) --}}
{{-- ====================================================== --}}
@if ($component->mode === 'kaos_donasi')
    {{-- Placeholder untuk langkah 2 & 3 (dirender di parent view) --}}
@endif

{{-- ====================================================== --}}
{{-- HIDDEN INPUTS KERANJANG (dibangun ulang oleh JS) --}}
{{-- ====================================================== --}}
<div id="{{ $componentId }}-cart-hidden" aria-hidden="true"></div>

{{-- ====================================================== --}}
{{-- SIDEBAR RINGKASAN (opsional) --}}
{{-- ====================================================== --}}
@if ($showSidebar)
    <aside class="lg:col-span-4 order-first lg:order-none">
        <div class="card p-6 bg-white border border-stone-200 rounded-2xl">
            <h2 class="font-serif text-lg font-bold text-stone-900">Ringkasan Pesanan</h2>
            <div id="{{ $componentId }}-side-items" class="mt-4 space-y-3"></div>

            <dl class="mt-4 pt-4 border-t border-stone-200 space-y-2 text-xs sm:text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-stone-500">Subtotal (<span id="{{ $componentId }}-side-count">0</span> kaos)</dt>
                    <dd id="{{ $componentId }}-side-subtotal" class="font-bold text-stone-900">Rp 0</dd>
                </div>
                @if ($component->mode === 'kaos_donasi')
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-stone-500">Donasi <span class="text-stone-400">&middot; Langkah 3</span></dt>
                        <dd id="{{ $componentId }}-side-donasi" class="font-bold text-stone-900">Rp 0</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-4 rounded-xl bg-[#efede3] p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-serif text-sm font-bold text-stone-900">Total Pembayaran</span>
                    <span id="{{ $componentId }}-side-total" class="text-xl font-extrabold text-stone-900">Rp 0</span>
                </div>
                <button type="button" id="{{ $componentId }}-side-next-btn"
                    class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark px-5 py-3 text-sm font-bold text-white shadow-xs transition cursor-pointer">
                    @if ($component->mode === 'kaos_donasi')
                        Lanjut ke Data Pribadi &rarr;
                    @else
                        Lanjut ke Pengiriman &rarr;
                    @endif
                </button>
            </div>

            @if ($component->mode === 'kaos_donasi')
                <div class="mt-5 rounded-xl border border-line bg-white p-4">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                        </svg>
                        <p class="text-xs font-bold text-ink">Donasi</p>
                    </div>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">
                        Nominal donasi ditentukan di <span class="font-semibold text-ink">Langkah 3</span> (minimal Rp{{ number_format(config('donation.min_kaos_donasi'), 0, ',', '.') }}).
                    </p>
                </div>

                <p class="mt-4 text-xs text-stone-400 leading-relaxed">
                    Pembayaran dilakukan manual (transfer/QRIS). Panitia mengirim nomor rekening &amp; total final setelah pesanan terkirim.
                </p>
            @endif
        </div>
    </aside>
@endif

{{-- ====================================================== --}}
{{-- INIT SCRIPT (dipanggil oleh parent setelah DOM ready) --}}
{{-- ====================================================== --}}
@push('scripts')
<script>
    // Inisialisasi KaosSelector untuk {{ $componentId }}
    window.KaosSelector = window.KaosSelector || {};
    window.KaosSelector['{{ $componentId }}'] = {
        componentId: '{{ $componentId }}',
        designs: {{ $jsonDesigns }},
        initialDraft: {{ $jsonDraft }},
        selectedId: {{ $jsonSelectedId }},
        mode: {{ $jsonMode }},
        config: {{ $jsonConfig }},
    };
</script>
@endpush
