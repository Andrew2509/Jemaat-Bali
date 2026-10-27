@extends('layouts.app')

@section('title', 'Informasi Pengiriman — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <a href="{{ route('checkout.pemesanan', ['desain' => $selected->id]) }}" class="text-sm font-semibold text-muted hover:text-primary">&larr; Kembali ke data pemesan</a>
    <div class="mt-6 flex gap-3 text-xs font-bold uppercase tracking-widest"><span class="text-muted">01 Data pemesan</span><span class="text-primary">— 02 Pengiriman</span><span class="text-muted">— 03 Pembayaran</span></div>
    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-serif text-3xl font-extrabold">Informasi Pengiriman</h1>
            <p class="mt-1 text-sm text-muted">Langkah 2 dari 3 · Pastikan alamat tujuan lengkap dan benar.</p>
        </div>
        @if (!empty($isRajaOngkirLive))
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> RajaOngkir Terhubung
            </span>
        @endif
    </div>
    <div class="mt-4 rounded-xl bg-stone-50 border border-stone-200 p-3.5 text-xs text-stone-600 flex items-center gap-2">
        <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
        </svg>
        <span>Pilih wilayah secara berurutan: <strong class="text-stone-800">Provinsi &rarr; Kota/Kabupaten &rarr; Kecamatan &rarr; Kelurahan</strong>. Kode pos akan terisi secara otomatis dari RajaOngkir.</span>
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <form action="{{ route('checkout.pengiriman.store') }}" method="POST" class="card space-y-5 p-6 sm:p-8 lg:col-span-2">
            @csrf
            @if ($errors->any())<div class="rounded-xl bg-error/5 p-4 text-sm text-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <input type="hidden" name="_identity" value="">
            <div><label for="alamat" class="label">Alamat lengkap *</label><textarea id="alamat" name="alamat" rows="3" class="input" placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan" required>{{ old('alamat', $draft['alamat'] ?? '') }}</textarea>@error('alamat')<p class="error-text">{{ $message }}</p>@enderror</div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <div class="flex items-center justify-between">
                        <label for="provinsi" class="label">Provinsi *</label>
                        <span id="loading-provinsi" class="hidden text-[11px] text-primary font-medium flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memuat...
                        </span>
                    </div>
                    <select id="provinsi" name="provinsi" class="input @error('provinsi') input-error @enderror" required>
                        <option value="">— Pilih Provinsi —</option>
                        @foreach ($provinsi as $p)
                            @php
                                $pName = is_array($p) ? ($p['name'] ?? '') : $p;
                                $pId = is_array($p) ? ($p['id'] ?? '') : '';
                            @endphp
                            <option value="{{ $pName }}" data-id="{{ $pId }}" @selected(strtoupper((string) old('provinsi', $draft['provinsi'] ?? '')) === strtoupper((string) $pName))>
                                {{ $pName }}
                            </option>
                        @endforeach
                    </select>
                    @error('provinsi')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="kabupaten" class="label">Kota / Kabupaten *</label>
                        <span id="loading-kabupaten" class="hidden text-[11px] text-primary font-medium flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memuat kota...
                        </span>
                    </div>
                    <select id="kabupaten" name="kabupaten" class="input @error('kabupaten') input-error @enderror" required disabled>
                        <option value="">— Pilih Provinsi Terlebih Dahulu —</option>
                    </select>
                    <input type="hidden" id="old-kabupaten" value="{{ old('kabupaten', $draft['kabupaten'] ?? '') }}">
                    @error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="kecamatan" class="label">Kecamatan *</label>
                        <span id="loading-kecamatan" class="hidden text-[11px] text-primary font-medium flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memuat kecamatan...
                        </span>
                    </div>
                    <select id="kecamatan" name="kecamatan" class="input @error('kecamatan') input-error @enderror" required disabled>
                        <option value="">— Pilih Kota/Kabupaten Dahulu —</option>
                    </select>
                    <input type="hidden" id="old-kecamatan" value="{{ old('kecamatan', $draft['kecamatan'] ?? '') }}">
                    @error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="kelurahan" class="label">Kelurahan / Desa</label>
                        <span id="loading-kelurahan" class="hidden text-[11px] text-primary font-medium flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memuat kelurahan...
                        </span>
                    </div>
                    <select id="kelurahan" name="kelurahan" class="input @error('kelurahan') input-error @enderror" disabled>
                        <option value="">— Pilih Kecamatan Dahulu —</option>
                    </select>
                    <input type="hidden" id="old-kelurahan" value="{{ old('kelurahan', $draft['kelurahan'] ?? '') }}">
                    @error('kelurahan')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="kode_pos" class="label">Kode pos *</label>
                        <span id="badge-kodepos" class="hidden text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            Otomatis RajaOngkir ✓
                        </span>
                    </div>
                    <input id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $draft['kode_pos'] ?? '') }}" class="input @error('kode_pos') input-error @enderror" inputmode="numeric" placeholder="Masukkan kode pos" required>
                    <p class="mt-1 text-[11px] text-stone-500">Terisi otomatis saat memilih kelurahan / kecamatan.</p>
                    @error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between"><h2 class="label">Daftar kaos *</h2><button type="button" data-open-size-chart class="text-xs font-bold text-primary hover:underline">Panduan ukuran</button></div>
                    <div id="co-cart" class="mt-2 space-y-3"></div>
                    <button type="button" id="co-add-item" class="btn btn-outline mt-3">+ Tambahkan baju lainnya</button>
                    <div id="co-hidden-items"></div>
                    @error('items')<p class="error-text">{{ $message }}</p>@enderror
                    @error('items.*.ukuran')<p class="error-text">{{ $message }}</p>@enderror
                </div>
            </div>
            <div><label for="catatan" class="label">Catatan <span class="font-normal text-muted">(opsional)</span></label><input id="catatan" name="catatan" value="{{ old('catatan', $draft['catatan'] ?? '') }}" class="input" maxlength="500"></div>
            @if (($tipe ?? 'kaos_saja') === 'kaos_donasi')
                <div><label for="donasi" class="label">Donasi sukarela (Rp)</label><input id="donasi" name="donasi" type="number" min="0" max="100000000" step="5000" value="{{ old('donasi', $draft['donasi'] ?? 50000) }}" class="input"></div>
            @endif
            <div class="rounded-xl bg-primary/5 p-4 text-sm text-ink"><strong>Ongkir belum dihitung.</strong> Panitia akan mengonfirmasi ongkir dan total melalui WhatsApp.</div>
            <button class="btn btn-primary w-full !py-3.5" type="submit">Simpan alamat &amp; lanjut ke pembayaran &rarr;</button>
        </form>
        <aside class="card overflow-hidden p-0"><img src="{{ asset($selected->gambar) }}" alt="{{ $selected->nama }}" class="aspect-[4/3] w-full object-cover"><div class="p-5"><h2 class="font-serif text-lg font-bold">{{ $selected->nama }}</h2><p class="mt-2 text-sm text-muted">{{ $draft['nama'] }}</p><p class="mt-2 font-bold">Rp {{ number_format($selected->harga, 0, ',', '.') }} / kaos</p></div></aside>
    </div>
