<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $nomor
 * @property string|null $email
 * @property int $nominal
 * @property \Illuminate\Support\Carbon|null $tanggal
 * @property string|null $catatan
 * @property string|null $metode
 * @property string|null $bukti_path
 * @property string $status
 * @property string|null $catatan_panitia
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Donation extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'nomor',
        'email',
        'nominal',
        'tanggal',
        'catatan',
        'metode',
        'bukti_path',
        'status',
        'catatan_panitia',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'tanggal' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function buktiUrl(): ?string
    {
        if (! $this->bukti_path) {
            return null;
        }

        return URL::signedRoute('bukti.show', ['type' => 'donasi', 'id' => $this->id]);
    }

    public function scopeTerverifikasi($query)
    {
        return $query->where('status', 'terverifikasi');
    }

    public function metodeLabel(): string
    {
        if (empty($this->metode)) {
            return 'Transfer Bank';
        }

        return strtolower($this->metode) === 'qris' ? 'QRIS' : 'Transfer Bank';
    }
}
