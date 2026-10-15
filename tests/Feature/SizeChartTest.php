<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\ProgramInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SizeChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_size_chart_structure(): void
    {
        $defaultChart = ProgramInfo::defaultSizeChart();

        $this->assertIsArray($defaultChart);
        $this->assertArrayHasKey('columns', $defaultChart);
        $this->assertArrayHasKey('rows', $defaultChart);
        $this->assertArrayHasKey('title', $defaultChart);
        $this->assertContains('Ukuran', $defaultChart['columns']);
        $this->assertNotEmpty($defaultChart['rows']);

        $design = new Design(['nama' => 'Test Kaos']);
        $this->assertSame($defaultChart['columns'], $design->getSizeChart()['columns']);
    }

    public function test_admin_can_view_size_chart_page(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->get(route('admin.designs.size_chart'));

        $response->assertStatus(200);
        $response->assertSee('Tabel Panduan Ukuran Baju');
        $response->assertSee('Pembuat Tabel Ukuran');
        $response->assertSee('+ Tambah Kolom');
        $response->assertSee('+ Tambah Baris');
        $response->assertSee('LIVE PREVIEW');
    }

    public function test_admin_can_update_global_size_chart_with_dynamic_columns_and_rows(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $customColumns = ['Ukuran', 'Lebar Dada (cm)', 'Panjang Baju (cm)', 'Lingkar Dada (cm)', 'Bahu (cm)'];
        $customRows = [
            ['S', '47', '67', '94', '42'],
            ['M', '50', '70', '100', '44'],
            ['L', '53', '73', '106', '46'],
            ['XL', '56', '75', '112', '48'],
            ['XXL', '59', '77', '118', '50'],
            ['XXXL', '62', '80', '124', '52'],
        ];

        $response = $this->actingAs($admin)->post(route('admin.designs.size_chart.update'), [
            'title' => 'Panduan Ukuran Kaos Spesial Pembangunan (cm)',
            'note' => 'Toleransi ukuran ±1.5 cm · Katun Organik',
            'columns' => $customColumns,
            'rows' => $customRows,
        ]);

        $response->assertRedirect(route('admin.designs.size_chart'));
        $response->assertSessionHas('success');

        $programInfo = ProgramInfo::first();
        $this->assertNotNull($programInfo);
        $sizeChart = $programInfo->getSizeChart();

        $this->assertSame('Panduan Ukuran Kaos Spesial Pembangunan (cm)', $sizeChart['title']);
        $this->assertSame('Toleransi ukuran ±1.5 cm · Katun Organik', $sizeChart['note']);
        $this->assertSame($customColumns, $sizeChart['columns']);
        $this->assertSame($customRows, $sizeChart['rows']);

        // Check that Home page displays the updated custom table
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Panduan Ukuran Kaos Spesial Pembangunan (cm)');
        $homeResponse->assertSee('Toleransi ukuran ±1.5 cm · Katun Organik');
        $homeResponse->assertSee('Lingkar Dada (cm)');
        $homeResponse->assertSee('XXXL');
    }

    public function test_admin_can_update_design_specific_size_chart(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();
        $design = Design::first();
        $this->assertNotNull($design);

        $customColumns = ['Size', 'Chest', 'Length'];
        $customRows = [
            ['Regular S', '48', '68'],
            ['Regular M', '51', '71'],
        ];

        $response = $this->actingAs($admin)->post(route('admin.designs.size_chart.update'), [
            'design_id' => $design->id,
            'title' => 'Custom Sizing for ' . $design->nama,
            'note' => 'Oversized fit',
            'columns' => $customColumns,
            'rows' => $customRows,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $design->refresh();
        $this->assertSame('Custom Sizing for ' . $design->nama, $design->getSizeChart()['title']);
        $this->assertSame($customColumns, $design->getSizeChart()['columns']);
        $this->assertSame($customRows, $design->getSizeChart()['rows']);
    }

    public function test_public_pages_include_size_chart_modal(): void
    {
        $this->seed();

        $designPage = $this->get('/design-kaos');
        $designPage->assertStatus(200);
        $designPage->assertSee('id="size-chart-modal"', false);

        $checkoutPage = $this->get('/pesan-kaos');
        $checkoutPage->assertStatus(200);
        $checkoutPage->assertSee('id="size-chart-modal"', false);
        $checkoutPage->assertSee('Panduan Ukuran');
    }
}
