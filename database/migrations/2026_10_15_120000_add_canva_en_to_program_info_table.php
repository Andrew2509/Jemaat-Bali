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
                if (! Schema::hasColumn('program_info', 'proposal_canva_url_en')) {
                    $table->text('proposal_canva_url_en')->nullable()->after('proposal_canva_url');
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
                if (Schema::hasColumn('program_info', 'proposal_canva_url_en')) {
                    $table->dropColumn('proposal_canva_url_en');
                }
            });
        }
    }
};
