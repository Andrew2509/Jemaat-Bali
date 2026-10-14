<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progresses', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->enum('tipe', ['foto', 'video'])->default('foto');
            $table->string('media_path')->nullable();   // untuk foto
            $table->string('video_url')->nullable();    // untuk video (embed)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progresses');
    }
};
