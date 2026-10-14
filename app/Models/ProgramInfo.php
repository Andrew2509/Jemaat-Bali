<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramInfo extends Model
{
    protected $table = 'program_info';

    protected $fillable = [
        'nama_program',
        'deskripsi',
        'whatsapp',
        'email',
        'judul_proposal',
    ];

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
}
