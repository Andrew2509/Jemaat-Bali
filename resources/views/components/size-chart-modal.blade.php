@php
    $source = $selected ?? null;
    if ($chartData ?? null) {
        $chartPria = $chartData;
        $chartWanita = $chartData;
    } else {
        $chartPria = $source
            ? $source->getSizeChart()
            : ($programInfo ? $programInfo->getSizeChart() : \App\Models\ProgramInfo::defaultSizeChartPria());
        $chartWanita = $source
            ? $source->getSizeChartWanita()
            : ($programInfo ? $programInfo->getSizeChartWanita() : \App\Models\ProgramInfo::defaultSizeChartWanita());
    }

    $charts = [
        'pria' => ['label' => 'Pria', 'icon' => '♂', 'chart' => $chartPria],
        'wanita' => ['label' => 'Cewe', 'icon' => '♀', 'chart' => $chartWanita],
    ];
@endphp

<!-- Modal Panduan Ukuran Kaos -->
<div id="size-chart-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="size-chart-modal-title">
    <!-- Backdrop -->
    <div id="size-chart-modal-backdrop" class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity cursor-pointer"></div>

    <!-- Modal Content Card -->
    <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-stone-200 bg-white p-5 sm:p-6 shadow-2xl transition-all max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-start justify-between gap-3 border-b border-stone-100 pb-3 shrink-0">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#b55322] block">PANDUAN UKURAN PAKAIAN</span>
                <h3 id="size-chart-modal-title" class="text-base sm:text-lg font-bold text-stone-900 font-sans tracking-tight">
                    Panduan Ukuran Kaos (cm)
                </h3>
                <p id="size-chart-modal-note" class="text-xs text-stone-500 mt-0.5"></p>
            </div>
            <button type="button" id="size-chart-modal-close" class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer" aria-label="Tutup">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Tab Selector Pria / Cewe -->
        <div class="mt-4 flex items-center gap-1.5 rounded-xl bg-stone-100 p-1 border border-stone-200 shrink-0" role="tablist">
            @foreach ($charts as $key => $item)
                <button type="button" data-size-gender-tab="{{ $key }}" role="tab"
                    class="flex-1 rounded-lg py-2 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer {{ $key === 'pria' ? 'bg-stone-900 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </button>
            @endforeach
        </div>

        <!-- Table Body -->
        <div class="overflow-y-auto py-4 space-y-4 flex-1">
            @foreach ($charts as $key => $item)
                @php
                    $chart = $item['chart'];
                    $cols = $chart['columns'] ?? ['Ukuran', 'Lebar Dada (cm)', 'Panjang Badan (cm)', 'Panjang Lengan (cm)'];
                    $rows = $chart['rows'] ?? [
                        ['S', '47', '67', '20'],
                        ['M', '50', '70', '21'],
                        ['L', '53', '73', '22'],
                        ['XL', '56', '75', '23'],
                        ['XXL', '59', '77', '24'],
                    ];
                    $title = $chart['title'] ?? 'Panduan Ukuran Kaos (cm)';
                    $note = $chart['note'] ?? 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed';
                @endphp
                <div data-size-gender-panel="{{ $key }}" data-chart-title="{{ $title }}" data-chart-note="{{ $note }}" class="{{ $key === 'pria' ? '' : 'hidden' }}">
                    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-[#fbf9f6]">
                        <table class="w-full text-center text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-stone-200 bg-stone-100/80 font-bold uppercase tracking-wider text-stone-700">
                                    @foreach ($cols as $cIdx => $col)
                                        <th class="py-2.5 px-3 {{ $cIdx === 0 ? 'text-left pl-3.5 font-bold text-stone-900' : '' }}">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200/70 font-mono">
                                @foreach ($rows as $row)
                                    <tr class="hover:bg-white/80 transition">
                                        @foreach ($cols as $cIdx => $col)
                                            @php $val = $row[$cIdx] ?? ''; @endphp
                                            <td class="py-2.5 px-3 {{ $cIdx === 0 ? 'text-left pl-3.5 font-sans font-bold text-stone-900 bg-stone-50/60' : 'text-stone-700' }}">{{ $val }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <!-- Petunjuk Pengukuran -->
            <div class="rounded-xl bg-stone-50 p-3.5 border border-stone-200/80 text-[11px] text-stone-600 space-y-1.5">
                <p class="font-bold text-stone-800 flex items-center gap-1.5">
                    <span>📏</span>
                    <span>Cara Menentukan Ukuran Kaos:</span>
                </p>
                <ul class="list-disc list-inside space-y-0.5 text-stone-500 pl-0.5">
                    <li>Bentangkan kaos yang paling nyaman Anda pakai di atas meja datar.</li>
                    <li><strong>Lebar Dada</strong>: Ukur jarak mendatar dari bawah ketiak kiri ke ketiak kanan.</li>
                    <li><strong>Panjang Baju</strong>: Ukur dari titik jahitan kerah bahu teratas hingga ke ujung keliman bawah.</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-stone-100 pt-3 flex justify-end shrink-0">
            <button type="button" id="btn-size-chart-modal-ok" class="rounded-xl bg-stone-900 hover:bg-stone-800 text-white px-5 py-2 text-xs font-bold transition cursor-pointer">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('size-chart-modal');
        const backdrop = document.getElementById('size-chart-modal-backdrop');
        const closeBtn = document.getElementById('size-chart-modal-close');
        const okBtn = document.getElementById('btn-size-chart-modal-ok');
        const titleEl = document.getElementById('size-chart-modal-title');
        const noteEl = document.getElementById('size-chart-modal-note');

        const genderTabs = document.querySelectorAll('[data-size-gender-tab]');
        const genderPanels = document.querySelectorAll('[data-size-gender-panel]');

        function activateGender(gender) {
            genderTabs.forEach(function (tab) {
                const on = tab.dataset.sizeGenderTab === gender;
                tab.classList.toggle('bg-stone-900', on);
                tab.classList.toggle('text-white', on);
                tab.classList.toggle('shadow-2xs', on);
                tab.classList.toggle('text-stone-600', !on);
                tab.classList.toggle('hover:text-stone-900', !on);
            });

            let activePanel = null;
            genderPanels.forEach(function (panel) {
                const on = panel.dataset.sizeGenderPanel === gender;
                panel.classList.toggle('hidden', !on);
                if (on) activePanel = panel;
            });

            if (activePanel && titleEl) {
                titleEl.textContent = activePanel.dataset.chartTitle || 'Panduan Ukuran Kaos (cm)';
            }
            if (activePanel && noteEl) {
                noteEl.textContent = activePanel.dataset.chartNote || '';
            }
        }

        window.SizeChartModal = {
            open: function () {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            },
            close: function () {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        };

        if (backdrop) backdrop.addEventListener('click', window.SizeChartModal.close);
        if (closeBtn) closeBtn.addEventListener('click', window.SizeChartModal.close);
        if (okBtn) okBtn.addEventListener('click', window.SizeChartModal.close);

        genderTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                activateGender(tab.dataset.sizeGenderTab);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                window.SizeChartModal.close();
            }
        });

        // Auto bind to buttons with data-open-size-chart or #size-guide
        document.querySelectorAll('[data-open-size-chart], #size-guide').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                activateGender('pria');
                window.SizeChartModal.open();
            });
        });

        // Sinkronkan judul/catatan saat halaman dimuat
        activateGender('pria');
    })();
</script>
