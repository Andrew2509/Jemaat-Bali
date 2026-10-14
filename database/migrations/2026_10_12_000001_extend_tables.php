<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('nomor_pesanan', 20)->nullable()->after('id');
            $table->enum('jenis', ['kaos_donasi', 'donasi_saja'])->default('kaos_donasi')->after('nomor_pesanan');
            $table->string('email')->nullable()->change();
            $table->unsignedBigInteger('subtotal_kaos')->default(0)->after('donasi');
            $table->unsignedBigInteger('ongkir')->default(0)->after('subtotal_kaos');
            $table->string('kabupaten', 80)->nullable()->after('provinsi');
            $table->string('kecamatan', 80)->nullable()->after('kabupaten');
            $table->string('kode_pos', 10)->nullable()->after('kecamatan');
            $table->string('metode', 30)->nullable()->after('kode_pos');
            $table->string('bukti_path')->nullable()->after('metode');
            $table->enum('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'terverifikasi', 'ditolak', 'bukti_ulang'])
                ->default('belum_bayar')->after('bukti_path');
            $table->enum('status_pengiriman', ['belum_diproduksi', 'produksi', 'siap_kirim', 'terkirim'])
                ->default('belum_diproduksi')->after('status_pembayaran');
            $table->string('resi', 60)->nullable()->after('status_pengiriman');
            $table->text('catatan_panitia')->nullable()->after('resi');
            $table->timestamp('verified_at')->nullable()->after('catatan_panitia');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
            $table->timestamp('dikirim_at')->nullable()->after('verified_by');
            $table->renameColumn('status', 'status_lama');
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 12);
            $table->string('nama', 120);
            $table->string('nomor', 30);
            $table->string('email', 150)->nullable();
            $table->unsignedBigInteger('nominal');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('metode', 30)->default('qris');
            $table->string('bukti_path')->nullable();
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'terverifikasi', 'ditolak', 'bukti_ulang'])
                ->default('menunggu_verifikasi');
            $table->text('catatan_panitia')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 60);
            $table->string('nama', 120);
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('program_info', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 120);
            $table->text('deskripsi');
            $table->string('whatsapp', 30);
            $table->string('email', 150);
            $table->string('judul_proposal', 120);
            $table->timestamps();
        });

        Schema::table('progresses', function (Blueprint $table) {
            $table->string('tahap', 60)->nullable()->after('judul');
            $table->enum('status', ['terbit', 'draf'])->default('terbit')->after('tahap');
            $table->string('teks_alt', 200)->nullable()->after('media_path');
            $table->timestamp('published_at')->nullable()->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('nama', 120)->nullable()->after('email');
            $table->enum('role', ['admin', 'super_admin'])->default('admin')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'role']);
        });
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn(['tahap', 'status', 'teks_alt', 'published_at']);
        });
        Schema::dropIfExists('program_info');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('donations');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pesanan', 'jenis', 'subtotal_kaos', 'ongkir', 'kabupaten', 'kecamatan',
                'kode_pos', 'metode', 'bukti_path', 'status_pembayaran', 'status_pengiriman',
                'resi', 'catatan_panitia', 'verified_at', 'verified_by', 'dikirim_at', 'status_lama',
            ]);
        });
    }
};
