<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected const API_BASE = 'https://api.fonnte.com';

    protected WhatsappSetting $setting;

    public function __construct()
    {
        $this->setting = WhatsappSetting::getSetting();
    }

    /**
     * Dapatkan setting saat ini
     */
    public function getSetting(): WhatsappSetting
    {
        return $this->setting;
    }

    /**
     * Cek status profil perangkat di Fonnte API (POST https://api.fonnte.com/device)
     */
    public function checkDevice(?string $token = null): array
    {
        $activeToken = trim((string) ($token ?: $this->setting->api_token));

        if (empty($activeToken)) {
            $this->setting->update([
                'device_status' => 'unconfigured',
                'last_checked_at' => now(),
            ]);

            return [
                'success' => false,
                'status' => 'unconfigured',
                'message' => 'Token API Fonnte belum diisi.',
                'data' => null,
            ];
        }

        try {
            $response = Http::timeout(3)
                ->withHeaders([
                    'Authorization' => $activeToken,
                ])
                ->post(self::API_BASE . '/device');

            $body = $response->json();

            if (!$response->successful()) {
                $statusMsg = $body['reason'] ?? $body['message'] ?? 'Gagal menghubungi server Fonnte (HTTP ' . $response->status() . ')';
                $this->setting->update([
                    'device_status' => 'error',
                    'last_checked_at' => now(),
                ]);

                return [
                    'success' => false,
                    'status' => 'error',
                    'message' => $statusMsg,
                    'data' => $body,
                ];
            }

            // Fonnte returns JSON with device, device_status ('connect' / 'disconnect'), name, quota, expired, status
            $isStatusOk = (bool) ($body['status'] ?? false);
            $rawStatus = strtolower((string) ($body['device_status'] ?? 'disconnect'));

            $updateData = [
                'device_status' => $rawStatus,
                'device_phone' => $body['device'] ?? $this->setting->device_phone,
                'device_name' => $body['name'] ?? $this->setting->device_name,
                'quota' => isset($body['quota']) ? (string) $body['quota'] : $this->setting->quota,
                'package' => $body['package'] ?? $this->setting->package,
                'expired' => $body['expired'] ?? $this->setting->expired,
                'last_checked_at' => now(),
            ];

            if ($token) {
                $updateData['api_token'] = $token;
            }

            $this->setting->update($updateData);

            $message = $rawStatus === 'connect'
                ? 'Perangkat WhatsApp terhubung dan siap digunakan.'
                : ($body['reason'] ?? 'Perangkat terputus. Silakan scan ulang QR code di dashboard Fonnte.');

            return [
                'success' => $isStatusOk && $rawStatus === 'connect',
                'status' => $rawStatus,
                'message' => $message,
                'data' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte checkDevice error: ' . $e->getMessage());

            $this->setting->update([
                'device_status' => 'error',
                'last_checked_at' => now(),
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Koneksi ke Fonnte gagal: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Kirim pesan WhatsApp melalui Fonnte (POST https://api.fonnte.com/send)
     */
    public function sendMessage(string $target, string $message, array $options = []): array
    {
        $token = trim((string) ($options['token'] ?? $this->setting->api_token));
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token API Fonnte belum dikonfigurasi.',
            ];
        }

        // Normalisasi nomor tujuan
        $cleanTarget = preg_replace('/[^0-9]/', '', $target);
        if (empty($cleanTarget)) {
            return [
                'success' => false,
                'message' => 'Nomor tujuan tidak valid.',
            ];
        }

        $countryCode = $options['countryCode'] ?? $this->setting->country_code ?: '62';

        $payload = [
            'target' => $cleanTarget,
            'message' => $message,
            'countryCode' => $countryCode,
        ];

        if (!empty($options['url'])) {
            $payload['url'] = $options['url'];
        }
        if (!empty($options['filename'])) {
            $payload['filename'] = $options['filename'];
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post(self::API_BASE . '/send', $payload);

            $result = $response->json();
            $isSuccess = (bool) ($result['status'] ?? false);

            if (!$isSuccess) {
                $err = $result['reason'] ?? $result['detail'] ?? 'Pengiriman gagal dari server Fonnte';
                Log::warning("Fonnte send to {$cleanTarget} failed: {$err}");

                return [
                    'success' => false,
                    'message' => $err,
                    'data' => $result,
                ];
            }

            return [
                'success' => true,
                'message' => 'Pesan berhasil dikirim ke ' . $cleanTarget,
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte sendMessage exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi WhatsApp saat pesanan masuk status Menunggu Konfirmasi (setelah upload bukti transfer)
     */
    public function sendWaitingNotification(Order $order): ?array
    {
        if (!$this->setting->is_active || !$this->setting->auto_notify_waiting || empty($order->nomor)) {
            return null;
        }

        $program = ProgramInfo::first();
        $programName = $program->nama_program ?? 'Jemaat Bali';

        // Tautan halaman menunggu konfirmasi / tracking status
        $link = ($order->jenis === 'kaos_donasi' || $order->design_id !== null)
            ? route('checkout.menunggu', $order)
            : route('shop.waiting', $order);

        $template = $this->setting->template_waiting ?: WhatsappSetting::defaultWaitingTemplate();

        $variables = [
            '{nama}' => $order->nama,
            '{nomor_pesanan}' => $order->nomor_pesanan ?: ('ORD-' . $order->id),
            '{jenis}' => $order->jenisLabel(),
            '{total}' => number_format($order->totalTagihan(), 0, ',', '.'),
            '{metode}' => $order->metode ? strtoupper($order->metode) : 'QRIS Manual',
            '{link}' => $link,
            '{nama_program}' => $programName,
        ];

        $message = str_replace(array_keys($variables), array_values($variables), $template);

        return $this->sendMessage($order->nomor, $message);
    }

    /**
     * Kirim notifikasi WhatsApp saat pembayaran telah terverifikasi
     */
    public function sendVerifiedNotification(Order|Donation $item, string $type = 'order', bool $force = false): ?array
    {
        if (empty($item->nomor)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp pemesan kosong / tidak ditemukan.',
            ];
        }

        if (empty($this->setting->api_token)) {
            return [
                'success' => false,
                'message' => 'Token API Fonnte belum diisi di menu Provider WA.',
            ];
        }

        if (!$force && !$this->setting->is_active) {
            return [
                'success' => false,
                'message' => 'Pengiriman WhatsApp otomatis sedang nonaktif di menu Provider WA.',
            ];
        }

        if (!$force && !$this->setting->auto_notify_verified) {
            return [
                'success' => false,
                'message' => 'Notifikasi otomatis terverifikasi dimatikan di menu Provider WA.',
            ];
        }

        $program = ProgramInfo::first();
        $programName = $program->nama_program ?? 'Jemaat Bali';

        $nomorPesanan = $type === 'order'
            ? ($item->nomor_pesanan ?: 'ORD-' . $item->id)
            : ($item->kode ?: 'DON-' . $item->id);

        $nominal = $type === 'order'
            ? $item->totalTagihan()
            : $item->nominal;

        $link = ($item instanceof Order)
            ? (($item->jenis === 'kaos_donasi' || $item->design_id !== null)
                ? route('checkout.menunggu', $item)
                : route('shop.waiting', $item))
            : route('home');

        $metode = ($item instanceof Order && $item->metode)
            ? strtoupper($item->metode)
            : 'QRIS Manual';

        $jenis = ($item instanceof Order)
            ? $item->jenisLabel()
            : 'Donasi Saja';

        $rincian = ($item instanceof Order)
            ? $item->rincianProduk()
            : ('Donasi Sukarela pembangunan ' . $programName);

        $alamat = ($item instanceof Order)
            ? $item->alamatLengkap()
            : '—';

        $produk = ($item instanceof Order && $item->design)
            ? $item->design->judul
            : ($item instanceof Order ? 'Kaos Merchandise' : 'Donasi');

        $ukuran = ($item instanceof Order && $item->ukuran)
            ? $item->ukuran
            : '—';

        $jumlah = ($item instanceof Order && $item->jumlah)
            ? ($item->jumlah . ' pcs')
            : '—';

        $message = $this->buildVerifiedMessage($item, $type);

        return $this->sendMessage($item->nomor, $message);
    }

    /**
     * Buat teks pesan verifikasi lengkap berdasarkan template
     */
    public function buildVerifiedMessage(Order|Donation $item, string $type = 'order'): string
    {
        $program = ProgramInfo::first();
        $programName = $program->nama_program ?? 'Jemaat Bali';

        $nomorPesanan = $type === 'order'
            ? ($item->nomor_pesanan ?: 'ORD-' . $item->id)
            : ($item->kode ?: 'DON-' . $item->id);

        $nominal = $type === 'order'
            ? $item->totalTagihan()
            : $item->nominal;

        $link = ($item instanceof Order)
            ? (($item->jenis === 'kaos_donasi' || $item->design_id !== null)
                ? route('checkout.menunggu', $item)
                : route('shop.waiting', $item))
            : route('home');

        $metode = ($item instanceof Order && $item->metode)
            ? strtoupper($item->metode)
            : 'QRIS Manual';

        $jenis = ($item instanceof Order)
            ? $item->jenisLabel()
            : 'Donasi Saja';

        $rincian = ($item instanceof Order)
            ? $item->rincianProduk()
            : ('Donasi Sukarela pembangunan ' . $programName);

        $alamat = ($item instanceof Order)
            ? $item->alamatLengkap()
            : '—';

        $produk = ($item instanceof Order && $item->design)
            ? $item->design->judul
            : ($item instanceof Order ? 'Kaos Merchandise' : 'Donasi');

        $ukuran = ($item instanceof Order && $item->ukuran)
            ? $item->ukuran
            : '—';

        $jumlah = ($item instanceof Order && $item->jumlah)
            ? ($item->jumlah . ' pcs')
            : '—';

        $template = $this->setting->template_verified ?: WhatsappSetting::defaultVerifiedTemplate();

        $variables = [
            '{nama}' => $item->nama,
            '{nomor_pesanan}' => $nomorPesanan,
            '{nominal}' => number_format($nominal, 0, ',', '.'),
            '{total}' => number_format($nominal, 0, ',', '.'),
            '{jenis}' => $jenis,
            '{produk}' => $produk,
            '{desain}' => $produk,
            '{ukuran}' => $ukuran,
            '{jumlah}' => $jumlah,
            '{rincian}' => $rincian,
            '{alamat}' => $alamat,
            '{metode}' => $metode,
            '{link}' => $link,
            '{catatan}' => $item->catatan_panitia ?: ($item->catatan ?? '—'),
            '{nama_program}' => $programName,
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Dapatkan link WhatsApp Web / direct click-to-chat
     */
    public function getDirectWaLink(Order|Donation $item, string $type = 'order'): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $item->nomor);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $message = $this->buildVerifiedMessage($item, $type);

        return 'https://api.whatsapp.com/send?phone=' . $cleanPhone . '&text=' . rawurlencode($message);
    }
}
