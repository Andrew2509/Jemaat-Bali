@extends('layouts.admin')

@section('title', 'Tabel Ukuran Baju — Koleksi Kaos Pembangunan')
@section('breadcrumb', 'Koleksi Kaos')

@section('content')
    @php
        $jsColumns = !empty($sizeChart['columns']) ? $sizeChart['columns'] : ['Ukuran', 'Lebar Dada (cm)', 'Panjang Badan (cm)', 'Panjang Lengan (cm)'];
        $jsRows = !empty($sizeChart['rows']) ? $sizeChart['rows'] : [
            ['S', '47', '67', '20'],
            ['M', '50', '70', '21'],
            ['L', '53', '73', '22'],
            ['XL', '56', '75', '23'],
            ['XXL', '59', '77', '24'],
        ];
    @endphp

    <!-- Toast / Notification -->
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800">
            <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Header -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.designs') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-stone-900 transition mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Koleksi Kaos</span>
            </a>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / PANDUAN UKURAN PAKAIAN</p>
            <h1 class="mt-1 font-serif text-3xl font-bold text-stone-900 tracking-tight">Tabel Panduan Ukuran Baju</h1>
            <p class="mt-1 text-sm text-stone-500">
                Kustomisasi baris, kolom, dan dimensi ukuran pakaian. Tabel ini otomatis tampil di etalase website publik.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('home') }}#kaos" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs transition">
                <span>Lihat di Website</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs Koleksi -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e8e4dc] mb-6">
        <nav class="flex gap-8 -mb-px">
            <a href="{{ route('admin.designs') }}" class="border-b-2 border-transparent text-stone-500 font-semibold hover:text-stone-900 pb-3 text-xs transition">Semua Koleksi</a>
            <a href="{{ route('admin.designs', ['status' => 'aktif']) }}" class="border-b-2 border-transparent text-stone-500 font-semibold hover:text-stone-900 pb-3 text-xs transition">Aktif di Website</a>
            <a href="{{ route('admin.designs', ['status' => 'nonaktif']) }}" class="border-b-2 border-transparent text-stone-500 font-semibold hover:text-stone-900 pb-3 text-xs transition">Draf / Nonaktif</a>
            <a href="{{ route('admin.designs.size_chart') }}" class="border-b-2 border-[#b55322] pb-3 text-xs font-bold text-[#b55322] flex items-center gap-1.5">
                <span>📏</span>
                <span>Tabel Ukuran Baju</span>
            </a>
        </nav>
    </div>

    <!-- Mode Selector: Standar Koleksi vs Desain Tertentu -->
    <div class="mb-6 rounded-2xl border border-[#e8e4dc] bg-white p-4 shadow-2xs">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-[#faefe8] text-[#b55322] font-mono text-sm font-bold shrink-0">
                    📏
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Target Tabel Ukuran:</span>
                    <h3 class="font-bold text-sm text-stone-900">
                        {{ $design ? "Khusus Desain: {$design->nama}" : 'Tabel Standar Seluruh Koleksi (Default Beranda & Katalog)' }}
                    </h3>
                </div>
            </div>

            <!-- Switcher Dropdown -->
            <form method="GET" action="{{ route('admin.designs.size_chart') }}" class="flex items-center gap-2">
                <label for="select-design-target" class="text-xs font-medium text-stone-500">Pilih Target:</label>
                <select id="select-design-target" name="design_id" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] px-3 py-1.5 text-xs text-stone-800 font-semibold focus:border-[#b55322] focus:outline-none">
                    <option value="">Tabel Standar Koleksi (Umum)</option>
                    @foreach ($designs as $d)
                        <option value="{{ $d->id }}" @selected($design && $design->id === $d->id)>
                            Desain: {{ $d->nama }} {{ $d->size_chart ? '★ (Khusus)' : '' }}
                        </option>
                    @endforeach
                </select>
                @if ($design)
                    <a href="{{ route('admin.designs.size_chart') }}" class="text-xs text-stone-400 hover:text-stone-700 underline ml-1">Ke Standar</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Grid: Left Editor (Col 7/12) & Right Live Preview (Col 5/12) -->
    <div class="grid gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left: Table Editor Form -->
        <div class="lg:col-span-7 xl:col-span-7 space-y-6">
            <form id="form-size-chart" action="{{ route('admin.designs.size_chart.update') }}" method="POST" class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-5">
                @csrf
                @if ($design)
                    <input type="hidden" name="design_id" value="{{ $design->id }}">
                @endif

                <!-- Form Header -->
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Pembuat Tabel Ukuran</h2>
                        <p class="text-[11px] text-stone-400">Ketik pada kotak kolom atau baris untuk mengubah nilai seketika.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

                <!-- Title & Note Inputs -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="chart-title" class="block text-xs font-semibold text-stone-700 mb-1">
                            Judul Tabel <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="chart-title" name="title" value="{{ old('title', $sizeChart['title'] ?? 'Panduan Ukuran Kaos (cm)') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] px-3.5 py-2 text-xs text-stone-900 font-semibold focus:border-[#b55322] focus:outline-none" required>
                    </div>
                    <div>
                        <label for="chart-note" class="block text-xs font-semibold text-stone-700 mb-1">
                            Catatan / Toleransi Ukuran
                        </label>
                        <input type="text" id="chart-note" name="note" value="{{ old('note', $sizeChart['note'] ?? 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-[#fbf9f6] px-3.5 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none placeholder:text-stone-400" placeholder="Contoh: Toleransi ±1 cm">
                    </div>
                </div>

                <!-- Toolbar Action Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-stone-100">
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-add-column" class="inline-flex items-center gap-1.5 rounded-xl border border-[#b55322] bg-[#faefe8] hover:bg-[#f6dfd1] text-[#b55322] px-3 py-1.5 text-xs font-bold transition cursor-pointer shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>+ Tambah Kolom</span>
                        </button>
                        <button type="button" id="btn-add-row" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-800 bg-stone-900 hover:bg-stone-800 text-white px-3 py-1.5 text-xs font-bold transition cursor-pointer shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>+ Tambah Baris</span>
                        </button>
                    </div>

                    <!-- Presets Menu -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Format Cepat:</span>
                        <button type="button" id="btn-preset-vertical" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-[10px] font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer" title="Kolom: Ukuran, Lebar, Panjang, Lengan">
                            Standar Kaos
                        </button>
                        <button type="button" id="btn-preset-horizontal" class="rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-[10px] font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer" title="Kolom: Dimensi, S, M, L, XL, XXL">
                            Size di Atas
                        </button>
                    </div>
                </div>

                <!-- Dynamic Table Container -->
                <div class="overflow-x-auto rounded-2xl border border-[#e8e4dc] bg-[#faf8f5] p-3">
                    <table id="builder-table" class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr id="builder-header-row" class="border-b border-[#e8e4dc]">
                                <!-- Columns dynamically injected via JS -->
                            </tr>
                        </thead>
                        <tbody id="builder-body" class="divide-y divide-[#ece8e0]">
                            <!-- Rows dynamically injected via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-stone-100 text-xs">
                    <div class="flex items-center gap-2 text-[11px] text-stone-400">
                        <span>💡 Klik tanda ✕ merah untuk menghapus kolom atau baris tertentu.</span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($design && $design->size_chart)
                            <button type="submit" name="reset_to_global" value="1" onclick="return confirm('Reset tabel khusus desain ini agar kembali mengikuti standar koleksi umum?')" class="rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 px-3.5 py-2 text-xs font-semibold transition cursor-pointer">
                                Reset ke Standar Koleksi
                            </button>
                        @endif
                        <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Real-time Live Preview -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-6 sticky top-20">
            <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-stone-900">Pratinjau Live</h2>
                        <p class="text-[11px] text-stone-400">Tampilan langsung hasil tabel di website.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        LIVE PREVIEW
                    </span>
                </div>

                <!-- Theme Switcher for Preview (Gelap vs Terang) -->
                <div class="flex items-center gap-1.5 rounded-xl bg-[#f5f2ec] p-1 border border-[#e8e4dc]">
                    <button type="button" id="tab-preview-dark" class="flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-stone-900 text-white shadow-2xs cursor-pointer">
                        <span>Tema Beranda (Gelap)</span>
                    </button>
                    <button type="button" id="tab-preview-light" class="flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Tema Katalog (Terang)</span>
                    </button>
                </div>

                <!-- Preview Box: Dark Theme (Default) -->
                <div id="preview-box-dark" class="rounded-2xl bg-gradient-to-b from-stone-900 to-stone-950 p-4 border border-stone-800 text-white shadow-md space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span id="preview-dark-title" class="text-xs font-extrabold uppercase tracking-wider text-white flex items-center gap-1.5">
                            <span class="text-amber-400">📏</span>
                            <span>Panduan Ukuran Kaos (cm)</span>
                        </span>
                        <span id="preview-dark-note" class="text-[10px] font-semibold text-white/60">Toleransi ±1 cm</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-white/15 bg-black/40 backdrop-blur-xs p-1">
                        <table id="preview-dark-table" class="w-full text-center text-[11px] text-white border-collapse">
                            <!-- Injected via JS -->
                        </table>
                    </div>
                    <p class="text-[10px] text-stone-400 italic text-center pt-1">
                        * Ukuran di atas diukur dalam keadaan pakaian dibentangkan datar (flat lay).
                    </p>
                </div>

                <!-- Preview Box: Light Theme -->
                <div id="preview-box-light" class="hidden rounded-2xl bg-[#fbf9f6] p-4 border border-[#e8e4dc] text-stone-900 shadow-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span id="preview-light-title" class="text-xs font-bold uppercase tracking-wider text-stone-900 flex items-center gap-1.5">
                            <span class="text-[#b55322]">📏</span>
                            <span>Panduan Ukuran Kaos (cm)</span>
                        </span>
                        <span id="preview-light-note" class="text-[10px] font-semibold text-stone-500">Toleransi ±1 cm</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-[#e8e4dc] bg-white p-1">
                        <table id="preview-light-table" class="w-full text-center text-[11px] text-stone-800 border-collapse">
                            <!-- Injected via JS -->
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-200/60 bg-amber-50/40 p-3 text-xs text-amber-900 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <span>✨ Fleksibel &amp; Otomatis</span>
                    </p>
                    <p class="text-[11px] text-amber-800 leading-relaxed">
                        Anda dapat menambah kolom ukuran baru seperti <strong>XXXL</strong>, atau ukuran anak-anak, serta dimensi seperti <strong>Lingkar Dada</strong> atau <strong>Panjang Bahu</strong> sesuai kebutuhan kampanye.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Interactive Builder Script -->
    <script>
        (function () {
            // Initial data from PHP
            let columns = @json($jsColumns);
            let rows = @json($jsRows);

            const inputTitle = document.getElementById('chart-title');
            const inputNote = document.getElementById('chart-note');
            const builderHeaderRow = document.getElementById('builder-header-row');
            const builderBody = document.getElementById('builder-body');

            const btnAddCol = document.getElementById('btn-add-column');
            const btnAddRow = document.getElementById('btn-add-row');
            const btnPresetVertical = document.getElementById('btn-preset-vertical');
            const btnPresetHorizontal = document.getElementById('btn-preset-horizontal');

            // Preview elements
            const tabDark = document.getElementById('tab-preview-dark');
            const tabLight = document.getElementById('tab-preview-light');
            const boxDark = document.getElementById('preview-box-dark');
            const boxLight = document.getElementById('preview-box-light');
            const previewDarkTitle = document.getElementById('preview-dark-title');
            const previewDarkNote = document.getElementById('preview-dark-note');
            const previewLightTitle = document.getElementById('preview-light-title');
            const previewLightNote = document.getElementById('preview-light-note');
            const previewDarkTable = document.getElementById('preview-dark-table');
            const previewLightTable = document.getElementById('preview-light-table');

            // Render Builder Table
            function renderBuilder() {
                // 1. Header
                builderHeaderRow.innerHTML = '';
                columns.forEach((col, cIdx) => {
                    const th = document.createElement('th');
                    th.className = 'p-2 min-w-[130px] font-bold text-stone-700';
                    th.innerHTML = `
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="columns[]" value="${escapeHtml(col)}" data-col="${cIdx}" class="col-input w-full rounded-lg border border-stone-300 bg-white px-2 py-1.5 text-xs font-bold text-stone-800 focus:border-[#b55322] focus:outline-none" placeholder="Nama Kolom..." required>
                            ${columns.length > 1 ? `
                                <button type="button" class="btn-del-col grid h-6 w-6 place-items-center rounded-md bg-stone-100 hover:bg-rose-100 text-stone-400 hover:text-rose-600 transition cursor-pointer text-xs" data-col="${cIdx}" title="Hapus Kolom Ini">
                                    ✕
                                </button>
                            ` : ''}
                        </div>
                    `;
                    builderHeaderRow.appendChild(th);
                });

                // Header right corner (empty for delete row buttons)
                const thAction = document.createElement('th');
                thAction.className = 'p-2 w-10 text-center text-stone-400 font-normal';
                thAction.innerHTML = '<span class="text-[10px]">Aksi</span>';
                builderHeaderRow.appendChild(thAction);

                // 2. Body Rows
                builderBody.innerHTML = '';
                rows.forEach((row, rIdx) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-white/60 transition';

                    for (let c = 0; c < columns.length; c++) {
                        const val = row[c] !== undefined ? row[c] : '';
                        const td = document.createElement('td');
                        td.className = 'p-2';
                        td.innerHTML = `
                            <input type="text" name="rows[${rIdx}][${c}]" value="${escapeHtml(val)}" data-row="${rIdx}" data-col="${c}" class="cell-input w-full rounded-lg border border-stone-200 bg-white px-2.5 py-1.5 text-xs font-medium text-stone-900 focus:border-[#b55322] focus:outline-none" placeholder="-">
                        `;
                        tr.appendChild(td);
                    }

                    // Delete row action
                    const tdAction = document.createElement('td');
                    tdAction.className = 'p-2 text-center';
                    if (rows.length > 1) {
                        tdAction.innerHTML = `
                            <button type="button" class="btn-del-row grid h-6 w-6 place-items-center rounded-md bg-stone-100 hover:bg-rose-100 text-stone-400 hover:text-rose-600 transition cursor-pointer text-xs" data-row="${rIdx}" title="Hapus Baris Ini">
                                ✕
                            </button>
                        `;
                    }
                    tr.appendChild(tdAction);

                    builderBody.appendChild(tr);
                });

                bindInputEvents();
                syncPreview();
            }

            function bindInputEvents() {
                // Column name change
                document.querySelectorAll('.col-input').forEach(input => {
                    input.addEventListener('input', function () {
                        const idx = parseInt(this.dataset.col, 10);
                        columns[idx] = this.value;
                        syncPreview();
                    });
                });

                // Cell value change
                document.querySelectorAll('.cell-input').forEach(input => {
                    input.addEventListener('input', function () {
                        const r = parseInt(this.dataset.row, 10);
                        const c = parseInt(this.dataset.col, 10);
                        if (!rows[r]) rows[r] = [];
                        rows[r][c] = this.value;
                        syncPreview();
                    });
                });

                // Delete Column
                document.querySelectorAll('.btn-del-col').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.dataset.col, 10);
                        if (columns.length <= 1) return;
                        if (!confirm(`Hapus kolom "${columns[idx]}"? Seluruh data kolom ini akan dihapus.`)) return;
                        columns.splice(idx, 1);
                        rows.forEach(r => r.splice(idx, 1));
                        renderBuilder();
                    });
                });

                // Delete Row
                document.querySelectorAll('.btn-del-row').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.dataset.row, 10);
                        if (rows.length <= 1) return;
                        rows.splice(idx, 1);
                        renderBuilder();
                    });
                });
            }

            // Sync Live Preview
            function syncPreview() {
                const titleText = inputTitle.value.trim() || 'Panduan Ukuran Kaos (cm)';
                const noteText = inputNote.value.trim();

                if (previewDarkTitle) previewDarkTitle.lastElementChild.textContent = titleText;
                if (previewDarkNote) previewDarkNote.textContent = noteText;
                if (previewLightTitle) previewLightTitle.lastElementChild.textContent = titleText;
                if (previewLightNote) previewLightNote.textContent = noteText;

                // Render Dark Table Preview
                let darkThead = '<thead><tr class="border-b border-white/20 bg-black/40 font-black uppercase tracking-wider text-white/90">';
                columns.forEach((col, idx) => {
                    const align = idx === 0 ? 'text-left pl-3' : 'text-center';
                    darkThead += `<th class="py-1.5 px-2 ${align}">${escapeHtml(col)}</th>`;
                });
                darkThead += '</tr></thead>';

                let darkTbody = '<tbody class="divide-y divide-white/10 font-mono font-semibold">';
                rows.forEach(row => {
                    darkTbody += '<tr class="hover:bg-white/5 transition">';
                    columns.forEach((col, cIdx) => {
                        const val = row[cIdx] !== undefined ? row[cIdx] : '';
                        const align = cIdx === 0 ? 'text-left pl-3 font-sans font-bold text-white/90' : 'text-center text-white/80';
                        darkTbody += `<td class="py-1.5 px-2 ${align}">${escapeHtml(val)}</td>`;
                    });
                    darkTbody += '</tr>';
                });
                darkTbody += '</tbody>';
                previewDarkTable.innerHTML = darkThead + darkTbody;

                // Render Light Table Preview
                let lightThead = '<thead><tr class="border-b border-stone-200 bg-stone-100 font-bold uppercase tracking-wider text-stone-700">';
                columns.forEach((col, idx) => {
                    const align = idx === 0 ? 'text-left pl-3' : 'text-center';
                    lightThead += `<th class="py-2 px-2.5 ${align}">${escapeHtml(col)}</th>`;
                });
                lightThead += '</tr></thead>';

                let lightTbody = '<tbody class="divide-y divide-stone-100 font-mono font-medium">';
                rows.forEach(row => {
                    lightTbody += '<tr class="hover:bg-stone-50 transition">';
                    columns.forEach((col, cIdx) => {
                        const val = row[cIdx] !== undefined ? row[cIdx] : '';
                        const align = cIdx === 0 ? 'text-left pl-3 font-sans font-bold text-stone-900' : 'text-center text-stone-700';
                        lightTbody += `<td class="py-2 px-2.5 ${align}">${escapeHtml(val)}</td>`;
                    });
                    lightTbody += '</tr>';
                });
                lightTbody += '</tbody>';
                previewLightTable.innerHTML = lightThead + lightTbody;
            }

            // Add Column
            btnAddCol.addEventListener('click', function () {
                const colName = prompt('Masukkan nama kolom baru:', 'Kolom Baru');
                if (!colName || !colName.trim()) return;
                columns.push(colName.trim());
                rows.forEach(r => r.push(''));
                renderBuilder();
            });

            // Add Row
            btnAddRow.addEventListener('click', function () {
                const newRow = [];
                for (let i = 0; i < columns.length; i++) {
                    newRow.push(i === 0 ? 'Size Baru' : '');
                }
                rows.push(newRow);
                renderBuilder();
            });

            // Presets
            btnPresetVertical.addEventListener('click', function () {
                if (!confirm('Terapkan preset standar kaos (Ukuran, Lebar Dada, Panjang Badan, Panjang Lengan)?')) return;
                columns = ['Ukuran', 'Lebar Dada (cm)', 'Panjang Badan (cm)', 'Panjang Lengan (cm)'];
                rows = [
                    ['S', '47', '67', '20'],
                    ['M', '50', '70', '21'],
                    ['L', '53', '73', '22'],
                    ['XL', '56', '75', '23'],
                    ['XXL', '59', '77', '24']
                ];
                inputTitle.value = 'Panduan Ukuran Kaos (cm)';
                inputNote.value = 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed';
                renderBuilder();
            });

            btnPresetHorizontal.addEventListener('click', function () {
                if (!confirm('Terapkan preset format horizontal (Dimensi, S, M, L, XL, XXL)?')) return;
                columns = ['Dimensi', 'S', 'M', 'L', 'XL', 'XXL'];
                rows = [
                    ['Lebar Dada (cm)', '47', '50', '53', '56', '59'],
                    ['Panjang Baju (cm)', '67', '70', '73', '75', '77'],
                    ['Panjang Lengan (cm)', '20', '21', '22', '23', '24']
                ];
                inputTitle.value = 'Tabel Ukuran Kaos (cm)';
                inputNote.value = 'Toleransi ±1 cm · Satuan Centimeter (cm)';
                renderBuilder();
            });

            // Theme switch
            tabDark.addEventListener('click', function () {
                boxDark.classList.remove('hidden');
                boxLight.classList.add('hidden');
                tabDark.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-stone-900 text-white shadow-2xs cursor-pointer';
                tabLight.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer';
            });

            tabLight.addEventListener('click', function () {
                boxLight.classList.remove('hidden');
                boxDark.classList.add('hidden');
                tabLight.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-stone-900 shadow-2xs cursor-pointer';
                tabDark.className = 'flex-1 rounded-lg py-1.5 px-3 text-xs font-semibold text-stone-600 hover:text-stone-900 transition flex items-center justify-center gap-1.5 cursor-pointer';
            });

            inputTitle.addEventListener('input', syncPreview);
            inputNote.addEventListener('input', syncPreview);

            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            // Initial render
            renderBuilder();
        })();
    </script>
@endsection
