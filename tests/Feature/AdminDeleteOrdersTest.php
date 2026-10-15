<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDeleteOrdersTest extends TestCase
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

    public function test_admin_can_delete_order_from_orders_page(): void
    {
        Storage::fake('public');
        $fakePath = 'bukti/test_order_bukti.jpg';
        Storage::disk('public')->put($fakePath, 'dummy receipt image content');

        $order = Order::create([
            'nomor_pesanan' => 'ORD-DEL-001',
            'nama' => 'Yohanes Pembeli',
            'nomor' => '081234567890',
            'email' => 'yohanes@example.com',
            'alamat' => 'Jl. Test No. 12',
            'ukuran' => 'L',
            'jumlah' => 1,
            'subtotal_kaos' => 150000,
            'donasi' => 50000,
            'ongkir' => 20000,
            'total' => 220000,
            'jenis' => 'kaos_donasi',
            'status_pembayaran' => 'terverifikasi',
            'status_pengiriman' => 'siap_kirim',
            'bukti_path' => $fakePath,
        ]);

        Storage::disk('public')->assertExists($fakePath);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.orders.destroy', $order));

        $response->assertRedirect(route('admin.orders'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);

        Storage::disk('public')->assertMissing($fakePath);
    }

    public function test_admin_can_delete_order_from_verification_page(): void
    {
        Storage::fake('public');
        $fakePath = 'bukti/verif_order.jpg';
        Storage::disk('public')->put($fakePath, 'dummy proof');

        $order = Order::create([
            'nomor_pesanan' => 'ORD-DEL-VERIF',
            'nama' => 'Maria Pembeli',
            'nomor' => '081298765432',
            'alamat' => 'Jl. Mawar No. 1',
            'ukuran' => 'M',
            'jumlah' => 2,
            'subtotal_kaos' => 300000,
            'donasi' => 0,
            'ongkir' => 15000,
            'total' => 315000,
            'jenis' => 'kaos',
            'status_pembayaran' => 'menunggu_verifikasi',
            'status_pengiriman' => 'belum_diproduksi',
            'bukti_path' => $fakePath,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.verification.destroy', [$order->id, 'order']));

        $response->assertRedirect(route('admin.verification'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);

        Storage::disk('public')->assertMissing($fakePath);
    }

    public function test_admin_can_delete_donation_from_verification_page(): void
    {
        Storage::fake('public');
        $fakePath = 'bukti/verif_donation.jpg';
        Storage::disk('public')->put($fakePath, 'dummy donation receipt');

        $donation = Donation::create([
            'kode' => 'DON-DEL-001',
            'nama' => 'Petrus Donatur',
            'nomor' => '081345678901',
            'nominal' => 250000,
            'tanggal' => now(),
            'status' => 'menunggu_verifikasi',
            'catatan' => 'Donasi untuk renovasi gedung',
            'bukti_path' => $fakePath,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.verification.destroy', [$donation->id, 'donasi']));

        $response->assertRedirect(route('admin.verification'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('donations', [
            'id' => $donation->id,
        ]);

        Storage::disk('public')->assertMissing($fakePath);
    }

    public function test_unauthenticated_cannot_delete(): void
    {
        $order = Order::create([
            'nomor_pesanan' => 'ORD-UNAUTH',
            'nama' => 'Test',
            'nomor' => '081234567890',
            'ukuran' => 'L',
            'jumlah' => 1,
            'subtotal_kaos' => 100000,
            'donasi' => 0,
            'ongkir' => 10000,
            'total' => 110000,
            'jenis' => 'kaos',
            'status_pembayaran' => 'menunggu_verifikasi',
            'status_pengiriman' => 'belum_diproduksi',
        ]);

        $response = $this->post(route('admin.orders.destroy', $order));
        $response->assertRedirect(route('login'));

        $responseVerif = $this->post(route('admin.verification.destroy', [$order->id, 'order']));
        $responseVerif->assertRedirect(route('login'));

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }
}
