<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($programInfo->nama_program ?? 'Jemaat Bali') . ' — Donasi & Kaos Fundraiser')</title>
    <meta name="description" content="@yield('description', $programInfo->deskripsi ?? 'Situs penggalangan dana pembangunan Jemaat Bali: progres, proposal, desain kaos, dan donasi.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-canvas text-ink">
    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-white transition-all">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-primary font-display text-sm font-extrabold text-white shadow-sm transition group-hover:scale-105">{{ $programInfo->initials ?? 'JB' }}</span>
                <div class="flex flex-col">
                    <span class="font-display text-base font-extrabold tracking-tight text-ink sm:text-lg leading-tight">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                    <span class="text-[10px] font-medium uppercase tracking-wider text-muted hidden sm:block">Bangun Bersama</span>
                </div>
            </a>

            <nav class="hidden items-center gap-1.5 lg:flex">
                @php
                    $nav = [
                        ['home', 'Home', route('home')],
                        ['proposal', 'Proposal', route('proposal')],
                        ['progres', 'Progres Pembangunan', route('progres')],
                        ['design', 'Design Kaos', route('design')],
                        ['shop', 'Shop + Donasi', route('shop')],
                    ];
                @endphp
                @foreach ($nav as [$route, $label, $url])
                    <a href="{{ $url }}"
                        class="rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ request()->routeIs($route) ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('shop') }}" class="btn !bg-primary !text-white hover:!bg-primary-dark rounded-full !px-5 !py-2 text-sm font-semibold shadow-sm transition">
                    Donasi
                </a>
                <button id="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false"
                    class="grid h-10 w-10 place-items-center rounded-full border border-stone-200 bg-white text-stone-700 shadow-xs transition hover:bg-stone-50 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="nav-menu" class="hidden border-t border-line bg-white lg:hidden">
            <div class="mx-auto max-w-6xl px-4 py-3 sm:px-6">
                <div class="flex flex-col gap-1">
                    @foreach ($nav as [$route, $label, $url])
                        <a href="{{ $url }}"
                            class="rounded-xl px-3 py-3 text-sm font-semibold {{ request()->routeIs($route) ? 'bg-primary/10 text-primary' : 'text-ink' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10 w-full bg-canvas">
        @yield('content')
    </main>

    <footer class="relative z-20 border-t border-line bg-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-primary font-display text-sm font-extrabold text-white">{{ $programInfo->initials ?? 'JB' }}</span>
                    <span class="font-display text-base font-extrabold">{{ $programInfo->nama_program ?? 'Jemaat Bali' }}</span>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    {{ $programInfo->deskripsi ?? 'Penggalangan dana pembangunan bersama. Transparan: setiap progres kami unggah di sini.' }}
                </p>
            </div>

            <div>
                <h3 class="font-display text-sm font-bold uppercase tracking-wide text-muted">Menu</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($nav as [, $label, $url])
                        <li><a href="{{ $url }}" class="text-ink hover:text-primary">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="font-display text-sm font-bold uppercase tracking-wide text-muted">Kontak</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-ink">
                    <li class="font-medium text-stone-900">Panitia {{ $programInfo->nama_program ?? 'Pembangunan Jemaat Bali' }}</li>
                    @if(!empty($programInfo->whatsapp))
                        <li>
                            <a href="{{ $programInfo->wa_url }}" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-primary transition inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#25D366] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>WhatsApp: {{ $programInfo->whatsapp }}</span>
                            </a>
                        </li>
                    @endif
                    @if(!empty($programInfo->email))
                        <li>
                            <a href="mailto:{{ $programInfo->email }}" class="text-muted hover:text-primary transition inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                <span>{{ $programInfo->email }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
                <a href="{{ route('admin.orders') }}" class="mt-3 inline-block text-sm font-semibold text-primary hover:underline">
                    Data masuk (pengurus) &rarr;
                </a>
            </div>
        </div>
        <div class="border-t border-line">
            <p class="mx-auto max-w-6xl px-4 py-5 text-xs text-muted sm:px-6">
                &copy; {{ date('Y') }} {{ $programInfo->nama_program ?? 'Jemaat Bali' }} — Donasi &amp; Kaos Fundraiser.
            </p>
        </div>
    </footer>

    <script>
        (function () {
            var toggle = document.getElementById('nav-toggle');
            var menu = document.getElementById('nav-menu');
            if (!toggle || !menu) return;
            toggle.addEventListener('click', function () {
                var open = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!open));
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
