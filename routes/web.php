<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DesignController as AdminDesignController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Http\Controllers\BuktiController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/proposal', [ProposalController::class, 'index'])->name('proposal');
Route::post('/proposal/upload', [ProposalController::class, 'upload'])->name('proposal.upload');
Route::get('/proposal/unduh', [ProposalController::class, 'download'])->name('proposal.download');
Route::get('/progres', [ProgressController::class, 'index'])->name('progres');
Route::get('/design-kaos', [DesignController::class, 'index'])->name('design');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::post('/shop', [ShopController::class, 'store'])->name('shop.store');
Route::get('/shop/{order}/pemesanan', [ShopController::class, 'pemesanan'])->name('shop.pemesanan');
Route::get('/shop/{order}/pembayaran', [ShopController::class, 'payment'])->name('shop.payment');
Route::post('/shop/{order}/pembayaran', [ShopController::class, 'paymentStore'])->name('shop.payment.store');
Route::get('/shop/{order}/menunggu', [ShopController::class, 'waiting'])->name('shop.waiting');

// Link gambar/file bukti pembayaran (signed, tanpa perlu symlink public/storage)
Route::get('/bukti/{type}/{id}', [BuktiController::class, 'show'])->middleware('signed')->name('bukti.show');

// Alur pesan kaos (tanpa donasi): Pemesanan -> Pembayaran -> Menunggu konfirmasi
Route::get('/pesan-kaos', [CheckoutController::class, 'create'])->name('checkout.pemesanan');
Route::post('/pesan-kaos', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pesan-kaos/{order}/pembayaran', [CheckoutController::class, 'bayar'])->name('checkout.bayar');
Route::post('/pesan-kaos/{order}/pembayaran', [CheckoutController::class, 'bayarStore'])->name('checkout.bayar.store');
Route::get('/pesan-kaos/{order}/menunggu', [CheckoutController::class, 'menunggu'])->name('checkout.menunggu');

// Endpoint API RajaOngkir untuk Pencarian Wilayah & Kode Pos Otomatis
Route::prefix('api/rajaongkir')->name('api.rajaongkir.')->group(function () {
    Route::get('/provinces', [RajaOngkirController::class, 'provinces'])->name('provinces');
    Route::get('/cities', [RajaOngkirController::class, 'cities'])->name('cities');
    Route::get('/districts', [RajaOngkirController::class, 'districts'])->name('districts');
    Route::get('/subdistricts', [RajaOngkirController::class, 'subdistricts'])->name('subdistricts');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard
    Route::get('/', [OrderController::class, 'dashboard'])->middleware('permission:dashboard.view')->name('dashboard');

    // 2. Verifikasi Pembayaran
    Route::middleware('permission:verification.view')->group(function () {
        Route::get('/verifikasi', [AdminController::class, 'verification'])->name('verification');
        Route::get('/verifikasi/{id}/{tipe}', [AdminController::class, 'verificationShow'])->name('verification.show');
    });
    Route::middleware('permission:verification.manage')->group(function () {
        Route::post('/verifikasi/{id}/{tipe}/aksi', [AdminController::class, 'verificationAction'])->name('verification.action');
        Route::post('/verifikasi/{id}/{tipe}/kirim-wa', [AdminController::class, 'resendVerificationWa'])->name('verification.resend_wa');
    });

    // 3. Pesanan Kaos
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
    Route::middleware('permission:orders.manage')->group(function () {
        Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    });

    // 4. Koleksi Kaos
    Route::middleware('permission:designs.view')->get('/koleksi-kaos', [AdminDesignController::class, 'index'])->name('designs');
    Route::middleware('permission:designs.manage')->group(function () {
        Route::get('/koleksi-kaos/tambah', [AdminDesignController::class, 'create'])->name('designs.create');
        Route::post('/koleksi-kaos/tambah', [AdminDesignController::class, 'store'])->name('designs.store');
        Route::get('/koleksi-kaos/{design}/edit', [AdminDesignController::class, 'edit'])->name('designs.edit');
        Route::post('/koleksi-kaos/{design}/simpan', [AdminDesignController::class, 'update'])->name('designs.update');
        Route::post('/koleksi-kaos/{design}/toggle', [AdminDesignController::class, 'toggleStatus'])->name('designs.toggle');
        Route::post('/koleksi-kaos/{design}/hapus', [AdminDesignController::class, 'destroy'])->name('designs.destroy');
    });

    // 5. Donasi
    Route::middleware('permission:donations.view')->group(function () {
        Route::get('/donasi', [AdminController::class, 'donations'])->name('donations');
        Route::get('/donasi/{donation}', [AdminController::class, 'donationShow'])->name('donations.show');
    });
    Route::middleware('permission:donations.manage')->group(function () {
        Route::post('/donasi/{donation}/aksi', [AdminController::class, 'donationAction'])->name('donations.action');
    });

    // 6. Laporan Dana
    Route::middleware('permission:reports.view')->get('/laporan', [AdminController::class, 'reports'])->name('reports');
    Route::middleware('permission:reports.export')->get('/laporan/export/{format}', [AdminController::class, 'reportExport'])->name('reports.export');

    // 7. Pengeluaran
    Route::middleware('permission:expenses.view')->get('/pengeluaran', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::middleware('permission:expenses.manage')->group(function () {
        Route::post('/pengeluaran', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/pengeluaran/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/pengeluaran/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // 8. Konten
    Route::middleware('permission:content.view')->get('/konten', [AdminController::class, 'content'])->name('content');
    Route::middleware('permission:content.manage')->group(function () {
        Route::get('/konten/progres/baru', [AdminController::class, 'contentCreate'])->name('content.create');
        Route::get('/konten/progres/{id}/edit', [AdminController::class, 'contentEdit'])->name('content.edit');
        Route::post('/konten/progres/{id}/simpan', [AdminController::class, 'contentUpdate'])->name('content.update');
        Route::post('/konten/progres/tambah', [AdminController::class, 'contentStore'])->name('content.store');
        Route::post('/konten/progres/{id}/hapus', [AdminController::class, 'contentDestroy'])->name('content.destroy');
        Route::post('/konten/convert-video', [AdminController::class, 'convertVideo'])->name('content.convert-video');
        Route::post('/konten/proposal', [ProposalController::class, 'upload'])->name('content.proposal');
        Route::post('/konten/info', [AdminController::class, 'contentInfo'])->name('content.info');
    });

    // 9. Provider Integrasi (WhatsApp Fonnte & RajaOngkir API)
    Route::middleware('permission:provider.view')->get('/provider', [ProviderController::class, 'index'])->name('provider');
    Route::middleware('permission:provider.manage')->group(function () {
        Route::post('/provider', [ProviderController::class, 'update'])->name('provider.update');
        Route::post('/provider/check', [ProviderController::class, 'checkStatus'])->name('provider.check');
        Route::post('/provider/test', [ProviderController::class, 'testSend'])->name('provider.test');
        Route::post('/provider/rajaongkir', [ProviderController::class, 'updateRajaOngkir'])->name('provider.rajaongkir.update');
        Route::post('/provider/rajaongkir/test', [ProviderController::class, 'testRajaOngkir'])->name('provider.rajaongkir.test');
    });

    // 10. Manajemen Pengguna (Users)
    Route::middleware('permission:users.view')->get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:users.manage')->group(function () {
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // 11. Manajemen Peran (Roles)
    Route::middleware('permission:roles.view')->get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:roles.manage')->group(function () {
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // 12. Manajemen Hak Akses (Permissions)
    Route::middleware('permission:permissions.view')->get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::middleware('permission:permissions.manage')->group(function () {
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Setup / Inisialisasi Database Server (khusus Super Admin untuk deploy Hostinger tanpa SSH)
    Route::get('/system/setup-database', function () {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang berhak menjalankan sinkronisasi database.');
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder', '--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            return redirect()->route('admin.dashboard')->with('success', 'Database dan tabel hak akses berhasil diperbarui dan disinkronkan di server Hostinger!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Gagal sinkronisasi database: ' . $e->getMessage()]);
        }
    })->name('system.setup-database');
});
