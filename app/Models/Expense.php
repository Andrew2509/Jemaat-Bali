<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kategori
 * @property string $nama
 * @property int $jumlah
 * @property \Illuminate\Support\Carbon|null $tanggal
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Expense extends Model
{
    protected $fillable = [
        'kategori',
        'nama',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal' => 'date',
        ];
    }
}
