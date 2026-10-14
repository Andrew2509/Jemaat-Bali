<?php

namespace App\Services;

use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected ShippingSetting $setting;

    public function __construct()
    {
        $this->setting = ShippingSetting::getSetting();
    }

    public function getSetting(): ShippingSetting
    {
        return $this->setting;
    }

    public function getApiKey(): ?string
    {
        return $this->setting->getEffectiveApiKey();
    }

    public function getBaseUrl(): string
    {
        return $this->setting->getEffectiveBaseUrl();
    }

    public function isConfigured(): bool
    {
        return !empty($this->getApiKey());
    }

    /**
     * Ambil daftar semua Provinsi dari RajaOngkir (atau fallback jika belum terhubung)
     */
    public function getProvinces(): array
    {
        if (!$this->isConfigured()) {
            return $this->fallbackProvinces();
        }

        return Cache::remember('rajaongkir_provinces', 86400, function () {
            try {
                $response = Http::withHeaders([
                    'Key' => $this->getApiKey(),
                    'key' => $this->getApiKey(),
                ])->timeout(8)->get($this->getBaseUrl() . '/destination/province');

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        return array_map(function ($item) {
                            return [
                                'id' => (int) ($item['id'] ?? 0),
                                'name' => strtoupper(trim($item['name'] ?? '')),
                            ];
                        }, $json['data']);
                    }
                }

                Log::warning('RajaOngkir getProvinces error: ' . $response->body());
            } catch (\Throwable $e) {
                Log::warning('RajaOngkir getProvinces exception: ' . $e->getMessage());
            }

            return $this->fallbackProvinces();
        });
    }

    /**
     * Ambil daftar Kota / Kabupaten berdasarkan province_id
     */
    public function getCities(int|string $provinceId): array
    {
        $provinceId = (int) $provinceId;
        if ($provinceId <= 0) {
            return [];
        }

        if (!$this->isConfigured()) {
            return $this->fallbackCities($provinceId);
        }

        return Cache::remember("rajaongkir_cities_{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::withHeaders([
                    'Key' => $this->getApiKey(),
                    'key' => $this->getApiKey(),
                ])->timeout(8)->get($this->getBaseUrl() . "/destination/city/{$provinceId}");

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        return array_map(function ($item) {
                            return [
                                'id' => (int) ($item['id'] ?? 0),
                                'name' => strtoupper(trim($item['name'] ?? '')),
                            ];
                        }, $json['data']);
                    }
                }

                Log::warning("RajaOngkir getCities({$provinceId}) error: " . $response->body());
            } catch (\Throwable $e) {
                Log::warning("RajaOngkir getCities({$provinceId}) exception: " . $e->getMessage());
            }

            return $this->fallbackCities($provinceId);
        });
    }

    /**
     * Ambil daftar Kecamatan (District) berdasarkan city_id
     * Setiap item menyertakan 'zip_code' yang dapat digunakan untuk mengisi kode pos otomatis
     */
    public function getDistricts(int|string $cityId): array
    {
        $cityId = (int) $cityId;
        if ($cityId <= 0) {
            return [];
        }

        if (!$this->isConfigured()) {
            return $this->fallbackDistricts($cityId);
        }

        return Cache::remember("rajaongkir_districts_{$cityId}", 86400, function () use ($cityId) {
            try {
                $response = Http::withHeaders([
                    'Key' => $this->getApiKey(),
                    'key' => $this->getApiKey(),
                ])->timeout(8)->get($this->getBaseUrl() . "/destination/district/{$cityId}");

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        return array_map(function ($item) {
                            $zip = trim((string) ($item['zip_code'] ?? ''));
                            if ($zip === '0') {
                                $zip = '';
                            }
                            return [
                                'id' => (int) ($item['id'] ?? 0),
                                'name' => strtoupper(trim($item['name'] ?? '')),
                                'zip_code' => $zip,
                            ];
                        }, $json['data']);
                    }
                }

                Log::warning("RajaOngkir getDistricts({$cityId}) error: " . $response->body());
            } catch (\Throwable $e) {
                Log::warning("RajaOngkir getDistricts({$cityId}) exception: " . $e->getMessage());
            }

            return $this->fallbackDistricts($cityId);
        });
    }

    /**
     * Ambil daftar Kelurahan / Desa (Subdistrict) berdasarkan district_id
     */
    public function getSubDistricts(int|string $districtId): array
    {
        $districtId = (int) $districtId;
        if ($districtId <= 0) {
            return [];
        }

        if (!$this->isConfigured()) {
            return $this->fallbackSubDistricts($districtId);
        }

        return Cache::remember("rajaongkir_subdistricts_{$districtId}", 86400, function () use ($districtId) {
            try {
                $response = Http::withHeaders([
                    'Key' => $this->getApiKey(),
                    'key' => $this->getApiKey(),
                ])->timeout(8)->get($this->getBaseUrl() . "/destination/sub-district/{$districtId}");

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        return array_map(function ($item) {
                            $zip = trim((string) ($item['zip_code'] ?? ''));
                            if ($zip === '0') {
                                $zip = '';
                            }
                            return [
                                'id' => (int) ($item['id'] ?? 0),
                                'name' => strtoupper(trim($item['name'] ?? '')),
                                'zip_code' => $zip,
                            ];
                        }, $json['data']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("RajaOngkir getSubDistricts({$districtId}) exception: " . $e->getMessage());
            }

            return $this->fallbackSubDistricts($districtId);
        });
    }

    /**
     * Tes koneksi ke API RajaOngkir dengan API Key yang diberikan
     */
    public function testConnection(?string $apiKey = null, ?string $baseUrl = null): array
    {
        $key = $apiKey ?: $this->getApiKey();
        $base = $baseUrl ?: $this->getBaseUrl();

        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'API Key RajaOngkir belum diisi.',
                'count' => 0,
                'code' => 400,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Key' => $key,
                'key' => $key,
            ])->timeout(10)->get(rtrim($base, '/') . '/destination/province');

            $code = $response->status();
            if ($response->successful()) {
                $json = $response->json();
                $count = isset($json['data']) && is_array($json['data']) ? count($json['data']) : 0;

                // Perbarui status setting jika tersimpan
                if ($this->setting->exists) {
                    $this->setting->update([
                        'status' => 'connected',
                        'provinces_count' => $count,
                        'last_checked_at' => now(),
                        'last_error' => null,
                    ]);
                }

                // Simpan cache provinsi jika berhasil
                if ($count > 0) {
                    Cache::put('rajaongkir_provinces', array_map(function ($item) {
                        return [
                            'id' => (int) ($item['id'] ?? 0),
                            'name' => strtoupper(trim($item['name'] ?? '')),
                        ];
                    }, $json['data']), 86400);
                }

                return [
                    'success' => true,
                    'message' => "Koneksi berhasil! Ditemukan {$count} provinsi aktif dari RajaOngkir.",
                    'count' => $count,
                    'code' => $code,
                ];
            }

            $errMsg = 'Gagal menghubungkan ke RajaOngkir (HTTP ' . $code . ')';
            $json = $response->json();
            if (isset($json['meta']['message'])) {
                $errMsg .= ': ' . $json['meta']['message'];
            } elseif (isset($json['message'])) {
                $errMsg .= ': ' . $json['message'];
            }

            if ($this->setting->exists) {
                $this->setting->update([
                    'status' => 'error',
                    'last_checked_at' => now(),
                    'last_error' => $errMsg,
                ]);
            }

            return [
                'success' => false,
                'message' => $errMsg,
                'count' => 0,
                'code' => $code,
            ];
        } catch (\Throwable $e) {
            $errMsg = 'Koneksi gagal: ' . $e->getMessage();
            if ($this->setting->exists) {
                $this->setting->update([
                    'status' => 'error',
                    'last_checked_at' => now(),
                    'last_error' => $errMsg,
                ]);
            }

            return [
                'success' => false,
                'message' => $errMsg,
                'count' => 0,
                'code' => 500,
            ];
        }
    }

    /**
     * Fallback 38 Provinsi di Indonesia
     */
    protected function fallbackProvinces(): array
    {
        return [
            ['id' => 1, 'name' => 'BALI'],
            ['id' => 2, 'name' => 'DKI JAKARTA'],
            ['id' => 3, 'name' => 'JAWA BARAT'],
            ['id' => 4, 'name' => 'JAWA TENGAH'],
            ['id' => 5, 'name' => 'JAWA TIMUR'],
            ['id' => 6, 'name' => 'DI YOGYAKARTA'],
            ['id' => 7, 'name' => 'BANTEN'],
            ['id' => 8, 'name' => 'SUMATERA UTARA'],
            ['id' => 9, 'name' => 'SUMATERA BARAT'],
            ['id' => 10, 'name' => 'RIAU'],
            ['id' => 11, 'name' => 'JAMBI'],
            ['id' => 12, 'name' => 'SUMATERA SELATAN'],
            ['id' => 13, 'name' => 'BENGKULU'],
            ['id' => 14, 'name' => 'LAMPUNG'],
            ['id' => 15, 'name' => 'KEPULAUAN BANGKA BELITUNG'],
            ['id' => 16, 'name' => 'KEPULAUAN RIAU'],
            ['id' => 17, 'name' => 'ACEH'],
            ['id' => 18, 'name' => 'NUSA TENGGARA BARAT (NTB)'],
            ['id' => 19, 'name' => 'NUSA TENGGARA TIMUR (NTT)'],
            ['id' => 20, 'name' => 'KALIMANTAN BARAT'],
            ['id' => 21, 'name' => 'KALIMANTAN TENGAH'],
            ['id' => 22, 'name' => 'KALIMANTAN SELATAN'],
            ['id' => 23, 'name' => 'KALIMANTAN TIMUR'],
            ['id' => 24, 'name' => 'KALIMANTAN UTARA'],
            ['id' => 25, 'name' => 'SULAWESI UTARA'],
            ['id' => 26, 'name' => 'SULAWESI TENGAH'],
            ['id' => 27, 'name' => 'SULAWESI SELATAN'],
            ['id' => 28, 'name' => 'SULAWESI TENGGARA'],
            ['id' => 29, 'name' => 'GORONTALO'],
            ['id' => 30, 'name' => 'SULAWESI BARAT'],
            ['id' => 31, 'name' => 'MALUKU'],
            ['id' => 32, 'name' => 'MALUKU UTARA'],
            ['id' => 33, 'name' => 'PAPUA'],
            ['id' => 34, 'name' => 'PAPUA BARAT'],
            ['id' => 35, 'name' => 'PAPUA SELATAN'],
            ['id' => 36, 'name' => 'PAPUA TENGAH'],
            ['id' => 37, 'name' => 'PAPUA PEGUNUNGAN'],
            ['id' => 38, 'name' => 'PAPUA BARAT DAYA'],
        ];
    }

    /**
     * Fallback data Kota/Kabupaten untuk provinsi terpilih
     */
    protected function fallbackCities(int $provinceId): array
    {
        // Bali
        if ($provinceId === 1) {
            return [
                ['id' => 101, 'name' => 'KABUPATEN BADUNG'],
                ['id' => 102, 'name' => 'KOTA DENPASAR'],
                ['id' => 103, 'name' => 'KABUPATEN GIANYAR'],
                ['id' => 104, 'name' => 'KABUPATEN TABANAN'],
                ['id' => 105, 'name' => 'KABUPATEN BULELENG'],
                ['id' => 106, 'name' => 'KABUPATEN BANGLI'],
                ['id' => 107, 'name' => 'KABUPATEN JEMBRANA'],
                ['id' => 108, 'name' => 'KABUPATEN KARANGASEM'],
                ['id' => 109, 'name' => 'KABUPATEN KLUNGKUNG'],
            ];
        }

        // DKI Jakarta
        if ($provinceId === 2) {
            return [
                ['id' => 201, 'name' => 'KOTA JAKARTA PUSAT'],
                ['id' => 202, 'name' => 'KOTA JAKARTA SELATAN'],
                ['id' => 203, 'name' => 'KOTA JAKARTA BARAT'],
                ['id' => 204, 'name' => 'KOTA JAKARTA TIMUR'],
                ['id' => 205, 'name' => 'KOTA JAKARTA UTARA'],
                ['id' => 206, 'name' => 'KABUPATEN KEPULAUAN SERIBU'],
            ];
        }

        // Jawa Barat
        if ($provinceId === 3) {
            return [
                ['id' => 301, 'name' => 'KOTA BANDUNG'],
                ['id' => 302, 'name' => 'KABUPATEN BANDUNG'],
                ['id' => 303, 'name' => 'KOTA BEKASI'],
                ['id' => 304, 'name' => 'KABUPATEN BEKASI'],
                ['id' => 305, 'name' => 'KOTA BOGOR'],
                ['id' => 306, 'name' => 'KABUPATEN BOGOR'],
                ['id' => 307, 'name' => 'KOTA DEPOK'],
                ['id' => 308, 'name' => 'KABUPATEN CIANJUR'],
            ];
        }

        // Jawa Timur
        if ($provinceId === 5) {
            return [
                ['id' => 501, 'name' => 'KOTA SURABAYA'],
                ['id' => 502, 'name' => 'KABUPATEN SIDOARJO'],
                ['id' => 503, 'name' => 'KOTA MALANG'],
                ['id' => 504, 'name' => 'KABUPATEN GRESIK'],
            ];
        }

        // Default generic cities based on province ID
        return [
            ['id' => ($provinceId * 100) + 1, 'name' => 'KOTA UTAMA'],
            ['id' => ($provinceId * 100) + 2, 'name' => 'KABUPATEN WILAYAH 1'],
            ['id' => ($provinceId * 100) + 3, 'name' => 'KABUPATEN WILAYAH 2'],
        ];
    }

    /**
     * Fallback data Kecamatan dengan Kode Pos
     */
    protected function fallbackDistricts(int $cityId): array
    {
        // Badung (Bali)
        if ($cityId === 101) {
            return [
                ['id' => 1001, 'name' => 'KUTA', 'zip_code' => '80361'],
                ['id' => 1002, 'name' => 'KUTA SELATAN', 'zip_code' => '80361'],
                ['id' => 1003, 'name' => 'KUTA UTARA', 'zip_code' => '80361'],
                ['id' => 1004, 'name' => 'MENGWI', 'zip_code' => '80351'],
                ['id' => 1005, 'name' => 'ABIANSEMAL', 'zip_code' => '80352'],
                ['id' => 1006, 'name' => 'PETANG', 'zip_code' => '80353'],
            ];
        }

        // Denpasar (Bali)
        if ($cityId === 102) {
            return [
                ['id' => 1011, 'name' => 'DENPASAR BARAT', 'zip_code' => '80119'],
                ['id' => 1012, 'name' => 'DENPASAR SELATAN', 'zip_code' => '80222'],
                ['id' => 1013, 'name' => 'DENPASAR TIMUR', 'zip_code' => '80235'],
                ['id' => 1014, 'name' => 'DENPASAR UTARA', 'zip_code' => '80115'],
            ];
        }

        // Gianyar (Bali)
        if ($cityId === 103) {
            return [
                ['id' => 1021, 'name' => 'UBUD', 'zip_code' => '80571'],
                ['id' => 1022, 'name' => 'GIANYAR', 'zip_code' => '80511'],
                ['id' => 1023, 'name' => 'SUKAWATI', 'zip_code' => '80582'],
                ['id' => 1024, 'name' => 'BLAHBATUH', 'zip_code' => '80581'],
            ];
        }

        // Jakarta Selatan
        if ($cityId === 202) {
            return [
                ['id' => 2001, 'name' => 'JAGAKARSA', 'zip_code' => '12630'],
                ['id' => 2002, 'name' => 'KEBAYORAN BARU', 'zip_code' => '12150'],
                ['id' => 2003, 'name' => 'KEBAYORAN LAMA', 'zip_code' => '12230'],
                ['id' => 2004, 'name' => 'MAMPANG PRAPATAN', 'zip_code' => '12790'],
                ['id' => 2005, 'name' => 'PASAR MINGGU', 'zip_code' => '12520'],
                ['id' => 2006, 'name' => 'TEBET', 'zip_code' => '12810'],
                ['id' => 2007, 'name' => 'CILANDAK', 'zip_code' => '12430'],
            ];
        }

        // Generic fallback districts with postal code
        $baseZip = 10000 + ($cityId % 80000);
        return [
            ['id' => ($cityId * 10) + 1, 'name' => 'KECAMATAN PUSAT', 'zip_code' => (string) $baseZip],
            ['id' => ($cityId * 10) + 2, 'name' => 'KECAMATAN UTARA', 'zip_code' => (string) ($baseZip + 10)],
            ['id' => ($cityId * 10) + 3, 'name' => 'KECAMATAN SELATAN', 'zip_code' => (string) ($baseZip + 20)],
            ['id' => ($cityId * 10) + 4, 'name' => 'KECAMATAN TIMUR', 'zip_code' => (string) ($baseZip + 30)],
            ['id' => ($cityId * 10) + 5, 'name' => 'KECAMATAN BARAT', 'zip_code' => (string) ($baseZip + 40)],
        ];
    }

    /**
     * Fallback data Kelurahan/Desa
     */
    protected function fallbackSubDistricts(int $districtId): array
    {
        return [
            ['id' => ($districtId * 10) + 1, 'name' => 'KELURAHAN 1', 'zip_code' => '80361'],
            ['id' => ($districtId * 10) + 2, 'name' => 'KELURAHAN 2', 'zip_code' => '80361'],
        ];
    }
}
