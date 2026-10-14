<?php

namespace Tests\Feature;

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
}