</section>
@include('components.size-chart-modal')
@push('scripts')
<script>
(function() {
    const form = document.querySelector('form[action="{{ route('checkout.pengiriman.store') }}"]');
    const cart = document.getElementById('co-cart');
    const hidden = document.getElementById('co-hidden-items');
    const designs = {!! json_encode($designsJson) !!};
    const initial = {!! json_encode($initialItems) !!};
    const money = value => new Intl.NumberFormat('id-ID').format(value);

    function render() {
        cart.innerHTML = initial.map((item, index) => {
            const selected = designs.find(design => design.id == item.design_id) || designs[0];
            return `<div class="grid gap-3 rounded-xl border border-stone-200 p-4 sm:grid-cols-3">
                <label class="text-sm font-semibold">Desain<select data-field="design_id" class="input mt-1">${designs.map(design => `<option value="${design.id}" ${selected.id == design.id ? 'selected' : ''}>${design.nama} — Rp${money(design.harga)}</option>`).join('')}</select></label>
                <label class="text-sm font-semibold">Ukuran<select data-field="ukuran" class="input mt-1" required><option value="">Pilih ukuran</option>${selected.sizes.map(size => `<option value="${size}" ${item.ukuran === size ? 'selected' : ''}>${size}</option>`).join('')}</select></label>
                <label class="text-sm font-semibold">Jumlah<input data-field="jumlah" type="number" min="1" max="100" value="${item.jumlah || 1}" class="input mt-1" required></label>
                ${initial.length > 1 ? `<button type="button" data-remove="${index}" class="text-left text-sm font-semibold text-error">Hapus kaos</button>` : ''}
            </div>`;
        }).join('');
        hidden.innerHTML = initial.map((item, index) => `<input type="hidden" name="items[${index}][design_id]" value="${item.design_id}"><input type="hidden" name="items[${index}][ukuran]" value="${item.ukuran}"><input type="hidden" name="items[${index}][jumlah]" value="${item.jumlah}">`).join('');
    }

    cart.addEventListener('change', event => {
        const row = event.target.closest('.grid');
        if (!row) return;
        const index = [...cart.children].indexOf(row);
        initial[index][event.target.dataset.field] = event.target.value;
        if (event.target.dataset.field === 'design_id') initial[index].ukuran = '';
        render();
    });
    cart.addEventListener('input', event => {
        if (event.target.dataset.field === 'jumlah') {
            const index = [...cart.children].indexOf(event.target.closest('.grid'));
            initial[index].jumlah = event.target.value;
            render();
        }
    });
    cart.addEventListener('click', event => {
        const index = event.target.dataset.remove;
        if (index !== undefined) { initial.splice(index, 1); render(); }
    });
    document.getElementById('co-add-item').addEventListener('click', () => {
        initial.push({design_id: designs[0].id, ukuran: '', jumlah: 1});
        render();
    });
    form.addEventListener('submit', () => {
        [...cart.querySelectorAll('.grid')].forEach((row, index) => row.querySelectorAll('[data-field]').forEach(input => initial[index][input.dataset.field] = input.value));
        render();
        const kab = document.getElementById('kabupaten');
        const kec = document.getElementById('kecamatan');
        const kel = document.getElementById('kelurahan');
        if (kab) kab.disabled = false;
        if (kec) kec.disabled = false;
        if (kel) kel.disabled = false;
    });
    render();
})();

