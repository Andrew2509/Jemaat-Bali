<?php

namespace Tests\Feature;

use App\Models\ProgramInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroVideoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_default_hero_video_configuration()
    {
        $info = ProgramInfo::create([
            'nama_program' => 'Pembangunan Jemaat Bali',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-0000-0000',
            'email' => 'admin@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
        ]);

        $this->assertEquals('default', $info->getHeroVideoType());
        $this->assertFalse($info->hasCustomHeroVideo());
        $this->assertStringContainsString('videos/hero-bg.mp4', $info->getActiveHeroVideoUrl());
        $this->assertStringContainsString('images/hero-poster.webp', $info->getHeroPosterUrl());

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('hero-bg-video');
        $response->assertSee('videos/hero-bg.mp4');
    }

    public function test_hero_video_di_dalam_section_yang_tidak_bisa_tertutup_navbar_maupun_konten_bawah()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // Hero memakai section in-flow (bukan fixed) dengan overflow-hidden,
        // sehingga tidak bisa tertutup navbar maupun konten di bawahnya.
        $response->assertSee('id="hero-section"', false);
        $response->assertSee('aspect-[854/480]', false);
        $response->assertSee('overflow-hidden', false);

        // Video mengikuti ukuran aslinya (object-contain) di mobile
        $response->assertSee('object-contain lg:object-cover', false);

        // Tidak ada lagi wrapper video fixed / spacer lama
        $response->assertDontSee('hero-video-wrapper', false);
        $response->assertDontSee('hero-spacer', false);
    }

    public function test_admin_content_page_renders_hero_video_management()
    {
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->get(route('admin.content'));
        $response->assertStatus(200);
        $response->assertSee('Video Pembuka Beranda');
        $response->assertSee('Upload Berkas');
        $response->assertSee('Link Google Drive');
        $response->assertSee('hero-video-section');
    }

    public function test_admin_can_set_google_drive_hero_video()
    {
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $gdriveUrl = 'https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/view?usp=sharing';

        $response = $this->actingAs($admin)->post(route('admin.content.hero_video'), [
            'hero_video_type' => 'gdrive',
            'video_url' => $gdriveUrl,
        ]);

        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $info = ProgramInfo::first();
        $this->assertEquals('gdrive', $info->getHeroVideoType());
        $this->assertEquals($gdriveUrl, $info->hero_video_url);
        $this->assertEquals('1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms', $info->getHeroGoogleDriveId());
        $this->assertEquals('https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/preview', $info->getHeroVideoEmbedUrl());

        // Check public home page renders iframe embed
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/preview');
        $homeResponse->assertSee('<iframe', false);
    }

    public function test_admin_can_upload_local_hero_video_file()
    {
        Storage::fake('public');
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $file = UploadedFile::fake()->create('custom-hero.mp4', 5000, 'video/mp4');

        $response = $this->actingAs($admin)->post(route('admin.content.hero_video'), [
            'hero_video_type' => 'file',
            'video_file' => $file,
        ]);

        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $info = ProgramInfo::first();
        $this->assertEquals('file', $info->getHeroVideoType());
        $this->assertNotNull($info->hero_video_path);
        Storage::disk('public')->assertExists($info->hero_video_path);

        // Check public home page renders the uploaded video source
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('storage/' . $info->hero_video_path);
        $homeResponse->assertSee('hero-bg-video');
    }

    public function test_admin_can_reset_hero_video_to_default()
    {
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $info = ProgramInfo::create([
            'nama_program' => 'Pembangunan Jemaat Bali',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-0000-0000',
            'email' => 'admin@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
            'hero_video_type' => 'gdrive',
            'hero_video_url' => 'https://drive.google.com/file/d/test1234/view',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.content.hero_video.reset'));
        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $fresh = ProgramInfo::first();
        $this->assertEquals('default', $fresh->getHeroVideoType());
        $this->assertFalse($fresh->hasCustomHeroVideo());
    }
}
