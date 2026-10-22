<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemOptimizeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $operatorRole = Role::where('name', 'operator')->first();

        $this->admin = User::create([
            'name' => 'Staf Operator',
            'email' => 'operator.optimasi@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role_id' => $operatorRole->id,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_dapat_membuka_halaman_optimasi_cache(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.system.optimize'));

        $response->assertStatus(200);
        $response->assertSee('Optimasi Cache');
        $response->assertSee('Configuration Cache');
        $response->assertSee('Route Cache');
        $response->assertSee('View Cache');
        $response->assertSee('Environment Server');
    }

    public function test_admin_biasa_tidak_bisa_membuka_halaman_optimasi_cache(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.system.optimize'));

        $response->assertStatus(403);
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get(route('admin.system.optimize'));

        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_dapat_menjalankan_optimize(): void
    {
        // Perintah artisan dimock supaya test tidak menulis cache sungguhan ke bootstrap/cache.
        Artisan::shouldReceive('call')->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('Cache built successfully.');

        $response = $this->actingAs($this->superAdmin)->post(route('admin.system.optimize.run'));

        $response->assertRedirect(route('admin.system.optimize'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('system_steps');

        $steps = session('system_steps');
        $this->assertCount(4, $steps);
        $this->assertSame('config:cache', $steps[0]['command']);
        $this->assertSame('route:cache', $steps[2]['command']);
        $this->assertTrue($steps[0]['success']);
    }

    public function test_kegagalan_salah_satu_langkah_optimize_ditampilkan(): void
    {
        Artisan::shouldReceive('call')->andReturn(1);
        Artisan::shouldReceive('output')->andReturn('Failed to clear config cache.');

        $response = $this->actingAs($this->superAdmin)->post(route('admin.system.optimize.run'));

        $response->assertRedirect(route('admin.system.optimize'));
        $response->assertSessionHas('error');
        $response->assertSessionHas('system_steps');
    }

    public function test_super_admin_dapat_membersihkan_cache(): void
    {
        Artisan::shouldReceive('call')->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('Cleared cached bootstrap files.');

        $response = $this->actingAs($this->superAdmin)->post(route('admin.system.clear-cache.run'));

        $response->assertRedirect(route('admin.system.optimize'));
        $response->assertSessionHas('success');

        $steps = session('system_steps');
        $this->assertSame('optimize:clear', $steps[0]['command']);
    }

    public function test_admin_biasa_tidak_bisa_menjalankan_optimize(): void
    {
        Artisan::shouldReceive('call')->never();

        $response = $this->actingAs($this->admin)->post(route('admin.system.optimize.run'));

        $response->assertStatus(403);
    }

    public function test_menu_optimasi_cache_hanya_untuk_super_admin(): void
    {
        $superAdminPage = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $superAdminPage->assertSee('Optimasi Cache');

        $adminPage = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminPage->assertDontSee('Optimasi Cache');
    }
}
