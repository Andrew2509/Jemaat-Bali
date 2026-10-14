<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shipping_settings')) {
            Schema::create('shipping_settings', function (Blueprint $table) {
                $table->id();
                $table->string('provider', 40)->default('rajaongkir');
                $table->string('api_key', 255)->nullable();
                $table->string('base_url', 255)->default('https://rajaongkir.komerce.id/api/v1');
                $table->boolean('is_active')->default(true);
                $table->string('status', 30)->default('unconfigured'); // unconfigured, connected, error
                $table->unsignedInteger('provinces_count')->default(0);
                $table->text('last_error')->nullable();
                $table->timestamp('last_checked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
