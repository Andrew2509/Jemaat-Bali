<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_admin_user_and_can_login(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('password123', $admin->password));
        $this->assertTrue($admin->isSuperAdmin());

        // Login attempt with seeded credentials
        $response = $this->post('/admin/login', [
            'email' => 'admin@jemaatbali.or.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_seeder_is_idempotent(): void
    {
        // Seeding multiple times should not throw unique constraint exceptions
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->assertEquals(1, User::where('email', 'admin@jemaatbali.or.id')->count());
    }

    public function test_invalid_password_cannot_login(): void
    {
        $this->seed(AdminSeeder::class);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@jemaatbali.or.id',
            'password' => 'salah-password',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_dashboard_displays_dynamic_activities(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@jemaatbali.or.id')->first();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Aktivitas terbaru');
        $response->assertSee('Hari ini');
        $response->assertViewHas('aktivitas', function ($aktivitas) {
            return $aktivitas instanceof \Illuminate\Support\Collection && $aktivitas->isNotEmpty();
        });
    }
}
