<?php

namespace App\Services;

use App\Models\Progress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoConverterService
{
    /**
     * Jalur ke file binary yt-dlp
     */
    public static function getYtDlpPath(): ?string
    {
        $localBin = storage_path('app/bin/yt-dlp.exe');
        if (file_exists($localBin)) {
            return $localBin;
        }

        $localBinLinux = storage_path('app/bin/yt-dlp');
        if (file_exists($localBinLinux)) {
            return $localBinLinux;
        }

        // Cek PATH sistem
        $which = PHP_OS_FAMILY === 'Windows' ? 'where yt-dlp' : 'which yt-dlp';
        $output = @shell_exec($which);
        if ($output && trim($output)) {
            $lines = explode("\n", trim($output));
            return trim($lines[0]);
        }

        return null;
    }

    /**
     * Jalur ke file binary ffmpeg jika ada
     */
    public static function getFfmpegPath(): ?string
    {
        $candidates = PHP_OS_FAMILY === 'Windows' ? [
            'C:\\Users\\HP\\AppData\\Local\\Microsoft\\WinGet\\Links\\ffmpeg.exe',
            'C:\\ProgramData\\chocolatey\\bin\\ffmpeg.exe',
            storage_path('app/bin/ffmpeg.exe'),
        ] : [
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            storage_path('app/bin/ffmpeg'),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        $which = PHP_OS_FAMILY === 'Windows' ? 'where ffmpeg' : 'which ffmpeg';
        $output = @shell_exec($which);
        if ($output && trim($output)) {
            $lines = explode("\n", trim($output));
            $found = trim($lines[0]);
            if ($found && file_exists($found)) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Format ukuran berkas ke bentuk teks yang mudah dibaca (MB, KB)
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Unduh video dari link medsos dan simpan sebagai file MP4 lokal dengan ukuran terkompresi
     */
    public function convertToLocalMp4(string $url, string $quality = '720p'): array
    {
        $url = trim($url);
        if (!$url) {
            return [
                'success' => false,
                'message' => 'Tautan video tidak boleh kosong.',
            ];
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        // Normalisasi URL lebih dulu (misal vt.tiktok.com)
        $url = Progress::normalizeVideoUrl($url);
        $quality = in_array($quality, ['480p', '720p', '1080p']) ? $quality : '720p';

        // 1. Jika TikTok, coba API cepat (no watermark)
        if (preg_match('/tiktok\.com/i', $url)) {
            $tiktokResult = $this->downloadTikTokDirect($url, $quality);
            if ($tiktokResult['success']) {
                return $tiktokResult;
            }
        }

        // 2. Gunakan yt-dlp untuk YouTube, Instagram, dan fallback TikTok
        $ytDlp = self::getYtDlpPath();
        if ($ytDlp) {
            return $this->downloadWithYtDlp($ytDlp, $url, $quality);
        }

        $platform = Progress::make(['video_url' => $url])->videoPlatformLabel();

        return [
            'success' => false,
            'message' => "Modul pengunduh video otomatis (yt-dlp) tidak terpasang di server hosting ini. Anda tidak perlu khawatir: tautan video {$platform} ini tetap dapat disimpan dan akan diputar dengan lancar melalui pemutar resmi di halaman progres. Jika Anda menginginkan berkas MP4 langsung di server, silakan unggah berkas .mp4 secara manual melalui tab 'Pilih File'.",
            'can_embed' => true,
        ];
    }

    /**
     * Unduh TikTok langsung tanpa watermark via API
     */
    protected function downloadTikTokDirect(string $url, string $quality = '720p'): array
    {
        try {
            $response = Http::timeout(15)->get('https://www.tikwm.com/api/', [
                'url' => $url,
                'count' => 12,
                'cursor' => 0,
                'web' => 1,
                'hd' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 0 && !empty($data['data']['play'])) {
                    // Jika 480p atau 720p, gunakan stream 'play' yang sangat ringan (~2-4 MB)
                    // Jika 1080p baru gunakan hdplay jika tersedia
                    $videoUrl = ($quality === '1080p') ? ($data['data']['hdplay'] ?? $data['data']['play']) : $data['data']['play'];

                    // Unduh video stream
                    $videoStream = Http::timeout(60)->get($videoUrl);
                    if ($videoStream->successful()) {
                        $filename = 'progresses/tiktok_' . time() . '_' . Str::random(6) . '.mp4';
                        Storage::disk('public')->put($filename, $videoStream->body());
                        $fullPath = storage_path('app/public/' . $filename);
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : strlen($videoStream->body());
                        $sizeHuman = self::formatBytes($fileSize);

                        return [
                            'success' => true,
                            'media_path' => $filename,
                            'media_url' => asset('storage/' . $filename),
                            'platform' => 'tiktok',
                            'size' => $fileSize,
                            'size_human' => $sizeHuman,
                            'quality' => $quality,
                            'message' => "Video TikTok berhasil diunduh & dikompresi ({$sizeHuman}) tanpa watermark!",
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TikTok direct download failed: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Unduh video menggunakan yt-dlp binary dengan kompresi ukuran resolusi optimal
     */
    protected function downloadWithYtDlp(string $ytDlpPath, string $url, string $quality = '720p'): array
    {
        // Pastikan folder storage/app/public/progresses ada
        $targetDir = storage_path('app/public/progresses');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $baseName = 'video_' . time() . '_' . Str::random(6);
        $outputTemplate = $targetDir . DIRECTORY_SEPARATOR . $baseName . '.%(ext)s';

        $nodePath = null;
        if (PHP_OS_FAMILY === 'Windows') {
            $windowsCandidates = [
                'C:\\nvm4w\\nodejs\\node.exe',
                'C:\\Program Files\\nodejs\\node.exe',
            ];
            foreach ($windowsCandidates as $cand) {
                if (file_exists($cand)) {
                    $nodePath = $cand;
                    break;
                }
            }
        } else {
            $linuxCandidates = [
                '/usr/bin/node',
                '/usr/local/bin/node',
            ];
            foreach ($linuxCandidates as $cand) {
                if (file_exists($cand)) {
                    $nodePath = $cand;
                    break;
                }
            }
        }
        if (!$nodePath) {
            $whichNode = PHP_OS_FAMILY === 'Windows' ? 'where node' : 'which node';
            $outputNode = @shell_exec($whichNode);
            if ($outputNode && trim($outputNode)) {
                $lines = explode("\n", trim($outputNode));
                $found = trim($lines[0]);
                if ($found && file_exists($found)) {
                    $nodePath = $found;
                }
            }
        }
        $nodeOpt = $nodePath ? '--js-runtimes node:' . escapeshellarg($nodePath) . ' ' : '';

        $ffmpegPath = self::getFfmpegPath();
        $ffmpegOpt = $ffmpegPath ? '--ffmpeg-location ' . escapeshellarg($ffmpegPath) . ' ' : '';

        // Tentukan format berdasarkan pilihan resolusi agar ukuran file tidak kebesaran
        if ($quality === '480p') {
            // Super Ringan: hemat kuota maksimal (~3 - 8 MB)
            $formatSelector = 'bv*[height<=480][ext=mp4]+ba[ext=m4a]/b[height<=480][ext=mp4]/bv*[height<=480]+ba/b[height<=480]/best[height<=480]/best';
        } elseif ($quality === '1080p') {
            // Kualitas Penuh
            $formatSelector = 'bv*[height<=1080][ext=mp4]+ba[ext=m4a]/b[height<=1080][ext=mp4]/bv*+ba/b';
        } else {
            // 720p (Standar Web Rekomendasi): resolusi tajam, ukuran file hemat (~6 - 18 MB)
            $formatSelector = 'bv*[height<=720][ext=mp4]+ba[ext=m4a]/b[height<=720][ext=mp4]/bv*[height<=720]+ba/b[height<=720]/best[height<=720]/best';
        }

        // Opsi yt-dlp:
        // - Batasi resolusi sesuai $quality
        // - Merge ke MP4
        // - movflags +faststart agar langsung dapat di-stream tanpa loading penuh di browser
        $cmd = sprintf(
            '"%s" %s%s-f "%s" --merge-output-format mp4 --postprocessor-args "ffmpeg:-movflags +faststart" --no-playlist --max-filesize 100M -o "%s" %s 2>&1',
            $ytDlpPath,
            $nodeOpt,
            $ffmpegOpt,
            $formatSelector,
            $outputTemplate,
            escapeshellarg($url)
        );

        $output = [];
        $returnCode = 0;
        @exec($cmd, $output, $returnCode);

        // Cari file yang dihasilkan dengan prefix baseName (hanya file selesai mp4/webm, abaikan .part)
        $rawMatches = glob($targetDir . DIRECTORY_SEPARATOR . $baseName . '.*');
        $matches = array_values(array_filter($rawMatches ?: [], function ($file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($ext, ['mp4', 'webm', 'm4v', 'mov', 'mkv']) && !str_ends_with($file, '.part') && !str_ends_with($file, '.ytdl');
        }));

        if (!empty($matches)) {
            $createdFile = $matches[0];
            $relativeStoragePath = 'progresses/' . basename($createdFile);
            $fileSize = filesize($createdFile);
            $sizeHuman = self::formatBytes($fileSize);

            return [
                'success' => true,
                'media_path' => $relativeStoragePath,
                'media_url' => asset('storage/' . $relativeStoragePath),
                'size' => $fileSize,
                'size_human' => $sizeHuman,
                'quality' => $quality,
                'message' => "Video berhasil dikonversi ke MP4 hemat ukuran ({$sizeHuman}, kualitas {$quality})!",
            ];
        }

        $errorLog = implode("\n", array_slice($output, -10));
        Log::error('yt-dlp download failed: ' . $errorLog);

        $errorHint = 'Tautan video tidak dapat diunduh otomatis oleh server (mungkin dibatasi oleh penyedia atau berdurasi terlalu panjang).';
        if (str_contains($errorLog, 'Requested format is not available') || str_contains($errorLog, 'ffmpeg')) {
            $errorHint = 'Video membutuhkan proses penggabungan audio/video. Anda dapat mengunggah langsung berkas asli .mp4 melalui tab "Pilih File" untuk video yang bersih 100%.';
        }

        return [
            'success' => false,
            'message' => $errorHint,
            'detail' => $output[0] ?? null,
            'can_embed' => true,
        ];
    }
}
