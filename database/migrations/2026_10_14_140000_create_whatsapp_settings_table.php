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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('fonnte');
            $table->text('api_token')->nullable();
            $table->string('device_phone', 30)->nullable();
            $table->string('device_name', 100)->nullable();
            $table->string('device_status', 30)->nullable(); // 'connect', 'disconnect', etc.
            $table->string('quota', 50)->nullable();
            $table->string('package', 50)->nullable();
            $table->string('expired', 100)->nullable();
            $table->string('country_code', 10)->default('62');
            $table->boolean('is_active')->default(false);
            $table->boolean('auto_notify_order')->default(true);
            $table->boolean('auto_notify_verified')->default(true);
            $table->text('template_order')->nullable();
            $table->text('template_verified')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
