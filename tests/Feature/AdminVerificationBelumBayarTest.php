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
            'kode' => 'DN-UNP-001',
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
        $response->assertSee('DN-UNP-001');
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

        \Illuminate\Support\Facades\Cache::forget('admin_verification_totals');

        $response = $this->actingAs($this->superAdmin)->get(route('admin.verification'));

        $response->assertStatus(200);
        $response->assertSee('Belum bayar');
        // Total should be: Order 200000 + Donation 75000 = 275000
        $response->assertSee('Rp275.000');
    }

    public function test_verification_button_is_disabled_and_action_blocked_when_order_has_no_proof(): void
    {
        $order = $this->createBelumBayarOrder();

        // 1. Check UI detail inspector: verification button should show disabled text
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.verification', ['selected_id' => $order->id, 'selected_tipe' => 'order']));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pembayaran (Belum Aktif)');
        $response->assertSee('Tombol verifikasi belum aktif karena user belum mengirim bukti pembayaran');

        // 2. Attempting to verify without proof should be blocked
        $actionResponse = $this->actingAs($this->superAdmin)
            ->post(route('admin.verification.action', [$order->id, 'order']), [
                'aksi' => 'verifikasi',
            ]);

        $actionResponse->assertRedirect(route('admin.verification', [
            'selected_id' => $order->id,
            'selected_tipe' => 'order',
        ]));
        $actionResponse->assertSessionHas('error', 'Pembayaran belum dapat diverifikasi karena pemesan/donatur belum mengunggah bukti pembayaran.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status_pembayaran' => 'belum_bayar',
        ]);
    }

    public function test_verification_succeeds_when_order_has_proof(): void
    {
        $order = $this->createBelumBayarOrder();
        $order->update([
            'bukti_path' => 'bukti/transfer_test.jpg',
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);

        // UI should show active verification button
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.verification', ['selected_id' => $order->id, 'selected_tipe' => 'order']));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pembayaran');
        $response->assertDontSee('Verifikasi Pembayaran (Belum Aktif)');

        // Action should succeed
        $actionResponse = $this->actingAs($this->superAdmin)
            ->post(route('admin.verification.action', [$order->id, 'order']), [
                'aksi' => 'verifikasi',
                'kirim_wa' => false,
            ]);

        $actionResponse->assertRedirect(route('admin.verification', [
            'selected_id' => $order->id,
            'selected_tipe' => 'order',
        ]));
        $actionResponse->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status_pembayaran' => 'terverifikasi',
        ]);
    }

    public function test_verification_blocked_when_donation_has_no_proof(): void
    {
        $donation = $this->createBelumBayarDonation();

        $actionResponse = $this->actingAs($this->superAdmin)
            ->post(route('admin.verification.action', [$donation->id, 'donation']), [
                'aksi' => 'verifikasi',
            ]);

        $actionResponse->assertSessionHas('error', 'Pembayaran belum dapat diverifikasi karena pemesan/donatur belum mengunggah bukti pembayaran.');

        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'status' => 'belum_bayar',
        ]);
    }
}
