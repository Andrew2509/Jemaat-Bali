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
     * Ambil data tabel panduan ukuran untuk desain ini atau fallback ke default program info
     */
    public function getSizeChart(): array
    {
        if (!empty($this->size_chart) && is_array($this->size_chart) && !empty($this->size_chart['columns']) && !empty($this->size_chart['rows'])) {
            return [
                'title' => $this->size_chart['title'] ?? 'Panduan Ukuran Kaos (cm)',
                'note' => $this->size_chart['note'] ?? 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed',
                'columns' => (array) $this->size_chart['columns'],
                'rows' => (array) $this->size_chart['rows'],
            ];
        }

        $programInfo = ProgramInfo::first();
        return $programInfo ? $programInfo->getSizeChart() : ProgramInfo::defaultSizeChart();
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
