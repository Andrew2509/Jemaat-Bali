<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('program_info')) {
            Schema::table('program_info', function (Blueprint $table) {
                if (! Schema::hasColumn('program_info', 'qris_code')) {
                    $table->text('qris_code')->nullable()->after('qris_image');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('program_info')) {
            Schema::table('program_info', function (Blueprint $table) {
                if (Schema::hasColumn('program_info', 'qris_code')) {
                    $table->dropColumn('qris_code');
                }
            });
        }
    }
};
