<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'nama' => 'Super Admin',
            'email' => 'admin@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        ProgramInfo::create([
            'nama_program' => 'Pembangunan Jemaat Bali',
            'deskripsi' => 'Penggalangan dana pembangunan bersama. Transparan: setiap progres kami unggah di sini.',
            'whatsapp' => '0812-0000-0000',
            'email' => 'admin@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
        ]);

        Expense::insert([
            ['kategori' => 'Produksi kaos', 'nama' => 'Produksi 4 kaos', 'jumlah' => 340000, 'tanggal' => '2026-09-20', 'keterangan' => '4 kaos × Rp85.000', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Pengiriman', 'nama' => 'Ongkir prabayar', 'jumlah' => 80000, 'tanggal' => '2026-09-22', 'keterangan' => 'Ongkir titipan pengiriman', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Material fondasi', 'nama' => 'Belanja material fondasi', 'jumlah' => 2000000, 'tanggal' => '2026-09-28', 'keterangan' => 'Belanja pembangunan', 'created_at' => now(), 'updated_at' => now()],
            ['kategori' => 'Transportasi material', 'nama' => 'Transportasi material', 'jumlah' => 500000, 'tanggal' => '2026-09-28', 'keterangan' => 'Belanja pembangunan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $donations = [
            ['kode' => 'DN-010', 'nama' => 'Clara Wijaya', 'nomor' => '0812-0000-0010', 'email' => 'clara@example.com', 'nominal' => 500000, 'tanggal' => '2026-10-12', 'catatan' => 'Untuk pembangunan.', 'metode' => 'qris', 'bukti_path' => 'bukti-pembayaran/sample-dn010.png', 'status' => 'menunggu_verifikasi', 'catatan_panitia' => null, 'verified_at' => null, 'verified_by' => null],
            ['kode' => 'DN-009', 'nama' => 'Putu Lestari', 'nomor' => '0812-0000-0009', 'email' => null, 'nominal' => 100000, 'tanggal' => '2026-10-11', 'catatan' => 'Dukungan keluarga.', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-dn009.png', 'status' => 'bukti_ulang', 'catatan_panitia' => 'Bukti kurang jelas', 'verified_at' => null, 'verified_by' => null],
            ['kode' => 'DN-008', 'nama' => 'Ben Pranata', 'nomor' => '0812-0000-0005', 'email' => null, 'nominal' => 200000, 'tanggal' => '2026-10-11', 'catatan' => 'Bersama membangun.', 'metode' => 'qris', 'bukti_path' => null, 'status' => 'belum_bayar', 'catatan_panitia' => null, 'verified_at' => null, 'verified_by' => null],
            ['kode' => 'DN-007', 'nama' => 'Stefan Aditya', 'nomor' => '0812-0000-0004', 'email' => 'stefan@example.com', 'nominal' => 200000, 'tanggal' => '2026-10-11', 'catatan' => 'Semoga lancar.', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks004.png', 'status' => 'menunggu_verifikasi', 'catatan_panitia' => null, 'verified_at' => null, 'verified_by' => null],
            ['kode' => 'DN-006', 'nama' => 'Yohana Sari', 'nomor' => '0812-0000-0003', 'email' => null, 'nominal' => 200000, 'tanggal' => '2026-07-18', 'catatan' => 'Bangun bersama.', 'metode' => 'qris', 'bukti_path' => 'bukti-pembayaran/sample-ks003.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-07-18 09:00:00', 'verified_by' => 1],
            ['kode' => 'DN-005', 'nama' => 'Lukas Wibowo', 'nomor' => '0812-0000-0002', 'email' => null, 'nominal' => 250000, 'tanggal' => '2026-08-16', 'catatan' => 'Semangat panitia!', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks002.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-08-16 14:00:00', 'verified_by' => 1],
            ['kode' => 'DN-004', 'nama' => 'Agatha Dewi', 'nomor' => '0812-0000-0004', 'email' => 'agatha@example.com', 'nominal' => 300000, 'tanggal' => '2026-10-10', 'catatan' => 'Tuhan memberkati.', 'metode' => 'qris', 'bukti_path' => 'bukti-pembayaran/sample-ks001.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-10-10 10:00:00', 'verified_by' => 1],
            ['kode' => 'DN-003', 'nama' => 'Paulus Wijaya', 'nomor' => '0812-0000-0003', 'email' => null, 'nominal' => 1000000, 'tanggal' => '2026-07-10', 'catatan' => 'Untuk rumah jemaat.', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks001.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-07-10 08:00:00', 'verified_by' => 1],
            ['kode' => 'DN-002', 'nama' => 'Daniel Santoso', 'nomor' => '0812-0000-0002', 'email' => null, 'nominal' => 2000000, 'tanggal' => '2026-08-20', 'catatan' => 'Dari keluarga kami.', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks002.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-08-20 11:30:00', 'verified_by' => 1],
            ['kode' => 'DN-001', 'nama' => 'Maria Santika', 'nomor' => '0812-0000-0001', 'email' => null, 'nominal' => 5000000, 'tanggal' => '2026-09-15', 'catatan' => 'Untuk fondasi.', 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks004.png', 'status' => 'terverifikasi', 'catatan_panitia' => null, 'verified_at' => '2026-09-15 10:00:00', 'verified_by' => 1],
        ];
        foreach ($donations as $d) {
            $d['created_at'] = now();
            $d['updated_at'] = now();
            Donation::create($d);
        }

        Progress::insert([
            [
                'judul' => 'Pemasangan fondasi bangunan utama',
                'deskripsi' => 'Tahap pondasi rampung setelah dua minggu pengerjaan. Pengawasan dilakukan panitia tiap Sabtu.',
                'tanggal' => '2026-09-26',
                'tahap' => 'Fondasi',
                'status' => 'terbit',
                'teks_alt' => 'Dokumentasi pemasangan fondasi bangunan utama.',
                'tipe' => 'foto',
                'media_path' => 'uploads/progres/fondasi.svg',
                'video_url' => null,
                'published_at' => '2026-09-26 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengecoran lantai dasar',
                'deskripsi' => 'Cor beton lantai dasar selesai, dilanjutkan pengeringan selama 14 hari sebelum dinding naik.',
                'tanggal' => '2026-09-12',
                'tahap' => 'Lantai dasar',
                'status' => 'terbit',
                'teks_alt' => 'Dokumentasi pengecoran lantai dasar.',
                'tipe' => 'foto',
                'media_path' => 'uploads/progres/pengecoran.svg',
                'video_url' => null,
                'published_at' => '2026-09-12 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pembersihan dan pengukuran lahan',
                'deskripsi' => 'Lahan dibersihkan dan batas bangunan dipastikan sesuai dokumen izin.',
                'tanggal' => '2026-08-29',
                'tahap' => 'Persiapan',
                'status' => 'terbit',
                'teks_alt' => 'Dokumentasi pembersihan dan pengukuran lahan.',
                'tipe' => 'foto',
                'media_path' => 'uploads/progres/lahan.svg',
                'video_url' => null,
                'published_at' => '2026-08-29 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Musyawarah panitia dan dokumentasi lahan',
                'deskripsi' => 'Rekaman rapat panitia pembangunan beserta hasil pengukuran lapangan.',
                'tanggal' => '2026-08-15',
                'tahap' => 'Perencanaan',
                'status' => 'terbit',
                'teks_alt' => 'Dokumentasi musyawarah panitia dan dokumentasi lahan.',
                'tipe' => 'video',
                'media_path' => null,
                'video_url' => 'https://www.youtube.com/embed/aqz-KE-bpKQ',
                'published_at' => '2026-08-15 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Peletakan batu pertama',
                'deskripsi' => 'Acara peletakan batu pertama dihadiri pengurus jemaat dan perwakilan donatur.',
                'tanggal' => '2026-08-01',
                'tahap' => 'Persiapan',
                'status' => 'terbit',
                'teks_alt' => 'Dokumentasi peletakan batu pertama.',
                'tipe' => 'foto',
                'media_path' => 'uploads/progres/batu-pertama.svg',
                'video_url' => null,
                'published_at' => '2026-08-01 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Video profil program pembangunan',
                'deskripsi' => 'Video singkat yang menjelaskan latar belakang, target, dan kebutuhan dana program.',
                'tanggal' => '2026-07-18',
                'tahap' => 'Perencanaan',
                'status' => 'draf',
                'teks_alt' => 'Video profil program pembangunan.',
                'tipe' => 'video',
                'media_path' => null,
                'video_url' => 'https://www.youtube.com/embed/aqz-KE-bpKQ',
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Design::insert([
            [
                'nama' => 'Kaos "Bangun Bersama"',
                'deskripsi' => 'Desain utama kampanye dengan tulisan besar di dada dan logo jemaat di punggung.',
                'harga' => 150000,
                'gambar' => 'designs/kaos-bangun-bersama.svg',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kaos "Satu Jemaat"',
                'deskripsi' => 'Desain minimalis warna natural, nyaman dipakai sehari-hari.',
                'harga' => 135000,
                'gambar' => 'designs/kaos-satu-jemaat.svg',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kaos "Bali Peduli"',
                'deskripsi' => 'Edisi khusus dengan motif lokal, cocok untuk acara dan kegiatan jemaat.',
                'harga' => 175000,
                'gambar' => 'designs/kaos-bali-peduli.svg',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kaos "Pemuda Berani"',
                'deskripsi' => 'Untuk ibadah pemuda: bahan katun adem, sablon awet meski sering dicuci.',
                'harga' => 160000,
                'gambar' => 'designs/kaos-pemuda-berani.svg',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kaos "Keluarga Kudus"',
                'deskripsi' => 'Edisi keluarga dengan warna marun dan tulisan emas, nyaman dipakai bersama.',
                'harga' => 145000,
                'gambar' => 'designs/kaos-keluarga-kudus.svg',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kaos "Flame Streetwear"',
                'deskripsi' => 'Edisi spesial streetwear two-tone motif api dengan potongan modern dan bahan 100% combed cotton adem. Dilengkapi pratinjau 360° interaktif.',
                'harga' => 185000,
                'gambar' => 'images/products/flame-shirt/frame_000.webp',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $designs = Design::pluck('id');

        $orders = [
            ['nomor_pesanan' => 'KS-001', 'jenis' => 'kaos_donasi', 'nama' => 'Agatha Dewi', 'nomor' => '0812-0000-0001', 'email' => 'agatha@example.com', 'alamat' => 'Jl. Contoh Melati No. 12, Kel. Panjer, Kec. Denpasar Selatan', 'provinsi' => 'Bali', 'kabupaten' => 'Denpasar', 'kecamatan' => 'Denpasar Selatan', 'kode_pos' => '80225', 'ukuran' => 'L', 'jumlah' => 2, 'design_id' => $designs[4], 'subtotal_kaos' => 290000, 'donasi' => 300000, 'ongkir' => 40000, 'total' => 630000, 'catatan' => null, 'metode' => 'qris', 'bukti_path' => 'bukti-pembayaran/sample-ks001.png', 'status_pembayaran' => 'terverifikasi', 'status_pengiriman' => 'siap_kirim', 'resi' => null, 'catatan_panitia' => null, 'verified_at' => '2026-10-10 08:10:00', 'verified_by' => 1, 'dikirim_at' => null, 'created_at' => '2026-10-10 08:05:00', 'updated_at' => now()],
            ['nomor_pesanan' => 'KS-004', 'jenis' => 'kaos_donasi', 'nama' => 'Stefan Aditya', 'nomor' => '0812-0000-0004', 'email' => 'stefan@example.com', 'alamat' => 'Jl. Contoh Raya No. 8, Bandung', 'provinsi' => 'Jawa Barat', 'kabupaten' => 'Bandung', 'kecamatan' => 'Coblong', 'kode_pos' => '40132', 'ukuran' => 'L', 'jumlah' => 1, 'design_id' => $designs[4], 'subtotal_kaos' => 145000, 'donasi' => 200000, 'ongkir' => 20000, 'total' => 365000, 'catatan' => null, 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks004.png', 'status_pembayaran' => 'menunggu_verifikasi', 'status_pengiriman' => 'belum_diproduksi', 'resi' => null, 'catatan_panitia' => null, 'verified_at' => null, 'verified_by' => null, 'dikirim_at' => null, 'created_at' => '2026-10-11 16:40:00', 'updated_at' => now()],
            ['nomor_pesanan' => 'KS-005', 'jenis' => 'kaos_donasi', 'nama' => 'Ben Pranata', 'nomor' => '0812-0000-0005', 'email' => null, 'alamat' => 'Jl. Contoh No. 5, Surabaya', 'provinsi' => 'Jawa Timur', 'kabupaten' => 'Surabaya', 'kecamatan' => 'Sukolilo', 'kode_pos' => '60111', 'ukuran' => 'XL', 'jumlah' => 2, 'design_id' => $designs[3], 'subtotal_kaos' => 320000, 'donasi' => 200000, 'ongkir' => 0, 'total' => 520000, 'catatan' => null, 'metode' => 'qris', 'bukti_path' => null, 'status_pembayaran' => 'belum_bayar', 'status_pengiriman' => 'belum_diproduksi', 'resi' => null, 'catatan_panitia' => null, 'verified_at' => null, 'verified_by' => null, 'dikirim_at' => null, 'created_at' => '2026-10-11 09:00:00', 'updated_at' => now()],
            ['nomor_pesanan' => 'KS-002', 'jenis' => 'kaos_donasi', 'nama' => 'Lukas Wibowo', 'nomor' => '0812-0000-0002', 'email' => null, 'alamat' => 'Jl. Contoh No. 2, Jakarta', 'provinsi' => 'DKI Jakarta', 'kabupaten' => 'Jakarta Selatan', 'kecamatan' => 'Kebayoran Baru', 'kode_pos' => '12130', 'ukuran' => 'M', 'jumlah' => 1, 'design_id' => $designs[0], 'subtotal_kaos' => 150000, 'donasi' => 250000, 'ongkir' => 40000, 'total' => 440000, 'catatan' => null, 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks002.png', 'status_pembayaran' => 'terverifikasi', 'status_pengiriman' => 'produksi', 'resi' => null, 'catatan_panitia' => null, 'verified_at' => '2026-08-16 14:20:00', 'verified_by' => 1, 'dikirim_at' => null, 'created_at' => '2026-08-16 10:20:00', 'updated_at' => now()],
            ['nomor_pesanan' => 'KS-003', 'jenis' => 'kaos_donasi', 'nama' => 'Yohana Sari', 'nomor' => '0812-0000-0003', 'email' => null, 'alamat' => 'Jl. Contoh No. 3, Yogyakarta', 'provinsi' => 'DI Yogyakarta', 'kabupaten' => 'Yogyakarta', 'kecamatan' => 'Gondokusuman', 'kode_pos' => '55223', 'ukuran' => 'S', 'jumlah' => 1, 'design_id' => $designs[1], 'subtotal_kaos' => 135000, 'donasi' => 200000, 'ongkir' => 40000, 'total' => 375000, 'catatan' => null, 'metode' => 'transfer', 'bukti_path' => 'bukti-pembayaran/sample-ks003.png', 'status_pembayaran' => 'terverifikasi', 'status_pengiriman' => 'terkirim', 'resi' => 'JNE123456789', 'catatan_panitia' => null, 'verified_at' => '2026-07-18 09:10:00', 'verified_by' => 1, 'dikirim_at' => '2026-07-25 10:00:00', 'created_at' => '2026-07-18 09:05:00', 'updated_at' => now()],
        ];
        foreach ($orders as $o) {
            Order::create($o);
        }
    }
}
