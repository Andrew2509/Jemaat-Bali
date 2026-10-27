<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Order;
use App\Models\Role;
use App\Models\ShippingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RajaOngkirIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_provinces_returns_success_and_provinces_list(): void
    {
        $response = $this->getJson(route('api.rajaongkir.provinces'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'is_live',
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_api_cities_returns_cities_for_given_province(): void
    {
        $provResponse = $this->getJson(route('api.rajaongkir.provinces'));
        $provinces = collect($provResponse->json('data'));
        $bali = $provinces->first(fn ($p) => str_contains(strtoupper($p['name'] ?? ''), 'BALI')) ?? $provinces->first();
        $provId = $bali['id'] ?? 1;

        $response = $this->getJson(route('api.rajaongkir.cities', ['province_id' => $provId]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'is_live',
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_api_districts_returns_districts_with_zip_code(): void
    {
        $provResponse = $this->getJson(route('api.rajaongkir.provinces'));
        $provinces = collect($provResponse->json('data'));
        $bali = $provinces->first(fn ($p) => str_contains(strtoupper($p['name'] ?? ''), 'BALI')) ?? $provinces->first();
        $provId = $bali['id'] ?? 1;

        $cityResponse = $this->getJson(route('api.rajaongkir.cities', ['province_id' => $provId]));
        $cityId = $cityResponse->json('data.0.id') ?? 101;

        $response = $this->getJson(route('api.rajaongkir.districts', ['city_id' => $cityId]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'is_live',
                'data' => [
                    '*' => ['id', 'name', 'zip_code'],
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_api_subdistricts_returns_subdistricts_with_zip_code(): void
    {
        $provResponse = $this->getJson(route('api.rajaongkir.provinces'));
        $provinces = collect($provResponse->json('data'));
        $bali = $provinces->first(fn ($p) => str_contains(strtoupper($p['name'] ?? ''), 'BALI')) ?? $provinces->first();
        $provId = $bali['id'] ?? 1;

        $cityResponse = $this->getJson(route('api.rajaongkir.cities', ['province_id' => $provId]));
        $cityId = $cityResponse->json('data.0.id') ?? 101;

        $distResponse = $this->getJson(route('api.rajaongkir.districts', ['city_id' => $cityId]));
        $distId = $distResponse->json('data.0.id') ?? 1001;

        $response = $this->getJson(route('api.rajaongkir.subdistricts', ['district_id' => $distId]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'is_live',
                'data' => [
                    '*' => ['id', 'name', 'zip_code'],
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_checkout_page_renders_rajaongkir_cascading_selectors(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();

        $this->post(route('checkout.data.store'), [
            'nama' => 'Tester',
            'nomor' => '08123456789',
            'email' => 'tester@example.com',
            'design_id' => $design->id,
        ]);

        $response = $this->get(route('checkout.pengiriman'));

        $response->assertStatus(200)
            ->assertSee('id="provinsi"', false)
            ->assertSee('id="kabupaten"', false)
            ->assertSee('id="kecamatan"', false)
            ->assertSee('id="kelurahan"', false)
            ->assertSee('id="kode_pos"', false)
            ->assertSee('BALI', false);
    }

    public function test_shop_page_renders_rajaongkir_cascading_selectors_with_kelurahan(): void
    {
        $response = $this->get(route('shop', ['tab' => 'kaos_donasi']));

        $response->assertStatus(200)
            ->assertSee('id="kd-provinsi"', false)
            ->assertSee('id="kd-kabupaten"', false)
            ->assertSee('id="kd-kecamatan"', false)
            ->assertSee('id="kd-kelurahan"', false)
            ->assertSee('id="kd-kode-pos"', false)
            ->assertSee('kd-badge-kodepos', false)
            ->assertSee('BALI', false);
    }

    public function test_order_can_be_stored_with_rajaongkir_location_and_kelurahan_in_checkout(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();

        $this->post(route('checkout.data.store'), [
            'nama' => 'I Putu Semeton',
            'nomor' => '081234567890',
            'email' => 'semeton@example.com',
            'design_id' => $design->id,
        ]);

        $payload = [
            'alamat' => 'Jl. Sunset Road No. 88',
            'provinsi' => 'BALI',
            'kabupaten' => 'KABUPATEN BADUNG',
            'kecamatan' => 'KUTA',
            'kelurahan' => 'LEGIAN',
            'kode_pos' => '80361',
            'items' => [
                ['design_id' => $design->id, 'ukuran' => 'XL', 'jumlah' => 2],
            ],
        ];

        $response = $this->post(route('checkout.pengiriman.store'), $payload);

        $order = Order::where('nomor', '081234567890')->first();
        $this->assertNotNull($order);
        $this->assertEquals('BALI', $order->provinsi);
        $this->assertEquals('KABUPATEN BADUNG', $order->kabupaten);
        $this->assertEquals('KUTA', $order->kecamatan);
        $this->assertEquals('LEGIAN', $order->kelurahan);
        $this->assertEquals('80361', $order->kode_pos);
        $this->assertStringContainsString('Kel./Desa LEGIAN', $order->alamatLengkap());

        $response->assertRedirect(route('checkout.bayar', $order));
    }

    public function test_order_can_be_stored_with_rajaongkir_location_and_kelurahan_in_shop(): void
    {
        $design = Design::where('aktif', true)->firstOrFail();

        $payload = [
            'jenis' => 'kaos_donasi',
            'nama' => 'Ni Wayan Sari',
            'nomor' => '081987654321',
            'email' => 'sari@example.com',
            'alamat' => 'Jl. Danau Tamblingan No. 12',
            'provinsi' => 'BALI',
            'kabupaten' => 'KOTA DENPASAR',
            'kecamatan' => 'DENPASAR SELATAN',
            'kelurahan' => 'SANUR',
            'kode_pos' => '80228',
            'items' => [
                ['design_id' => $design->id, 'ukuran' => 'M', 'jumlah' => 1],
            ],
            'donasi' => 200000,
        ];

        $response = $this->post(route('shop.store'), $payload);

        $order = Order::where('nomor', '081987654321')->first();
        $this->assertNotNull($order);
        $this->assertEquals('BALI', $order->provinsi);
        $this->assertEquals('KOTA DENPASAR', $order->kabupaten);
        $this->assertEquals('DENPASAR SELATAN', $order->kecamatan);
        $this->assertEquals('SANUR', $order->kelurahan);
        $this->assertEquals('80228', $order->kode_pos);
        $this->assertStringContainsString('Kel./Desa SANUR', $order->alamatLengkap());

        $response->assertRedirect(route('checkout.bayar', $order));
    }

    public function test_admin_can_view_rajaongkir_provider_tab(): void
    {
        $user = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($user)->get(route('admin.provider', ['tab' => 'rajaongkir']));

        $response->assertStatus(200)
            ->assertSee('Provider RajaOngkir (Lokasi & Ekspedisi)', false)
            ->assertSee('Search Province', false)
            ->assertSee('Search District', false)
            ->assertSee('rajaongkir_api_key', false);
    }

    public function test_admin_can_update_rajaongkir_settings(): void
    {
        $user = User::where('email', 'admin@jemaatbali.or.id')->first();

        Http::fake([
            'https://rajaongkir.komerce.id/api/v1/destination/province' => Http::response([
                'meta' => ['code' => 200, 'status' => 'success', 'message' => 'Success'],
                'data' => [
                    ['id' => 1, 'name' => 'BALI'],
                    ['id' => 2, 'name' => 'DKI JAKARTA'],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.provider.rajaongkir.update'), [
            'api_key' => 'mock_api_key_12345',
            'base_url' => 'https://rajaongkir.komerce.id/api/v1',
            'is_active' => 1,
        ]);

        $response->assertRedirect();

        $setting = ShippingSetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('mock_api_key_12345', $setting->api_key);
        $this->assertEquals('connected', $setting->status);
    }

    public function test_admin_can_update_rajaongkir_with_key_header_prefix(): void
    {
        $user = User::where('email', 'admin@jemaatbali.or.id')->first();

        Http::fake([
            'https://rajaongkir.komerce.id/api/v1/destination/province' => Http::response([
                'meta' => ['code' => 200, 'status' => 'success'],
                'data' => [['id' => 1, 'name' => 'BALI']],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('admin.provider.rajaongkir.update'), [
            'api_key' => 'Key: clean_token_99999',
            'base_url' => 'https://rajaongkir.komerce.id/api/v1',
            'is_active' => 1,
        ]);

        $response->assertRedirect();

        $setting = ShippingSetting::getSetting();
        $this->assertEquals('clean_token_99999', $setting->getEffectiveApiKey());

        // Verifikasi muncul di input value pada halaman provider
        $pageResponse = $this->actingAs($user)->get(route('admin.provider', ['tab' => 'rajaongkir']));
        $pageResponse->assertStatus(200)
            ->assertSee('value="clean_token_99999"', false);
    }
}

