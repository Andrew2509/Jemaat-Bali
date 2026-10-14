<?php

/**
 * Membuat berkas PDF proposal contoh yang valid (xref dihitung otomatis).
 * Jalankan: php scripts/make_proposal_pdf.php
 */

$target = __DIR__.'/../public/uploads/proposal/proposal-pembangunan.pdf';

if (! is_dir(dirname($target))) {
    mkdir(dirname($target), 0775, true);
}

$lines = [
    [18, 'PROPOSAL PEMBANGUNAN'],
    [12, 'Jemaat Bali'],
    ['', ''],
    [11, 'Berkas contoh untuk keperluan demonstrasi situs.'],
    [11, 'Ganti file ini dengan proposal asli Anda:'],
    [11, '  public/uploads/proposal/proposal-pembangunan.pdf'],
    ['', ''],
    [11, '1. Latar Masalah'],
    [10, '   Ruang jemaat yang terbatas dan kebutuhan kegiatan yang terus'],
    [10, '   bertambah menuntut adanya bangunan representatif.'],
    ['', ''],
    [11, '2. Tahapan Kerja'],
    [10, '   Tahap I   : pondasi, lantai dasar, dan dinding.'],
    [10, '   Tahap II  : atap, listrik, dan penyelesaian interior.'],
    [10, '   Tahap III : halaman, parkir, dan penataan taman.'],
    ['', ''],
    [11, '3. Kebutuhan Dana'],
    [10, '   Sumber pendanaan berasal dari donasi jemaat, hasil penjualan'],
    [10, '   kaos kampanye, dan bantuan para simpatisan.'],
    ['', ''],
    [11, '4. Transparansi'],
    [10, '   Progres pembangunan dipublikasikan dalam bentuk foto dan video'],
    [10, '   pada halaman Progres Pembangunan di situs ini.'],
    ['', ''],
    [10, 'Dicetak pada '.date('d F Y').'.'],
];

$stream = "BT\n";
$y = 780;
foreach ($lines as [$size, $text]) {
    if ($text === '') {
        $y -= 14;
        continue;
    }
    $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    $stream .= "/F1 {$size} Tf\n1 0 0 1 60 {$y} Tm ({$text}) Tj\n";
    $y -= ($size + 8);
}
$stream .= "ET\n";

$objects = [
    1 => "<< /Type /Catalog /Pages 2 0 R >>",
    2 => "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
    3 => "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>",
    4 => "<< /Length ".strlen($stream)." >>\nstream\n".$stream."endstream",
    5 => "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>",
];

$pdf = "%PDF-1.4\n";
$offsets = [];

foreach ($objects as $i => $body) {
    $offsets[$i] = strlen($pdf);
    $pdf .= "{$i} 0 obj\n{$body}\nendobj\n";
}

$xref = strlen($pdf);
$pdf .= "xref\n0 ".(count($objects) + 1)."\n";
$pdf .= "0000000000 65535 f \n";
foreach ($offsets as $off) {
    $pdf .= str_pad((string) $off, 10, '0', STR_PAD_LEFT)." 00000 n \n";
}
$pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";

file_put_contents($target, $pdf);

echo "OK: {$target} (".number_format(strlen($pdf))." bytes)\n";
