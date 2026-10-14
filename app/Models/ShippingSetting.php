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
     * Pastikan tabel shipping_settings sudah ada di database server (auto-create)
     */
    public static function ensureTable(): bool
    {
        try {
            if (! Schema::hasTable('shipping_settings')) {
                Schema::create('shipping_settings', function ($table) {
                    $table->id();
                    $table->string('provider', 50)->default('rajaongkir');
                    $table->string('api_key', 500)->nullable();
                    $table->string('base_url', 255)->default('https://rajaongkir.komerce.id/api/v1');
                    $table->boolean('is_active')->default(true);
                    $table->string('status', 30)->default('unconfigured');
                    $table->unsignedInteger('provinces_count')->default(0);
                    $table->text('last_error')->nullable();
                    $table->timestamp('last_checked_at')->nullable();
                    $table->timestamps();
                });
            }
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ShippingSetting::ensureTable exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * File backup jika database server belum termigrasi atau terkunci
     */
    public static function backupFilePath(): string
    {
        return storage_path('app/shipping_setting.json');
    }

    public static function saveBackupSetting(array $data): void
    {
        try {
            $path = self::backupFilePath();
            $dir = dirname($path);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            // Ignore backup file error
        }
    }

    public static function loadBackupSetting(): array
    {
        try {
            $path = self::backupFilePath();
            if (file_exists($path)) {
                $content = @file_get_contents($path);
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            // Ignore
        }
        return [];
    }

    /**
     * Ambil record pengaturan pengiriman tunggal (singleton pattern)
     */
    public static function getSetting(): self
    {
        self::ensureTable();

        $backup = self::loadBackupSetting();

        try {
            if (Schema::hasTable('shipping_settings')) {
                $setting = self::first();
                if (! $setting) {
                    $defaultKey = $backup['api_key'] ?? config('services.rajaongkir.key');
                    $defaultBase = $backup['base_url'] ?? config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');

                    $setting = self::create([
                        'provider' => 'rajaongkir',
                        'api_key' => $defaultKey,
                        'base_url' => $defaultBase,
                        'is_active' => $backup['is_active'] ?? true,
                        'status' => !empty($defaultKey) ? 'connected' : 'unconfigured',
                    ]);
                } elseif (empty($setting->api_key) && !empty($backup['api_key'])) {
                    // Sinkronkan dari backup jika di database masih kosong
                    $setting->api_key = $backup['api_key'];
                    if (!empty($backup['base_url'])) {
                        $setting->base_url = $backup['base_url'];
                    }
                    $setting->save();
                }
                return $setting;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ShippingSetting::getSetting DB query failed: ' . $e->getMessage());
        }

        $fallback = new self();
        $fallback->provider = 'rajaongkir';
        $fallback->api_key = $backup['api_key'] ?? config('services.rajaongkir.key');
        $fallback->base_url = $backup['base_url'] ?? config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
        $fallback->is_active = $backup['is_active'] ?? true;
        $fallback->status = !empty($fallback->api_key) ? 'connected' : 'unconfigured';
        return $fallback;
    }

    /**
     * Simpan pengaturan dengan jaminan persistensi (ke DB + Backup File)
     */
    public function saveSettings(array $data): bool
    {
        self::ensureTable();

        $this->fill($data);

        // Selalu simpan ke backup JSON agar tidak pernah hilang
        self::saveBackupSetting([
            'api_key' => $this->api_key,
            'base_url' => $this->base_url,
            'is_active' => (bool) $this->is_active,
            'status' => $this->status,
        ]);

        try {
            if ($this->exists) {
                return $this->save();
            }

            if (Schema::hasTable('shipping_settings')) {
                $saved = self::updateOrCreate(
                    ['provider' => 'rajaongkir'],
                    $data
                );
                $this->id = $saved->id;
                $this->exists = true;
                return true;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ShippingSetting::saveSettings error: ' . $e->getMessage());
        }

        return true;
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
        if (!empty($this->api_key)) {
            return $this->api_key;
        }

        $backup = self::loadBackupSetting();
        if (!empty($backup['api_key'])) {
            return $backup['api_key'];
        }

        return config('services.rajaongkir.key');
    }

    public function getEffectiveBaseUrl(): string
    {
        if (!empty($this->base_url)) {
            return rtrim($this->base_url, '/');
        }

        $backup = self::loadBackupSetting();
        if (!empty($backup['base_url'])) {
            return rtrim($backup['base_url'], '/');
        }

        return 'https://rajaongkir.komerce.id/api/v1';
    }
}
