<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administrasi') — {{ $programInfo->nama_program ?? 'Jemaat Bali' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f8f6f0] font-sans text-stone-900 antialiased selection:bg-[#b55322]/20 selection:text-[#b55322]">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-[#e8e4dc] bg-white select-none">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 border-b border-[#e8e4dc] px-6 py-5">
                <span class="grid h-10 w-10 place-items-center rounded-full bg-[#b55322] font-serif text-sm font-bold text-white shadow-xs shrink-0">{{ $programInfo->initials ?? 'JB' }}</span>
                <div>
                    <p class="font-serif text-[17px] font-bold text-stone-900 leading-tight">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-stone-400 mt-0.5">BANGUN BERSAMA</p>
                </div>
            </div>

            <!-- Section Label -->
            <div class="px-6 pt-5 pb-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-stone-400">RUANG PENGELOLA</p>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <rect width="7" height="7" x="3" y="3" rx="1.5" />
                        <rect width="7" height="7" x="14" y="3" rx="1.5" />
                        <rect width="7" height="7" x="14" y="14" rx="1.5" />
                        <rect width="7" height="7" x="3" y="14" rx="1.5" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.verification') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.verification*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span class="truncate">Verifikasi Pembayaran</span>
                    @php $pendingCount = App\Models\Donation::where('status', 'menunggu_verifikasi')->count(); @endphp
                    @if ($pendingCount)
                        <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.verification*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.orders*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l1.5 2.25a3 3 0 0 0 4.5 0l1.5-2.25 5.25 2.25-1.5 4.5h-2.25V20.25a.75.75 0 0 1-.75.75H7.5a.75.75 0 0 1-.75-.75V11.25H4.5L3 6.75 8.25 4.5z" />
                    </svg>
                    <span>Pesanan Kaos</span>
                </a>

                <a href="{{ route('admin.designs') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.designs*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    <span class="truncate">Koleksi Kaos</span>
                    @php $designsCount = App\Models\Design::where('aktif', 1)->count(); @endphp
                    <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.designs*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $designsCount }}</span>
                </a>

                <a href="{{ route('admin.donations') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.donations*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <span>Donasi</span>
                </a>

                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.reports*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                    <span>Laporan Dana</span>
                </a>

                <a href="{{ route('admin.expenses.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.expenses*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75a.75.75 0 0 1 .75.75v.75m0-9.75h18a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25H3.75m18-9a2.25 2.25 0 0 0-2.25-2.25H3.75" />
                    </svg>
                    <span class="truncate">Dana Pengeluaran</span>
                    @php $expensesCount = App\Models\Expense::count(); @endphp
                    <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.expenses*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $expensesCount }}</span>
                </a>

                <a href="{{ route('admin.content') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.content*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span>Pengelolaan Konten</span>
                </a>

                <a href="{{ route('admin.provider') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.provider*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-.974-.94 4.025 4.025 0 0 0 .524-1.782C3.125 16.71 2.25 14.475 2.25 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                    </svg>
                    <span>Provider WA</span>
                    @php $wsSetting = \App\Models\WhatsappSetting::first(); @endphp
                    @if ($wsSetting && $wsSetting->isConnected())
                        <span class="ml-auto inline-block h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100" title="Fonnte Terhubung"></span>
                    @elseif($wsSetting && $wsSetting->isConfigured())
                        <span class="ml-auto inline-block h-2 w-2 rounded-full bg-amber-500 ring-2 ring-amber-100" title="Menunggu Koneksi"></span>
                    @endif
                </a>

                <!-- Section Label: MANAJEMEN AKSES -->
                <div class="pt-4 pb-1 px-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-stone-400">PENGGUNA & HAK AKSES</p>
                </div>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.users*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>Pengguna</span>
                    @php $usersCount = App\Models\User::count(); @endphp
                    <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.users*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $usersCount }}</span>
                </a>

                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.roles*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span>Peran (Roles)</span>
                    @php $rolesCount = App\Models\Role::count(); @endphp
                    <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.roles*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $rolesCount }}</span>
                </a>

                <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-[13px] font-semibold transition {{ request()->routeIs('admin.permissions*') ? 'bg-[#f8eee7] text-[#b55322]' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                    <span class="truncate">Hak Akses</span>
                    @php $permsCount = App\Models\Permission::count(); @endphp
                    <span class="ml-auto text-xs font-semibold shrink-0 {{ request()->routeIs('admin.permissions*') ? 'text-[#b55322]' : 'text-stone-400' }}">{{ $permsCount }}</span>
                </a>
            </nav>

            <!-- Program Widget in Sidebar -->
            <div class="px-3.5 pb-2">
                <div class="rounded-2xl bg-[#f5f2ec] border border-[#ede8df] p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-stone-400">PROGRAM {{ date('Y') }}</p>
                    <p class="mt-1 font-serif text-sm font-bold text-stone-900 leading-snug line-clamp-1">{{ $programInfo->nama_program ?? 'Membangun rumah jemaat.' }}</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-stone-500 line-clamp-2">{{ $programInfo->deskripsi ?? 'Donasi & kaos fundraiser untuk pembangunan.' }}</p>
                    <a href="{{ route('home') }}" target="_blank" class="mt-2.5 inline-flex items-center gap-1.5 text-xs font-semibold text-[#b55322] hover:underline">
                        <span>Lihat website publik</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div class="border-t border-[#e8e4dc] px-5 py-4 space-y-2.5">
                <div class="flex items-center gap-2.5 text-xs font-medium text-stone-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-400 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span>Akses: {{ Auth::user()->role_display_name }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 text-xs font-medium text-stone-500 hover:text-stone-900 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4 text-stone-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Keluar dari akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="ml-64 flex min-w-0 flex-1 flex-col min-h-screen bg-[#f8f6f0]">
            <!-- Topbar Header -->
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-[#e8e4dc] bg-[#f8f6f0]/95 px-8 py-3.5 backdrop-blur-sm">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-xs text-stone-400">
                    <span>Administrasi</span>
                    <span class="text-stone-300">&rsaquo;</span>
                    <span class="font-semibold text-stone-800">@yield('breadcrumb', 'Dashboard')</span>
                </div>

                <!-- Right Utility Badges & User Profile -->
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#faefe8] px-3 py-1 text-xs font-semibold text-[#b55322]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#b55322]"></span>
                        Data contoh
                    </span>

                    <button type="button" aria-label="Notifikasi" class="grid h-9 w-9 place-items-center rounded-full border border-[#e2ddd5] bg-white text-stone-600 hover:bg-stone-50 transition cursor-pointer shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.966 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2.5 pl-1 cursor-pointer">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#ede8df] text-xs font-bold text-stone-800 shrink-0">SA</span>
                        <div class="text-left">
                            <p class="text-xs font-bold text-stone-900 leading-tight">Super Admin</p>
                            <p class="text-[10px] text-stone-400 leading-tight">Panitia {{ $programInfo->nama_program ?? 'Jemaat Bali' }}</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-stone-400 ml-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </header>

            <!-- Main Content Slot -->
            <main class="flex-1 p-8">
                @yield('content')
            </main>

            <!-- Admin Footer -->
            <footer class="flex items-center justify-between border-t border-[#e8e4dc] px-8 py-4 text-xs text-stone-400">
                <span>Data aktif program &middot; Terhubung ke sistem informasi publik</span>
                <span class="font-semibold tracking-widest text-stone-400 uppercase">{{ $programInfo->nama_program ?? 'JEMAAT BALI' }} &copy; {{ date('Y') }}</span>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
