<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_info', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('nama_program');
            $table->string('tagline', 120)->nullable()->default('BANGUN BERSAMA')->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('program_info', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'tagline']);
        });
    }
};
