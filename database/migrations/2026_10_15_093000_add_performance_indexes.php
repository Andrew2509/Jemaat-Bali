<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('nomor_pesanan');
            $table->index(['status_pembayaran', 'created_at']);
            $table->index('status_pengiriman');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->index('kode');
            $table->index(['status', 'tanggal']);
        });

        Schema::table('progresses', function (Blueprint $table) {
            $table->index(['status', 'tanggal']);
        });

        Schema::table('designs', function (Blueprint $table) {
            $table->index(['aktif', 'harga']);
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropIndex(['aktif', 'harga']);
        });

        Schema::table('progresses', function (Blueprint $table) {
            $table->dropIndex(['status', 'tanggal']);
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['kode']);
            $table->dropIndex(['status', 'tanggal']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['nomor_pesanan']);
            $table->dropIndex(['status_pembayaran', 'created_at']);
            $table->dropIndex(['status_pengiriman']);
        });
    }
};
