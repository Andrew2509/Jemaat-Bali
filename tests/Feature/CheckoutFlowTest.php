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
            ->assertSee('Data Pribadi', false)
            ->assertSee('Alamat Pengiriman', false)
            ->assertSee('Beli Kaos Saja', false)
            ->assertSee($this->design->nama)
            ->assertDontSee('name="donasi"', false);
    }

    public function test_pemesanan_ui_matches_shop_layout_with_size_selection_and_cart(): void
    {
        $response = $this->get(route('checkout.pemesanan'));
        $response->assertStatus(200);

        // Header and hero identical layout
        $response->assertSee('Beli Kaos Saja', false);
        $response->assertSee('Satu Kaos, Sejuta Arti', false);
        $response->assertSee('Pesanan', false);

        // Mode pill toggle
        $response->assertSee('Pesan Kaos + Donasi', false);
        $response->assertSee('Donasi Saja', false);

        // Wizard steps indicator
        $response->assertSee('PILIH KAOS', false);
        $response->assertSee('DATA &amp; ALAMAT', false);
        $response->assertSee('PEMBAYARAN', false);

        // Product selection, steppers, and sidebar containers
        $response->assertSee('id="product-cards"', false);
        $response->assertSee('id="product-detail"', false);
        $response->assertSee('id="other-products"', false);
        $response->assertSee('Ringkasan Pesanan', false);
        $response->assertSee('Subtotal', false);
        $response->assertSee('Total Pembayaran', false);
        $response->assertSee('Lanjut ke Data &amp; Alamat', false);

        // Trust badges
        $response->assertSee('Pengiriman ke seluruh Indonesia', false);
        $response->assertSee('Pembayaran Aman', false);
        $response->assertSee('Setiap Pembelian Berdampak', false);

        // Size guide modal integration
        $response->assertSee('data-open-size-chart', false);
        $response->assertSee('id="size-chart-modal"', false);

        // No donation field
        $response->assertDontSee('name="donasi"', false);
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

    public function test_multi_item_order_creation_calculates_subtotal_and_compatibility_fields(): void
    {
        $designs = Design::where('aktif', true)->take(2)->get();
        if ($designs->count() < 2) {
            $this->markTestSkipped('At least 2 active designs are needed for multi-item test.');
        }

        $d1 = $designs[0];
        $d2 = $designs[1];

        // Step 1: Save identity to session
        $this->post(route('checkout.data.store'), [
            'nama' => 'Multi Item Tester',
            'nomor' => '081999888777',
            'email' => 'multi@example.com',
            'design_id' => $d1->id,
            'tipe' => 'kaos_donasi',
        ])->assertRedirect(route('checkout.pengiriman'));

        // Step 2: Submit shipping + items
        $this->post(route('checkout.pengiriman.store'), [
            'alamat' => 'Jl. Bypass Ngurah Rai No. 99',
            'provinsi' => 'Bali',
            'kabupaten' => 'Badung',
            'kecamatan' => 'Kuta Selatan',
            'kelurahan' => 'Jimbaran',
            'kode_pos' => '80361',
            'items' => [
                ['design_id' => $d1->id, 'ukuran' => 'M', 'jumlah' => 2],
                ['design_id' => $d2->id, 'ukuran' => 'XL', 'jumlah' => 3],
            ],
            'donasi' => 50000,
        ])->assertRedirect();

        $order = Order::where('nama', 'Multi Item Tester')->latest('id')->firstOrFail();

        $expectedSubtotal = ($d1->harga * 2) + ($d2->harga * 3);
        $expectedTotal = $expectedSubtotal + 50000;

        $this->assertSame($expectedSubtotal, (int) $order->subtotal_kaos);
        $this->assertSame($expectedTotal, (int) $order->total);
        $this->assertSame($expectedTotal, (int) $order->totalTagihan());

        // Legacy compatibility fields should reflect the first item
        $this->assertSame($d1->id, (int) $order->design_id);
        $this->assertSame('M', $order->ukuran);
        $this->assertSame(2, (int) $order->jumlah);

        // Verify JSON items structure
        $this->assertCount(2, $order->items_list);
        $this->assertSame($d1->nama, $order->items_list[0]['nama']);
        $this->assertSame($d2->nama, $order->items_list[1]['nama']);
        $this->assertSame($d1->harga * 2, $order->items_list[0]['subtotal']);
        $this->assertSame($d2->harga * 3, $order->items_list[1]['subtotal']);

        // Test rincianProduk contains both items
        $rincian = $order->rincianProduk();
        $this->assertStringContainsString($d1->nama, $rincian);
        $this->assertStringContainsString($d2->nama, $rincian);
    }

    public function test_order_rejects_inactive_design_in_multi_item(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();
        $inactive = Design::create([
            'nama' => 'Desain Nonaktif Test',
            'deskripsi' => 'Deskripsi',
            'harga' => 150000,
            'gambar' => 'images/test.jpg',
            'aktif' => false,
        ]);

        // Step 1: Save identity to session
        $this->post(route('checkout.data.store'), [
            'nama' => 'Inactive Design Tester',
            'nomor' => '081777666555',
            'email' => 'inactive@example.com',
            'design_id' => $design->id,
            'tipe' => 'kaos_saja',
        ])->assertRedirect(route('checkout.pengiriman'));

        // Step 2: Try to submit with inactive design in items
        $this->post(route('checkout.pengiriman.store'), [
            'alamat' => 'Jl. Test No. 1',
            'provinsi' => 'Bali',
            'kabupaten' => 'Badung',
            'kecamatan' => 'Kuta',
            'kode_pos' => '80361',
            'items' => [
                ['design_id' => $inactive->id, 'ukuran' => 'L', 'jumlah' => 1],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(0, Order::where('nomor', '081777666555')->count());
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

    public function test_checkout_page_renders_payment_method_options(): void
    {
        $this->get(route('checkout.pemesanan'))
            ->assertStatus(200)
            ->assertSee('Pilih Metode Pembayaran')
            ->assertSee('QRIS')
            ->assertSee('Scan QRIS untuk melakukan pembayaran.')
            ->assertSee('Transfer Bank')
            ->assertSee('Transfer melalui rekening bank terdaftar.')
            ->assertSee('Pilih metode pembayaran di bawah');
    }

    public function test_checkout_can_select_transfer_bank_payment_method(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();

        $response = $this->post(route('checkout.data.store'), [
            'nama' => 'Transfer Tester',
            'nomor' => '08123456789',
            'email' => 'transfer@example.com',
            'design_id' => $design->id,
            'ukuran' => 'L',
            'jumlah' => 1,
            'alamat' => 'Jl. Teuku Umar No. 10',
            'provinsi' => 'Bali',
            'kabupaten' => 'Kota Denpasar',
            'kecamatan' => 'Denpasar Barat',
            'kode_pos' => '80113',
            'metode' => 'transfer',
        ]);

        $order = Order::where('nama', 'Transfer Tester')->latest('id')->firstOrFail();
        $this->assertSame('transfer', $order->metode);
        $response->assertRedirect(route('checkout.bayar', $order));
    }

    public function test_payment_page_adapts_to_order_payment_method(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();
        \App\Models\ProgramInfo::first()->update([
            'bank_nama' => 'BCA',
            'bank_rekening' => '1234567890',
            'bank_atas_nama' => 'Panitia Pembangunan',
        ]);

        $transferOrder = Order::create([
            'nama' => 'User Transfer',
            'nomor' => '08111222333',
            'email' => 'transfer@test.com',
            'alamat' => 'Alamat Transfer',
            'total' => 150000,
            'metode' => 'transfer',
            'status' => 'menunggu_pembayaran',
            'order_type' => 'kaos',
            'design_id' => $design->id,
            'ukuran' => 'XL',
            'jumlah' => 1,
            'harga_satuan' => 150000,
        ]);

        $qrisOrder = Order::create([
            'nama' => 'User QRIS',
            'nomor' => '08111222444',
            'email' => 'qris@test.com',
            'alamat' => 'Alamat QRIS',
            'total' => 150000,
            'metode' => 'qris',
            'status' => 'menunggu_pembayaran',
            'order_type' => 'kaos',
            'design_id' => $design->id,
            'ukuran' => 'M',
            'jumlah' => 1,
            'harga_satuan' => 150000,
        ]);

        $this->get(route('checkout.bayar', $transferOrder))
            ->assertStatus(200)
            ->assertSee('Transfer Bank')
            ->assertSee('Lakukan transfer ke rekening bank di atas.')
            ->assertSee('name="metode" id="input-metode-bayar" value="transfer"', false);

        $this->get(route('checkout.bayar', $qrisOrder))
            ->assertStatus(200)
            ->assertSee('Pembayaran via QRIS')
            ->assertSee('Lakukan pembayaran menggunakan scan QRIS di atas.')
            ->assertSee('name="metode" id="input-metode-bayar" value="qris"', false);
    }

    public function test_payment_submission_updates_order_metode_if_switched(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();

        $order = Order::create([
            'nama' => 'Switch Method Tester',
            'nomor' => '081999888777',
            'email' => 'switch@test.com',
            'alamat' => 'Alamat Switch',
            'total' => 150000,
            'metode' => 'qris',
            'status' => 'menunggu_pembayaran',
            'order_type' => 'kaos',
            'design_id' => $design->id,
            'ukuran' => 'L',
            'jumlah' => 1,
            'harga_satuan' => 150000,
        ]);

        \Illuminate\Support\Facades\Storage::fake('public');

        $response = $this->post(route('checkout.bayar.store', $order), [
            'bukti' => \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg'),
            'metode' => 'transfer',
        ]);

        $response->assertRedirect(route('checkout.menunggu', $order));
        $this->assertSame('transfer', $order->fresh()->metode);
    }
}

