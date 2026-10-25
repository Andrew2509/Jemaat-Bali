<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankInfoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@jemaatbali.or.id')->firstOrFail();
    }

    private function infoPayload(array $overrides = []): array
    {
        return array_merge([
            'nama_program' => 'Pembangunan Jemaat Bali',
            'tagline' => 'BANGUN BERSAMA',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-3456-7890',
            'email' => 'panitia@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
        ], $overrides);
    }

    private function configureBank(): void
    {
        ProgramInfo::first()->update([
            'qris_code' => '000201010211266006ID102002112800139200140000000000',
            'bank_nama' => 'BCA',
            'bank_rek' => '1234567890',
            'bank_atas_nama' => 'Yayasan Jemaat Bali',
        ]);
    }

    private function makeOrder(string $jenis = 'kaos_donasi'): Order
    {
        return Order::create([
            'nomor_pesanan' => $jenis === 'kaos_donasi' ? 'KS-261025-0001' : 'DN-261025-0001',
            'jenis' => $jenis,
            'nama' => 'Tester Bank',
            'nomor' => '08123456789',
            'email' => 'tester@example.com',
            'alamat' => $jenis === 'kaos_donasi' ? 'Jl. Merpati No 1' : null,
            'provinsi' => $jenis === 'kaos_donasi' ? 'Bali' : null,
            'design_id' => $jenis === 'kaos_donasi' ? Design::where('aktif', true)->firstOrFail()->id : null,
            'ukuran' => $jenis === 'kaos_donasi' ? 'L' : null,
            'jumlah' => $jenis === 'kaos_donasi' ? 1 : 0,
            'subtotal_kaos' => $jenis === 'kaos_donasi' ? 150000 : 0,
            'donasi' => 100000,
            'ongkir' => 0,
            'total' => 250000,
            'metode' => 'qris',
            'status_pembayaran' => 'belum_bayar',
            'status_pengiriman' => 'belum_diproduksi',
        ]);
    }

    public function test_admin_content_page_renders_bank_fields(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.content'));

        $response->assertStatus(200);
        $response->assertSee('Informasi rekening bank');
        $response->assertSee('name="bank_nama"', false);
        $response->assertSee('name="bank_rek"', false);
        $response->assertSee('name="bank_atas_nama"', false);
    }

    public function test_admin_can_save_and_normalize_bank_info(): void
    {
        $response = $this->actingAs($this->admin())->post(
            route('admin.content.info'),
            $this->infoPayload([
                'bank_nama' => "  BCA  ",
                'bank_rek' => " 1234567890\n",
                'bank_atas_nama' => "Yayasan\t Jemaat  Bali ",
            ])
        );

        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $info = ProgramInfo::first();
        $this->assertSame('BCA', $info->bank_nama);
        $this->assertSame('1234567890', $info->bank_rek);
        $this->assertSame('Yayasan Jemaat Bali', $info->bank_atas_nama);
        $this->assertTrue($info->hasBankInfo());
    }

    public function test_admin_can_clear_bank_info(): void
    {
        $this->configureBank();

        $response = $this->actingAs($this->admin())->post(
            route('admin.content.info'),
            $this->infoPayload(['bank_nama' => '', 'bank_rek' => '', 'bank_atas_nama' => ''])
        );

        $response->assertRedirect(route('admin.content'));

        $info = ProgramInfo::first();
        $this->assertNull($info->bank_nama);
        $this->assertNull($info->bank_rek);
        $this->assertNull($info->bank_atas_nama);
        $this->assertFalse($info->hasBankInfo());
    }

    public function test_shop_payment_page_shows_bank_info_below_qris(): void
    {
        $this->configureBank();
        $order = $this->makeOrder('donasi_saja');

        $response = $this->get(route('shop.payment', $order));
        $response->assertStatus(200);
        $response->assertSee('Atau Transfer Bank');
        $response->assertSee('BCA');
        $response->assertSee('1234567890');
        $response->assertSee('a.n. Yayasan Jemaat Bali');

        // Blok rekening harus berada tepat setelah (di bawah) logo QRIS
        $content = $response->getContent();
        $qrisPos = strpos($content, 'Scan to Pay');
        $bankPos = strpos($content, 'Atau Transfer Bank');
        $this->assertNotFalse($qrisPos, 'QRIS harus tampil di halaman pembayaran.');
        $this->assertNotFalse($bankPos, 'Info bank harus tampil di halaman pembayaran.');
        $this->assertGreaterThan($qrisPos, $bankPos, 'Info bank harus berada di bawah logo QRIS.');
    }

    public function test_checkout_payment_page_shows_bank_info(): void
    {
        $this->configureBank();
        $order = $this->makeOrder('kaos_donasi');

        $response = $this->get(route('checkout.bayar', $order));
        $response->assertStatus(200);
        $response->assertSee('Atau Transfer Bank');
        $response->assertSee('BCA');
        $response->assertSee('1234567890');
        $response->assertSee('a.n. Yayasan Jemaat Bali');
    }

    public function test_payment_pages_hide_bank_info_when_not_configured(): void
    {
        $orderShop = $this->makeOrder('donasi_saja');
        $orderCheckout = $this->makeOrder('kaos_donasi');

        $this->get(route('shop.payment', $orderShop))
            ->assertStatus(200)
            ->assertDontSee('Atau Transfer Bank');

        $this->get(route('checkout.bayar', $orderCheckout))
            ->assertStatus(200)
            ->assertDontSee('Atau Transfer Bank');
    }
}
