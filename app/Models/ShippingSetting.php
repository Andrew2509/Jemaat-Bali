<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ShippingSetting extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
        'base_url',
        'is_active',
        'status',
        'provinces_count',
        'last_error',
        'last_checked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'provinces_count' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Ambil record pengaturan pengiriman tunggal (singleton pattern)
     */
    public static function getSetting(): self
    {
        try {
            if (Schema::hasTable('shipping_settings')) {
                $setting = self::first();
                if (!$setting) {
                    $setting = self::create([
                        'provider' => 'rajaongkir',
                        'api_key' => config('services.rajaongkir.key'),
                        'base_url' => config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1'),
                        'is_active' => true,
                        'status' => !empty(config('services.rajaongkir.key')) ? 'connected' : 'unconfigured',
                    ]);
                }
                return $setting;
            }
        } catch (\Throwable $e) {
            // Fallback object jika tabel belum termigrasi
        }

        $fallback = new self();
        $fallback->provider = 'rajaongkir';
        $fallback->api_key = config('services.rajaongkir.key');
        $fallback->base_url = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
        $fallback->is_active = true;
        $fallback->status = !empty(config('services.rajaongkir.key')) ? 'connected' : 'unconfigured';
        return $fallback;
    }

    public function isConfigured(): bool
    {
        return !empty($this->getEffectiveApiKey());
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected' && $this->isConfigured();
    }

    public function getEffectiveApiKey(): ?string
    {
        return !empty($this->api_key) ? $this->api_key : config('services.rajaongkir.key');
    }

    public function getEffectiveBaseUrl(): string
    {
        return !empty($this->base_url) ? rtrim($this->base_url, '/') : 'https://rajaongkir.komerce.id/api/v1';
    }
}
