<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->enum('kategori', ['pembangunan', 'kegiatan'])->default('pembangunan')->after('tahap');
        });
    }

    public function down(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
