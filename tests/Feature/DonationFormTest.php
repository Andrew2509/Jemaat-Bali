<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationFormTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Nyoman Wirata',
            'nomor' => '081234556677',
            'email' => 'nyoman@example.com',
            'donasi' => 100000,
        ], $overrides);
    }

    public function test_donation_page_loads_without_kaos_fields(): void
    {
        $this->get('/shop')
            ->assertStatus(200)
            ->assertSee('Donasi')
            ->assertSee('name="donasi"', false);
    }

    public function test_donation_only_order_is_saved(): void
    {
        $this->post('/shop', $this->validPayload())
            ->assertRedirect();

        $order = Order::latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('donasi_saja', $order->jenis);
        $this->assertNull($order->design_id);
        $this->assertSame(100000, $order->donasi);
        $this->assertSame(100000, $order->total);
        $this->assertSame(0, (int) $order->subtotal_kaos);
        $this->assertStringStartsWith('DN-', (string) $order->nomor_pesanan);
    }

    public function test_donation_requires_minimum_amount(): void
    {
        $this->post('/shop', $this->validPayload(['donasi' => 500]))
            ->assertSessionHasErrors('donasi');

        $this->assertSame(0, Order::count());
    }

    private function kaosDonasiPayload(array $overrides = []): array
    {
        $this->seed();
        $design = Design::where('aktif', true)->firstOrFail();

        return array_merge([
            'jenis' => 'kaos_donasi',
            'nama' => 'I Wayan Suarta',
            'nomor' => '081298765432',
            'email' => 'wayan@example.com',
            'alamat' => 'Jl. Sunset Road No. 10',
            'provinsi' => 'Bali',
            'kabupaten' => 'Badung',
            'kecamatan' => 'Kuta',
            'kelurahan' => 'Legian',
            'kode_pos' => '80361',
            'items' => [['design_id' => $design->id, 'ukuran' => 'L', 'jumlah' => 1]],
            'donasi' => 50000,
            'metode' => 'qris',
        ], $overrides);
    }

    public function test_kaos_donation_accepts_minimum_of_50000(): void
    {
        $this->post('/shop', $this->kaosDonasiPayload(['donasi' => 50000]))
            ->assertSessionHasNoErrors();

        $order = Order::where('nomor', '081298765432')->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('kaos_donasi', $order->jenis);
        $this->assertSame(50000, (int) $order->donasi);
    }

    public function test_kaos_donation_rejects_amount_below_50000(): void
    {
        $this->post('/shop', $this->kaosDonasiPayload(['donasi' => 49000]))
            ->assertSessionHasErrors('donasi');

        $this->assertSame(0, Order::where('nomor', '081298765432')->count());
    }

    public function test_shop_page_displays_minimum_donation_of_50000(): void
    {
        $this->get('/shop')
            ->assertStatus(200)
            ->assertSee('Minimal Rp50.000')
            ->assertSee('min="50000"', false);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->from('/shop')->post('/shop', [
            'nama' => '',
            'nomor' => 'abc',
            'email' => 'bukan-email',
            'donasi' => '',
        ])->assertRedirect('/shop')
            ->assertSessionHasErrors(['nama', 'nomor', 'email', 'donasi']);

        $this->assertSame(0, Order::count());
    }

    public function test_donation_flow_reaches_payment_page(): void
    {
        $this->post('/shop', $this->validPayload());

        $order = Order::latest('id')->first();

        $this->get(route('shop.pemesanan', $order))
            ->assertStatus(200)
            ->assertSee('Donasi Saja');

        $this->get(route('shop.payment', $order))
            ->assertStatus(200)
            ->assertSee('name="bukti"', false);

        $this->post(route('shop.payment.store', $order), [
            '_token' => csrf_token(),
        ])->assertSessionHasErrors('bukti');

        $file = \Illuminate\Http\UploadedFile::fake()->create('bukti.png', 20, 'image/png');

        $this->post(route('shop.payment.store', $order), [
            '_token' => csrf_token(),
            'bukti' => $file,
        ])->assertRedirect(route('shop.waiting', $order));

        $this->assertSame('menunggu_verifikasi', $order->fresh()->status_pembayaran);

        $this->get(route('shop.waiting', $order))
            ->assertStatus(200)
            ->assertSee('Verifikasi');
    }

    public function test_kaos_donation_flow_reaches_payment_and_admin_verification(): void
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $admin = \App\Models\User::where('email', 'admin@jemaatbali.or.id')->first();

        \App\Models\ProgramInfo::firstOrCreate(
            ['id' => 1],
            [
                'nama_program' => 'Pembangunan Jemaat Bali',
                'deskripsi' => 'Penggalangan dana pembangunan bersama.',
                'whatsapp' => '0812-0000-0000',
                'email' => 'admin@jemaatbali.or.id',
                'judul_proposal' => 'Proposal pembangunan',
                'proposal_tipe' => 'pdf',
                'bank_nama' => 'BCA',
                'bank_rek' => '1234567890',
                'bank_atas_nama' => 'Panitia Pembangunan',
            ]
        );

        $response = $this->post('/shop', $this->kaosDonasiPayload([
            'donasi' => 75000,
            'metode' => 'transfer',
        ]));

        $order = Order::where('nomor', '081298765432')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame('kaos_donasi', $order->jenis);
        $this->assertSame('transfer', $order->metode);
        $this->assertSame(75000, (int) $order->donasi);

        $response->assertRedirect(route('checkout.bayar', $order));

        $this->get(route('checkout.bayar', $order))
            ->assertStatus(200)
            ->assertSee('Transfer Bank')
            ->assertSee('Donasi sukarela');

        $file = \Illuminate\Http\UploadedFile::fake()->create('bukti.png', 20, 'image/png');
        $this->post(route('checkout.bayar.store', $order), [
            '_token' => csrf_token(),
            'bukti' => $file,
            'metode' => 'transfer',
        ])->assertRedirect(route('checkout.menunggu', $order));

        $this->assertSame('menunggu_verifikasi', $order->fresh()->status_pembayaran);

        // Check in admin verification
        $this->actingAs($admin)
            ->get(route('admin.verification', ['q' => $order->nomor_pesanan]))
            ->assertStatus(200)
            ->assertSee($order->nomor_pesanan);

        // Verify order in admin
        $this->actingAs($admin)
            ->post(route('admin.verification.action', ['id' => $order->id, 'tipe' => 'order']), [
                'aksi' => 'verifikasi',
                'kirim_wa' => false,
            ])->assertRedirect();

        $this->assertSame('terverifikasi', $order->fresh()->status_pembayaran);

        // Check in admin orders
        $this->actingAs($admin)
            ->get(route('admin.orders', ['q' => $order->nomor_pesanan]))
            ->assertStatus(200)
            ->assertSee($order->nomor_pesanan);
    }
}

