<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    private Design $design;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->design = Design::where('aktif', true)->firstOrFail();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Tester Kaos',
            'nomor' => '08123456789',
            'email' => 'tester@example.com',
            'alamat' => 'Jl. Merpati No 1',
            'provinsi' => 'Bali',
            'kabupaten' => 'Denpasar',
            'kecamatan' => 'Denpasar Selatan',
            'kode_pos' => '80221',
            'design_id' => $this->design->id,
            'ukuran' => 'L',
            'jumlah' => 2,
            'metode' => 'qris',
        ], $overrides);
    }

    private function pngContents(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );
    }

    public function test_pemesanan_page_loads_with_selected_design_and_no_donation_field(): void
    {
        $this->get(route('checkout.pemesanan', ['desain' => $this->design->id, 'ukuran' => 'L', 'jumlah' => 2]))
            ->assertStatus(200)
            ->assertSee('Selesaikan Pemesanan', false)
            ->assertSee('Data pemesan', false)
            ->assertSee($this->design->nama)
            ->assertDontSee('name="donasi"', false);
    }

    public function test_pemesanan_falls_back_to_first_design_when_id_missing(): void
    {
        $this->get(route('checkout.pemesanan'))->assertStatus(200);
    }

    public function test_checkout_creates_kaos_order_without_donation(): void
    {
        $this->post(route('checkout.store'), $this->payload())
            ->assertStatus(302);

        $order = Order::where('nama', 'Tester Kaos')->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('kaos_donasi', $order->jenis);
        $this->assertSame(0, (int) $order->donasi);
        $this->assertSame($this->design->id, (int) $order->design_id);
        $this->assertSame('L', $order->ukuran);
        $this->assertSame(2, $order->jumlah);
        $this->assertSame($this->design->harga * 2, (int) $order->subtotal_kaos);
        $this->assertSame($this->design->harga * 2, (int) $order->total);
        $this->assertSame('belum_bayar', $order->status_pembayaran);
        $this->assertSame('belum_diproduksi', $order->status_pengiriman);
        $this->assertStringStartsWith('KS-', (string) $order->nomor_pesanan);
        $this->assertSame('Jl. Merpati No 1', $order->alamat);
        $this->assertSame('Bali', $order->provinsi);
    }

    public function test_checkout_redirects_to_payment_page(): void
    {
        $response = $this->post(route('checkout.store'), $this->payload());
        $order = Order::where('nama', 'Tester Kaos')->latest('id')->first();

        $response->assertRedirect(route('checkout.bayar', $order));

        $this->get(route('checkout.bayar', $order))
            ->assertStatus(200)
            ->assertSee('Pembayaran Kaos', false)
            ->assertSee($this->design->nama)
            ->assertDontSee('name="donasi"', false)
            ->assertSee('name="bukti"', false);
    }

    public function test_bukti_wajib_dan_status_berubah(): void
    {
        $this->post(route('checkout.store'), $this->payload());
        $order = Order::where('nama', 'Tester Kaos')->latest('id')->firstOrFail();

        $this->post(route('checkout.bayar.store', $order), ['_token' => csrf_token()])
            ->assertSessionHasErrors('bukti');

        $this->assertSame('belum_bayar', $order->fresh()->status_pembayaran);

        $file = UploadedFile::fake()->createWithContent('bukti.png', $this->pngContents());

        $this->post(route('checkout.bayar.store', $order), [
            '_token' => csrf_token(),
            'bukti' => $file,
        ])->assertRedirect(route('checkout.menunggu', $order));

        $order->refresh();
        $this->assertSame('menunggu_verifikasi', $order->status_pembayaran);
        $this->assertNotNull($order->bukti_path);
        $this->assertFileExists(storage_path('app/public/' . $order->bukti_path));
    }

    public function test_menunggu_page_shows_status_and_order_number(): void
    {
        $this->post(route('checkout.store'), $this->payload());
        $order = Order::where('nama', 'Tester Kaos')->latest('id')->firstOrFail();

        $file = UploadedFile::fake()->createWithContent('bukti.png', $this->pngContents());
        $this->post(route('checkout.bayar.store', $order), ['_token' => csrf_token(), 'bukti' => $file]);

        $this->get(route('checkout.menunggu', $order))
            ->assertStatus(200)
            ->assertSee('Menunggu Konfirmasi', false)
            ->assertSee($order->nomor_pesanan)
            ->assertDontSee('name="donasi"', false);
    }

    public function test_payment_page_is_forbidden_when_already_verified(): void
    {
        $this->post(route('checkout.store'), $this->payload());
        $order = Order::where('nama', 'Tester Kaos')->latest('id')->firstOrFail();
        $order->status_pembayaran = 'terverifikasi';
        $order->save();

        $this->get(route('checkout.bayar', $order))->assertStatus(403);
    }

    public function test_menunggu_page_is_forbidden_for_donation_order(): void
    {
        $this->post('/shop', [
            'nama' => 'Donatur',
            'nomor' => '08120000000',
            'email' => 'd@example.com',
            'donasi' => 50000,
        ]);
        $order = Order::where('nama', 'Donatur')->latest('id')->firstOrFail();

        $this->get(route('checkout.menunggu', $order))->assertStatus(404);
        $this->get(route('checkout.bayar', $order))->assertStatus(404);
    }

    public function test_pemesanan_requires_valid_design_and_fields(): void
    {
        $this->post(route('checkout.store'), $this->payload([
            'design_id' => 999999,
            'ukuran' => 'ZZZ',
            'jumlah' => 0,
            'provinsi' => 'Narnia',
            'nomor' => 'abc',
        ]))->assertSessionHasErrors([
            'design_id',
            'ukuran',
            'jumlah',
            'provinsi',
            'nomor',
        ]);

        $this->post(route('checkout.store'), $this->payload([
            'nama' => '',
            'alamat' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kode_pos' => '',
        ]))->assertSessionHasErrors([
            'nama',
            'alamat',
            'kabupaten',
            'kecamatan',
            'kode_pos',
        ]);

        $this->assertSame(5, Order::count());
    }

    public function test_design_cta_points_to_new_checkout(): void
    {
        $this->get('/design-kaos')
            ->assertStatus(200)
            ->assertSee('pesan-kaos?desain=', false)
            ->assertDontSee('/shop?desain=', false);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('pesan-kaos', false)
            ->assertDontSee('/shop?desain=', false);
    }
}
