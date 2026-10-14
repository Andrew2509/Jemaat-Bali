`# Product Requirements Document

## 1. Product Overview

**Product Name:** Jemaat Bali — Donasi & Kaos Fundraiser

**One-Line Description:** Situs profil dan penggalangan dana pembangunan, lengkap dengan galeri progres, dokumen proposal, katalog desain kaos, dan formulir donasi/pemesanan kaos.

**Product Type:** Crowdfunding / fundraiser + mini shop (campaign site)

**Primary Platform:** Web (mobile-first, desktop responsive)

---

## 2. Problem

**What problem are we solving?**
Jemaat sedang menjalani program pembangunan, tetapi informasinya tersebar: proposal di file terpisah, foto/video progres di grup chat, dan pemesanan kaos/donasi dicatat manual lewat pesan.

**Why does this problem matter?**
Tanpa satu halaman, donatur tidak bisa melihat bukti progres, pengurus kesulitan mencatat pesanan, dan campaign terasa tidak transparan.

**How are users solving this today?**
Chat berantai, catatan manual, dan penyebaran file proposal satu-satu.

---

## 3. Goal

**Primary Goal:**
Menerima pesanan kaos dan donasi lewat satu formulir tunggal yang tersimpan rapi di database.

**Secondary Goals:**
- Menampilkan progres pembangunan (foto & video) sebagai bukti transparansi.
- Menyebarluaskan dokumen proposal.
- Menampilkan pilihan desain kaos beserta harganya.

---

## 4. Target Users

### User Type 1 — Jemaat / Donatur
**Description:** Anggota jemaat dan simpatisan yang ingin menyumbang atau membeli kaos.
**Needs:** Melihat progres, mengunduh proposal, mengirim donasi/pesan kaos.
**Pain Points:** Tidak tahu harus transfer ke mana, ragu karena tidak ada bukti progres.

### User Type 2 — Pengurus
**Description:** Panitia pembangunan yang mengelola data masuk.
**Needs:** Melihat daftar pesanan & donasi, mengeksport data.

---

## 5. User Stories

- Sebagai pengunjung, saya ingin melihat video di halaman utama agar mengerti tujuan penggalangan dana.
- Sebagai donatur, saya ingin melihat foto & video progres pembangunan agar percaya dan yakin menyumbang.
- Sebagai pengunjung, saya ingin mengunduh proposal sehingga tahu rincian program.
- Sebagai pembeli kaos, saya ingin memilih desain, ukuran, dan jumlah lalu mengirim alamat lengkap.
- Sebagai donatur, saya ingin mengirim donasi beserta nominal tanpa membeli kaos.
- Sebagai pengurus, saya ingin melihat semua data masuk di satu tempat.

---

## 6. Core Features

### Feature 1 — Home (Video)
**Purpose:** Menyampaikan cerita singkat program lewat video.
**User Flow:** Buka situs → video tampil → CTA ke donasi/progres.
**Acceptance Criteria:**
- [ ] Video embed (YouTube/Vimeo) tampil penuh di area hero.
- [ ] Tombol CTA menuju halaman Shop + Donasi.

### Feature 2 — Proposal (File)
**Purpose:** Menyediakan dokumen proposal untuk diunduh.
**Acceptance Criteria:**
- [ ] Tombol unduh proposal tersedia.
- [ ] File ditampilkan di `/storage` tanpa error 404.

### Feature 3 — Progres Pembangunan (Foto & Video)
**Purpose:** Bukti transparansi progres.
**Acceptance Criteria:**
- [ ] Entri progres punya judul, tanggal, dan media.
- [ ] Foto tampil sebagai thumbnail, video dibuka sebagai modal/embed.
- [ ] Urutan terbaru di atas.

### Feature 4 — Design Kaos
**Purpose:** Katalog desain kaos yang bisa dibeli.
**Acceptance Criteria:**
- [ ] Tiap desain punya gambar, nama, harga.
- [ ] Tombol "Pesan" membawa item ke formulir.

### Feature 5 — Shop + UI / Donasi (Formulir)
**Purpose:** Satu formulir input untuk pesanan kaos dan/atau donasi.
**Inputs:** Nama, Nomor (HP/WA), Email, Alamat lengkap, Provinsi, Ukuran kaos (S/M/L/XL/XXL), Jumlah baju, Nominal donasi (opsional), Catatan.
**Outputs:** Baris tersimpan di tabel `orders`, halaman terima kasih.
**Acceptance Criteria:**
- [ ] Semua field wajib divalidasi server-side (`nama`, `nomor`, `email`, `alamat`, `provinsi`).
- [ ] `provinsi` berupa dropdown 38 provinsi Indonesia.
- [ ] `jumlah` dan `ukuran` wajib jika ada item kaos yang dipilih.
- [ ] Data tetap tersimpan walau validasi gagal (old input dipertahankan).
- [ ] Pesan sukses tampil setelah submit.

**Edge Cases:**
- Submit tanpa memilih kaos dan tanpa nominal donasi → ditolak dengan pesan jelas.
- Nomor/HP non-numerik → ditolak.
- Provinsi kosong → ditolak.

---

## 7. Non-Goals (versi ini)

- Pembayaran online otomatis (payment gateway)
- Autentikasi pengguna publik / akun jemaat
- Kirim email notifikasi otomatis
- Multi-campaign

---

## 8. MVP Scope

**Must Have:**
- 5 halaman sesuai struktur menu: Home, Proposal, Progres Pembangunan, Design Kaos, Shop + Donasi
- Formulir donasi/pesanan yang tervalidasi dan tersimpan ke DB
- Halaman daftar data masuk (admin tanpa login untuk MVP internal)
- Seed data demo

**Nice To Have:**
- Filter progres per bulan
- Ringkasan total donasi terkumpul
- Unduh data pesanan sebagai CSV

**Future:**
- Payment gateway (QRIS/transfer otomatis terverifikasi)
- Login pengurus

---

## 9. Success Metrics

- Jumlah formulir terkirim per minggu
- Jumlah pengunjung halaman Progres
- Rasio pengunjung → pengirim formulir

---

## 10. Constraints

**Technical:** Laravel 12 + PHP 8.3, SQLite, Vite + Tailwind.
**Business:** Bahasa antarmuka Indonesia.
**Design:** Mobile-first, satu CTA utama per halaman.

---

## 11. Definition of Done

- [ ] `php artisan migrate --seed` berjalan bersih
- [ ] Kelima halaman tampil tanpa error
- [ ] Formulir tervalidasi dan data masuk ke database
- [ ] Tidak ada error di log Laravel
