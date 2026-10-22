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
        'qris_code',
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
     * Format default tabel panduan ukuran kaos Pria
     */
    public static function defaultSizeChartPria(): array
    {
        return [
            'title' => 'Panduan Ukuran Kaos Pria (cm)',
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
     * Format default tabel panduan ukuran kaos Wanita (Cewe)
     */
    public static function defaultSizeChartWanita(): array
    {
        return [
            'title' => 'Panduan Ukuran Kaos Wanita (cm)',
            'note' => 'Toleransi ukuran ±1 cm · Bahan 100% Cotton Combed',
            'columns' => ['Ukuran', 'Lebar Dada (cm)', 'Panjang Badan (cm)', 'Panjang Lengan (cm)'],
            'rows' => [
                ['S', '44', '60', '18'],
                ['M', '47', '63', '19'],
                ['L', '50', '66', '20'],
                ['XL', '53', '68', '21'],
                ['XXL', '56', '70', '22'],
            ],
        ];
    }

    /**
     * Alias lama: tabel default dianggap versi Pria.
     */
    public static function defaultSizeChart(): array
    {
        return static::defaultSizeChartPria();
    }

    /**
     * Ambil data tabel panduan ukuran untuk gender tertentu.
     * Struktur baru: ['pria' => [...], 'wanita' => [...]].
     * Data lama (satu tabel di root) diperlakukan sebagai Pria.
     */
    public function getSizeChartFor(string $gender = 'pria'): array
    {
        $gender = $gender === 'wanita' ? 'wanita' : 'pria';
        $default = $gender === 'wanita'
            ? static::defaultSizeChartWanita()
            : static::defaultSizeChartPria();

        $raw = $this->size_chart;

        if (! is_array($raw)) {
            return $default;
        }

        if (isset($raw['pria']) || isset($raw['wanita'])) {
            return static::normalizeSizeChart($raw[$gender] ?? null, $default);
        }

        if ($gender === 'pria') {
            return static::normalizeSizeChart($raw, $default);
        }

        return $default;
    }

    /**
     * Ambil data tabel panduan ukuran Pria (kompatibilitas lama).
     */
    public function getSizeChart(): array
    {
        return $this->getSizeChartFor('pria');
    }

    /**
     * Ambil data tabel panduan ukuran Wanita (Cewe).
     */
    public function getSizeChartWanita(): array
    {
        return $this->getSizeChartFor('wanita');
    }

    /**
     * Ambil kedua tabel panduan ukuran.
     */
    public function getSizeCharts(): array
    {
        return [
            'pria' => $this->getSizeChartFor('pria'),
            'wanita' => $this->getSizeChartFor('wanita'),
        ];
    }

    /**
     * Normalisasi satu tabel ukuran agar selalu memiliki title, note, columns, rows.
     */
    protected static function normalizeSizeChart($raw, array $default): array
    {
        if (! is_array($raw) || empty($raw['columns']) || empty($raw['rows'])) {
            return $default;
        }

        return [
            'title' => $raw['title'] ?? $default['title'],
            'note' => $raw['note'] ?? $default['note'],
            'columns' => (array) $raw['columns'],
            'rows' => (array) $raw['rows'],
        ];
    }

    protected static function booted(): void
    {
        static::created(fn () => static::flushMemo());
        static::updated(fn () => static::flushMemo());
        static::deleted(fn () => static::flushMemo());
    }

    /**
     * Cache statis per-request untuk hasil ProgramInfo::first().
     * Menghindari query berulang (cache database = 1 query per panggilan).
     */
    protected static ?Model $memoInstance = null;

    protected static ?int $memoRequestId = null;

    public static function first(): ?Model
    {
        $requestId = static::currentRequestId();

        if (static::$memoRequestId === $requestId && static::$memoInstance !== null) {
            return static::$memoInstance;
        }

        $model = cache()->rememberForever('program_info', fn () => static::query()->first());

        static::$memoInstance = $model;
        static::$memoRequestId = $requestId;

        return $model;
    }

    /**
     * Bersihkan cache statis (dipanggil ketika data berubah).
     */
    public static function flushMemo(): void
    {
        cache()->forget('program_info');
        static::$memoInstance = null;
        static::$memoRequestId = null;
    }

    /**
     * Identitas request saat ini, agar memo tidak bocor antar-request.
     */
    protected static function currentRequestId(): int
    {
        if (app()->bound('request')) {
            return spl_object_id(app('request'));
        }

        return -1;
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
     * Cek apakah tersedia payload QRIS untuk digenerate menjadi gambar QR
     */
    public function hasQrisCode(): bool
    {
        return !empty(trim((string) $this->qris_code));
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
        return asset('images/hero-poster.webp');
    }

    /**
     * Cek apakah menggunakan video kustom (file atau link drive)
     */
    public function hasCustomHeroVideo(): bool
    {
        return $this->getHeroVideoType() !== 'default';
    }
}
