<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramInfo extends Model
{
    protected $table = 'program_info';

    protected $fillable = [
        'nama_program',
        'logo_path',
        'tagline',
        'deskripsi',
        'whatsapp',
        'email',
        'judul_proposal',
        'proposal_tipe',
        'proposal_canva_url',
        'proposal_canva_url_en',
        'qris_image',
        'size_chart',
        'hero_video_type',
        'hero_video_path',
        'hero_video_url',
        'hero_video_poster',
    ];

    protected function casts(): array
    {
        return [
            'size_chart' => 'array',
        ];
    }

    /**
     * Format default tabel panduan ukuran kaos
     */
    public static function defaultSizeChart(): array
    {
        return [
            'title' => 'Panduan Ukuran Kaos (cm)',
            'note' => 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed',
            'columns' => ['Ukuran', 'Lebar Dada (cm)', 'Panjang Badan (cm)', 'Panjang Lengan (cm)'],
            'rows' => [
                ['S', '47', '67', '20'],
                ['M', '50', '70', '21'],
                ['L', '53', '73', '22'],
                ['XL', '56', '75', '23'],
                ['XXL', '59', '77', '24'],
            ],
        ];
    }

    /**
     * Ambil data tabel panduan ukuran yang valid
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

        return static::defaultSizeChart();
    }

    protected static function booted(): void
    {
        static::created(fn () => cache()->forget('program_info'));
        static::updated(fn () => cache()->forget('program_info'));
        static::deleted(fn () => cache()->forget('program_info'));
    }

    public static function first(): ?Model
    {
        return cache()->rememberForever('program_info', fn () => static::query()->first());
    }

    /**
     * URL logo resmi jika diunggah
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!empty($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    /**
     * Cek apakah logo gambar tersedia
     */
    public function hasLogo(): bool
    {
        return !empty($this->logo_path);
    }

    /**
     * Teks tagline / slogan / subjudul program (default: BANGUN BERSAMA)
     */
    public function getTaglineOrDefaultAttribute(): string
    {
        return !empty(trim((string) $this->tagline)) ? trim($this->tagline) : 'BANGUN BERSAMA';
    }

    /**
     * Inisial 2 huruf untuk logo bulat navbar & footer (misal: "Jemaat Bali" -> "JB")
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->nama_program ?? 'Jemaat Bali');
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($name, 0, 2));
    }

    /**
     * Nomor WhatsApp bersih berawalan kode negara 62 untuk URL wa.me
     */
    public function getCleanWhatsappAttribute(): string
    {
        $num = preg_replace('/[^0-9]/', '', $this->whatsapp ?? '');
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        }
        return $num ?: '6281200000000';
    }

    /**
     * Tautan langsung kirim pesan ke WhatsApp
     */
    public function getWaUrlAttribute(): string
    {
        return 'https://wa.me/' . $this->clean_whatsapp;
    }

    /**
     * URL sematan resmi untuk iframe Canva (mendukung tautan cerdas maupun kode embed HTML)
     */
    public function getCanvaEmbedUrlAttribute(): ?string
    {
        return $this->normalizeCanvaUrl($this->proposal_canva_url);
    }

    /**
     * URL sematan Canva versi bahasa Inggris
     */
    public function getCanvaEmbedUrlEnAttribute(): ?string
    {
        return $this->normalizeCanvaUrl($this->proposal_canva_url_en);
    }

    /**
     * Cek apakah tersedia versi bahasa Inggris
     */
    public function hasCanvaVersionEn(): bool
    {
        return !empty($this->canva_embed_url_en);
    }

    /**
     * URL gambar QRIS merchant untuk halaman pembayaran
     */
    public function getQrisImageUrlAttribute(): ?string
    {
        if (!empty($this->qris_image)) {
            return asset('storage/' . $this->qris_image);
        }
        return null;
    }

    /**
     * Normalisasi tautan atau kode sematan Canva menjadi URL embed siap iframe
     */
    protected function normalizeCanvaUrl(?string $raw): ?string
    {
        $val = trim($raw ?? '');
        if (!$val) {
            return null;
        }

        // 1. Ekstrak src jika pemakai menempelkan tag <iframe> atau HTML embed Canva
        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $val, $m)) {
            $val = $m[1];
        }

        $val = html_entity_decode($val);

        // 2. Normalisasi jika berupa tautan canva.com
        if (preg_match('~^https?://(?:www\.)?canva\.com/design/([^?#]+)~i', $val, $m)) {
            $path = trim($m[1], '/');
            // Jika path berakhiran /view, /watch, /edit atau /embed, bersihkan
            $path = preg_replace('~/(view|watch|edit|embed)$~i', '', $path);
            return "https://www.canva.com/design/{$path}/view?embed";
        }

        if (filter_var($val, FILTER_VALIDATE_URL)) {
            return str_contains($val, 'embed') ? $val : ($val . (str_contains($val, '?') ? '&embed' : '?embed'));
        }

        return null;
    }

    /**
     * Cek apakah mode proposal aktif adalah Canva
     */
    public function isCanvaProposal(): bool
    {
        if ($this->proposal_tipe === 'canva') {
            return !empty($this->canva_embed_url);
        }

        return !empty($this->canva_embed_url) && $this->proposal_tipe !== 'pdf';
    }

    /**
     * Tipe video pembuka beranda aktif: 'default', 'file', atau 'gdrive'
     */
    public function getHeroVideoType(): string
    {
        $type = $this->hero_video_type ?? 'default';
        if ($type === 'file' && !empty($this->hero_video_path)) {
            return 'file';
        }
        if ($type === 'gdrive' && !empty($this->hero_video_url)) {
            return 'gdrive';
        }
        if ($type === 'default') {
            return 'default';
        }
        if (!empty($this->hero_video_path)) {
            return 'file';
        }
        if (!empty($this->hero_video_url)) {
            return 'gdrive';
        }
        return 'default';
    }

    /**
     * URL embed video Google Drive untuk iframe
     */
    public function getHeroVideoEmbedUrl(): ?string
    {
        $url = trim($this->hero_video_url ?? '');
        if (!$url) {
            return null;
        }

        if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|(?:open|uc)\?(?:[^\s"&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }

        return $url;
    }

    /**
     * ID Google Drive untuk thumbnail / preview
     */
    public function getHeroGoogleDriveId(): ?string
    {
        $url = trim($this->hero_video_url ?? '');
        if (!$url) {
            return null;
        }
        if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|(?:open|uc)\?(?:[^\s"&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * URL file video untuk tag HTML5 <video>
     */
    public function getHeroVideoFileUrl(): ?string
    {
        if (!empty($this->hero_video_path)) {
            return asset('storage/' . $this->hero_video_path);
        }
        return null;
    }

    /**
     * URL aktif untuk pemutaran video hero di halaman depan
     */
    public function getActiveHeroVideoUrl(): string
    {
        $type = $this->getHeroVideoType();
        if ($type === 'file' && $this->getHeroVideoFileUrl()) {
            return $this->getHeroVideoFileUrl();
        }
        if ($type === 'gdrive' && $this->getHeroVideoEmbedUrl()) {
            return $this->getHeroVideoEmbedUrl();
        }
        return asset('videos/hero-bg.mp4');
    }

    /**
     * URL poster / thumbnail hero video
     */
    public function getHeroPosterUrl(): string
    {
        if (!empty($this->hero_video_poster)) {
            return asset('storage/' . $this->hero_video_poster);
        }
        if ($this->getHeroVideoType() === 'gdrive' && $driveId = $this->getHeroGoogleDriveId()) {
            return 'https://drive.google.com/thumbnail?id=' . $driveId . '&sz=w1200';
        }
        return asset('images/hero-poster.jpg');
    }

    /**
     * Cek apakah menggunakan video kustom (file atau link drive)
     */
    public function hasCustomHeroVideo(): bool
    {
        return $this->getHeroVideoType() !== 'default';
    }
}
