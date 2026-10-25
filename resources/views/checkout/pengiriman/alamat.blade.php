@extends('layouts.app')

@section('title', 'Informasi Pengiriman — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <a href="{{ route('checkout.pemesanan', ['desain' => $selected->id]) }}" class="text-sm font-semibold text-muted hover:text-primary">&larr; Kembali ke data pemesan</a>
    <div class="mt-6 flex gap-3 text-xs font-bold uppercase tracking-widest"><span class="text-muted">01 Data pemesan</span><span class="text-primary">— 02 Pengiriman</span><span class="text-muted">— 03 Pembayaran</span></div>
    <h1 class="mt-4 font-serif text-3xl font-extrabold">Informasi Pengiriman</h1>
    <p class="mt-2 text-muted">Langkah 2 dari 3 · Pastikan alamat tujuan lengkap dan benar.</p>
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <form action="{{ route('checkout.pengiriman.store') }}" method="POST" class="card space-y-5 p-6 sm:p-8 lg:col-span-2">
            @csrf
            @if ($errors->any())<div class="rounded-xl bg-error/5 p-4 text-sm text-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <input type="hidden" name="_identity" value="">
            <div><label for="alamat" class="label">Alamat lengkap *</label><textarea id="alamat" name="alamat" rows="3" class="input" placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan" required>{{ old('alamat', $draft['alamat'] ?? '') }}</textarea>@error('alamat')<p class="error-text">{{ $message }}</p>@enderror</div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label for="provinsi" class="label">Provinsi *</label><select id="provinsi" name="provinsi" class="input" required><option value="">— Pilih Provinsi —</option>@foreach ($provinsi as $p) @php($pName = is_array($p) ? ($p['name'] ?? '') : $p)<option value="{{ $pName }}" @selected(strtoupper(old('provinsi', $draft['provinsi'] ?? '')) === strtoupper($pName))>{{ $pName }}</option>@endforeach</select>@error('provinsi')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kabupaten" class="label">Kota / Kabupaten *</label><input id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $draft['kabupaten'] ?? '') }}" class="input" required>@error('kabupaten')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kecamatan" class="label">Kecamatan *</label><input id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $draft['kecamatan'] ?? '') }}" class="input" required>@error('kecamatan')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kelurahan" class="label">Kelurahan / Desa</label><input id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $draft['kelurahan'] ?? '') }}" class="input">@error('kelurahan')<p class="error-text">{{ $message }}</p>@enderror</div>
                <div><label for="kode_pos" class="label">Kode pos *</label><input id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $draft['kode_pos'] ?? '') }}" class="input" inputmode="numeric" required>@error('kode_pos')<p class="error-text">{{ $message }}</p>@enderror</div>
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
    });
    render();
})();
</script>
@endpush
@endsection
