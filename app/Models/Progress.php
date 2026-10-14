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
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'terbit');
    }

    /**
     * Deteksi platform video: 'youtube', 'instagram', 'tiktok', 'mp4', atau 'other'
     */
    public function videoPlatform(): ?string
    {
        $url = $this->video_url;
        if (!$url) {
            return $this->tipe === 'video' ? 'mp4' : null;
        }

        if (preg_match('/(youtube\.com|youtu\.be)/i', $url)) {
            return 'youtube';
        }
        if (preg_match('/(instagram\.com|instagr\.am)/i', $url)) {
            return 'instagram';
        }
        if (preg_match('/(tiktok\.com)/i', $url)) {
            return 'tiktok';
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

        // 1. YouTube (Watch, Shorts, Embed, youtu.be)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&controls=0&rel=0&modestbranding=1&playsinline=1&iv_load_policy=3&disablekb=1&fs=0';
        }

        // 2. Instagram (Reels, Post, TV)
        if (preg_match('/(?:instagram\.com|instagr\.am)\/(?:p|reel|tv)\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return 'https://www.instagram.com/reel/' . $m[1] . '/embed/';
        }

        // 3. TikTok (Video standard & webapp)
        if (preg_match('/(?:tiktok\.com\/@[^\/]+\/video\/|tiktok\.com\/v\/)(\d+)/i', $url, $m)) {
            return 'https://www.tiktok.com/player/v1/' . $m[1] . '?autoplay=1&music_info=0&description=0';
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
            // YouTube thumbnail otomatis
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $m)) {
                return 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
            }
        }

        return null;
    }

    /**
     * Normalisasi URL video (resolve redirect shortlink TikTok vt.tiktok.com atau query string berlebih)
     */
    public static function normalizeVideoUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $url = trim($url);

        // Jika shortlink TikTok vt.tiktok.com atau vm.tiktok.com
        if (preg_match('/(?:vt|vm)\.tiktok\.com\/[a-zA-Z0-9_\-]+/i', $url)) {
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
                curl_exec($ch);
                $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                if ($effective && $effective !== $url) {
                    return $effective;
                }
            } catch (\Throwable $e) {
                // Abaikan bila timeout atau error jaringan
            }
        }

        return $url;
    }

    /**
     * Label platform yang ramah pengguna
     */
    public function videoPlatformLabel(): string
    {
        return match ($this->videoPlatform()) {
            'youtube' => 'YouTube',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'mp4' => 'Video MP4',
            default => 'Video Lapangan',
        };
    }
}
