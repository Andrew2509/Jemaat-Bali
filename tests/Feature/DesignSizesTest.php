<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DesignSizesTest extends TestCase
{
    use RefreshDatabase;

    public function test_design_model_has_default_sizes_when_ukuran_is_null(): void
    {
        $design = new Design([
            'nama' => 'Kaos Uji',
            'harga' => 150000,
            'aktif' => true,
        ]);

        $this->assertSame(['S', 'M', 'L', 'XL', 'XXL'], $design->pilihanUkuran());
        $this->assertTrue($design->isUkuranTersedia('S'));
        $this->assertTrue($design->isUkuranTersedia('XXL'));
    }

    public function test_design_model_respects_custom_available_sizes(): void
    {
        $design = new Design([
            'nama' => 'Kaos Edisi Terbatas',
            'harga' => 150000,
            'aktif' => true,
            'ukuran' => ['M', 'L'],
        ]);

        $this->assertSame(['M', 'L'], $design->pilihanUkuran());
        $this->assertTrue($design->isUkuranTersedia('M'));
        $this->assertTrue($design->isUkuranTersedia('L'));
        $this->assertFalse($design->isUkuranTersedia('S'));
        $this->assertFalse($design->isUkuranTersedia('XL'));
        $this->assertFalse($design->isUkuranTersedia('XXL'));
    }

    public function test_admin_can_create_design_with_selected_sizes(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $image = UploadedFile::fake()->image('kaos-baru.png', 400, 400);

        $response = $this->actingAs($admin)
            ->post(route('admin.designs.store'), [
                'nama' => 'Kaos Uji Custom Size',
                'harga' => 165000,
                'deskripsi' => 'Deskripsi kaos bahan combed 30s',
                'aktif' => 1,
                'ukuran' => ['S', 'L', 'XXL'],
                'gambar' => $image,
            ]);

        $response->assertRedirect();
        
        $design = Design::where('nama', 'Kaos Uji Custom Size')->first();
        $this->assertNotNull($design);
        $this->assertSame(['S', 'L', 'XXL'], $design->ukuran);
        $this->assertTrue($design->isUkuranTersedia('S'));
        $this->assertFalse($design->isUkuranTersedia('M'));
        $this->assertTrue($design->isUkuranTersedia('L'));
        $this->assertFalse($design->isUkuranTersedia('XL'));
        $this->assertTrue($design->isUkuranTersedia('XXL'));
    }

    public function test_admin_can_update_design_sizes_to_deactivate_out_of_stock(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $design = Design::first();
        $this->assertNotNull($design);

        // Update sizes: matikan S dan XXL (stok habis)
        $response = $this->actingAs($admin)
            ->post(route('admin.designs.update', $design->id), [
                'nama' => $design->nama,
                'harga' => $design->harga,
                'deskripsi' => $design->deskripsi,
                'aktif' => 1,
                'ukuran' => ['M', 'L', 'XL'],
            ]);

        $response->assertRedirect();

        $design->refresh();
        $this->assertSame(['M', 'L', 'XL'], $design->pilihanUkuran());
        $this->assertFalse($design->isUkuranTersedia('S'));
        $this->assertTrue($design->isUkuranTersedia('M'));
        $this->assertTrue($design->isUkuranTersedia('L'));
        $this->assertTrue($design->isUkuranTersedia('XL'));
        $this->assertFalse($design->isUkuranTersedia('XXL'));
    }

    public function test_admin_create_page_contains_interactive_size_toggles(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->get(route('admin.designs.create'));
        $response->assertStatus(200);
        $response->assertSee('Pilihan Ukuran Tersedia');
        $response->assertSee('size-toggle-card', false);
        $response->assertSee('preview-size-btn', false);
        $response->assertSee('name="ukuran[]"', false);
    }
}
