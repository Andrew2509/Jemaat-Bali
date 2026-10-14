<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $provider
 * @property string|null $api_token
 * @property string|null $device_phone
 * @property string|null $device_name
 * @property string|null $device_status
 * @property string|null $quota
 * @property string|null $package
 * @property string|null $expired
 * @property string $country_code
 * @property bool $is_active
 * @property bool $auto_notify_order
 * @property bool $auto_notify_verified
 * @property string|null $template_order
 * @property string|null $template_verified
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WhatsappSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'provider',
        'api_token',
        'device_phone',
        'device_name',
        'device_status',
        'quota',
        'package',
        'expired',
        'country_code',
        'is_active',
        'auto_notify_order',
        'auto_notify_waiting',
        'auto_notify_verified',
        'template_order',
        'template_waiting',
        'template_verified',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'auto_notify_order' => 'boolean',
            'auto_notify_waiting' => 'boolean',
            'auto_notify_verified' => 'boolean',
            'last_checked_at' => 'datetime',
        ];
    }

    public static function defaultOrderTemplate(): string
    {
        return "Halo *{nama}*,\n\n"
            . "Terima kasih telah berpartisipasi dalam program *{nama_program}*.\n"
            . "Pesanan Anda dengan nomor *#{nomor_pesanan}* telah kami terima.\n\n"
            . "*Rincian:*\n"
            . "• Jenis: {jenis}\n"
            . "• Total: Rp{total}\n"
            . "• Metode: {metode}\n\n"
            . "Silakan selesaikan pembayaran dan unggah bukti transfer melalui tautan berikut:\n"
            . "{link}\n\n"
            . "Tuhan memberkati.\n"
            . "Panitia {nama_program}";
    }

    public static function defaultWaitingTemplate(): string
    {
        return "Halo *{nama}*,\n\n"
            . "Bukti pembayaran untuk pesanan *#{nomor_pesanan}* sebesar *Rp{total}* telah kami terima.\n"
            . "Status pesanan Anda saat ini: *MENUNGGU KONFIRMASI*.\n\n"
            . "*Rincian Pesanan:*\n"
            . "• Nomor: #{nomor_pesanan}\n"
            . "• Rincian: {jenis}\n"
            . "• Total: Rp{total}\n"
            . "• Metode: {metode}\n\n"
            . "Panitia sedang memeriksa dan memverifikasi pembayaran Anda secara manual. Anda dapat memantau status pesanan kapan saja melalui tautan:\n"
            . "{link}\n\n"
            . "Terima kasih atas partisipasi dan dukungannya bagi kelancaran program *{nama_program}*.\n\n"
            . "Tuhan senantiasa memberkati.\n"
            . "Panitia {nama_program}";
    }

    public static function defaultVerifiedTemplate(): string
    {
        return "Halo *{nama}*,\n\n"
            . "Puji Tuhan! Pembayaran Anda untuk pesanan *#{nomor_pesanan}* sebesar *Rp{nominal}* telah *TERVERIFIKASI* oleh panitia *{nama_program}*.\n\n"
            . "*Rincian Pesanan:*\n"
            . "• Nomor: #{nomor_pesanan}\n"
            . "• Rincian: {rincian}\n"
            . "• Total: Rp{nominal}\n"
            . "• Metode: {metode}\n"
            . "• Status: Terverifikasi (Siap diproses)\n"
            . "• Alamat Kirim: {alamat}\n\n"
            . "Pesanan Anda sedang dipersiapkan oleh panitia. Anda dapat memantau status pesanan kapan saja melalui tautan:\n"
            . "{link}\n\n"
            . "Terima kasih banyak atas partisipasi dan dukungan kasih Anda bagi kelancaran program *{nama_program}*. Semoga Tuhan senantiasa memberkati Anda sekeluarga!\n\n"
            . "Salam kasih,\n"
            . "Panitia {nama_program}";
    }

    public static function getSetting(): self
    {
        $setting = static::first();
        if (!$setting) {
            $setting = static::create([
                'provider' => 'fonnte',
                'api_token' => null,
                'country_code' => '62',
                'is_active' => false,
                'auto_notify_order' => true,
                'auto_notify_waiting' => true,
                'auto_notify_verified' => true,
                'template_order' => static::defaultOrderTemplate(),
                'template_waiting' => static::defaultWaitingTemplate(),
                'template_verified' => static::defaultVerifiedTemplate(),
            ]);
        } else {
            $updates = [];
            if (empty($setting->template_waiting)) {
                $updates['auto_notify_waiting'] = true;
                $updates['template_waiting'] = static::defaultWaitingTemplate();
            }
            if (empty($setting->template_verified) || !str_contains($setting->template_verified, '{rincian}')) {
                $updates['template_verified'] = static::defaultVerifiedTemplate();
            }
            if (!empty($updates)) {
                $setting->update($updates);
            }
        }

        return $setting;
    }

    public function isConnected(): bool
    {
        return strtolower((string) $this->device_status) === 'connect';
    }

    public function isConfigured(): bool
    {
        return !empty(trim((string) $this->api_token));
    }
}
