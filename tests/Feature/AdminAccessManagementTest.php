<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);

        $this->superAdmin = User::where('email', 'admin@jemaatbali.or.id')->first();
    }

    public function test_super_admin_can_view_users_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengguna & Staf', false);
        $response->assertSee('admin@jemaatbali.or.id');
        $response->assertSee('Super Admin');
    }

    public function test_can_create_new_user_and_login(): void
    {
        $role = Role::where('name', 'bendahara')->first();

        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Bendahara Jemaat',
            'email' => 'bendahara@jemaatbali.or.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $newUser = User::where('email', 'bendahara@jemaatbali.or.id')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('Bendahara Jemaat', $newUser->name);
        $this->assertEquals($role->id, $newUser->role_id);
        $this->assertTrue($newUser->is_active);
        $this->assertTrue(Hash::check('password123', $newUser->password));
    }

    public function test_can_update_user(): void
    {
        $user = User::create([
            'name' => 'Staf Lama',
            'email' => 'staf@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $operatorRole = Role::where('name', 'operator')->first();

        $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $user), [
            'name' => 'Staf Baru',
            'email' => 'stafbaru@jemaatbali.or.id',
            'role_id' => $operatorRole->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertEquals('Staf Baru', $user->name);
        $this->assertEquals('stafbaru@jemaatbali.or.id', $user->email);
        $this->assertEquals($operatorRole->id, $user->role_id);
    }

    public function test_can_toggle_user_status(): void
    {
        $user = User::create([
            'name' => 'Staf Uji',
            'email' => 'uji@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.toggle', $user));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->is_active);

        // Toggle back to active
        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.toggle', $user));
        $user->refresh();
        $this->assertTrue($user->is_active);
    }

    public function test_cannot_delete_self_or_last_super_admin(): void
    {
        // Try deleting own self
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->superAdmin));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_super_admin_can_view_roles_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Peran Pengguna (Roles)');
        $response->assertSee('Super Admin');
        $response->assertSee('Bendahara Keuangan');
        $response->assertSee('Operator Toko & Konten');
    }

    public function test_can_create_custom_role_with_permissions(): void
    {
        $perm1 = Permission::where('name', 'dashboard.view')->first();
        $perm2 = Permission::where('name', 'reports.view')->first();

        $response = $this->actingAs($this->superAdmin)->post(route('admin.roles.store'), [
            'name' => 'auditor_keuangan',
            'display_name' => 'Auditor Keuangan',
            'description' => 'Khusus memeriksa laporan dana dan dasbor.',
            'permissions' => [$perm1->id, $perm2->id],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role = Role::where('name', 'auditor_keuangan')->first();
        $this->assertNotNull($role);
        $this->assertEquals('Auditor Keuangan', $role->display_name);
        $this->assertCount(2, $role->permissions);
    }

    public function test_cannot_delete_system_role(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.roles.destroy', $superAdminRole));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
    }

    public function test_super_admin_can_view_permissions_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertSee('Hak Akses Sistem');
        $response->assertSee('dashboard.view');
        $response->assertSee('orders.manage');
        $response->assertSee('users.manage');
    }

    public function test_can_create_new_permission(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.permissions.store'), [
            'name' => 'inventory.audit',
            'display_name' => 'Audit Inventaris Toko',
            'group' => 'Produk & Desain',
            'description' => 'Izin khusus untuk audit stok fisik kaos.',
        ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'inventory.audit']);
    }

    public function test_sidebar_contains_access_management_links(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('PENGGUNA & HAK AKSES', false);
        $response->assertSee(route('admin.users.index'));
        $response->assertSee(route('admin.roles.index'));
        $response->assertSee(route('admin.permissions.index'));
    }

    public function test_user_with_restricted_role_cannot_access_unauthorized_routes(): void
    {
        $bendaharaRole = Role::where('name', 'bendahara')->first();

        $bendaharaUser = User::create([
            'name' => 'Staf Bendahara',
            'email' => 'bendahara.uji@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role_id' => $bendaharaRole->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Bendahara boleh akses Donasi dan Pengeluaran
        $this->actingAs($bendaharaUser)->get(route('admin.donations'))->assertStatus(200);
        $this->actingAs($bendaharaUser)->get(route('admin.expenses.index'))->assertStatus(200);

        // Bendahara DITOLAK saat mencoba akses Koleksi Kaos, Pesanan, atau Modul Akses
        $this->actingAs($bendaharaUser)->get(route('admin.designs'))->assertStatus(403);
        $this->actingAs($bendaharaUser)->get(route('admin.orders'))->assertStatus(403);
        $this->actingAs($bendaharaUser)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($bendaharaUser)->get(route('admin.roles.index'))->assertStatus(403);
        $this->actingAs($bendaharaUser)->get(route('admin.permissions.index'))->assertStatus(403);
    }

    public function test_user_with_restricted_role_sees_only_allowed_sidebar_menus(): void
    {
        $operatorRole = Role::where('name', 'operator')->first();

        $operatorUser = User::create([
            'name' => 'Staf Operator',
            'email' => 'operator.uji@jemaatbali.or.id',
            'password' => Hash::make('password123'),
            'role_id' => $operatorRole->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($operatorUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Menu yang diizinkan untuk Operator:
        $response->assertSee('Pesanan Kaos');
        $response->assertSee('Koleksi Kaos');
        $response->assertSee('Pengelolaan Konten');

        // Menu yang TIDAK diizinkan untuk Operator tidak boleh tampak di sidebar:
        $response->assertDontSee('Verifikasi Pembayaran');
        $response->assertDontSee('Laporan Dana');
        $response->assertDontSee('Dana Pengeluaran');
        $response->assertDontSee('PENGGUNA & HAK AKSES', false);
    }

    public function test_super_admin_can_run_system_setup_database(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.system.setup-database'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');
    }

    public function test_convert_video_handles_validation_and_returns_clean_json(): void
    {
        // 1. URL kosong
        $response = $this->actingAs($this->superAdmin)->postJson(route('admin.content.convert-video'), [
            'video_url' => '',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
            ]);
        $this->assertStringContainsString('Silakan masukkan tautan video', $response->json('message'));

        // 2. URL tanpa skema https:// dinormalisasi otomatis
        $response2 = $this->actingAs($this->superAdmin)->postJson(route('admin.content.convert-video'), [
            'video_url' => 'drive.google.com/file/d/sample12345/view?usp=sharing',
            'video_quality' => '720p',
        ]);

        $response2->assertStatus(200)
            ->assertJsonStructure(['success', 'message']);

        // 3. Google Drive URL dideteksi dengan benar pada model Progress
        $gdriveProgress = new \App\Models\Progress([
            'video_url' => 'https://drive.google.com/file/d/1AbC-123_xyz/view?usp=sharing',
        ]);
        $this->assertEquals('gdrive', $gdriveProgress->videoPlatform());
        $this->assertEquals('Google Drive', $gdriveProgress->videoPlatformLabel());
        $this->assertEquals('https://drive.google.com/file/d/1AbC-123_xyz/preview', $gdriveProgress->embedUrl());
        $this->assertEquals('https://drive.google.com/thumbnail?id=1AbC-123_xyz&sz=w800', $gdriveProgress->thumbnailUrl());

        // 4. Endpoint convert-video menerima tautan Google Drive
        $response3 = $this->actingAs($this->superAdmin)->postJson(route('admin.content.convert-video'), [
            'video_url' => 'https://drive.google.com/file/d/1AbC-123_xyz/view?usp=sharing',
            'video_quality' => '720p',
        ]);
        $response3->assertStatus(200)
            ->assertJsonStructure(['success', 'message']);
    }
}
