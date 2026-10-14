# Design System

## 1. Design Direction

**Style:** Editorial — hangat, gotong royong, rapi
**Mood:** Percaya transparan, penuh harap, tidak komersial
**Design Principles:**
- Bukti dulu (foto/video progres) sebelum permintaan (donasi)
- Satu CTA utama per halaman
- Mobile-first: jempol mudah menjangkau tombol

---

## 2. Brand

**Brand Name:** Jemaat Bali
**Logo:** Teks wordmark (belum ada aset)
**Brand Voice:** Sapaan akrab, bahasa Indonesia sederhana, tidak jargon

---

## 3. Color Palette

| Token | Hex | Pemakaian |
|---|---|---|
| Primary | `#B4531F` | CTA, link aktif, aksen |
| Secondary | `#0F5132` | Aksen sekunder, status sukses |
| Background | `#FBF8F4` | Latar halaman |
| Surface | `#FFFFFF` | Kartu, form |
| Text | `#1C1917` | Teks utama |
| Muted Text | `#78716C` | Label, meta |
| Border | `#E7E5E4` | Garis, input |
| Success | `#15803D` | Toast sukses |
| Warning | `#B45306` | Peringatan |
| Error | `#B91C1C` | Error validasi |

---

## 4. Typography

**Font Family:** `Inter` untuk body, `Plus Jakarta Sans` untuk heading (fallback system sans).
**H1:** 32/40px · 800 · 1.15
**H2:** 24/32px · 700 · 1.25
**H3:** 18/20px · 600 · 1.35
**Body:** 16px · 400 · 1.6
**Small:** 14px · 400 · 1.5

---

## 5. Spacing

Base unit 4px; skala: 4, 8, 12, 16, 24, 32, 48, 64, 96.
Konten utama: `max-w-6xl`, padding `px-4 sm:px-6`.

---

## 6. Border Radius

Small 6px · Medium 10px · Large 16px · Pill 999px

---

## 7. Shadows

Small `0 1px 2px rgb(0 0 0 / .05)` · Medium `0 4px 12px rgb(0 0 0 / .08)` · Large `0 12px 32px rgb(0 0 0 / .12)`

---

## 8. Buttons

**Primary:** bg Primary, teks putih, radius pill, tinggi 44px, hover gelap 8%.
**Secondary:** border 1px Border, teks Text, transparan.
**Danger:** bg Error.
**Disabled:** opacity 50, cursor not-allowed.
**Loading:** ganti label jadi spinner, tombol `disabled`.

---

## 9. Inputs

**Default:** bg putih, border 1px `#E7E5E4`, radius 10px, tinggi 44px, px 14.
**Focus:** border Primary + ring `2px rgb(180 83 31 / .25)`.
**Error:** border Error, pesan error 14px teks Error di bawah field.
**Placeholder:** `#A8A29E`.

---

## 10. Cards

Padding 20–24px, radius 16px, border 1px Border, shadow Small; foto progres radius 16px tanpa border.

---

## 11. Navigation

**Header:** sticky, bg putih/95 blur, logo kiri, 5 menu kanan, aktif = teks Primary + underline.
**Mobile:** hamburger → drawer vertikal, CTA Donasi selalu tampil.
**Footer:** ringkasan kampanye, tautan menu, kontak.

---

## 12. States

**Loading:** skeleton abu `#F5F5F4`.
**Empty:** ikon + kalimat ajakan + tombol.
**Error:** banner merah muda + pesan.
**Success:** banner hijau muda + pesan + aksi lanjut.

---

## 13. Icons

Icon Library: Heroicons (inline SVG)
Default Size: 20px
Icon Style: Outline

---

## 14. Responsive Design

Breakpoints: Mobile `0–639`, Tablet `640–1023`, Desktop `1024+`.
Grid katalog: 1 kolom mobile, 2 tablet, 3 desktop.

---

## 15. Accessibility

- HTML semantik (`header`, `main`, `nav`, `footer`, `label`)
- Kontras minimal 4.5:1
- Navigasi keyboard + focus ring terlihat
- Setiap input punya `<label>` eksplisit
- Error tidak hanya dibedakan oleh warna (ada ikon + teks)
