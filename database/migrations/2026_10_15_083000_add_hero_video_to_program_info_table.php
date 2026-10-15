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
                if (! Schema::hasColumn('program_info', 'hero_video_type')) {
                    $table->string('hero_video_type', 20)->default('default')->after('proposal_canva_url');
                }
                if (! Schema::hasColumn('program_info', 'hero_video_path')) {
                    $table->string('hero_video_path')->nullable()->after('hero_video_type');
                }
                if (! Schema::hasColumn('program_info', 'hero_video_url')) {
                    $table->text('hero_video_url')->nullable()->after('hero_video_path');
                }
                if (! Schema::hasColumn('program_info', 'hero_video_poster')) {
                    $table->string('hero_video_poster')->nullable()->after('hero_video_url');
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
                $columns = ['hero_video_poster', 'hero_video_url', 'hero_video_path', 'hero_video_type'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('program_info', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
