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
        'kelurahan',
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
        'items',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
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

    protected static function booted(): void
    {
        static::ensureKelurahanColumn();
    }

    public static function ensureKelurahanColumn(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('orders') && ! \Illuminate\Support\Facades\Schema::hasColumn('orders', 'kelurahan')) {
                \Illuminate\Support\Facades\Schema::table('orders', function ($table) {
                    $table->string('kelurahan', 120)->nullable()->after('kecamatan');
                });
            }
        } catch (\Throwable $e) {
            // Ignore if permission or already exists
        }
    }

    public function totalTagihan(): int
    {
        return $this->subtotal_kaos + $this->ongkir + $this->donasi;
    }

    public function metodeLabel(): string
    {
        if (empty($this->metode)) {
            return 'Transfer Bank';
        }

        return strtolower($this->metode) === 'qris' ? 'QRIS' : 'Transfer Bank';
    }

    /**
     * Return items array or fallback to single legacy item.
     *
     * @return array<array{design_id: int|null, nama: string, ukuran: string|null, jumlah: int, harga: int, subtotal: int}>
     */
    public function getItemsListAttribute(): array
    {
        if (is_array($this->items) && count($this->items) > 0) {
            return $this->items;
        }

        if ($this->isDonasiOnly()) {
            return [];
        }

        return [[
            'design_id' => $this->design_id,
            'nama' => $this->design?->nama ?? 'Kaos Merchandise',
            'ukuran' => $this->ukuran,
            'jumlah' => (int) ($this->jumlah ?: 1),
            'harga' => $this->jumlah > 0 ? (int) ($this->subtotal_kaos / $this->jumlah) : (int) $this->subtotal_kaos,
            'subtotal' => (int) $this->subtotal_kaos,
        ]];
    }

    /**
     * Pesanan yang sudah dikirim tidak boleh dihapus dari panel daftar/detil.
     */
    public function canBeDeleted(): bool
    {
        return $this->status_pengiriman !== 'terkirim';
    }

    public function alamatLengkap(): string
    {
        $parts = array_filter([
            ltrim(trim($this->alamat ?? ''), ', '),
            $this->kelurahan ? 'Kel./Desa ' . $this->kelurahan : null,
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

        $parts = [];
        foreach ($this->items_list as $item) {
            $gender = ! empty($item['gender']) ? ucfirst($item['gender']) : null;
            $parts[] = "{$item['nama']} ({$item['ukuran']}" . ($gender ? ", {$gender}" : '') . ", {$item['jumlah']} pcs)";
        }
        $res = implode('; ', $parts);

        if ((int) $this->donasi > 0) {
            $res .= ' + Donasi Rp' . number_format($this->donasi, 0, ',', '.');
        }

        return $res;
    }
}
