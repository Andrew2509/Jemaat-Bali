@extends('layouts.app')

@section('title', ($programInfo->judul_proposal ?? 'Proposal Pembangunan') . ' — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Lihat presentasi dan dokumen resmi proposal program ' . ($programInfo->nama_program ?? 'Jemaat Bali') . '.')

@section('content')
    @php
        $isCanvaActive = !empty($isCanva) && !empty($canvaEmbedUrl);
    @endphp

    @if ($isCanvaActive)
        {{-- ================= MODE CANVA EMBED ================= --}}
        <div id="canva-view" class="bg-[#f5f3ef] border-b border-stone-200">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:py-10">
                
                {{-- Header Bar Canva --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-0.5 text-xs font-bold uppercase tracking-wider text-purple-800">
                                ✦ Presentasi Canva Interaktif
                            </span>
                            @if ($adaFile)
                                <button type="button" id="btn-toggle-to-pdf" class="text-xs font-semibold text-stone-500 hover:text-stone-900 underline transition cursor-pointer">
                                    Beralih ke Buku PDF &rarr;
                                </button>
                            @endif
                        </div>
                        <h1 class="mt-2 font-display text-2xl font-extrabold text-stone-900 sm:text-3xl">
                            {{ $programInfo->judul_proposal ?? 'Proposal Pembangunan' }}
                        </h1>
                        <p class="mt-1 text-xs sm:text-sm text-stone-500">
                            {{ $programInfo->nama_program ?? 'Jemaat Bali' }} &middot; Geser atau klik panah untuk membaca setiap halaman proposal.
                        </p>
                    </div>

                    {{-- Action button: Fullscreen --}}
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="btn-fullscreen-canva" title="Layar Penuh"
                            class="inline-flex items-center gap-2 rounded-full border border-stone-300 bg-white px-4 py-2 text-xs font-bold text-stone-800 shadow-sm transition hover:bg-stone-50 hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                            </svg>
                            <span>Layar Penuh</span>
                        </button>
                    </div>
                </div>

                {{-- Canva Frame Container --}}
                <div id="canva-wrapper" class="relative w-full aspect-[16/9] min-h-[460px] sm:min-h-[580px] md:min-h-[680px] lg:min-h-[760px] overflow-hidden rounded-2xl sm:rounded-3xl border border-stone-200 bg-stone-950 shadow-2xl">
                    <iframe id="canva-frame"
                        src="{{ $canvaEmbedUrl }}"
                        title="{{ $programInfo->judul_proposal ?? 'Proposal Pembangunan' }}"
                        loading="lazy"
                        allow="autoplay; fullscreen"
                        allowfullscreen="allowfullscreen"
                        class="absolute inset-0 h-full w-full border-0">
                    </iframe>
                </div>

                {{-- Info Bawah & Tombol Navigasi Cepat --}}
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
                    <div>
                        <h3 class="font-serif text-base font-bold text-stone-900">Ingin Berpartisipasi Mendukung Program?</h3>
                        <p class="mt-0.5 text-xs text-stone-500">Anda dapat langsung berdonasi atau memesan kaos resmi pembangunan.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="{{ route('shop') }}" class="inline-flex items-center rounded-xl bg-[#b55322] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition hover:bg-[#9a4216]">
                            Donasi Sekarang &rarr;
                        </a>
                        <a href="{{ route('design') }}" class="inline-flex items-center rounded-xl border border-stone-300 bg-white px-4 py-2 text-xs font-bold uppercase tracking-wider text-stone-800 transition hover:bg-stone-50">
                            Pesan Kaos
                        </a>
                        <a href="{{ $programInfo->wa_url ?? '#' }}" target="_blank" class="inline-flex items-center rounded-xl border border-stone-300 bg-white px-4 py-2 text-xs font-bold uppercase tracking-wider text-stone-800 transition hover:bg-stone-50">
                            Tanya Panitia
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Jika ada file PDF, sediakan container PDF reader yang bisa di-toggle --}}
        @if ($adaFile)
            <div id="pdf-view" class="hidden">
                <div class="bg-stone-100 border-b border-stone-200 px-4 py-3">
                    <div class="mx-auto max-w-6xl flex items-center justify-between">
                        <button type="button" id="btn-toggle-to-canva" class="text-xs font-semibold text-purple-700 hover:text-purple-900 underline transition cursor-pointer">
                            &larr; Kembali ke Presentasi Canva
                        </button>
                    </div>
                </div>
                @include('partials.proposal-reader')
            </div>
        @endif

    @else
        {{-- ================= MODE BUKU DIGITAL PDF BIASA ================= --}}
        @include('partials.proposal-reader')
    @endif

    @if ($adaFile && session('success'))
        <div class="mx-auto max-w-6xl px-3 sm:px-6">
            <div class="mx-auto mt-4 flex max-w-2xl items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-200 text-xs font-bold">✓</span>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var canvaWrap = document.getElementById('canva-wrapper');
            var btnFs = document.getElementById('btn-fullscreen-canva');
            var canvaView = document.getElementById('canva-view');
            var pdfView = document.getElementById('pdf-view');
            var btnToPdf = document.getElementById('btn-toggle-to-pdf');
            var btnToCanva = document.getElementById('btn-toggle-to-canva');

            if (btnFs && canvaWrap) {
                btnFs.addEventListener('click', function () {
                    if (!document.fullscreenElement) {
                        canvaWrap.requestFullscreen().catch(function () {
                            var frame = document.getElementById('canva-frame');
                            if (frame && frame.requestFullscreen) frame.requestFullscreen();
                        });
                    } else {
                        document.exitFullscreen();
                    }
                });
            }

            if (btnToPdf && pdfView && canvaView) {
                btnToPdf.addEventListener('click', function () {
                    canvaView.classList.add('hidden');
                    pdfView.classList.remove('hidden');
                    window.dispatchEvent(new Event('resize'));
                });
            }

            if (btnToCanva && pdfView && canvaView) {
                btnToCanva.addEventListener('click', function () {
                    pdfView.classList.add('hidden');
                    canvaView.classList.remove('hidden');
                });
            }
        });
    </script>
@endpush
