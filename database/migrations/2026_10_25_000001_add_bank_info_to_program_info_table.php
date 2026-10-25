<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_info', function (Blueprint $table) {
            if (! Schema::hasColumn('program_info', 'bank_nama')) {
                $table->string('bank_nama', 60)->nullable()->after('qris_code');
                $table->string('bank_rek', 40)->nullable()->after('bank_nama');
                $table->string('bank_atas_nama', 120)->nullable()->after('bank_rek');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_info', function (Blueprint $table) {
            if (Schema::hasColumn('program_info', 'bank_rek')) {
                $table->dropColumn(['bank_nama', 'bank_rek', 'bank_atas_nama']);
            }
        });
    }
};
