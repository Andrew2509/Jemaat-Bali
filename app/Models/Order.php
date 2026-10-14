<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property string $nomor_pesanan
 * @property string $nama
 * @property string|null $nomor
 * @property string|null $email
 * @property string|null $alamat
 * @property string|null $provinsi
 * @property string|null $kabupaten
 * @property string|null $kecamatan
 * @property string|null $kode_pos
 * @property string|null $ukuran
 * @property int $jumlah
 * @property int|null $design_id
 * @property int $donasi
 * @property int $subtotal_kaos
 * @property int $ongkir
 * @property int $total
 * @property string|null $catatan
 * @property string $jenis
 * @property string|null $metode
 * @property string|null $bukti_path
 * @property string $status_pembayaran
 * @property string $status_pengiriman
 * @property string|null $resi
 * @property string|null $catatan_panitia
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $dikirim_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Design|null $design
 * @property User|null $verifier
 */
class Order extends Model
{
    protected $fillable = [
        'nomor_pesanan',
        'nama',
        'nomor',
        'email',
        'alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kode_pos',
        'ukuran',
        'jumlah',
        'design_id',
        'donasi',
        'subtotal_kaos',
        'ongkir',
        'total',
        'catatan',
        'jenis',
        'metode',
        'bukti_path',
        'status_pembayaran',
        'status_pengiriman',
        'resi',
        'catatan_panitia',
        'verified_at',
        'verified_by',
        'dikirim_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'donasi' => 'integer',
            'subtotal_kaos' => 'integer',
            'ongkir' => 'integer',
            'total' => 'integer',
            'verified_at' => 'datetime',
            'dikirim_at' => 'datetime',
        ];
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isDonasiOnly(): bool
    {
        return $this->jenis === 'donasi_saja';
    }

    public function jenisLabel(): string
    {
        if ($this->isDonasiOnly()) {
            return 'Donasi Saja';
        }

        return (int) $this->donasi > 0 ? 'Kaos + Donasi' : 'Pesan Kaos';
    }

    public function buktiUrl(): ?string
    {
        if (! $this->bukti_path) {
            return null;
        }

        return URL::signedRoute('bukti.show', ['type' => 'pesanan', 'id' => $this->id]);
    }

    public function totalTagihan(): int
    {
        return $this->subtotal_kaos + $this->ongkir + $this->donasi;
    }

    public function alamatLengkap(): string
    {
        $parts = array_filter([
            ltrim(trim($this->alamat ?? ''), ', '),
            $this->kecamatan ? 'Kec. ' . $this->kecamatan : null,
            $this->kabupaten,
            $this->provinsi,
            $this->kode_pos,
        ]);

        return !empty($parts) ? implode(', ', $parts) : '—';
    }

    public function rincianProduk(): string
    {
        if ($this->isDonasiOnly()) {
            return 'Donasi sukarela';
        }

        $desain = $this->design?->judul ?? 'Kaos Merchandise';
        $specs = array_filter([
            $this->ukuran ? "ukuran {$this->ukuran}" : null,
            $this->jumlah ? "{$this->jumlah} pcs" : null,
        ]);
        $specText = !empty($specs) ? ' (' . implode(', ', $specs) . ')' : '';

        $donasiText = (int) $this->donasi > 0
            ? ' + Donasi Rp' . number_format($this->donasi, 0, ',', '.')
            : '';

        return $desain . $specText . $donasiText;
    }
}
