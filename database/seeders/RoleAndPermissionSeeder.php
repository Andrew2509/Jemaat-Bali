<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsData = [
            // Dashboard & Umum
            [
                'name' => 'dashboard.view',
                'display_name' => 'Lihat Dashboard',
                'group' => 'Dasbor & Umum',
                'description' => 'Melihat ringkasan metrik statistik dan aktivitas sistem.',
            ],

            // Verifikasi & Transaksi
            [
                'name' => 'verification.view',
                'display_name' => 'Lihat Verifikasi Pembayaran',
                'group' => 'Verifikasi & Transaksi',
                'description' => 'Melihat antrean verifikasi bukti transfer pesanan & donasi.',
            ],
            [
                'name' => 'verification.manage',
                'display_name' => 'Proses Verifikasi',
                'group' => 'Verifikasi & Transaksi',
                'description' => 'Menyetujui, menolak, atau meminta upload ulang bukti pembayaran.',
            ],
            [
                'name' => 'orders.view',
                'display_name' => 'Lihat Pesanan Kaos',
                'group' => 'Verifikasi & Transaksi',
                'description' => 'Melihat daftar pesanan kaos, detail pemesan, dan alamat pengiriman.',
            ],
            [
                'name' => 'orders.manage',
                'display_name' => 'Kelola Pesanan & Resi',
                'group' => 'Verifikasi & Transaksi',
                'description' => 'Memperbarui status produksi, pengiriman, dan menginput nomor resi.',
            ],

            // Produk & Desain
            [
                'name' => 'designs.view',
                'display_name' => 'Lihat Koleksi Kaos',
                'group' => 'Produk & Desain',
                'description' => 'Melihat daftar desain kaos dan status tayang di halaman publik.',
            ],
            [
                'name' => 'designs.manage',
                'display_name' => 'Kelola Koleksi Kaos',
                'group' => 'Produk & Desain',
                'description' => 'Menambah desain kaos baru, mengubah harga, stok, dan menghapus desain.',
            ],

            // Keuangan & Donasi
            [
                'name' => 'donations.view',
                'display_name' => 'Lihat Donasi',
                'group' => 'Keuangan & Donasi',
                'description' => 'Melihat data donasi jemaat dan riwayat partisipasi.',
            ],
            [
                'name' => 'donations.manage',
                'display_name' => 'Kelola Donasi',
                'group' => 'Keuangan & Donasi',
                'description' => 'Memvalidasi donasi manual dan mengirimkan konfirmasi WhatsApp.',
            ],
            [
                'name' => 'expenses.view',
                'display_name' => 'Lihat Pengeluaran',
                'group' => 'Keuangan & Donasi',
                'description' => 'Melihat catatan pembukuan dana pengeluaran pembangunan.',
            ],
            [
                'name' => 'expenses.manage',
                'display_name' => 'Kelola Pengeluaran',
                'group' => 'Keuangan & Donasi',
                'description' => 'Mencatat pengeluaran baru, mengedit, atau menghapus item belanja.',
            ],
            [
                'name' => 'reports.view',
                'display_name' => 'Lihat Laporan Dana',
                'group' => 'Keuangan & Donasi',
                'description' => 'Melihat rekap pemasukan, pengeluaran, dan saldo program.',
            ],
            [
                'name' => 'reports.export',
                'display_name' => 'Ekspor Laporan Dana',
                'group' => 'Keuangan & Donasi',
                'description' => 'Mengunduh laporan rekapitulasi dana ke format berkas Excel (.xlsx).',
            ],

            // Konten & Sistem
            [
                'name' => 'content.view',
                'display_name' => 'Lihat Pengelolaan Konten',
                'group' => 'Konten & Sistem',
                'description' => 'Melihat halaman draf progres pembangunan dan profil program.',
            ],
            [
                'name' => 'content.manage',
                'display_name' => 'Kelola Konten & Progres',
                'group' => 'Konten & Sistem',
                'description' => 'Mengunggah dokumentasi progres, mengubah profil program, dan proposal.',
            ],
            [
                'name' => 'provider.view',
                'display_name' => 'Lihat Provider WhatsApp',
                'group' => 'Konten & Sistem',
                'description' => 'Mengecek status koneksi gateway WhatsApp Fonnte.',
            ],
            [
                'name' => 'provider.manage',
                'display_name' => 'Kelola Provider WhatsApp',
                'group' => 'Konten & Sistem',
                'description' => 'Mengonfigurasi token API Fonnte dan templat pesan WhatsApp.',
            ],

            // Manajemen Akses
            [
                'name' => 'users.view',
                'display_name' => 'Lihat Pengguna',
                'group' => 'Manajemen Akses',
                'description' => 'Melihat daftar admin, staf, dan status keaktifan akun.',
            ],
            [
                'name' => 'users.manage',
                'display_name' => 'Kelola Pengguna',
                'group' => 'Manajemen Akses',
                'description' => 'Menambah pengguna baru, mengatur password, dan mengubah hak peran.',
            ],
            [
                'name' => 'roles.view',
                'display_name' => 'Lihat Peran (Roles)',
                'group' => 'Manajemen Akses',
                'description' => 'Melihat daftar peran pengguna dan jumlah staf per peran.',
            ],
            [
                'name' => 'roles.manage',
                'display_name' => 'Kelola Peran & Hak Akses',
                'group' => 'Manajemen Akses',
                'description' => 'Menambah peran kustom baru dan mencentang wewenang izin akses.',
            ],
            [
                'name' => 'permissions.view',
                'display_name' => 'Lihat Hak Akses (Permissions)',
                'group' => 'Manajemen Akses',
                'description' => 'Melihat daftar seluruh izin akses yang terdaftar di dalam sistem.',
            ],
            [
                'name' => 'permissions.manage',
                'display_name' => 'Kelola Hak Akses (Permissions)',
                'group' => 'Manajemen Akses',
                'description' => 'Menambah atau memperbarui metadata izin akses sistem.',
            ],
        ];

        // 1. Simpan semua permission
        $allPermissionIds = [];
        foreach ($permissionsData as $perm) {
            $permission = Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'group' => $perm['group'],
                    'description' => $perm['description'],
                ]
            );
            $allPermissionIds[$perm['name']] = $permission->id;
        }

        // 2. Buat atau perbarui Peran (Roles)
        // A. Super Admin (semua hak akses)
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Pemegang wewenang penuh tanpa batas atas seluruh sistem, modul, dan manajemen pengguna.',
                'is_system' => true,
            ]
        );
        $superAdminRole->permissions()->sync(array_values($allPermissionIds));

        // B. Administrator (operasional harian lengkap)
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Mengelola operasional harian: pesanan, verifikasi donasi, pengeluaran, konten, dan laporan.',
                'is_system' => true,
            ]
        );
        $adminPermissions = array_diff_key($allPermissionIds, array_flip([
            'roles.manage',
            'permissions.manage',
        ]));
        $adminRole->permissions()->sync(array_values($adminPermissions));

        // C. Bendahara Keuangan
        $bendaharaRole = Role::updateOrCreate(
            ['name' => 'bendahara'],
            [
                'display_name' => 'Bendahara Keuangan',
                'description' => 'Fokus pada verifikasi pembayaran, pembukuan donasi, pencatatan pengeluaran, dan ekspor laporan keuangan.',
                'is_system' => false,
            ]
        );
        $bendaharaPerms = array_intersect_key($allPermissionIds, array_flip([
            'dashboard.view',
            'verification.view',
            'verification.manage',
            'donations.view',
            'donations.manage',
            'expenses.view',
            'expenses.manage',
            'reports.view',
            'reports.export',
        ]));
        $bendaharaRole->permissions()->sync(array_values($bendaharaPerms));

        // D. Operator Toko & Konten
        $operatorRole = Role::updateOrCreate(
            ['name' => 'operator'],
            [
                'display_name' => 'Operator Toko & Konten',
                'description' => 'Mengelola pesanan kaos jemaat, penginputan nomor resi, koleksi desain kaos, serta dokumentasi progres pembangunan.',
                'is_system' => false,
            ]
        );
        $operatorPerms = array_intersect_key($allPermissionIds, array_flip([
            'dashboard.view',
            'orders.view',
            'orders.manage',
            'designs.view',
            'designs.manage',
            'content.view',
            'content.manage',
        ]));
        $operatorRole->permissions()->sync(array_values($operatorPerms));

        // 3. Hubungkan pengguna yang sudah ada ke peran terkait
        User::where('role', 'super_admin')->update([
            'role_id' => $superAdminRole->id,
            'is_active' => true,
        ]);

        User::where('role', 'admin')->whereNull('role_id')->update([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }
}
