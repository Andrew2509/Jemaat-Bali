@extends('layouts.admin')

@section('title', 'Integrasi & Provider')
@section('breadcrumb', 'Provider & Integrasi')

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
        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-xs text-red-800 shadow-2xs">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-red-200 text-xs font-bold text-red-900 mt-0.5">✕</span>
                <div>
                    <p class="font-bold">Terdapat kesalahan pengisian formulir:</p>
                    <ul class="mt-1 list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="font-serif text-2xl font-bold tracking-tight text-stone-900">
                        {!! request('tab') === 'rajaongkir' ? 'Provider RajaOngkir (Lokasi & Ekspedisi)' : 'Provider WhatsApp (Fonnte)' !!}
                    </h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#e8e4dc] bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-stone-600 shadow-2xs">
                        {{ request('tab') === 'rajaongkir' ? 'API V2 (Komerce)' : 'API v1.0' }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-stone-500 leading-relaxed">
                    {{ request('tab') === 'rajaongkir'
                        ? 'Integrasi API resmi RajaOngkir untuk pencarian hierarki Provinsi, Kota/Kabupaten, Kecamatan, dan otomatisasi kode pos pada pemesanan pakaian.'
                        : 'Integrasi WhatsApp Gateway resmi via Fonnte API untuk notifikasi otomatis pemesanan dan bukti donasi.' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if (request('tab') === 'rajaongkir')
                    <a href="https://rajaongkir.com/docs" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>Dokumentasi RajaOngkir</span>
                    </a>
                @else
                    <a href="https://docs.fonnte.com/category/api/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>Dokumentasi Fonnte</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Provider Tabs Navigation --}}
        <div class="flex items-center gap-2 border-b border-[#e8e4dc] pb-3">
            <a href="{{ route('admin.provider', ['tab' => 'whatsapp']) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition {{ request('tab', 'whatsapp') !== 'rajaongkir' ? 'bg-[#b55322] text-white shadow-2xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-[#e8e4dc]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>WhatsApp (Fonnte)</span>
                @if ($setting->isConnected())
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                @endif
            </a>

            <a href="{{ route('admin.provider', ['tab' => 'rajaongkir']) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition {{ request('tab') === 'rajaongkir' ? 'bg-[#b55322] text-white shadow-2xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-[#e8e4dc]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.375c0-.621-.504-1.125-1.125-1.125H4.125C3.504 2.25 3 2.754 3 3.375v10.875" />
                </svg>
                <span>RajaOngkir (Lokasi & Ekspedisi)</span>
                @if ($shippingSetting && $shippingSetting->isConnected())
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                @endif
            </a>
        </div>

        @if (request('tab') === 'rajaongkir')
            {{-- ======================================================== --}}
            {{-- TAB: RAJAONGKIR (LOKASI & EKSPEDISI) --}}
            {{-- ======================================================== --}}

            {{-- 1. Status Koneksi RajaOngkir --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-stone-100 pb-5">
                    <div class="flex items-center gap-3.5">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl {{ $shippingSetting->isConnected() ? 'bg-emerald-50 text-emerald-600' : 'bg-[#faefe8] text-[#b55322]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-serif text-base font-bold text-stone-900">Status Provider RajaOngkir</h2>
                                @if ($shippingSetting->isConnected())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Terhubung (Live API)
                                    </span>
                                @elseif ($shippingSetting->isConfigured())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 border border-amber-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu Uji Coba
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-2.5 py-0.5 text-[11px] font-bold text-stone-600 border border-stone-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                                        Mode Cadangan (38 Provinsi Aktif)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-stone-500 mt-0.5">
                                Endpoint Utama: <code class="rounded bg-stone-100 px-1.5 py-0.5 text-[11px] text-stone-700">{{ $shippingSetting->getEffectiveBaseUrl() }}</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.provider.rajaongkir.test') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs transition hover:bg-stone-50 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-3.5 w-3.5 text-stone-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>Uji Koneksi API</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Status Metrics Grid --}}
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Cakupan Wilayah</span>
                        <p class="mt-1 font-serif text-lg font-bold text-stone-900">
                            {{ $shippingSetting->provinces_count > 0 ? $shippingSetting->provinces_count . ' Provinsi' : '38 Provinsi (Nasional)' }}
                        </p>
                        <span class="text-[10px] text-stone-400">Seluruh Indonesia</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Otomatisasi Kode Pos</span>
                        <p class="mt-1 font-serif text-lg font-bold text-emerald-600">Aktif ✓</p>
                        <span class="text-[10px] text-stone-400">Terisi saat pilih kecamatan</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Status Form Checkout</span>
                        <p class="mt-1 font-serif text-lg font-bold text-stone-900">
                            {{ $shippingSetting->is_active ? 'Terhubung' : 'Non-Aktif' }}
                        </p>
                        <span class="text-[10px] text-stone-400">/pesan-kaos</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Terakhir Dicek</span>
                        <p class="mt-1 font-serif text-sm font-bold text-stone-900">
                            {{ $shippingSetting->last_checked_at ? $shippingSetting->last_checked_at->diffForHumans() : 'Belum Pernah' }}
                        </p>
                        <span class="text-[10px] text-stone-400">{{ $shippingSetting->last_checked_at ? $shippingSetting->last_checked_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                </div>

                @if ($shippingSetting->last_error)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/60 p-3 text-xs text-amber-800">
                        <span class="font-bold">Catatan respons terakhir:</span> {{ $shippingSetting->last_error }}
                    </div>
                @endif
            </section>

            {{-- 2. Form Konfigurasi RajaOngkir --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <h2 class="font-serif text-base font-bold text-stone-900">Konfigurasi Kunci API RajaOngkir</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Dapatkan API Key di akun <a href="https://rajaongkir.com" target="_blank" class="text-[#b55322] font-semibold hover:underline">RajaOngkir</a> untuk pencarian alamat real-time.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.provider.rajaongkir.update') }}" class="mt-5 space-y-5">
                    @csrf

                    <div>
                        <label for="rajaongkir_api_key" class="block text-xs font-semibold text-stone-700 mb-1.5">
                            API Key RajaOngkir <span class="text-stone-400 font-normal">(Header: Key)</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="rajaongkir_api_key" name="api_key" value="{{ old('api_key', $shippingSetting->getEffectiveApiKey()) }}" placeholder="Tempelkan API Key (contoh: 8c3f...)" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none pr-24">
                            <button type="button" id="toggle-rajaongkir-key" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-stone-100 px-2.5 py-1 text-[11px] font-medium text-stone-600 hover:bg-stone-200 transition cursor-pointer">
                                Lihat
                            </button>
                        </div>
                        <p class="mt-1 text-[11px] text-stone-500">
                            Masukkan kode token API saja (sistem otomatis membersihkan jika ada tulisan <code>Key:</code> di depannya).
                        </p>
                    </div>

                    <div>
                        <label for="rajaongkir_base_url" class="block text-xs font-semibold text-stone-700 mb-1.5">
                            Base URL API RajaOngkir <span class="text-stone-400 font-normal">(Default V2 Komerce)</span>
                        </label>
                        <input type="text" id="rajaongkir_base_url" name="base_url" value="{{ old('base_url', $shippingSetting->base_url ?: 'https://rajaongkir.komerce.id/api/v1') }}" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none">
                        <p class="mt-1 text-[11px] text-stone-500">Gunakan <code class="bg-stone-100 px-1 rounded">https://rajaongkir.komerce.id/api/v1</code> sesuai dokumentasi resmi RajaOngkir Docs V2.</p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-stone-800">
                            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]" @checked(old('is_active', $shippingSetting->is_active))>
                            Aktifkan integrasi RajaOngkir di form pemesanan
                        </label>
                    </div>

                    <div class="flex justify-end border-t border-stone-100 pt-4">
                        <button type="submit" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                            Simpan Konfigurasi RajaOngkir
                        </button>
                    </div>
                </form>
            </section>

            {{-- 3. Panduan Endpoint Terhubung --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <h3 class="font-serif text-base font-bold text-stone-900 mb-2">Endpoint RajaOngkir yang Terhubung</h3>
                <p class="text-xs text-stone-500 mb-4">Hierarki pemilihan wilayah bertingkat yang diaktifkan pada sistem pemesanan pakaian:</p>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-stone-900">1. Search Province</span>
                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">GET</span>
                        </div>
                        <code class="text-[11px] font-mono text-stone-700 block truncate">/destination/province</code>
                        <p class="mt-1 text-[11px] text-stone-500">Mengambil daftar 38 provinsi di Indonesia.</p>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-stone-900">2. Search City</span>
                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">GET</span>
                        </div>
                        <code class="text-[11px] font-mono text-stone-700 block truncate">/destination/city/{province_id}</code>
                        <p class="mt-1 text-[11px] text-stone-500">Mengambil daftar Kota / Kabupaten dalam provinsi yang dipilih.</p>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-stone-900">3. Search District (Kecamatan)</span>
                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">GET</span>
                        </div>
                        <code class="text-[11px] font-mono text-stone-700 block truncate">/destination/district/{city_id}</code>
                        <p class="mt-1 text-[11px] text-stone-500">Mengambil daftar Kecamatan beserta informasi <b class="text-emerald-700">zip_code</b>.</p>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-stone-900">4. Search Sub-District</span>
                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">GET</span>
                        </div>
                        <code class="text-[11px] font-mono text-stone-700 block truncate">/destination/sub-district/{district_id}</code>
                        <p class="mt-1 text-[11px] text-stone-500">Mendukung resolusi kode pos spesifik per kelurahan.</p>
                    </div>
                </div>
            </section>
        @else
            {{-- ======================================================== --}}
            {{-- TAB: WHATSAPP GATEWAY (FONNTE) --}}
            {{-- ======================================================== --}}

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
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Perangkat</span>
                        <p class="mt-1 font-serif text-lg font-bold text-stone-900">{{ $setting->device_name ?: 'Belum Terhubung' }}</p>
                        <span class="text-[10px] text-stone-400 font-mono">{{ $setting->device_id ?: '-' }}</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Nomor Pengirim</span>
                        <p class="mt-1 font-serif text-lg font-bold text-stone-900">{{ $setting->sender_number ?: '-' }}</p>
                        <span class="text-[10px] text-stone-400">Kode Negara: +{{ $setting->country_code }}</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Sisa Kuota Pesan</span>
                        <p class="mt-1 font-serif text-lg font-bold text-stone-900">{{ number_format($setting->quota, 0, ',', '.') }}</p>
                        <span class="text-[10px] text-stone-400">Paket: {{ strtoupper($setting->package ?: 'Free/Reguler') }}</span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3.5">
                        <span class="text-[11px] font-medium text-stone-500">Pembaruan Terakhir</span>
                        <p class="mt-1 font-serif text-sm font-bold text-stone-900">
                            {{ $setting->last_checked_at ? $setting->last_checked_at->diffForHumans() : 'Belum Pernah' }}
                        </p>
                        <span class="text-[10px] text-stone-400">{{ $setting->last_checked_at ? $setting->last_checked_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                </div>
            </section>

            {{-- 2. Form Konfigurasi Token & Opsi Otomatisasi --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <h2 class="font-serif text-base font-bold text-stone-900">Konfigurasi API Token & Opsi Pengiriman</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Kelola token autentikasi Fonnte dan pilih notifikasi yang aktif otomatis.</p>
                    </div>
                </div>

                <form id="provider-config-form" method="POST" action="{{ route('admin.provider.update') }}" class="mt-5 space-y-5">
                    @csrf

                    <div>
                        <label for="api_token" class="block text-xs font-semibold text-stone-700 mb-1.5">
                            Fonnte API Token <span class="text-stone-400 font-normal">(Authorization Header)</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="api_token" name="api_token" value="{{ old('api_token', $setting->api_token) }}" placeholder="Contoh: pz#123abc456def..." class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none pr-24">
                            <button type="button" id="toggle-token-btn" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-stone-100 px-2.5 py-1 text-[11px] font-medium text-stone-600 hover:bg-stone-200 transition">
                                Lihat
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="country_code" class="block text-xs font-semibold text-stone-700 mb-1.5">Default Kode Negara</label>
                            <input type="text" id="country_code" name="country_code" value="{{ old('country_code', $setting->country_code) }}" placeholder="62" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2.5 text-xs text-stone-800 focus:border-[#b55322] focus:outline-none">
                        </div>

                        <div class="flex items-center pt-5">
                            <label class="flex cursor-pointer items-center gap-2.5 text-xs font-semibold text-stone-800">
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]" @checked(old('is_active', $setting->is_active))>
                                Aktifkan Pengiriman WhatsApp
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-stone-100 pt-4">
                        <span class="block text-xs font-bold text-stone-800 mb-3">Pemicu Pengiriman Otomatis (Auto Triggers):</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-2.5 rounded-xl border border-[#e8e4dc] bg-stone-50/50 p-3 cursor-pointer hover:bg-stone-50 transition">
                                <input type="checkbox" name="auto_notify_waiting" value="1" class="mt-0.5 h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]" @checked(old('auto_notify_waiting', $setting->auto_notify_waiting))>
                                <div>
                                    <span class="block text-xs font-bold text-stone-800">1. Menunggu Konfirmasi</span>
                                    <span class="text-[11px] text-stone-500 leading-tight">Kirim saat bukti bayar diupload.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 rounded-xl border border-[#e8e4dc] bg-stone-50/50 p-3 cursor-pointer hover:bg-stone-50 transition">
                                <input type="checkbox" name="auto_notify_verified" value="1" class="mt-0.5 h-4 w-4 rounded border-stone-300 text-[#b55322] focus:ring-[#b55322]" @checked(old('auto_notify_verified', $setting->auto_notify_verified))>
                                <div>
                                    <span class="block text-xs font-bold text-stone-800">2. Terverifikasi</span>
                                    <span class="text-[11px] text-stone-500 leading-tight">Kirim saat admin setujui pembayaran.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </section>

            {{-- 3. Uji Coba Kirim Pesan --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <h2 class="font-serif text-base font-bold text-stone-900 mb-1">Uji Coba Pengiriman Pesan (Sandbox Test)</h2>
                <p class="text-xs text-stone-500 mb-4">Kirim pesan WhatsApp tes ke nomor tujuan untuk memastikan Fonnte berjalan lancar.</p>

                <form id="test-send-form" method="POST" action="{{ route('admin.provider.test') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="test_target" class="block text-xs font-semibold text-stone-700 mb-1">Nomor WhatsApp Tujuan</label>
                            <input type="text" id="test_target" name="target" placeholder="Contoh: 08123456789" class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs text-stone-800 focus:border-[#b55322] focus:outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="test_message" class="block text-xs font-semibold text-stone-700 mb-1">Isi Pesan Uji Coba</label>
                            <input type="text" id="test_message" name="message" value="Halo, ini pesan uji coba WhatsApp resmi dari Sistem Jemaat Bali melalui Fonnte Gateway." class="w-full rounded-xl border border-[#e8e4dc] bg-white px-3.5 py-2 text-xs text-stone-800 focus:border-[#b55322] focus:outline-none" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div id="test-feedback" class="hidden text-xs"></div>
                        <button type="submit" id="btn-submit-test" class="ml-auto inline-flex items-center gap-2 rounded-xl bg-stone-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-stone-800 cursor-pointer">
                            <span id="btn-submit-text">Kirim Pesan WhatsApp Tes</span>
                        </button>
                    </div>
                </form>
            </section>

            {{-- 4. Template Pesan WhatsApp --}}
            <section class="rounded-2xl border border-[#e8e4dc] bg-white p-5 sm:p-6 shadow-2xs">
                <div class="border-b border-stone-100 pb-4">
                    <h2 class="font-serif text-base font-bold text-stone-900">Templat Pesan WhatsApp</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Sesuaikan teks pesan WhatsApp yang dikirimkan kepada jemaat pada tiap tahapan.</p>
                </div>

                <div class="mt-6 space-y-6">
                    {{-- Template 1: Menunggu Konfirmasi --}}
                    <div class="space-y-3 rounded-2xl border border-amber-200/70 bg-amber-50/30 p-4">
                        <div class="flex items-center justify-between">
                            <label for="template_waiting" class="text-xs font-bold text-amber-950">1. Menunggu Konfirmasi (Upload Bukti)</label>
                            <span class="rounded bg-amber-200/70 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-900">Tahap 1</span>
                        </div>
                        <textarea id="template_waiting" form="provider-config-form" name="template_waiting" rows="10" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none leading-relaxed">{{ old('template_waiting', $setting->template_waiting ?: \App\Models\WhatsappSetting::defaultWaitingTemplate()) }}</textarea>
                    </div>

                    {{-- Template 2: Pembayaran Terverifikasi --}}
                    <div class="space-y-3 rounded-2xl border border-emerald-200/70 bg-emerald-50/30 p-4">
                        <div class="flex items-center justify-between">
                            <label for="template_verified" class="text-xs font-bold text-emerald-950">2. Pembayaran Terverifikasi</label>
                            <span class="rounded bg-emerald-200/70 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-900">Tahap 2</span>
                        </div>
                        <textarea id="template_verified" form="provider-config-form" name="template_verified" rows="10" class="w-full rounded-xl border border-[#e8e4dc] bg-white p-3 text-xs font-mono text-stone-800 focus:border-[#b55322] focus:outline-none leading-relaxed">{{ old('template_verified', $setting->template_verified ?: \App\Models\WhatsappSetting::defaultVerifiedTemplate()) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end border-t border-stone-100 pt-4">
                    <button type="submit" form="provider-config-form" class="rounded-xl bg-[#b55322] hover:bg-[#9d4419] px-6 py-2.5 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        Simpan Semua Perubahan
                    </button>
                </div>
            </section>
        @endif

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
            // Toggle Token Fonnte
            const tokenInput = document.getElementById('api_token');
            const toggleBtn = document.getElementById('toggle-token-btn');
            if (tokenInput && toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = tokenInput.type === 'password';
                    tokenInput.type = isPassword ? 'text' : 'password';
                    toggleBtn.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
                });
            }

            // Toggle Key RajaOngkir
            const rajaKeyInput = document.getElementById('rajaongkir_api_key');
            const toggleRajaKey = document.getElementById('toggle-rajaongkir-key');
            if (rajaKeyInput && toggleRajaKey) {
                toggleRajaKey.addEventListener('click', function () {
                    const isPassword = rajaKeyInput.type === 'password';
                    rajaKeyInput.type = isPassword ? 'text' : 'password';
                    toggleRajaKey.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
                });
            }

            // AJAX Test Send Fonnte
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
