<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progresses';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'tipe',
        'media_path',
        'video_url',
        'tahap',
        'status',
        'teks_alt',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function mediaUrl(): ?string
    {
        if (!$this->media_path) {
            return null;
        }

        if (str_starts_with($this->media_path, 'http://') || str_starts_with($this->media_path, 'https://')) {
            return $this->media_path;
        }

        if (str_starts_with($this->media_path, 'uploads/') || str_starts_with($this->media_path, 'images/') || str_starts_with($this->media_path, 'designs/')) {
            return asset($this->media_path);
        }

        if (str_starts_with($this->media_path, 'storage/')) {
            return asset($this->media_path);
        }

        return asset('storage/' . $this->media_path);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'terbit');
    }

    /**
     * Deteksi platform video: 'gdrive', 'mp4', atau 'other'
     */
    public function videoPlatform(): ?string
    {
        $url = $this->video_url;
        if (!$url) {
            return $this->tipe === 'video' ? 'mp4' : null;
        }

        if (preg_match('/(drive\.google\.com|docs\.google\.com)/i', $url)) {
            return 'gdrive';
        }

        return 'other';
    }

    /**
     * Dapatkan URL embed resmi yang siap dimasukkan ke dalam <iframe>
     */
    public function embedUrl(): ?string
    {
        $url = trim($this->video_url ?? '');
        if (!$url) {
            return null;
        }

        // Google Drive
        if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|(?:open|uc)\?(?:[^\s"&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }

        return $url;
    }

    /**
     * Thumbnail gambar untuk video atau foto
     */
    public function thumbnailUrl(): ?string
    {
        // Jika foto, gunakan media_path
        if ($this->media_path && $this->tipe === 'foto') {
            return $this->mediaUrl();
        }

        $url = $this->video_url;
        if ($url) {
            // Google Drive thumbnail otomatis
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|(?:open|uc)\?(?:[^\s"&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
                return 'https://drive.google.com/thumbnail?id=' . $m[1] . '&sz=w800';
            }
        }

        return null;
    }

    /**
     * Normalisasi URL video
     */
    public static function normalizeVideoUrl(?string $url): ?string
    {
        return $url ? trim($url) : null;
    }

    /**
     * Label platform yang ramah pengguna
     */
    public function videoPlatformLabel(): string
    {
        return match ($this->videoPlatform()) {
            'gdrive' => 'Google Drive',
            'mp4' => 'Video MP4',
            default => 'Video Lapangan',
        };
    }
}
