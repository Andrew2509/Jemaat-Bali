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
                if (! Schema::hasColumn('program_info', 'size_chart')) {
                    $table->json('size_chart')->nullable()->after('proposal_canva_url');
                }
            });
        }

        if (Schema::hasTable('designs')) {
            Schema::table('designs', function (Blueprint $table) {
                if (! Schema::hasColumn('designs', 'size_chart')) {
                    $table->json('size_chart')->nullable()->after('ukuran');
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
                if (Schema::hasColumn('program_info', 'size_chart')) {
                    $table->dropColumn('size_chart');
                }
            });
        }

        if (Schema::hasTable('designs')) {
            Schema::table('designs', function (Blueprint $table) {
                if (Schema::hasColumn('designs', 'size_chart')) {
                    $table->dropColumn('size_chart');
                }
            });
        }
    }
};
