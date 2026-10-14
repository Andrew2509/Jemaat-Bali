# Architecture

## 1. System Overview

Situs konten + formulir satu arah: pengunjung membaca halaman publik, lalu mengirim satu formulir (pesan kaos / donasi) yang disimpan ke database. Pengurus membaca data lewat halaman admin ringkas.

## 2. Architecture Pattern

**Pattern:** Modular Monolith (Laravel 12, konvensional)
**Reason:** Cakupan kecil, satu deploy, tidak perlu microservices.

## 3. Technology Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS (Vite) |
| Database | SQLite (dev) — MySQL/Postgres siap ganti |
| Auth | Tidak ada di MVP (admin tanpa login, jaringan internal) |
| Storage | `public/storage` (symlink) untuk proposal, foto, gambar kaos |
| Video | Embed YouTube/Vimeo |
| Hosting | Laragon (dev) / shared hosting / VPS |
| Payments | Belum ada — manual transfer |

## 4. System Components

**Frontend (Blade):** layout bersama, nav, footer, 5 halaman publik, 1 halaman admin.
**Routes (web.php):** GET `/`, `/proposal`, `/progres`, `/design-kaos`, `/shop`, POST `/shop`, GET `/admin/orders`.
**Model:** `Progress`, `Design`, `Order`.
**Storage:** file proposal `public/proposal/`, foto progres `public/progres/`, gambar kaos `public/designs/`.

## 5. Request Flow

```
Browser → Route → Controller → Validation → Model (Eloquent) → SQLite
                                    ↓
                               Blade view → HTML
```

## 6. Project Structure

```text
app/
├── Http/Controllers/
│   ├── HomeController.php
│   ├── ProposalController.php
│   ├── ProgressController.php
│   ├── DesignController.php
│   ├── ShopController.php
│   └── Admin/OrderController.php
├── Models/{Progress,Design,Order}.php
database/
├── migrations/{progresses,designs,orders}_table.php
├── factories/
└── seeders/DatabaseSeeder.php
resources/views/
├── layouts/app.blade.php
├── home.blade.php
├── proposal.blade.php
├── progres.blade.php
├── design-kaos.blade.php
├── shop.blade.php
├── shop/success.blade.php
└── admin/orders.blade.php
routes/web.php
```

## 7. Data Flow (submit formulir)

1. Pengunjung mengisi form di `/shop`
2. Controller memvalidasi (`nama`, `nomor`, `email`, `alamat`, `provinsi`, dsb.)
3. Gagal → redirect balik + `withErrors` + old input
4. Sukses → `Order::create()` → redirect `/shop?sukses=1`
5. Baris dibaca kembali di `/admin/orders`

## 8. Dependency Rules

- Controller tidak mengakses DB tanpa Model.
- Validasi hanya di Controller (Form Request kalau nanti bertambah).
- View tidak menulis query.
- Tidak ada secret di repository: `.env` di-gitignore.

## 9. Key Technical Decisions

| Keputusan | Alternatif | Alasan |
|---|---|---|
| SQLite | MySQL | Tanpa setup server saat dev, cukup ganti DSN |
| Satu form untuk kaos & donasi | Form terpisah | Sesuai catatan asli, mengurangi langkah |
| Admin tanpa auth | Laravel Breeze | MVP internal, auth menyusul |
| Media progres = path + tipe | Tabel terpisah foto/video | Satu list, satu tampilan |

## 10. Failure Scenarios

- **Validasi gagal** → input lama dipertahankan, pesan error per field.
- **File proposal tidak ada** → tombol tetap tampil, controller cek `file_exists()`.
- **DB tidak tersedia** → pesan error generik, detail hanya di `storage/logs`.
