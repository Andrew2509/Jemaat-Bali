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
                if (! Schema::hasColumn('program_info', 'proposal_tipe')) {
                    $table->string('proposal_tipe', 20)->default('pdf')->after('judul_proposal');
                }
                if (! Schema::hasColumn('program_info', 'proposal_canva_url')) {
                    $table->text('proposal_canva_url')->nullable()->after('proposal_tipe');
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
                if (Schema::hasColumn('program_info', 'proposal_canva_url')) {
                    $table->dropColumn('proposal_canva_url');
                }
                if (Schema::hasColumn('program_info', 'proposal_tipe')) {
                    $table->dropColumn('proposal_tipe');
                }
            });
        }
    }
};
