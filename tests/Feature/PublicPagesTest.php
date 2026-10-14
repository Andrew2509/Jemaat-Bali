<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_with_seeded_content(): void
    {
        $this->seed();

        $this->get('/')->assertStatus(200)->assertSee('Jemaat Bali');
    }

    public function test_all_public_pages_load(): void
    {
        $this->seed();

        foreach (['/', '/proposal', '/progres', '/design-kaos', '/shop', '/pesan-kaos'] as $uri) {
            $this->get($uri)->assertStatus(200);
        }
    }

    public function test_admin_pages_require_login(): void
    {
        $this->seed();

        $this->get('/admin/orders')
            ->assertRedirect(route('login'));
    }

    public function test_progress_page_lists_fotos_and_videos(): void
    {
        $this->seed();

        $this->get('/progres')
            ->assertStatus(200)
            ->assertSee('Foto Lapangan')
            ->assertSee('Video Lapangan')
            ->assertSee('Pengecoran lantai dasar');
    }

    public function test_proposal_file_is_downloadable(): void
    {
        $this->seed();

        $this->get('/proposal/unduh')->assertStatus(200);
    }

    public function test_proposal_page_displays_embedded_proposal_viewer(): void
    {
        $this->seed();

        $this->get('/proposal')
            ->assertStatus(200)
            ->assertSee('Proposal', false)
            // Reader buku digital (partial proposal-reader)
            ->assertSee('id="book-stage"', false)
            ->assertSee('id="canvas-page-left"', false)
            ->assertSee('id="btn-prev-page"', false);
    }

    public function test_proposal_upload_accepts_valid_pdf_and_redirects(): void
    {
        $this->seed();

        $path = public_path(\App\Http\Controllers\ProposalController::FILE);
        $original = file_exists($path) ? file_get_contents($path) : null;

        try {
            $file = \Illuminate\Http\UploadedFile::fake()->create('proposal-baru.pdf', 50, 'application/pdf');

            $response = $this->post('/proposal/upload', [
                'proposal' => $file,
            ]);

            $response->assertRedirect(route('admin.content'));
            $response->assertSessionHas('success');
        } finally {
            if ($original !== null) {
                file_put_contents($path, $original);
            } elseif (file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function test_proposal_upload_rejects_non_pdf_file(): void
    {
        $this->seed();

        $file = \Illuminate\Http\UploadedFile::fake()->create('dokumen.txt', 50, 'text/plain');

        $response = $this->post('/proposal/upload', [
            'proposal' => $file,
        ]);

        $response->assertSessionHasErrors('proposal');
    }

    public function test_design_page_shows_seeded_designs(): void
    {
        $this->seed();

        $this->get('/design-kaos')
            ->assertStatus(200)
            ->assertSee('Bangun Bersama')
            ->assertSee('Anda Mungkin Suka')
            ->assertSee('150.000');
    }
}
