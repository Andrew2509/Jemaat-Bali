<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nama
 * @property string|null $deskripsi
 * @property int $harga
 * @property string|null $gambar
 * @property string|null $video_360
 * @property bool $aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Design extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'gambar',
        'video_360',
        'aktif',
        'ukuran',
        'size_chart',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'aktif' => 'boolean',
            'ukuran' => 'array',
            'size_chart' => 'array',
        ];
    }

    /**
     * Ambil data tabel panduan ukuran untuk gender tertentu.
     * Bila desain tidak punya tabel khusus, fallback ke tabel standar koleksi.
     */
    public function getSizeChartFor(string $gender = 'pria'): array
    {
        $gender = $gender === 'wanita' ? 'wanita' : 'pria';
        $default = $gender === 'wanita'
            ? ProgramInfo::defaultSizeChartWanita()
            : ProgramInfo::defaultSizeChartPria();

        $raw = $this->size_chart;

        if (is_array($raw)) {
            if (isset($raw['pria']) || isset($raw['wanita'])) {
                $chart = $raw[$gender] ?? null;
                if (! empty($chart['columns']) && ! empty($chart['rows'])) {
                    return [
                        'title' => $chart['title'] ?? $default['title'],
                        'note' => $chart['note'] ?? $default['note'],
                        'columns' => (array) $chart['columns'],
                        'rows' => (array) $chart['rows'],
                    ];
                }
            } elseif ($gender === 'pria' && ! empty($raw['columns']) && ! empty($raw['rows'])) {
                // Data lama: satu tabel di root dianggap Pria
                return [
                    'title' => $raw['title'] ?? $default['title'],
                    'note' => $raw['note'] ?? $default['note'],
                    'columns' => (array) $raw['columns'],
                    'rows' => (array) $raw['rows'],
                ];
            }
        }

        $programInfo = ProgramInfo::first();
        return $programInfo ? $programInfo->getSizeChartFor($gender) : $default;
    }

    /**
     * Tabel panduan ukuran Pria (kompatibilitas lama).
     */
    public function getSizeChart(): array
    {
        return $this->getSizeChartFor('pria');
    }

    /**
     * Tabel panduan ukuran Wanita (Cewe).
     */
    public function getSizeChartWanita(): array
    {
        return $this->getSizeChartFor('wanita');
    }

    /**
     * Kedua tabel panduan ukuran.
     */
    public function getSizeCharts(): array
    {
        return [
            'pria' => $this->getSizeChartFor('pria'),
            'wanita' => $this->getSizeChartFor('wanita'),
        ];
    }

    public const ALL_UKURAN = ['S', 'M', 'L', 'XL', 'XXL'];

    /**
     * Mengambil daftar ukuran yang tersedia untuk desain ini.
     * Jika belum disetel (null/kosong), otomatis default semua ukuran ['S', 'M', 'L', 'XL', 'XXL'].
     *
     * @return array<string>
     */
    public function pilihanUkuran(): array
    {
        if (is_array($this->ukuran) && count($this->ukuran) > 0) {
            return array_values($this->ukuran);
        }

        return self::ALL_UKURAN;
    }

    /**
     * Periksa apakah ukuran tertentu aktif/tersedia untuk desain ini.
     */
    public function isUkuranTersedia(string $size): bool
    {
        return in_array(strtoupper(trim($size)), array_map('strtoupper', $this->pilihanUkuran()), true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function gambarUrl(): string
    {
        if (! $this->gambar) {
            return asset('designs/clean-kaos-bangun-bersama.svg');
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        if (file_exists(public_path($this->gambar))) {
            return asset($this->gambar);
        }

        if (file_exists(public_path('designs/' . basename($this->gambar)))) {
            return asset('designs/' . basename($this->gambar));
        }

        if (file_exists(public_path('storage/' . $this->gambar))) {
            return asset('storage/' . $this->gambar);
        }

        if (str_starts_with($this->gambar, 'storage/')) {
            return asset($this->gambar);
        }

        return asset('designs/clean-kaos-bangun-bersama.svg');
    }

    /**
     * Periksa apakah berkas 360° berupa gambar animasi (GIF) alih-alih video.
     */
    public function isVideo360Gif(): bool
    {
        if (! $this->video_360) {
            return false;
        }

        return strtolower(pathinfo(parse_url($this->video_360, PHP_URL_PATH) ?? $this->video_360, PATHINFO_EXTENSION)) === 'gif';
    }

    public function video360Url(): ?string
    {
        if (! $this->video_360) {
            return null;
        }

        if (str_starts_with($this->video_360, 'http://') || str_starts_with($this->video_360, 'https://')) {
            return $this->video_360;
        }

        if (file_exists(public_path($this->video_360))) {
            return asset($this->video_360);
        }

        if (file_exists(public_path('storage/' . $this->video_360))) {
            return asset('storage/' . $this->video_360);
        }

        if (str_starts_with($this->video_360, 'storage/')) {
            return asset($this->video_360);
        }

        return asset('storage/' . $this->video_360);
    }
}
