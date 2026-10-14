@extends('layouts.admin')

@section('title', 'Manajemen Peran (Roles)')
@section('breadcrumb', 'Peran')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">MANAJEMEN AKSES / PERAN</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Peran Pengguna (Roles)</h1>
            <p class="mt-1 text-sm text-stone-500">Definisikan level wewenang dan izin modul yang dapat diakses oleh setiap kelompok staf pengelola.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
                <span>Kelola Hak Akses (Permissions)</span>
            </a>
            <button type="button" onclick="openAddRoleModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Peran Baru</span>
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

    <!-- 4 Metrics Row -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Peran -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Total Peran</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-stone-100 text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $totalRoles }}</p>
            <p class="mt-2 text-xs text-stone-400">Tingkat kewenangan aktif</p>
        </div>

        <!-- Peran Bawaan Sistem -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Bawaan Sistem</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $systemRolesCount }}</p>
            <p class="mt-2 text-xs text-stone-400">Terkunci untuk integritas sistem</p>
        </div>

        <!-- Peran Kustom -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Peran Kustom</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-sky-50 text-sky-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $customRolesCount }}</p>
            <p class="mt-2 text-xs text-stone-400">Dapat disesuaikan sepenuhnya</p>
        </div>

        <!-- Total Hak Akses -->
        <div class="rounded-2xl bg-[#444d41] p-5 text-white flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-white/70">Hak Akses Tersedia</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-white/10 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-white leading-none">{{ $totalPermissions }}</p>
            <p class="mt-2 text-xs text-white/60">Izin individual dalam sistem</p>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid gap-5 md:grid-cols-2">
        @foreach ($roles as $role)
            <div class="flex flex-col justify-between rounded-3xl border border-[#e8e4dc] bg-white p-6 shadow-2xs hover:shadow-xs transition">
                <div>
                    <!-- Top Role Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-2xl shrink-0 {{ $role->is_system ? 'bg-[#f8eee7] text-[#b55322]' : 'bg-stone-100 text-stone-700' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-serif text-lg font-bold text-stone-900 leading-tight">{{ $role->display_name }}</h3>
                                <p class="text-xs font-mono text-stone-400 mt-0.5">{{ $role->name }}</p>
                            </div>
                        </div>

                        <div>
                            @if ($role->is_system)
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800">
                                    Sistem
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 border border-stone-200 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-stone-600">
                                    Kustom
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Role Description -->
                    <p class="mt-3 text-xs leading-relaxed text-stone-600">
                        {{ $role->description ?: 'Belum ada deskripsi untuk peran ini.' }}
                    </p>

                    <!-- Users & Permission Count Pills -->
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.index', ['role_id' => $role->id]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-stone-50 border border-[#ede8df] px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 text-stone-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span>{{ $role->users_count }} Pengguna</span>
                        </a>

                        <div class="inline-flex items-center gap-1.5 rounded-xl bg-[#f8eee7] px-3 py-1.5 text-xs font-semibold text-[#b55322]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>
                                @if ($role->name === 'super_admin')
                                    Semua Hak Akses (Penuh)
                                @else
                                    {{ $role->permissions->count() }} / {{ $totalPermissions }} Hak Akses
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Permission Tag Previews -->
                    <div class="mt-4 border-t border-[#f2efe9] pt-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">Cakupan Izin Akses:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @if ($role->name === 'super_admin')
                                <span class="rounded-lg bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 text-[11px] font-medium text-emerald-800">
                                    ✓ Akses Penuh ke Semua Fitur & Data Sistem
                                </span>
                            @else
                                @forelse ($role->permissions->take(6) as $p)
                                    <span class="rounded-lg bg-stone-100 px-2 py-0.5 text-[11px] font-medium text-stone-700">
                                        {{ $p->display_name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-stone-400 italic">Belum ada hak akses yang dicentang.</span>
                                @endforelse

                                @if ($role->permissions->count() > 6)
                                    <span class="rounded-lg bg-stone-200/80 px-2 py-0.5 text-[11px] font-bold text-stone-600">
                                        +{{ $role->permissions->count() - 6 }} lainnya
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 flex items-center justify-end gap-2 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="openEditRoleModal({{ json_encode([
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                        'description' => $role->description,
                        'is_system' => (bool)$role->is_system,
                        'permission_ids' => $role->permissions->pluck('id')->toArray(),
                    ]) }})" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e2ddd5] bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 text-stone-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Edit Peran & Wewenang</span>
                    </button>

                    @if (!$role->is_system && $role->users_count === 0)
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran {{ $role->display_name }}?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl p-1.5 text-stone-400 hover:bg-rose-50 hover:text-rose-600 transition cursor-pointer" title="Hapus Peran">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah Peran Baru -->
    <div id="addRoleModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-2xl max-h-[90vh] flex flex-col rounded-3xl bg-white shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] px-6 py-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Tambah Peran Baru</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Buat klasifikasi peran dan pilih hak akses yang diizinkan.</p>
                </div>
                <button type="button" onclick="closeAddRoleModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST" class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Tampilan Peran</label>
                        <input type="text" name="display_name" required placeholder="Contoh: Koordinator Logistik" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kode Identitas (Slug)</label>
                        <input type="text" name="name" required placeholder="Contoh: koordinator_logistik" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Deskripsi Tanggung Jawab</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan ruang lingkup wewenang pemegang peran ini..." class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition"></textarea>
                </div>

                <!-- Matrix Permission Checkbox Grouped -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-stone-800 uppercase tracking-wider">Pilih Hak Akses (Permissions)</label>
                        <button type="button" onclick="toggleAllAddPermissions(true)" class="text-[11px] font-semibold text-[#b55322] hover:underline cursor-pointer">Centang Semua</button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($groupedPermissions as $groupName => $perms)
                            <div class="rounded-2xl border border-[#ede8df] bg-[#faf8f4] p-4">
                                <div class="flex items-center justify-between border-b border-[#f0ede6] pb-2.5 mb-3">
                                    <span class="text-xs font-bold text-stone-800 flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-[#b55322]"></span>
                                        {{ $groupName }}
                                    </span>
                                    <button type="button" onclick="toggleGroupCheckboxes('add_group_{{ Str::slug($groupName) }}')" class="text-[10px] font-semibold text-stone-500 hover:text-stone-900 cursor-pointer">
                                        Pilih / Batal Grup Ini
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach ($perms as $p)
                                        <label class="flex items-start gap-2.5 rounded-xl bg-white border border-[#e8e4dc] p-2.5 text-xs cursor-pointer hover:border-[#b55322]/50 transition">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="add-permission-checkbox add_group_{{ Str::slug($groupName) }} mt-0.5 h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]">
                                            <div>
                                                <p class="font-semibold text-stone-900 leading-tight">{{ $p->display_name }}</p>
                                                <p class="text-[10px] font-mono text-stone-400 mt-0.5">{{ $p->name }}</p>
                                                @if ($p->description)
                                                    <p class="text-[10px] text-stone-500 mt-0.5 line-clamp-1">{{ $p->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeAddRoleModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Simpan Peran Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Peran & Wewenang -->
    <div id="editRoleModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-2xl max-h-[90vh] flex flex-col rounded-3xl bg-white shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] px-6 py-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Edit Peran & Hak Akses</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Sesuaikan wewenang dan izin modul untuk peran ini.</p>
                </div>
                <button type="button" onclick="closeEditRoleModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editRoleForm" method="POST" class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Tampilan Peran</label>
                        <input type="text" name="display_name" id="edit_display_name" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kode Identitas (Slug)</label>
                        <input type="text" id="edit_name" disabled class="w-full rounded-xl border border-[#e2ddd5] bg-stone-100 px-3.5 py-2.5 text-xs text-stone-500 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Deskripsi Tanggung Jawab</label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition"></textarea>
                </div>

                <!-- Matrix Permission Checkbox for Edit -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-stone-800 uppercase tracking-wider">Atur Hak Akses</label>
                        <div id="editSuperAdminNotice" class="hidden text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                            Super Admin selalu memiliki akses penuh otomatis.
                        </div>
                    </div>

                    <div id="editPermissionsWrap" class="space-y-4">
                        @foreach ($groupedPermissions as $groupName => $perms)
                            <div class="rounded-2xl border border-[#ede8df] bg-[#faf8f4] p-4">
                                <div class="flex items-center justify-between border-b border-[#f0ede6] pb-2.5 mb-3">
                                    <span class="text-xs font-bold text-stone-800 flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-[#b55322]"></span>
                                        {{ $groupName }}
                                    </span>
                                    <button type="button" onclick="toggleGroupCheckboxes('edit_group_{{ Str::slug($groupName) }}')" class="text-[10px] font-semibold text-stone-500 hover:text-stone-900 cursor-pointer">
                                        Pilih / Batal Grup Ini
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach ($perms as $p)
                                        <label class="flex items-start gap-2.5 rounded-xl bg-white border border-[#e8e4dc] p-2.5 text-xs cursor-pointer hover:border-[#b55322]/50 transition">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" id="edit_perm_{{ $p->id }}" class="edit-permission-checkbox edit_group_{{ Str::slug($groupName) }} mt-0.5 h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]">
                                            <div>
                                                <p class="font-semibold text-stone-900 leading-tight">{{ $p->display_name }}</p>
                                                <p class="text-[10px] font-mono text-stone-400 mt-0.5">{{ $p->name }}</p>
                                                @if ($p->description)
                                                    <p class="text-[10px] text-stone-500 mt-0.5 line-clamp-1">{{ $p->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeEditRoleModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Perbarui Peran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Role Modals -->
    <script>
        function openAddRoleModal() {
            document.getElementById('addRoleModal').classList.remove('hidden');
        }
        function closeAddRoleModal() {
            document.getElementById('addRoleModal').classList.add('hidden');
        }

        function toggleAllAddPermissions(val) {
            document.querySelectorAll('.add-permission-checkbox').forEach(function(cb) {
                cb.checked = val;
            });
        }

        function toggleGroupCheckboxes(groupClass) {
            var cbs = document.querySelectorAll('.' + groupClass);
            if (!cbs.length) return;
            var anyUnchecked = Array.from(cbs).some(function(cb) { return !cb.checked; });
            cbs.forEach(function(cb) {
                cb.checked = anyUnchecked;
            });
        }

        function openEditRoleModal(role) {
            var form = document.getElementById('editRoleForm');
            form.action = "{{ url('admin/roles') }}/" + role.id;

            document.getElementById('edit_name').value = role.name;
            document.getElementById('edit_display_name').value = role.display_name;
            document.getElementById('edit_description').value = role.description || '';

            // Reset all checkboxes
            document.querySelectorAll('.edit-permission-checkbox').forEach(function(cb) {
                cb.checked = false;
            });

            // Check attached permissions
            if (role.permission_ids && role.permission_ids.length) {
                role.permission_ids.forEach(function(id) {
                    var el = document.getElementById('edit_perm_' + id);
                    if (el) el.checked = true;
                });
            }

            var isSuperAdmin = (role.name === 'super_admin');
            document.getElementById('editSuperAdminNotice').classList.toggle('hidden', !isSuperAdmin);

            document.getElementById('editRoleModal').classList.remove('hidden');
        }

        function closeEditRoleModal() {
            document.getElementById('editRoleModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddRoleModal();
                closeEditRoleModal();
            }
        });
    </script>
@endsection
