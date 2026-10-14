@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Pengguna')

@section('content')
    <!-- Header Page Row -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#b55322]">MANAJEMEN AKSES / PENGGUNA</p>
            <h1 class="mt-1 font-serif text-3xl md:text-[34px] font-bold text-stone-900 tracking-tight">Pengguna & Staf</h1>
            <p class="mt-1 text-sm text-stone-500">Kelola akun administrator, staf operasional, dan hak akses yang diberikan ke masing-masing pengelola.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#e2ddd5] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Kelola Peran (Roles)</span>
            </a>
            <button type="button" onclick="openAddUserModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Pengguna</span>
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
                Terjadi kesalahan:
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
        <!-- Total Pengguna -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Total Pengguna</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-stone-100 text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $totalUsers }}</p>
            <p class="mt-2 text-xs text-stone-400">Akun terdaftar dalam sistem</p>
        </div>

        <!-- Super Admin -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Super Administrator</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $superAdminCount }}</p>
            <p class="mt-2 text-xs text-stone-400">Hak penuh atas semua modul</p>
        </div>

        <!-- Staff / Admin Lainnya -->
        <div class="rounded-2xl border border-[#e8e4dc] bg-white p-5 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-stone-500">Staf & Operasional</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-sky-50 text-sky-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-stone-900 leading-none">{{ $staffCount }}</p>
            <p class="mt-2 text-xs text-stone-400">Dibatasi sesuai izin peran</p>
        </div>

        <!-- Akun Aktif -->
        <div class="rounded-2xl bg-[#444d41] p-5 text-white flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-white/70">Akun Aktif</p>
                <span class="grid h-8 w-8 place-items-center rounded-xl bg-white/10 text-white">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
                    </span>
                </span>
            </div>
            <p class="mt-3 font-serif text-[28px] font-bold text-white leading-none">{{ $activeUsers }} <span class="text-xs font-sans font-normal text-white/60">/ {{ $totalUsers }}</span></p>
            <p class="mt-2 text-xs text-white/60">Dapat masuk dan beroperasi</p>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="mb-6 rounded-2xl border border-[#e8e4dc] bg-white p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[240px]">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email pengguna..." class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/60 py-2 pl-9 pr-3 text-xs text-stone-900 placeholder-stone-400 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
            </div>

            <!-- Role Filter -->
            <div class="w-48">
                <select name="role_id" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/60 px-3 py-2 text-xs text-stone-800 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    <option value="">Semua Peran</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}" {{ (string)$roleId === (string)$r->id ? 'selected' : '' }}>
                            {{ $r->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-36">
                <select name="status" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/60 px-3 py-2 text-xs text-stone-800 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    <option value="">Semua Status</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-stone-900 px-4 py-2 text-xs font-semibold text-white hover:bg-stone-800 transition cursor-pointer">
                    <span>Terapkan</span>
                </button>
                @if ($search || $roleId || $status !== null && $status !== '')
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-xl border border-[#e2ddd5] bg-white px-3 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="overflow-hidden rounded-2xl border border-[#e8e4dc] bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#e8e4dc] bg-[#faf8f4] text-[11px] font-bold uppercase tracking-wider text-stone-500">
                        <th class="py-3.5 pl-6 pr-4">Pengguna</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 pl-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0ede6] text-xs">
                    @forelse ($users as $u)
                        <tr class="hover:bg-stone-50/80 transition">
                            <!-- Pengguna -->
                            <td class="py-4 pl-6 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 place-items-center rounded-full font-serif font-bold text-sm shrink-0 {{ $u->isSuperAdmin() ? 'bg-amber-100 text-amber-800 ring-2 ring-amber-200/70' : 'bg-[#f8eee7] text-[#b55322]' }}">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-stone-900 truncate">{{ $u->name }}</p>
                                            @if ($u->id === Auth::id())
                                                <span class="rounded-md bg-stone-100 px-1.5 py-0.5 text-[10px] font-bold text-stone-600">Anda</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-stone-500 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="py-4 px-4">
                                @php
                                    $roleName = $u->roleModel?->name ?? $u->role;
                                    $roleBadge = match($roleName) {
                                        'super_admin' => 'bg-amber-50 text-amber-800 border-amber-200/80',
                                        'admin' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                        'bendahara' => 'bg-indigo-50 text-indigo-800 border-indigo-200/80',
                                        'operator' => 'bg-sky-50 text-sky-800 border-sky-200/80',
                                        default => 'bg-stone-100 text-stone-700 border-stone-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[11px] font-semibold {{ $roleBadge }}">
                                    @if ($u->isSuperAdmin())
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-amber-600">
                                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    <span>{{ $u->role_display_name }}</span>
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold cursor-pointer transition {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-stone-100 text-stone-500 hover:bg-stone-200' }}" title="Klik untuk mengubah status aktif/nonaktif">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                                        <span>{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Terdaftar -->
                            <td class="py-4 px-4 text-stone-500 text-[11px]">
                                {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 pl-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditUserModal({{ json_encode([
                                        'id' => $u->id,
                                        'name' => $u->name,
                                        'email' => $u->email,
                                        'role_id' => $u->role_id,
                                        'is_active' => (bool)$u->is_active,
                                    ]) }})" class="rounded-lg p-1.5 text-stone-500 hover:bg-stone-100 hover:text-stone-900 transition cursor-pointer" title="Edit Data Pengguna">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    @if ($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-stone-400 hover:bg-rose-50 hover:text-rose-600 transition cursor-pointer" title="Hapus Pengguna">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-stone-500">
                                <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-stone-100 text-stone-400 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </div>
                                <p class="font-serif text-base font-bold text-stone-800">Tidak ada data pengguna</p>
                                <p class="text-xs text-stone-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-[#e8e4dc] px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pengguna -->
    <div id="addUserModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] pb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Tambah Pengguna Baru</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Berikan akses sistem kepada staf atau pengelola baru.</p>
                </div>
                <button type="button" onclick="closeAddUserModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Yohanes Prasetya" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required placeholder="yohanes@jemaatbali.or.id" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                        <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi sandi" class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Peran (Role)</label>
                    <select name="role_id" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]">
                    <label for="add_is_active" class="text-xs font-medium text-stone-700 select-none cursor-pointer">Akun langsung aktif dan dapat masuk sistem</label>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeAddUserModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden bg-stone-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl border border-[#ede8df] animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-[#f0ede6] pb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-stone-900">Edit Data Pengguna</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Perbarui informasi profil, peran, atau kata sandi pengguna.</p>
                </div>
                <button type="button" onclick="closeEditUserModal()" class="rounded-xl p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editUserForm" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                </div>

                <div class="rounded-2xl bg-stone-50 border border-[#e8e4dc] p-3.5 space-y-2.5">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Ganti Kata Sandi (Opsional)</p>
                    <p class="text-[11px] text-stone-400">Kosongkan bila tidak ingin mengubah kata sandi pengguna.</p>
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <input type="password" name="password" minlength="8" placeholder="Sandi baru" class="w-full rounded-xl border border-[#e2ddd5] bg-white px-3 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:outline-hidden transition">
                        <input type="password" name="password_confirmation" minlength="8" placeholder="Konfirmasi sandi" class="w-full rounded-xl border border-[#e2ddd5] bg-white px-3 py-2 text-xs text-stone-900 focus:border-[#b55322] focus:outline-hidden transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Peran (Role)</label>
                    <select name="role_id" id="edit_role_id" required class="w-full rounded-xl border border-[#e2ddd5] bg-stone-50/50 px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:bg-white focus:outline-hidden transition">
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]">
                    <label for="edit_is_active" class="text-xs font-medium text-stone-700 select-none cursor-pointer">Akun aktif dan diizinkan masuk</label>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-[#f0ede6] pt-4">
                    <button type="button" onclick="closeEditUserModal()" class="rounded-xl border border-[#e2ddd5] bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-4.5 py-2 text-xs font-semibold text-white shadow-2xs transition cursor-pointer">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Modals -->
    <script>
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
        }
        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }

        function openEditUserModal(user) {
            var form = document.getElementById('editUserForm');
            form.action = "{{ url('admin/users') }}/" + user.id;

            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            if (user.role_id) {
                document.getElementById('edit_role_id').value = user.role_id;
            }
            document.getElementById('edit_is_active').checked = Boolean(user.is_active);

            document.getElementById('editUserModal').classList.remove('hidden');
        }
        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddUserModal();
                closeEditUserModal();
            }
        });
    </script>
@endsection
