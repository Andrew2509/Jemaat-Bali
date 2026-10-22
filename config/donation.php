<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Minimal Nominal Donasi
    |--------------------------------------------------------------------------
    |
    | Minimal nominal (dalam rupiah) untuk setiap jenis donasi. Nilai bisa
    | ditimpa lewat file .env tanpa perlu mengubah kode.
    |
    */

    // Minimal donasi untuk paket "Pesan Kaos + Donasi"
    'min_kaos_donasi' => (int) env('MIN_DONASI_KAOS', 50000),

    // Minimal donasi untuk "Donasi Saja" (sukarela)
    'min_donasi_saja' => (int) env('MIN_DONASI_SAJA', 1000),

    // Preset nominal donasi pada form "Pesan Kaos + Donasi"
    'preset_kaos_donasi' => [50000, 100000, 250000, 500000, 1000000],

];
