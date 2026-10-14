<?php

return [

    /*
    |------------------------------------------------------------------------
    | Pesan validasi Bahasa Indonesia (hanya aturan yang dipakai situs ini)
    |------------------------------------------------------------------------
    */

    'accepted' => ':attribute harus diterima.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak tersedia.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'numeric' => ':attribute harus berupa angka.',
    'regex' => ':attribute tidak valid.',
    'string' => ':attribute harus berupa teks.',

    'required' => ':attribute wajib diisi.',
    'nullable' => ':attribute wajib diisi.',

    'in' => ':attribute yang dipilih tidak valid.',
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],

    'custom' => [
        'donasi' => [
            'min' => 'Nominal donasi minimal Rp '.number_format(1000, 0, ',', '.').'.',
        ],
    ],

    'attributes' => [
        'nama' => 'Nama',
        'nomor' => 'Nomor (HP/WA)',
        'email' => 'Email',
        'alamat' => 'Alamat lengkap',
        'provinsi' => 'Provinsi',
        'design_id' => 'Desain kaos',
        'ukuran' => 'Ukuran kaos',
        'jumlah' => 'Jumlah baju',
        'donasi' => 'Nominal donasi',
        'catatan' => 'Catatan',
    ],

];
