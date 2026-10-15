@extends('layouts.admin')

@section('title', 'Dana Pengeluaran')
@section('breadcrumb', 'Dana Pengeluaran')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">JEMAAT BALI / ADMINISTRASI KEUANGAN</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Dana Pengeluaran</h1>
            <p class="mt-1 text-sm text-stone-500">Catat dan kelola pengeluaran dana pembangunan & operasional program. Setiap pengeluaran pembangunan otomatis memotong Saldo Pembangunan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Lihat Laporan Dana</span>
            </a>
            <a href="{{ route('admin.reports.export', ['format' => 'xlsx', 'from' => $from, 'to' => $to]) }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition" title="Unduh rekap laporan dalam format Excel (.xlsx)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-1.125c0-.621-.504-1.125-1.125-1.125H12M3.375 4.5h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 0 1 2.25 16.125V5.625C2.25 5.004 2.754 4.5 3.375 4.5Z" />
                </svg>
                <span>Ekspor Excel</span>
            </a>
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Pengeluaran</span>
            </button>
        </div>
    </div>

    <!-- Flash Notification -->
    @if (session('success'))
        <div class="mb-6 flex items-center justify-between gap-3 rounded-2xl bg-emerald-50 border border-emerald-200/80 p-4 text-xs font-semibold text-emerald-800 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-emerald-600 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-800">
            <p class="font-bold">Gagal menyimpan pengeluaran:</p>
            <ul class="mt-1 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="mb-6 rounded-2xl border border-[#e8e4dc] bg-white p-4">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[220px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama pengeluaran atau keterangan..."
                    class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 pl-10 pr-3.5 py-2 text-xs font-medium text-stone-800 placeholder:text-stone-400 focus:bg-white focus:border-[#b55322] focus:outline-none transition">
            </div>

            <!-- Kategori Filter -->
            <select name="kategori" onchange="this.form.submit()" class="rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs focus:border-[#b55322] focus:outline-none cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach ($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>

            <!-- Tanggal Dari -->
            <div class="flex items-center gap-2 text-xs text-stone-500">
                <span class="font-medium">Periode:</span>
                <input type="date" name="from" value="{{ $from }}" class="rounded-xl border border-[#e8e4dc] bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 focus:bg-white focus:border-[#b55322] focus:outline-none">
                <span>s/d</span>
                <input type="date" name="to" value="{{ $to }}" class="rounded-xl border border-[#e8e4dc] bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 focus:bg-white focus:border-[#b55322] focus:outline-none">
            </div>

            <button type="submit" class="rounded-xl bg-stone-900 hover:bg-stone-800 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                Filter
            </button>

            @if ($search || $kategori || $from || $to)
                <a href="{{ route('admin.expenses.index') }}" class="rounded-xl border border-dashed border-stone-300 px-3 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Expenses Table Card -->
    <div class="rounded-2xl border border-[#e8e4dc] bg-white overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#e8e4dc] bg-[#faf8f5] text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <th class="py-3.5 px-5">Tanggal</th>
                        <th class="py-3.5 px-5">Nama Pengeluaran</th>
                        <th class="py-3.5 px-5">Kategori</th>
                        <th class="py-3.5 px-5">Keterangan</th>
                        <th class="py-3.5 px-5 text-right">Jumlah</th>
                        <th class="py-3.5 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse ($expenses as $item)
                        @php
                            $isPembangunan = !in_array($item->kategori, ['Produksi kaos', 'Pengiriman']);
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-5 font-medium text-stone-600 whitespace-nowrap">
                                {{ $item->tanggal ? $item->tanggal->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-5">
                                <p class="font-bold text-stone-900">{{ $item->nama }}</p>
                                @if ($isPembangunan)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 mt-0.5">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Memotong Saldo Pembangunan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-stone-500 mt-0.5">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                        Biaya Kaos / Operasional
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if (str_contains(strtolower($item->kategori), 'fondasi') || str_contains(strtolower($item->kategori), 'bangunan') || str_contains(strtolower($item->kategori), 'material'))
                                    <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200/60">
                                        {{ $item->kategori }}
                                    </span>
                                @elseif (str_contains(strtolower($item->kategori), 'transportasi'))
                                    <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200/60">
                                        {{ $item->kategori }}
                                    </span>
                                @elseif (str_contains(strtolower($item->kategori), 'tukang') || str_contains(strtolower($item->kategori), 'upah'))
                                    <span class="inline-flex items-center rounded-lg bg-purple-50 px-2.5 py-1 text-[11px] font-semibold text-purple-700 border border-purple-200/60">
                                        {{ $item->kategori }}
                                    </span>
                                @elseif (str_contains(strtolower($item->kategori), 'kaos'))
                                    <span class="inline-flex items-center rounded-lg bg-[#b55322]/10 px-2.5 py-1 text-[11px] font-semibold text-[#b55322] border border-[#b55322]/20">
                                        {{ $item->kategori }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-lg bg-stone-100 px-2.5 py-1 text-[11px] font-semibold text-stone-700 border border-stone-200">
                                        {{ $item->kategori }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-stone-500 max-w-xs truncate" title="{{ $item->keterangan }}">
                                {{ $item->keterangan ?: '—' }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-serif font-bold text-sm text-stone-900 whitespace-nowrap">
                                Rp{{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                        onclick="openEditModal({{ json_encode($item) }})"
                                        class="p-1.5 rounded-lg border border-stone-200 text-stone-600 hover:border-[#b55322] hover:text-[#b55322] hover:bg-[#b55322]/5 transition cursor-pointer"
                                        title="Edit Pengeluaran">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.expenses.destroy', $item) }}" onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini? Saldo laporan keuangan akan terhitung ulang.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg border border-stone-200 text-stone-600 hover:border-rose-600 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Pengeluaran">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-stone-100 text-stone-400 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <p class="font-serif font-bold text-stone-800 text-base">Belum ada data pengeluaran</p>
                                <p class="text-xs text-stone-400 mt-1">Catat pengeluaran pertama Anda agar saldo pembangunan terpantau akurat.</p>
                                <button type="button" onclick="openAddModal()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition">
                                    <span>Tambah Pengeluaran</span>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($expenses->hasPages())
            <div class="border-t border-[#e8e4dc] px-5 py-3.5 bg-[#faf8f5]">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl border border-[#e8e4dc]">
            <div class="flex items-start justify-between pb-4 border-b border-stone-100">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-[#b55322]/10 text-[#b55322]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-serif font-bold text-stone-900 text-base">Tambah Pengeluaran Baru</h3>
                        <p class="text-xs text-stone-400 mt-0.5">Catat biaya pembangunan atau operasional</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-stone-400 hover:text-stone-700 p-1.5 rounded-xl hover:bg-stone-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.expenses.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="add_nama" class="block text-xs font-bold text-stone-700 mb-1">Nama Pengeluaran <span class="text-[#b55322]">*</span></label>
                    <input type="text" name="nama" id="add_nama" required placeholder="Contoh: Belanja semen 50 sak, Kayu bekisting, dll."
                        class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="add_kategori" class="block text-xs font-bold text-stone-700 mb-1">Kategori <span class="text-[#b55322]">*</span></label>
                        <select name="kategori" id="add_kategori" required
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="add_tanggal" class="block text-xs font-bold text-stone-700 mb-1">Tanggal Transaksi <span class="text-[#b55322]">*</span></label>
                        <input type="date" name="tanggal" id="add_tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="add_jumlah" class="block text-xs font-bold text-stone-700 mb-1">Jumlah Pengeluaran (Rp) <span class="text-[#b55322]">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400">Rp</span>
                        <input type="number" name="jumlah" id="add_jumlah" min="1" step="1" required placeholder="0"
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 pl-10 pr-3.5 py-2.5 text-xs font-bold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="add_keterangan" class="block text-xs font-bold text-stone-700 mb-1">Keterangan / Rincian (Opsional)</label>
                    <textarea name="keterangan" id="add_keterangan" rows="2.5" placeholder="Catatan tambahan nota, kuitansi, atau nama vendor..."
                        class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2 text-xs font-medium text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition"></textarea>
                </div>

                <!-- Callout note -->
                <div class="flex items-start gap-2.5 rounded-xl bg-amber-50 border border-amber-200/70 p-3 text-xs text-amber-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="leading-relaxed">Pengeluaran kategori pembangunan otomatis memotong <strong>Saldo Pembangunan</strong> pada halaman Laporan Dana.</p>
                </div>

                <div class="pt-3 border-t border-stone-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl border border-stone-200 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pengeluaran -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl border border-[#e8e4dc]">
            <div class="flex items-start justify-between pb-4 border-b border-stone-100">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-stone-100 text-stone-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-serif font-bold text-stone-900 text-base">Edit Data Pengeluaran</h3>
                        <p class="text-xs text-stone-400 mt-0.5">Ubah nominal atau kategori pengeluaran</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-stone-400 hover:text-stone-700 p-1.5 rounded-xl hover:bg-stone-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" action="" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_nama" class="block text-xs font-bold text-stone-700 mb-1">Nama Pengeluaran <span class="text-[#b55322]">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_kategori" class="block text-xs font-bold text-stone-700 mb-1">Kategori <span class="text-[#b55322]">*</span></label>
                        <select name="kategori" id="edit_kategori" required
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_tanggal" class="block text-xs font-bold text-stone-700 mb-1">Tanggal Transaksi <span class="text-[#b55322]">*</span></label>
                        <input type="date" name="tanggal" id="edit_tanggal" required
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2.5 text-xs font-semibold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="edit_jumlah" class="block text-xs font-bold text-stone-700 mb-1">Jumlah Pengeluaran (Rp) <span class="text-[#b55322]">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400">Rp</span>
                        <input type="number" name="jumlah" id="edit_jumlah" min="1" step="1" required
                            class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 pl-10 pr-3.5 py-2.5 text-xs font-bold text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="edit_keterangan" class="block text-xs font-bold text-stone-700 mb-1">Keterangan / Rincian</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="2.5"
                        class="w-full rounded-xl border border-[#e8e4dc] bg-stone-50 px-3.5 py-2 text-xs font-medium text-stone-800 focus:bg-white focus:border-[#b55322] focus:ring-2 focus:ring-[#b55322]/10 focus:outline-none transition"></textarea>
                </div>

                <div class="pt-3 border-t border-stone-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl border border-stone-200 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        const modal = document.getElementById('addModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('add_nama')?.focus(), 50);
        }
    }

    function closeAddModal() {
        const modal = document.getElementById('addModal');
        if (modal) modal.classList.add('hidden');
    }

    function openEditModal(expense) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        if (!modal || !form || !expense) return;

        form.action = `/admin/pengeluaran/${expense.id}`;
        document.getElementById('edit_nama').value = expense.nama || '';
        document.getElementById('edit_kategori').value = expense.kategori || '';
        
        let tgl = '';
        if (expense.tanggal) {
            tgl = expense.tanggal.substring(0, 10);
        }
        document.getElementById('edit_tanggal').value = tgl;
        document.getElementById('edit_jumlah').value = expense.jumlah || 0;
        document.getElementById('edit_keterangan').value = expense.keterangan || '';

        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        if (modal) modal.classList.add('hidden');
    }

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
        }
    });
</script>
@endpush
