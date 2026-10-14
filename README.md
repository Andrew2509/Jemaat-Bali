# Jemaat Bali — Donasi & Kaos Fundraiser

Situs penggalangan dana pembangunan (Laravel 12 + Tailwind CSS v4). Rancangan mengikuti
struktur menu dari catatan tangan: **Home (video), Proposal (file), Progres Pembangunan
(foto & video), Design Kaos, Shop + Donasi (formulir)**.

Dokumen desain ada di [`docs/`](docs):
[PRD](docs/PRD.md) · [Design System](docs/DESIGN_SYSTEM.md) · [Architecture](docs/ARCHITECTURE.md)

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve            # http://127.0.0.1:8000
```

## Rute

| Rute | Halaman |
|---|---|
| `/` | Home — hero video + preview progres & kaos |
| `/proposal` · `/proposal/unduh` | Dokumen proposal (unduh) |
| `/progres` | Galeri foto & video progres pembangunan |
| `/design-kaos` | Katalog desain kaos |
| `/shop` (GET/POST) | Formulir pesan kaos + donasi |
| `/admin/orders` | Data masuk untuk pengurus |

## Data

- `progresses` — entri progres (`tipe`: foto / video)
- `designs` — katalog kaos (nama, harga, gambar)
- `orders` — pesanan kaos & donasi (`ukuran`, `jumlah`, `donasi`, `total`, `status`)

Reset ke data contoh: `php artisan migrate:fresh --seed`.

## Aset

- Berkas proposal: `public/uploads/proposal/proposal-pembangunan.pdf`
  (regenerasi: `php scripts/make_proposal_pdf.php`)
- Foto progres: `public/uploads/progres/`
- Gambar kaos: `public/designs/`

> Folder aset berada di bawah `public/uploads/` — jangan dipindah ke `public/progres`
> atau `public/proposal`, karena nama itu sama dengan URL rute dan akan menimbulkan 404.

## Pengujian

```bash
php artisan test
```
