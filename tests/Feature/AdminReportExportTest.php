<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@jemaatbali.or.id')->first();
    }

    private function seedTerverifikasi(): void
    {
        Donation::create([
            'kode' => 'DN-REP-001',
            'nama' => 'Donatur Laporan',
            'nomor' => '081234500001',
            'email' => 'laporan@example.com',
            'nominal' => 250000,
            'tanggal' => '2026-10-15',
            'catatan' => 'Doa untuk pembangunan',
            'metode' => 'qris',
            'status' => 'terverifikasi',
        ]);

        Order::create([
            'nomor_pesanan' => 'DN-REP-ORD-001',
            'nama' => 'Donatur Saja Laporan',
            'nomor' => '081234500002',
            'email' => 'laporan2@example.com',
            'jumlah' => 1,
            'subtotal_kaos' => 0,
            'donasi' => 0,
            'ongkir' => 0,
            'total' => 75000,
            'jenis' => 'donasi_saja',
            'status_pembayaran' => 'terverifikasi',
            'status_pengiriman' => 'belum_diproduksi',
        ]);
    }

    public function test_excel_export_semua_jenis_tidak_error(): void
    {
        $this->seedTerverifikasi();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.reports.export', ['format' => 'xlsx']));

        $response->assertStatus(200);
    }

    public function test_excel_export_jenis_donasi_tidak_error(): void
    {
        $this->seedTerverifikasi();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.reports.export', ['format' => 'xlsx', 'jenis' => 'donasi']));

        $response->assertStatus(200);
    }

    public function test_excel_export_jenis_kaos_tidak_error(): void
    {
        $this->seedTerverifikasi();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.reports.export', ['format' => 'xlsx', 'jenis' => 'kaos']));

        $response->assertStatus(200);
    }

    public function test_csv_export_tidak_error(): void
    {
        $this->seedTerverifikasi();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.reports.export', ['format' => 'csv']));

        $response->assertStatus(200);
    }
}
