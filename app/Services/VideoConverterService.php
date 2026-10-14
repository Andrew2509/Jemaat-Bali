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
     * Unduh video dari tautan Google Drive dan simpan sebagai file MP4 lokal di server
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

        // Unduh langsung berkas MP4 dari Google Drive tanpa yt-dlp
        if (preg_match('/(?:drive\.google\.com|docs\.google\.com)/i', $url)) {
            return $this->downloadGoogleDriveDirect($url);
        }

        return [
            'success' => false,
            'message' => 'Sistem dikonfigurasi khusus untuk tautan video Google Drive. Silakan masukkan tautan Google Drive yang valid, atau unggah berkas MP4 langsung melalui tab "Upload File".',
            'can_embed' => true,
        ];
    }

    /**
     * Unduh video langsung dari tautan Google Drive publik tanpa membutuhkan yt-dlp
     */
    protected function downloadGoogleDriveDirect(string $url): array
    {
        if (!preg_match('/(?:drive\.google\.com\/(?:file\/d\/|(?:open|uc)\?(?:[^\s"&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return [
                'success' => false,
                'message' => 'Format tautan Google Drive tidak valid. Pastikan link berbentuk: https://drive.google.com/file/d/.../view?usp=sharing',
            ];
        }

        $fileId = $m[1];
        $targetDir = storage_path('app/public/progresses');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        try {
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
            $endpoints = [
                "https://drive.usercontent.google.com/download?id={$fileId}&export=download&authuser=0",
                "https://drive.google.com/uc?export=download&id={$fileId}",
            ];

            foreach ($endpoints as $downloadUrl) {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ])->withOptions([
                    'allow_redirects' => true,
                    'cookies' => $cookieJar,
                ])->timeout(90)->get($downloadUrl);

                $body = $response->body();

                // Deteksi halaman peringatan ukuran besar Google Drive / virus scan
                if ($response->successful() && str_contains($body, 'confirm=')) {
                    $confirmUrl = null;
                    if (preg_match('/confirm=([0-9a-zA-Z_-]+)/', $body, $cm)) {
                        $confirmUrl = "https://drive.usercontent.google.com/download?id={$fileId}&export=download&confirm={$cm[1]}&authuser=0";
                    } elseif (preg_match('/<a[^>]+id="uc-download-link"[^>]+href="([^"]+)"/i', $body, $am)) {
                        $confirmUrl = html_entity_decode($am[1]);
                        if (!str_starts_with($confirmUrl, 'http')) {
                            $confirmUrl = 'https://drive.google.com' . $confirmUrl;
                        }
                    }

                    if ($confirmUrl) {
                        $response = Http::withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        ])->withOptions([
                            'allow_redirects' => true,
                            'cookies' => $cookieJar,
                        ])->timeout(90)->get($confirmUrl);
                        $body = $response->body();
                    }
                }

                $contentType = strtolower($response->header('Content-Type') ?? '');
                if ($response->successful() && !str_contains($contentType, 'text/html') && strlen($body) > 1000) {
                    $filename = 'progresses/gdrive_' . time() . '_' . Str::random(6) . '.mp4';
                    Storage::disk('public')->put($filename, $body);
                    $fullPath = storage_path('app/public/' . $filename);
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : strlen($body);
                    $sizeHuman = self::formatBytes($fileSize);

                    return [
                        'success' => true,
                        'media_path' => $filename,
                        'media_url' => asset('storage/' . $filename),
                        'platform' => 'gdrive',
                        'size' => $fileSize,
                        'size_human' => $sizeHuman,
                        'message' => "Video Google Drive berhasil diunduh ke server ({$sizeHuman})!",
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Google Drive direct download failed: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => 'Berkas Google Drive tidak dapat diunduh otomatis (pastikan setelan akses di Google Drive disetel ke "Siapa saja yang memiliki link / Anyone with the link"). Namun tautan ini tetap bisa disimpan dan langsung diputar via pemutar resmi Google Drive!',
            'can_embed' => true,
        ];
    }
}
