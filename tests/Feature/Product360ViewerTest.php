<?php

namespace Tests\Feature;

use App\Models\Design;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Product360ViewerTest extends TestCase
{
    use RefreshDatabase;

    public function test_garment_360_video_asset_exists_and_is_valid_mp4(): void
    {
        $video = public_path('videos/Garment_rotating_360_degrees_1080p_20261012065448.mp4');

        $this->assertFileExists($video, 'Video 360 garment harus tersedia di public/videos.');
        $this->assertGreaterThan(50000, filesize($video), 'Video 360 garment tidak boleh kosong.');

        // Pastikan benar-benar file MP4 (kotak 'ftyp' di awal file)
        $handle = fopen($video, 'rb');
        fseek($handle, 4);
        $box = fread($handle, 4);
        fclose($handle);
        $this->assertSame('ftyp', $box, 'File harus berupa video MP4 yang valid.');
    }

    public function test_design_page_serves_360_catalog_and_viewer_config(): void
    {
        $this->seed();

        $response = $this->get('/design-kaos');
        $response->assertStatus(200);

        // Struktur 360 viewer untuk desain default (video 360)
        $response->assertSee('id="viewer-360"', false);
        $response->assertSee('id="viewer-360-video"', false);
        $response->assertSee('id="viewer-360-counter"', false);
        $response->assertSee('id="gal-panel-360"', false);
        $response->assertSee('id="gal-counter"', false);

        // Galeri foto/360° (kontrol putar dihapus, putaran via drag) & panduan ukuran
        $response->assertSee('data-gal="foto"', false);
        $response->assertSee('data-gal="360"', false);
        $response->assertSee('data-open-size-chart', false);
    }

    public function test_every_design_has_working_360_viewer_page(): void
    {
        $this->seed();

        $designs = Design::where('aktif', true)->get();
        $this->assertNotEmpty($designs, 'Harus ada desain aktif untuk diuji.');

        foreach ($designs as $design) {
            $response = $this->get('/design-kaos?desain=' . $design->id);
            $response->assertStatus(200);

            if ($design->video360Url()) {
                $response->assertSee('id="viewer-360"', false);
                $response->assertSee('id="viewer-360-video"', false);
            } else {
                $response->assertSee($design->gambarUrl(), false);
            }

            // Setiap desain menampilkan CTA pesan ke checkout kaos baru
            $response->assertSee('pesan-kaos?desain=' . $design->id, false);
        }
    }
}
