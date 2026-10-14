<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('nomor', 30);
            $table->string('email', 150);
            $table->string('alamat');
            $table->string('provinsi', 60);
            $table->string('ukuran', 5)->nullable();      // S, M, L, XL, XXL
            $table->unsignedInteger('jumlah')->default(0); // jumlah baju
            $table->foreignId('design_id')->nullable()->constrained('designs')->nullOnDelete();
            $table->unsignedBigInteger('donasi')->default(0); // rupiah
            $table->unsignedBigInteger('total')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
