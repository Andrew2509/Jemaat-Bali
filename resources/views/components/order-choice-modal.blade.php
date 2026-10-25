{{-- Modal Pilihan: Beli Kaos Saja vs Kaos + Donasi --}}
<div id="order-choice-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-choice-title">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity" id="order-modal-backdrop"></div>

    {{-- Modal Card --}}
    <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl border border-stone-200 bg-white p-5 sm:p-6 shadow-2xl transition-all">
        {{-- Header: Judul & Tombol Tutup --}}
        <div class="flex items-start justify-between gap-3 border-b border-stone-100 pb-3.5">
            <div>
                <h3 id="modal-choice-title" class="text-base sm:text-lg font-bold text-stone-900 font-sans tracking-tight">
                    Pilih Cara Pemesanan
                </h3>
                <p class="text-xs text-stone-500 mt-0.5">
                    Tentukan apakah ingin memesan kaos saja atau beserta donasi
                </p>
            </div>
            <button type="button" id="order-modal-close"
                class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer"
                aria-label="Tutup">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        {{-- Pratinjau Produk --}}
        <div class="my-4 flex items-center gap-3 rounded-xl bg-stone-50/80 p-3 border border-stone-200/70">
            <div class="h-12 w-12 shrink-0 rounded-lg bg-white border border-stone-200 p-1 flex items-center justify-center overflow-hidden">
                <img id="modal-shirt-img" src="" alt="Kaos" class="h-full w-full object-contain hidden">
                <div id="modal-shirt-icon" class="grid h-full w-full place-items-center text-stone-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <p id="modal-shirt-name" class="font-bold text-sm text-stone-900 truncate">-</p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span id="modal-shirt-meta" class="text-xs text-stone-500">Ukuran L &bull; 1 pcs</span>
                </div>
            </div>
        </div>

        {{-- 2 Opsi Pemesanan --}}
        <div class="space-y-2.5">
            {{-- Opsi 1: Beli Kaos Saja --}}
            <a id="btn-choice-kaos-saja" href="#"
                class="group flex items-center justify-between rounded-xl border border-stone-200 bg-white p-3.5 transition hover:border-stone-400 hover:bg-stone-50/60 cursor-pointer">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-stone-100 text-stone-600 transition group-hover:bg-stone-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-sm text-stone-900">Beli Kaos Saja</div>
                        <p class="text-xs text-stone-500 mt-0.5">Hanya bayar kaos &amp; ongkir</p>
                    </div>
                </div>
                <div class="text-right shrink-0 flex items-center gap-2">
                    <div>
                        <span id="modal-price-kaos-saja" class="font-bold text-sm text-stone-900 block">Rp 0</span>
                        <span class="text-[10px] text-stone-400 block">Harga standar</span>
                    </div>
                    <svg class="h-4 w-4 text-stone-400 transition group-hover:translate-x-0.5 group-hover:text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>

            {{-- Opsi 2: Kaos + Donasi --}}
            <a id="btn-choice-kaos-donasi" href="#"
                class="group flex items-center justify-between rounded-xl border border-primary/50 bg-[#fdf8f4] p-3.5 transition hover:border-primary hover:bg-[#faefe5] cursor-pointer"
                style="border-color: rgba(181, 83, 34, 0.45); background-color: #fdf8f5;">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-white" style="background-color: #b55322;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-sm text-stone-900">Kaos + Donasi</span>
                            <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold text-white" style="background-color: #b55322;">Bantu Kas</span>
                        </div>
                        <p class="text-xs text-stone-600 mt-0.5">Kaos sekaligus donasi sukarela</p>
                    </div>
                </div>
                <div class="text-right shrink-0 flex items-center gap-2">
                    <div>
                        <span id="modal-price-kaos-donasi" class="font-bold text-sm text-stone-900 block">Rp 0</span>
                        <span class="text-[10px] font-semibold block" style="color: #b55322;">+ Donasi Bebas</span>
                    </div>
                    <svg class="h-4 w-4 transition group-hover:translate-x-0.5" style="color: #b55322;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>
        </div>

        {{-- Keterangan Alur --}}
        <div class="mt-4 pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 text-xs text-stone-500">
            <span>Kaos + Donasi langsung ke form Shop + Donasi</span>
            <button type="button" onclick="document.getElementById('order-modal-close').click()" class="text-stone-400 hover:text-stone-700 underline text-xs cursor-pointer">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
    window.OrderChoiceModal = {
        open: function (options) {
            const modal = document.getElementById('order-choice-modal');
            if (!modal) return;

            const nameEl = document.getElementById('modal-shirt-name');
            const metaEl = document.getElementById('modal-shirt-meta');
            const priceEl = document.getElementById('modal-price-kaos-saja');
            const priceDonasiEl = document.getElementById('modal-price-kaos-donasi');
            const imgEl = document.getElementById('modal-shirt-img');
            const iconEl = document.getElementById('modal-shirt-icon');
            const btnSaja = document.getElementById('btn-choice-kaos-saja');
            const btnDonasi = document.getElementById('btn-choice-kaos-donasi');

            const name = options.name || 'Kaos Jemaat';
            const size = options.size || 'L';
            const qty = parseInt(options.qty || 1, 10);
            const price = parseInt(options.price || 150000, 10);
            const designId = options.designId || 1;
            const imgUrl = options.imgUrl || null;
            const baseUrl = options.baseUrl || '{{ route("checkout.pemesanan") }}';

            if (nameEl) nameEl.textContent = name;
            if (metaEl) metaEl.textContent = 'Ukuran ' + size + ' \u2022 ' + qty + ' kaos';

            const subtotalStr = 'Rp ' + Number(price * qty).toLocaleString('id-ID');
            if (priceEl) priceEl.textContent = subtotalStr;
            if (priceDonasiEl) priceDonasiEl.textContent = subtotalStr;

            if (imgEl && iconEl) {
                if (imgUrl) {
                    imgEl.src = imgUrl;
                    imgEl.classList.remove('hidden');
                    iconEl.classList.add('hidden');
                } else {
                    imgEl.classList.add('hidden');
                    iconEl.classList.remove('hidden');
                }
            }

            if (btnSaja) {
                const shopUrl = options.shopUrl || '{{ route("shop") }}';
                btnSaja.href = shopUrl + '?tab=kaos_donasi' +
                    '&desain=' + encodeURIComponent(designId) +
                    '&ukuran=' + encodeURIComponent(size) +
                    '&jumlah=' + encodeURIComponent(qty);
            }

            if (btnDonasi) {
                const shopUrl = options.shopUrl || '{{ route("shop") }}';
                btnDonasi.href = shopUrl + '?tab=kaos_donasi' +
                    '&desain=' + encodeURIComponent(designId) +
                    '&ukuran=' + encodeURIComponent(size) +
                    '&jumlah=' + encodeURIComponent(qty);
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        },
        close: function () {
            const modal = document.getElementById('order-choice-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('order-choice-modal');
        const closeBtn = document.getElementById('order-modal-close');
        const backdrop = document.getElementById('order-modal-backdrop');

        if (closeBtn) closeBtn.addEventListener('click', window.OrderChoiceModal.close);
        if (backdrop) backdrop.addEventListener('click', window.OrderChoiceModal.close);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                window.OrderChoiceModal.close();
            }
        });
    });
</script>
