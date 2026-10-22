<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVerificationBelumBayarTest extends TestCase
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

    private function createBelumBayarOrder(): Order
    {
        return Order::create([
            'nomor_pesanan' => 'KD-UNPAID-001',
            'nama' => 'Belum Bayar Order',
            'nomor' => '081200000001',
            'email' => 'unpaid@example.com',
            'ukuran' => 'M',
            'jumlah' => 1,
            'subtotal_kaos' => 150000,
            'donasi' => 50000,
            'ongkir' => 0,
            'total' => 200000,
            'jenis' => 'kaos_donasi',
            'status_pembayaran' => 'belum_bayar',
            'status_pengiriman' => 'belum_diproduksi',
        ]);
    }

    private function createBelumBayarDonation(): Donation
    {
        return Donation::create([
            'kode' => 'DN-UNPAID-001',
            'nama' => 'Belum Bayar Donatur',
            'nomor' => '081200000002',
            'nominal' => 75000,
            'tanggal' => '2026-10-20',
            'metode' => 'qris',
            'status' => 'belum_bayar',
        ]);
    }

    public function test_verification_table_lists_unpaid_orders(): void
    {
        $order = $this->createBelumBayarOrder();

        $response = $this->actingAs($this->superAdmin)->get(route('admin.verification'));

        $response->assertStatus(200);
        $response->assertSee('KD-UNPAID-001');
        $response->assertSee('Belum Bayar Order');
    }

    public function test_verification_table_lists_unpaid_donations(): void
    {
        $donation = $this->createBelumBayarDonation();

        $response = $this->actingAs($this->superAdmin)->get(route('admin.verification'));

        $response->assertStatus(200);
        $response->assertSee('DN-UNPAID-001');
        $response->assertSee('Belum Bayar Donatur');
    }

    public function test_verification_filter_by_unpaid_status_returns_rows(): void
    {
        $this->createBelumBayarOrder();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.verification', ['status' => 'belum_bayar']));

        $response->assertStatus(200);
        $response->assertSee('KD-UNPAID-001');
        $response->assertDontSee('Tidak ada transaksi yang cocok dengan filter.');
    }

    public function test_unpaid_statistic_counts_orders_and_donations(): void
    {
        $this->createBelumBayarOrder();
        $this->createBelumBayarDonation();

        $response = $this->actingAs($this->superAdmin)->get(route('admin.verification'));

        $response->assertStatus(200);
        $response->assertSee('Belum bayar');
        $response->assertSee('275.000', false);
    }
}
