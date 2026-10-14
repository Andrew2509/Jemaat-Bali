@extends('layouts.admin')

@section('title', 'Provider WhatsApp (Fonnte)')
@section('breadcrumb', 'Provider WhatsApp')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3.5 text-xs text-emerald-800 shadow-2xs">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-200 text-xs font-bold text-emerald-900">✓</span>
                <p class="font-medium leading-relaxed">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('warning'))
            <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3.5 text-xs text-amber-800 shadow-2xs">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-amber-200 text-xs font-bold text-amber-900">!</span>
                <p class="font-medium leading-relaxed">{{ session('warning') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-xs text-red-800 shadow-2xs">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-red-200 text-xs font-bold text-red-900">✕</span>
                <p class="font-medium leading-relaxed">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="font-serif text-2xl font-bold tracking-tight text-stone-900">Provider WhatsApp (Fonnte)</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#e8e4dc] bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-stone-600 shadow-2xs">
                        API v1.0
                    </span>
                </div>
                <p class="mt-1 text-xs text-stone-500 leading-relaxed">
                    Integrasi WhatsApp Gateway resmi via <a href="https://docs.fonnte.com/category/api/" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#b55322] hover:underline">Fonnte API</a> untuk notifikasi otomatis pemesanan dan bukti donasi.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="https://docs.fonnte.com/category/api/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Dokumentasi API</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3 text-stone-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                </a>
                <a href="https://fonnte.com" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl bg-stone-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-stone-800">
                    <span>Dashboard Fonnte</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- 1. Status Perangkat (Device Connection Card) --}}
        <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-stone-100 pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl {{ $setting->isConnected() ? 'bg-emerald-50 text-emerald-600' : 'bg-[#faefe8] text-[#b55322]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-serif text-base font-bold text-stone-900">Status Koneksi Perangkat Fonnte</h2>
                            @if ($setting->isConnected())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Terhubung (Connect)
                                </span>
                            @elseif ($setting->isConfigured())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 border border-amber-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Terputus (Disconnect)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-2.5 py-0.5 text-[11px] font-bold text-stone-600 border border-stone-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                    Belum Diatur
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-500 mt-0.5">
                            Endpoint: <code class="rounded bg-stone-100 px-1.5 py-0.5 text-[11px] text-stone-700">POST https://api.fonnte.com/device</code>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.provider.check') }}">
                        @csrf
                        <button type="submit" id="btn-sync-device" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span>Perbarui Status</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Device Metrics Grid --}}
            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 text-xs">
                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Nomor WhatsApp</span>
                    <span class="mt-1 block font-mono text-xs font-bold text-stone-900 truncate">
                        {{ $setting->device_phone ?: '—' }}
                    </span>
                </div>

                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Nama Perangkat</span>
                    <span class="mt-1 block font-semibold text-stone-900 truncate">
                        {{ $setting->device_name ?: '—' }}
                    </span>
                </div>

                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Kuota Pesan</span>
                    <span class="mt-1 block font-semibold text-stone-900 truncate">
                        {{ $setting->quota !== null ? $setting->quota . ' pesan' : '—' }}
                    </span>
                </div>

                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Paket Fonnte</span>
                    <span class="mt-1 block font-semibold uppercase text-stone-900 truncate">
                        {{ $setting->package ?: 'Regular' }}
                    </span>
                </div>

                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Masa Aktif</span>
                    <span class="mt-1 block font-semibold text-stone-900 truncate">
                        {{ $setting->expired ?: '—' }}
                    </span>
                </div>

                <div class="rounded-xl border border-stone-100 bg-[#fbf9f5] p-3.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Terakhir Dicek</span>
                    <span class="mt-1 block text-stone-600 truncate">
                        {{ $setting->last_checked_at ? $setting->last_checked_at->format('d M H:i') : 'Belum' }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Form Konfigurasi & Uji Coba (2 Kolom) --}}
        <div class="grid gap-6 lg:grid-cols-12">

            {{-- Kolom Kiri: Form Konfigurasi Token & Fitur --}}
            <div class="lg:col-span-7 space-y-6">
                <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                    <div class="border-b border-stone-100 pb-4">
                        <h2 class="font-serif text-base font-bold text-stone-900">Konfigurasi Fonnte API</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Masukkan token perangkat yang diperoleh dari akun Fonnte Anda.</p>
                    </div>

                    <form id="provider-config-form" method="POST" action="{{ route('admin.provider.update') }}" class="mt-5 space-y-4">
                        @csrf

                        {{-- Toggle Status Integrasi --}}
                        <div class="flex items-center justify-between rounded-xl border border-[#ede8df] bg-[#fbf9f5] p-4">
                            <div>
                                <label for="is_active" class="text-xs font-bold text-stone-900 cursor-pointer">Aktifkan Pengiriman WhatsApp</label>
                                <p class="text-[11px] text-stone-500 mt-0.5">Jika aktif, sistem otomatis mengirimkan pesan WA melalui Fonnte.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="is_active" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-stone-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#b55322]"></div>
                            </label>
                        </div>

                        {{-- Token Device Fonnte --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="api_token" class="text-xs font-semibold text-stone-700">Fonnte Device Token <span class="text-red-500">*</span></label>
                                <a href="https://fonnte.com" target="_blank" class="text-[11px] font-semibold text-[#b55322] hover:underline">Ambil token di Fonnte &rarr;</a>
                            </div>
                            <div class="relative">
                                <input type="password" id="api_token" name="api_token" value="{{ old('api_token', $setting->api_token) }}" placeholder="Contoh: pz#XXXXXXXXXXXXX" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 pr-20 text-xs font-mono text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                                <button type="button" id="toggle-token-btn" class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-[11px] font-semibold text-stone-500 hover:text-stone-800 hover:bg-stone-100 transition">
                                    Lihat
                                </button>
                            </div>
                            <p class="mt-1.5 text-[11px] text-stone-400">
                                Buka Fonnte &gt; Menu <b>Device</b> &gt; Klik nama perangkat WhatsApp &gt; Salin <b>Token</b>.
                            </p>
                        </div>

                        {{-- Country Code --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="country_code" class="block text-xs font-semibold text-stone-700 mb-1.5">Country Code</label>
                                <input type="text" id="country_code" name="country_code" value="{{ old('country_code', $setting->country_code ?: '62') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                                <p class="mt-1 text-[10px] text-stone-400">Default: 62 (Indonesia)</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Metode API</label>
                                <div class="w-full rounded-xl border border-[#ede8df] bg-[#fbf9f5] px-3.5 py-2.5 text-xs text-stone-600 font-mono">
                                    POST /send
                                </div>
                                <p class="mt-1 text-[10px] text-stone-400">Header: Authorization</p>
                            </div>
                        </div>

                        {{-- Notifikasi Otomatis Toggles --}}
                        <div class="space-y-2.5 pt-2">
                            <span class="block text-xs font-bold text-stone-900">Pemicu Pengiriman Otomatis</span>

                            <label class="flex items-start gap-3 rounded-xl border border-[#ede8df] bg-[#fbf9f5] p-3 text-xs text-stone-700 cursor-pointer hover:bg-stone-50 transition">
                                <input type="checkbox" name="auto_notify_order" value="1" class="mt-0.5 rounded text-[#b55322] focus:ring-[#b55322]" {{ old('auto_notify_order', $setting->auto_notify_order) ? 'checked' : '' }}>
                                <div>
                                    <span class="font-semibold text-stone-900">Kirim notifikasi saat pesanan baru dibuat</span>
                                    <p class="text-[11px] text-stone-500 mt-0.5">Kirim WhatsApp berisi rincian pesanan dan tautan upload bukti transfer ke donatur.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 rounded-xl border border-[#ede8df] bg-[#fbf9f5] p-3 text-xs text-stone-700 cursor-pointer hover:bg-stone-50 transition">
                                <input type="checkbox" name="auto_notify_waiting" value="1" class="mt-0.5 rounded text-[#b55322] focus:ring-[#b55322]" {{ old('auto_notify_waiting', $setting->auto_notify_waiting) ? 'checked' : '' }}>
                                <div>
                                    <span class="font-semibold text-stone-900">Kirim notifikasi saat bukti diunggah (Menunggu Konfirmasi)</span>
                                    <p class="text-[11px] text-stone-500 mt-0.5">Kirim WhatsApp otomatis ke pemesan begitu bukti transfer diunggah bahwa pesanan sedang menunggu verifikasi panitia.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 rounded-xl border border-[#ede8df] bg-[#fbf9f5] p-3 text-xs text-stone-700 cursor-pointer hover:bg-stone-50 transition">
                                <input type="checkbox" name="auto_notify_verified" value="1" class="mt-0.5 rounded text-[#b55322] focus:ring-[#b55322]" {{ old('auto_notify_verified', $setting->auto_notify_verified) ? 'checked' : '' }}>
                                <div>
                                    <span class="font-semibold text-stone-900">Kirim notifikasi saat pembayaran terverifikasi</span>
                                    <p class="text-[11px] text-stone-500 mt-0.5">Kirim ucapan terima kasih dan konfirmasi bahwa dana telah diterima panitia.</p>
                                </div>
                            </label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full rounded-xl bg-[#b55322] hover:bg-[#9d4419] py-3 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Simpan Konfigurasi</span>
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            {{-- Kolom Kanan: Uji Kirim Pesan & Panduan Cepat --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- Live Test Send Card --}}
                <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                    <div class="border-b border-stone-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">▶</span>
                            <h2 class="font-serif text-base font-bold text-stone-900">Uji Kirim WhatsApp Langsung</h2>
                        </div>
                        <p class="text-xs text-stone-500 mt-1">Uji coba kirim pesan WhatsApp ke nomor HP Anda untuk memastikan Fonnte berjalan normal.</p>
                    </div>

                    <form id="test-send-form" method="POST" action="{{ route('admin.provider.test') }}" class="mt-4 space-y-3.5">
                        @csrf

                        <div>
                            <label for="test-target" class="block text-xs font-semibold text-stone-700 mb-1">Nomor WhatsApp Tujuan</label>
                            <input type="text" id="test-target" name="target" placeholder="Contoh: 081234567890" value="{{ old('whatsapp', $programInfo->whatsapp ?? '') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none" required>
                            <p class="mt-1 text-[10px] text-stone-400">Format: 08... atau 62...</p>
                        </div>

                        <div>
                            <label for="test-message" class="block text-xs font-semibold text-stone-700 mb-1">Pesan Uji Coba</label>
                            <textarea id="test-message" name="message" rows="3" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-900 focus:border-[#b55322] focus:outline-none leading-relaxed" required>Halo! Ini adalah pesan uji coba dari sistem donasi pembangunan Jemaat Bali melalui Fonnte API.</textarea>
                        </div>

                        <div id="test-feedback" class="hidden rounded-xl p-3 text-xs"></div>

                        <button type="submit" id="btn-submit-test" class="w-full rounded-xl bg-stone-900 hover:bg-stone-800 py-2.5 text-xs font-bold text-white shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span id="btn-submit-text">Kirim Pesan WhatsApp Tes</span>
                        </button>
                    </form>
                </section>

                {{-- Panduan 3 Langkah --}}
                <section class="rounded-2xl border border-[#ede8df] bg-[#fbf9f5] p-5 shadow-2xs text-xs">
                    <h3 class="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-3">Panduan Menghubungkan Fonnte</h3>
                    <ol class="space-y-3 text-stone-600 leading-relaxed list-decimal list-inside">
                        <li>
                            <b class="text-stone-900">Daftar Akun:</b> Buat akun gratis di <a href="https://fonnte.com" target="_blank" class="text-[#b55322] font-semibold underline">fonnte.com</a>.
                        </li>
                        <li>
                            <b class="text-stone-900">Scan QR Code WhatsApp:</b> Pada dashboard Fonnte, buka menu <b>Device</b>, tambahkan perangkat lalu scan QR code menggunakan aplikasi WhatsApp di HP panitia.
                        </li>
                        <li>
                            <b class="text-stone-900">Salin &amp; Simpan Token:</b> Salin <b>Token Perangkat</b> lalu tempelkan di form konfigurasi sebelah kiri, simpan, dan klik tombol <b>Uji Coba</b>.
                        </li>
                    </ol>

                    <div class="mt-4 pt-3 border-t border-stone-200/80 text-[11px] text-stone-500">
                        <span class="font-semibold text-stone-700">Tips Penting:</span>
                        Pastikan HP panitia memiliki koneksi internet aktif agar pesan WhatsApp dapat terkirim secara otomatis kapan pun ada transaksi.
                    </div>
                </section>
            </div>
        </div>

        {{-- 3. Template Pesan Otomatis --}}
        <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
            <div class="border-b border-stone-100 pb-4">
                <div class="flex items-center gap-2">
                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-[#faefe8] text-[#b55322] text-xs font-bold">📝</span>
                    <h2 class="font-serif text-base font-bold text-stone-900">Format Template Pesan WhatsApp</h2>
                </div>
                <p class="text-xs text-stone-500 mt-1">
                    Sesuaikan pesan WhatsApp otomatis yang dikirim kepada pemesan &amp; donatur. Klik tombol tag variabel untuk menyisipkannya ke dalam template.
                </p>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                {{-- Template 1: Pesanan Baru --}}
                <div class="space-y-3 rounded-2xl border border-stone-100 bg-[#fbf9f5] p-4">
                    <div class="flex items-center justify-between">
                        <label for="template_order" class="text-xs font-bold text-stone-900">1. Pesanan Baru Dibuat</label>
                        <span class="rounded bg-stone-200/70 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-stone-700">Tahap 1</span>
                    </div>

                    {{-- Variabel Chips --}}
                    <div class="flex flex-wrap gap-1 text-[11px]">
                        @php
                            $varsOrder = ['{nama}', '{nomor_pesanan}', '{jenis}', '{total}', '{metode}', '{link}', '{nama_program}'];
                        @endphp
                        @foreach ($varsOrder as $v)
                            <button type="button" onclick="insertVariable('template_order', '{{ $v }}')" class="rounded-md border border-stone-200 bg-white px-1.5 py-0.5 font-mono text-[10px] font-semibold text-stone-700 hover:border-[#b55322] hover:text-[#b55322] transition cursor-pointer">
                                + {{ $v }}
                            </button>
                        @endforeach
                    </div>

                    <textarea id="template_order" form="provider-config-form" name="template_order" rows="12" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none leading-relaxed">{{ old('template_order', $setting->template_order ?: \App\Models\WhatsappSetting::defaultOrderTemplate()) }}</textarea>
                </div>

                {{-- Template 2: Menunggu Konfirmasi (Setelah Upload Bukti) --}}
                <div class="space-y-3 rounded-2xl border border-amber-200/70 bg-amber-50/30 p-4">
                    <div class="flex items-center justify-between">
                        <label for="template_waiting" class="text-xs font-bold text-amber-950">2. Menunggu Konfirmasi (Upload Bukti)</label>
                        <span class="rounded bg-amber-200/70 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-900">Tahap 2</span>
                    </div>

                    {{-- Variabel Chips --}}
                    <div class="flex flex-wrap gap-1 text-[11px]">
                        @php
                            $varsWaiting = ['{nama}', '{nomor_pesanan}', '{jenis}', '{total}', '{metode}', '{link}', '{nama_program}'];
                        @endphp
                        @foreach ($varsWaiting as $v)
                            <button type="button" onclick="insertVariable('template_waiting', '{{ $v }}')" class="rounded-md border border-amber-200 bg-white px-1.5 py-0.5 font-mono text-[10px] font-semibold text-stone-700 hover:border-[#b55322] hover:text-[#b55322] transition cursor-pointer">
                                + {{ $v }}
                            </button>
                        @endforeach
                    </div>

                    <textarea id="template_waiting" form="provider-config-form" name="template_waiting" rows="12" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none leading-relaxed">{{ old('template_waiting', $setting->template_waiting ?: \App\Models\WhatsappSetting::defaultWaitingTemplate()) }}</textarea>
                </div>

                {{-- Template 3: Pembayaran Terverifikasi --}}
                <div class="space-y-3 rounded-2xl border border-emerald-200/70 bg-emerald-50/30 p-4">
                    <div class="flex items-center justify-between">
                        <label for="template_verified" class="text-xs font-bold text-emerald-950">3. Pembayaran Terverifikasi</label>
                        <span class="rounded bg-emerald-200/70 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-900">Tahap 3</span>
                    </div>

                    {{-- Variabel Chips --}}
                    <div class="flex flex-wrap gap-1 text-[11px]">
                        @php
                            $varsVerified = ['{nama}', '{nomor_pesanan}', '{rincian}', '{produk}', '{ukuran}', '{jumlah}', '{total}', '{nominal}', '{metode}', '{alamat}', '{link}', '{catatan}', '{nama_program}'];
                        @endphp
                        @foreach ($varsVerified as $v)
                            <button type="button" onclick="insertVariable('template_verified', '{{ $v }}')" class="rounded-md border border-emerald-200 bg-white px-1.5 py-0.5 font-mono text-[10px] font-semibold text-stone-700 hover:border-[#b55322] hover:text-[#b55322] transition cursor-pointer">
                                + {{ $v }}
                            </button>
                        @endforeach
                    </div>

                    <textarea id="template_verified" form="provider-config-form" name="template_verified" rows="12" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none leading-relaxed">{{ old('template_verified', $setting->template_verified ?: \App\Models\WhatsappSetting::defaultVerifiedTemplate()) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-stone-100 pt-4">
                <button type="submit" form="provider-config-form" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                    Simpan Semua Perubahan
                </button>
            </div>
        </section>

    </div>

    {{-- Script untuk Show/Hide Token, Insert Variabel, dan AJAX Test Send --}}
    <script>
        function insertVariable(textareaId, variable) {
            const el = document.getElementById(textareaId);
            if (!el) return;
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const text = el.value;
            el.value = text.substring(0, start) + variable + text.substring(end);
            el.focus();
            el.selectionStart = el.selectionEnd = start + variable.length;
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Toggle Token Visibility
            const tokenInput = document.getElementById('api_token');
            const toggleBtn = document.getElementById('toggle-token-btn');
            if (tokenInput && toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = tokenInput.type === 'password';
                    tokenInput.type = isPassword ? 'text' : 'password';
                    toggleBtn.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
                });
            }

            // AJAX Test Send Handler
            const testForm = document.getElementById('test-send-form');
            const feedbackEl = document.getElementById('test-feedback');
            const btnSubmit = document.getElementById('btn-submit-test');
            const btnText = document.getElementById('btn-submit-text');

            if (testForm && feedbackEl) {
                testForm.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    feedbackEl.className = 'hidden rounded-xl p-3 text-xs';
                    feedbackEl.textContent = '';
                    btnSubmit.disabled = true;
                    btnText.textContent = 'Sedang Mengirim ke Fonnte...';

                    const formData = new FormData(testForm);

                    try {
                        const response = await fetch(testForm.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const res = await response.json();

                        if (response.ok && res.success) {
                            feedbackEl.className = 'rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800';
                            feedbackEl.textContent = '✓ ' + (res.message || 'Pesan berhasil dikirim via Fonnte!');
                        } else {
                            feedbackEl.className = 'rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-800';
                            feedbackEl.textContent = '✕ ' + (res.message || 'Gagal mengirim pesan via Fonnte.');
                        }
                    } catch (err) {
                        feedbackEl.className = 'rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-800';
                        feedbackEl.textContent = '✕ Terjadi kesalahan jaringan: ' + err.message;
                    } finally {
                        btnSubmit.disabled = false;
                        btnText.textContent = 'Kirim Pesan WhatsApp Tes';
                    }
                });
            }
        });
    </script>
@endsection
