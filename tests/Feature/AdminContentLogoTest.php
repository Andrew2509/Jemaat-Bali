<?php

namespace Tests\Feature;

use App\Models\ProgramInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentLogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_content_page_renders_logo_upload_and_tagline_field(): void
    {
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->get(route('admin.content'));

        $response->assertStatus(200);
        $response->assertSee('Logo program');
        $response->assertSee('Slogan / Subjudul');
        $response->assertSee('BANGUN BERSAMA');
    }

    public function test_admin_can_update_tagline(): void
    {
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->post(route('admin.content.info'), [
            'nama_program' => 'Pembangunan Jemaat Bali',
            'tagline' => 'BERSATU MEMBANGUN',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-3456-7890',
            'email' => 'panitia@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
        ]);

        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $info = ProgramInfo::first();
        $this->assertEquals('BERSATU MEMBANGUN', $info->tagline);

        // Sidebar admin renders new tagline
        $adminPage = $this->actingAs($admin)->get(route('admin.content'));
        $adminPage->assertSee('BERSATU MEMBANGUN');

        // Public page renders new tagline
        $homePage = $this->get(route('home'));
        $homePage->assertSee('BERSATU MEMBANGUN');
    }

    public function test_admin_can_upload_and_delete_logo(): void
    {
        Storage::fake('public');
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $fakeLogo = UploadedFile::fake()->image('logo.png', 200, 200);

        $response = $this->actingAs($admin)->post(route('admin.content.info'), [
            'nama_program' => 'Pembangunan Jemaat Bali',
            'tagline' => 'BANGUN BERSAMA',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-3456-7890',
            'email' => 'panitia@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
            'logo_file' => $fakeLogo,
        ]);

        $response->assertRedirect(route('admin.content'));
        $response->assertSessionHas('success');

        $info = ProgramInfo::first();
        $this->assertNotNull($info->logo_path);
        $this->assertTrue(Storage::disk('public')->exists($info->logo_path));

        // Check sidebar renders logo image
        $adminPage = $this->actingAs($admin)->get(route('admin.content'));
        $adminPage->assertSee($info->logo_url);

        // Now test delete logo
        $responseDelete = $this->actingAs($admin)->post(route('admin.content.info'), [
            'nama_program' => 'Pembangunan Jemaat Bali',
            'tagline' => 'BANGUN BERSAMA',
            'deskripsi' => 'Penggalangan dana pembangunan bersama.',
            'whatsapp' => '0812-3456-7890',
            'email' => 'panitia@jemaatbali.or.id',
            'judul_proposal' => 'Proposal pembangunan',
            'hapus_logo' => '1',
        ]);

        $responseDelete->assertRedirect(route('admin.content'));
        $infoFresh = ProgramInfo::first();
        $this->assertNull($infoFresh->logo_path);
    }
}
