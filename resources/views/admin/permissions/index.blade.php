@extends('layouts.admin')

@section('title', 'Hak Akses (Permissions)')
@section('breadcrumb', 'Hak Akses')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">MANAJEMEN AKSES / PERMISSIONS</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Hak Akses Sistem</h1>
            <p class="mt-1 text-sm text-stone-500">Katalog seluruh izin akses granular yang menjadi dasar kewenangan peran di setiap fitur & data aplikasi.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Kelola Peran (Roles)</span>
            </a>
            <button type="button" onclick="openAddPermModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Hak Akses</span>
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
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-800 shadow-2xs">
            <p class="font-bold flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-rose-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 7.5h.008v.008H12v-.008Z" />
                </svg>
                Terjadi kendala:
            </p>
            <ul class="mt-1.5 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3 Metrics Row -->
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <!-- Total Permissions -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Total Hak Akses</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-stone-100 text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $totalPermissions }}</p>
            <p class="mt-2 text-xs text-stone-400">Granular wewenang sistem</p>
        </div>

        <!-- Kategori / Modul -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Kategori Modul</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6A1.125 1.125 0 0 1 2.25 9.375v-2.25ZM2.25 14.625c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-2.25ZM13.5 7.125c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6A1.125 1.125 0 0 1 13.5 9.375v-2.25ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $totalGroups }}</p>
            <p class="mt-2 text-xs text-stone-400">Pengelompokan fitur aplikasi</p>
        </div>

        <!-- Total Peran Terdaftar -->
        <div class="rounded-2xl bg-[#444d41] p-5 text-white flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-white/70">Peran Terhubung</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-white/10 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-white leading-none">{{ $totalRoles }}</p>
            <p class="mt-2 text-xs text-white/60">Peran yang mengikat hak akses</p>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="mb-6 rounded-2xl border border-[#e8e4dc] bg-white p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.permissions.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama izin, kode slug, atau kelompok modul..." class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/60 py-2 pl-9 pr-3 text-xs text-stone-900 placeholder-stone-400 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
            </div>

            <div class="w-56">
                <select name="group" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/60 px-3 py-2 text-xs text-stone-800 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    <option value="">Semua Kelompok Modul</option>
                    @foreach ($distinctGroups as $g)
                        <option value="{{ $g }}" {{ $groupFilter === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-stone-900 px-4 py-2 text-xs font-semibold text-white hover:bg-stone-800 transition cursor-pointer">
                    <span>Filter</span>
                </button>
                @if ($search || $groupFilter)
                    <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center rounded-xl border border-[#e2ddd5] bg-white px-3 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grouped Permissions List -->
    <div class="space-y-6">
        @forelse ($groupedPermissions as $groupName => $perms)
            <div class="rounded-3xl border border-[#e8e4dc] bg-white overflow-hidden shadow-2xs">
                <!-- Group Header -->
                <div class="flex items-center justify-between border-b border-[#e8e4dc] bg-[#faf8f4] px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-[#b55322]"></span>
                        <h3 class="font-serif text-base font-bold text-stone-900">{{ $groupName }}</h3>
                    </div>
                    <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-[11px] font-semibold text-stone-600">
                        {{ $perms->count() }} Hak Akses
                    </span>
                </div>

                <!-- Table of Permissions in this Group -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#f0ede6] text-[10px] font-bold uppercase tracking-wider text-stone-400">
                                <th class="py-3 pl-6 pr-4">Nama & Keterangan</th>
                                <th class="py-3 px-4">Kode Slug</th>
                                <th class="py-3 px-4">Digunakan Oleh Peran</th>
                                <th class="py-3 pl-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f4f1eb] text-xs">
                            @foreach ($perms as $p)
                                <tr class="hover:bg-stone-50/70 transition">
                                    <td class="py-3.5 pl-6 pr-4 max-w-sm">
                                        <p class="font-bold text-stone-900">{{ $p->display_name }}</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">{{ $p->description ?: 'Tidak ada keterangan.' }}</p>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-stone-600">
                                        <span class="rounded-md bg-stone-100 px-2 py-0.5 border border-stone-200/80">{{ $p->name }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($p->roles as $r)
                                                <span class="rounded-md px-1.5 py-0.5 text-[10px] font-semibold {{ $r->name === 'super_admin' ? 'bg-amber-50 text-amber-800' : 'bg-stone-100 text-stone-700' }}">
                                                    {{ $r->display_name }}
                                                </span>
                                            @empty
                                                <span class="text-[11px] text-stone-400 italic">Belum dipilih</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-3.5 pl-4 pr-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" onclick="openEditPermModal({{ json_encode([
                                                'id' => $p->id,
                                                'name' => $p->name,
                                                'display_name' => $p->display_name,
                                                'group' => $p->group,
                                                'description' => $p->description,
                                            ]) }})" class="rounded-lg p-1.5 text-stone-500 hover:bg-stone-100 hover:text-stone-900 transition cursor-pointer" title="Edit Hak Akses">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>

                                            <form action="{{ route('admin.permissions.destroy', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hak akses {{ $p->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-1.5 text-stone-400 hover:bg-rose-50 hover:text-rose-600 transition cursor-pointer" title="Hapus Hak Akses">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-[#e8e4dc] bg-white p-12 text-center text-stone-500 shadow-2xs">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-stone-100 text-stone-400 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </div>
                <p class="font-serif text-base font-bold text-stone-800">Tidak ada hak akses yang sesuai</p>
                <p class="text-xs text-stone-400 mt-1">Sesuaikan kata kunci pencarian atau kategori filter.</p>
            </div>
        @endforelse
    </div>

    <!-- Datalist untuk Kelompok Modul -->
    <datalist id="groupSuggestions">
        @foreach ($distinctGroups as $g)
            <option value="{{ $g }}">
        @endforeach
    </datalist>

    <!-- Modal Tambah Hak Akses -->
    <div id="addPermModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] pb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Tambah Hak Akses Baru</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Daftarkan izin granular baru ke dalam sistem.</p>
                </div>
                <button type="button" onclick="closeAddPermModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.permissions.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Tampilan Izin</label>
                    <input type="text" name="display_name" required placeholder="Contoh: Ekspor Audit Keuangan" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kode Identitas (Slug)</label>
                    <input type="text" name="name" required placeholder="Contoh: reports.audit_export" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs font-stone-900 font-mono focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    <p class="text-[11px] text-stone-400 mt-1">Gunakan huruf kecil, angka, titik, atau garis bawah.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kelompok / Modul</label>
                    <input type="text" name="group" list="groupSuggestions" required placeholder="Contoh: Keuangan & Donasi" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Keterangan Izin</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan apa yang diizinkan oleh permission ini..." class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeAddPermModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Hak Akses -->
    <div id="editPermModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] pb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Edit Hak Akses</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Perbarui nama tampilan, kelompok, atau keterangan izin.</p>
                </div>
                <button type="button" onclick="closeEditPermModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editPermForm" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kode Identitas (Slug)</label>
                    <input type="text" id="edit_perm_slug" disabled class="w-full rounded-xl border border-[#e2ddd5] bg-stone-100 px-3.5 py-2.5 text-xs text-stone-500 font-mono cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Tampilan Izin</label>
                    <input type="text" name="display_name" id="edit_perm_display_name" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kelompok / Modul</label>
                    <input type="text" name="group" id="edit_perm_group" list="groupSuggestions" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Keterangan Izin</label>
                    <textarea name="description" id="edit_perm_description" rows="2" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeEditPermModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Perbarui Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Perm Modals -->
    <script>
        function openAddPermModal() {
            document.getElementById('addPermModal').classList.remove('hidden');
        }
        function closeAddPermModal() {
            document.getElementById('addPermModal').classList.add('hidden');
        }

        function openEditPermModal(perm) {
            var form = document.getElementById('editPermForm');
            form.action = "{{ url('admin/permissions') }}/" + perm.id;

            document.getElementById('edit_perm_slug').value = perm.name;
            document.getElementById('edit_perm_display_name').value = perm.display_name;
            document.getElementById('edit_perm_group').value = perm.group;
            document.getElementById('edit_perm_description').value = perm.description || '';

            document.getElementById('editPermModal').classList.remove('hidden');
        }

        function closeEditPermModal() {
            document.getElementById('editPermModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddPermModal();
                closeEditPermModal();
            }
        });
    </script>
@endsection
