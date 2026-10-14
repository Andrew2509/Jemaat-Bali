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
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'aktif' => 'boolean',
        ];
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