// ==========================================
// Integrasi RajaOngkir (Cascading Wilayah & Kode Pos)
// ==========================================
(function () {
    const provSelect = document.getElementById('provinsi');
    const kabSelect = document.getElementById('kabupaten');
    const kecSelect = document.getElementById('kecamatan');
    const kelSelect = document.getElementById('kelurahan');
    const posInput = document.getElementById('kode_pos');
    const badgePos = document.getElementById('badge-kodepos');

    const loadProv = document.getElementById('loading-provinsi');
    const loadKab = document.getElementById('loading-kabupaten');
    const loadKec = document.getElementById('loading-kecamatan');
    const loadKel = document.getElementById('loading-kelurahan');

    const oldKab = document.getElementById('old-kabupaten')?.value?.trim() || '';
    const oldKec = document.getElementById('old-kecamatan')?.value?.trim() || '';
    const oldKel = document.getElementById('old-kelurahan')?.value?.trim() || '';

    if (!provSelect || !kabSelect || !kecSelect) return;

    const cityCache = {};
    const districtCache = {};
    const subdistrictCache = {};

    function getSelectedOption(selectEl) {
        return selectEl.options[selectEl.selectedIndex];
    }

    function loadCities(provId, selectedCityName) {
        kabSelect.disabled = true;
        kabSelect.innerHTML = '<option value="">Memuat kota...</option>';
        kecSelect.disabled = true;
        kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
        if (kelSelect) {
            kelSelect.disabled = true;
            kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
        }
        if (loadKab) loadKab.classList.remove('hidden');

        function populate(cities) {
            if (loadKab) loadKab.classList.add('hidden');
            kabSelect.innerHTML = '<option value="">— Pilih Kota / Kabupaten —</option>';
            let matchedCityId = null;

            cities.forEach(function (c) {
                const opt = document.createElement('option');
                opt.value = c.name;
                opt.textContent = c.name;
                opt.dataset.id = c.id;
                if (selectedCityName && selectedCityName.toUpperCase() === c.name.toUpperCase()) {
                    opt.selected = true;
                    matchedCityId = c.id;
                }
                kabSelect.appendChild(opt);
            });

            kabSelect.disabled = false;

            if (matchedCityId) {
                loadDistricts(matchedCityId, oldKec);
            }
        }

        if (cityCache[provId]) {
            populate(cityCache[provId]);
            return;
        }

        fetch('/api/rajaongkir/cities?province_id=' + encodeURIComponent(provId))
            .then(r => r.json())
            .then(res => {
                const data = (res && res.data) ? res.data : [];
                cityCache[provId] = data;
                populate(data);
            })
            .catch(() => {
                if (loadKab) loadKab.classList.add('hidden');
                kabSelect.innerHTML = '<option value="">Gagal memuat kota, silakan coba lagi</option>';
                kabSelect.disabled = false;
            });
    }

    function loadDistricts(cityId, selectedDistrictName) {
        kecSelect.disabled = true;
        kecSelect.innerHTML = '<option value="">Memuat kecamatan...</option>';
        if (kelSelect) {
            kelSelect.disabled = true;
            kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
        }
        if (loadKec) loadKec.classList.remove('hidden');

        function populate(districts) {
            if (loadKec) loadKec.classList.add('hidden');
            kecSelect.innerHTML = '<option value="">— Pilih Kecamatan —</option>';
            let matchedDistId = null;
            let matchedZip = null;

            districts.forEach(function (d) {
                const opt = document.createElement('option');
                opt.value = d.name;
                opt.textContent = d.name;
                opt.dataset.id = d.id;
                opt.dataset.zip = d.zip_code || '';
                if (selectedDistrictName && selectedDistrictName.toUpperCase() === d.name.toUpperCase()) {
                    opt.selected = true;
                    matchedDistId = d.id;
                    matchedZip = d.zip_code || '';
                }
                kecSelect.appendChild(opt);
            });

            kecSelect.disabled = false;

            if (matchedDistId) {
                loadSubDistricts(matchedDistId, oldKel, matchedZip);
            }
        }

        if (districtCache[cityId]) {
            populate(districtCache[cityId]);
            return;
        }

        fetch('/api/rajaongkir/districts?city_id=' + encodeURIComponent(cityId))
            .then(r => r.json())
            .then(res => {
                const data = (res && res.data) ? res.data : [];
                districtCache[cityId] = data;
                populate(data);
            })
            .catch(() => {
                if (loadKec) loadKec.classList.add('hidden');
                kecSelect.innerHTML = '<option value="">Gagal memuat kecamatan, silakan coba lagi</option>';
                kecSelect.disabled = false;
            });
    }

    function loadSubDistricts(districtId, selectedSubDistrictName, fallbackZip) {
        if (!kelSelect) return;
        kelSelect.disabled = true;
        kelSelect.innerHTML = '<option value="">Memuat kelurahan...</option>';
        if (loadKel) loadKel.classList.remove('hidden');

        function populate(subdistricts) {
            if (loadKel) loadKel.classList.add('hidden');
            kelSelect.innerHTML = '<option value="">— Pilih Kelurahan / Desa —</option>';
            let matchedZip = null;

            if (subdistricts && subdistricts.length > 0) {
                subdistricts.forEach(function (s) {
                    const opt = document.createElement('option');
                    opt.value = s.name;
                    opt.textContent = s.name + (s.zip_code ? ' (' + s.zip_code + ')' : '');
                    opt.dataset.id = s.id;
                    opt.dataset.zip = s.zip_code || '';
                    if (selectedSubDistrictName && selectedSubDistrictName.toUpperCase() === s.name.toUpperCase()) {
                        opt.selected = true;
                        matchedZip = s.zip_code || '';
                    }
                    kelSelect.appendChild(opt);
                });
                kelSelect.disabled = false;
            } else {
                kelSelect.innerHTML = '<option value="">— Tidak ada data kelurahan khusus —</option>';
                kelSelect.disabled = false;
            }

            const finalZip = matchedZip || fallbackZip;
            if (finalZip && (!posInput.value || posInput.value === '0')) {
                posInput.value = finalZip;
                if (badgePos) badgePos.classList.remove('hidden');
            }
        }

        if (subdistrictCache[districtId]) {
            populate(subdistrictCache[districtId]);
            return;
        }

        fetch('/api/rajaongkir/subdistricts?district_id=' + encodeURIComponent(districtId))
            .then(r => r.json())
            .then(res => {
                const data = (res && res.data) ? res.data : [];
                subdistrictCache[districtId] = data;
                populate(data);
            })
            .catch(() => {
                if (loadKel) loadKel.classList.add('hidden');
                kelSelect.innerHTML = '<option value="">Gagal memuat kelurahan, silakan coba lagi</option>';
                kelSelect.disabled = false;
            });
    }

    provSelect.addEventListener('change', function () {
        const opt = getSelectedOption(provSelect);
        const provId = opt ? opt.dataset.id : null;
        posInput.value = '';
        if (badgePos) badgePos.classList.add('hidden');

        if (!provId) {
            kabSelect.disabled = true;
            kabSelect.innerHTML = '<option value="">— Pilih Provinsi Terlebih Dahulu —</option>';
            kecSelect.disabled = true;
            kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
            if (kelSelect) {
                kelSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
            }
            return;
        }

        loadCities(provId, null);
    });

    kabSelect.addEventListener('change', function () {
        const opt = getSelectedOption(kabSelect);
        const cityId = opt ? opt.dataset.id : null;
        posInput.value = '';
        if (badgePos) badgePos.classList.add('hidden');

        if (!cityId) {
            kecSelect.disabled = true;
            kecSelect.innerHTML = '<option value="">— Pilih Kota/Kabupaten Dahulu —</option>';
            if (kelSelect) {
                kelSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
            }
            return;
        }

        loadDistricts(cityId, null);
    });

    kecSelect.addEventListener('change', function () {
        const opt = getSelectedOption(kecSelect);
        if (!opt || !opt.value) {
            posInput.value = '';
            if (badgePos) badgePos.classList.add('hidden');
            if (kelSelect) {
                kelSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">— Pilih Kecamatan Dahulu —</option>';
            }
            return;
        }

        const zip = opt.dataset.zip;
        const distId = opt.dataset.id;

        if (zip && zip !== '0') {
            posInput.value = zip;
            if (badgePos) badgePos.classList.remove('hidden');
        }

        if (distId) {
            loadSubDistricts(distId, null, zip);
        }
    });

    if (kelSelect) {
        kelSelect.addEventListener('change', function () {
            const opt = getSelectedOption(kelSelect);
            if (!opt || !opt.value) return;

            const zip = opt.dataset.zip;
            if (zip && zip !== '0') {
                posInput.value = zip;
                if (badgePos) badgePos.classList.remove('hidden');
            }
        });
    }

    // Inisialisasi saat pertama kali buka halaman jika provinsi sudah terpilih
    const initOpt = getSelectedOption(provSelect);
    if (initOpt && initOpt.dataset.id) {
        loadCities(initOpt.dataset.id, oldKab);
    }
})();
</script>
@endpush
@endsection
