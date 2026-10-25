<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderDeleteDisabledTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::where('email', 'admin@jemaatbali.or.id')->first();
    }

    public function test_terkirim_order_has_no_delete_button_in_orders_table(): void
    {
        $design = Design::create([
            'nama' => 'Kaos Test',
            'judul' => 'Kaos Test',
            'harga' => 150000,
            'gambar' => 'designs/kaos-keluarga-kudus.svg',
            'aktif' => true,
        ]);

        $deliveredOrder = Order::create([
            'nomor_pesanan' => 'ORD-TERK-001',
            'design_id' => $design->id,
            'nama' => 'Wisnu Terkirim',
            'nomor' => '081111111111',
            'email' => 'wisnu@example.com',
            'alamat' => 'Jl. Contoh No. 1',
            'ukuran' => 'L',
            'jumlah' => 1,
            'subtotal_kaos' => 150000,
            'donasi' => 0,
            'ongkir' => 20000,
            'total' => 170000,
            'jenis' => 'kaos_donasi',
            'status_pembayaran' => 'terverifikasi',
            'status_pengiriman' => 'terkirim',
        ]);

        $deletableOrder = Order::create([
            'nomor_pesanan' => 'ORD-BISA-001',
            'design_id' => $design->id,
            'nama' => 'Misha Siap',
            'nomor' => '082222222222',
            'email' => 'misha@example.com',
            'alamat' => 'Jl. Contoh No. 2',
            'ukuran' => 'M',
            'jumlah' => 1,
            'subtotal_kaos' => 150000,
            'donasi' => 0,
            'ongkir' => 20000,
            'total' => 170000,
            'jenis' => 'kaos_donasi',
            'status_pembayaran' => 'terverifikasi',
            'status_pengiriman' => 'siap_kirim',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders'));

        $response->assertStatus(200);

        // Both orders are terverifikasi, so both appear on the page.
        $response->assertSee($deliveredOrder->nomor_pesanan);
        $response->assertSee($deletableOrder->nomor_pesanan);

        // Page should show the informational chip for the delivered order.
        $response->assertSee('Sudah dikirim');

        // Both rows now open a small popup modal instead of browser confirm().
        // A delivered row must not render its own trigger button or hidden delete form.
        $response->assertDontSee('data-order-id="' . $deliveredOrder->id . '"');
        $response->assertDontSee('delete-form-' . $deliveredOrder->id);

        // A non-delivered row must still expose its trigger button and hidden delete
        // form so the modal can submit the right endpoint for eligible rows.
        $response->assertSee('data-order-id="' . $deletableOrder->id . '"');
        $response->assertSee('delete-form-' . $deletableOrder->id);

        // Do not assertDontSee('Hapus') globally: other rows may still use the
        // word "Hapus" for their own delete trigger, which is expected behavior.
    }

    public function test_terkirim_selected_order_has_no_hapus_pesanan_in_detail_panel(): void
    {
        $design = Design::create([
            'nama' => 'Kaos Test',
            'judul' => 'Kaos Test',
            'harga' => 150000,
            'gambar' => 'designs/kaos-keluarga-kudus.svg',
            'aktif' => true,
        ]);

        $deliveredOrder = Order::create([
            'nomor_pesanan' => 'ORD-TERK-002',
            'design_id' => $design->id,
            'nama' => 'Terdekri',
            'nomor' => '083333333333',
            'email' => 'ter@example.com',
            'alamat' => 'Jl. Contoh No. 3',
            'ukuran' => 'L',
            'jumlah' => 1,
            'subtotal_kaos' => 150000,
            'donasi' => 0,
            'ongkir' => 20000,
            'total' => 170000,
            'jenis' => 'kaos',
            'status_pembayaran' => 'terverifikasi',
            'status_pengiriman' => 'terkirim',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders', ['selected_id' => $deliveredOrder->id]));

        $response->assertStatus(200);
        $response->assertSee($deliveredOrder->nomor_pesanan);

        // Detail panel should not contain the "Hapus Pesanan Ini" button for a delivered order.
        $response->assertDontSee('Hapus Pesanan Ini');

        // It should show the informational message instead.
        $response->assertSee('Pesanan ini sudah dikirim dan tidak dapat dihapus.');
    }
}
