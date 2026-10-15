<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinancialReportExcelService
{
    // Color Palette Berdasarkan Identitas Jemaat Bali
    private const COLOR_PRIMARY = 'B55322';       // Terracotta hangat
    private const COLOR_DARK = '292524';          // Charcoal gelap
    private const COLOR_FOREST = '444D41';        // Hijau sage / hutan
    private const COLOR_LIGHT_BG = 'F8F6F0';      // Ivory lembut
    private const COLOR_ZEBRA = 'FAFAFA';         // Baris selang-seling
    private const COLOR_BORDER = 'E7E5E4';        // Border abu-abu tipis
    private const COLOR_BORDER_DARK = 'A8A29E';   // Border penutup tabel
    private const COLOR_CARD_BG = 'F5F2EB';       // Background kartu ringkasan
    private const COLOR_GREEN_BG = 'ECFDF5';      // Hijau saldo positif
    private const COLOR_GREEN_TEXT = '047857';

    // Format Accounting Rupiah Excel resmi
    private const FORMAT_CURRENCY = '"Rp "#,##0;[Red]-"Rp "#,##0;"Rp 0"';
    private const FORMAT_DATE = 'DD/MM/YYYY';

    public function generate(string $from, string $to, string $jenis = ''): Spreadsheet
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $fromStr = $fromDate->format('Y-m-d');
        $toStr = $toDate->format('Y-m-d');

        // Ambil Data Terverifikasi
        $donasiQuery = Donation::where('status', 'terverifikasi')->whereBetween('tanggal', [$fromStr, $toStr]);
        $orderQuery = Order::where('status_pembayaran', 'terverifikasi')->whereBetween('created_at', [$fromStr . ' 00:00:00', $toStr . ' 23:59:59'])->whereNotIn('jenis', ['donasi_saja']);
        $expenseQuery = Expense::whereBetween('tanggal', [$fromStr, $toStr]);
        $donasiSajaOrders = Order::where('jenis', 'donasi_saja')
            ->where('status_pembayaran', 'terverifikasi')
            ->whereBetween('created_at', [$fromStr . ' 00:00:00', $toStr . ' 23:59:59'])
            ->get();

        if ($jenis === 'donasi') {
            $donasis = $donasiQuery->orderBy('tanggal')->get();
            $orders = collect();
            $expenses = $expenseQuery->whereNotIn('kategori', ['Produksi kaos'])->orderBy('tanggal')->get();
        } elseif ($jenis === 'kaos') {
            $donasis = collect();
            $orders = $orderQuery->orderBy('created_at')->get();
            $expenses = $expenseQuery->whereIn('kategori', ['Produksi kaos', 'Pengiriman'])->orderBy('tanggal')->get();
        } else {
            $donasis = $donasiQuery->orderBy('tanggal')->get();
            $orders = $orderQuery->orderBy('created_at')->get();
            $expenses = $expenseQuery->orderBy('tanggal')->get();
        }

        // Kalkulasi Metrik
        $totalDonasi = $donasis->sum('nominal') + $donasiSajaOrders->sum('total');
        $omzetKaos = $orders->sum('subtotal_kaos');
        $donasiDariKaos = $orders->sum('donasi');
        $ongkir = $orders->sum('ongkir') > 0 ? $orders->sum('ongkir') : ($expenses->where('kategori', 'Pengiriman')->sum('jumlah') ?: 0);
        if ($jenis === 'donasi') {
            $ongkir = 0;
        }

        $biayaProduksi = $expenses->where('kategori', 'Produksi kaos')->sum('jumlah');
        $biayaPengiriman = $expenses->where('kategori', 'Pengiriman')->sum('jumlah');
        $danaBersihKaos = max(0, $omzetKaos - $biayaProduksi);
        $belanjaPembangunan = $expenses->reject(fn($e) => in_array($e->kategori, ['Produksi kaos', 'Pengiriman']))->sum('jumlah');
        $danaPembangunan = $totalDonasi + $danaBersihKaos;
        $saldoPembangunan = $danaPembangunan - $belanjaPembangunan;

        $totalPenerimaan = $totalDonasi + $omzetKaos + $donasiDariKaos + $ongkir;
        $totalPengeluaran = $expenses->sum('jumlah');
        $saldoKasTotal = $totalPenerimaan - $totalPengeluaran;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sistem Informasi Jemaat Bali')
            ->setLastModifiedBy('Admin Jemaat Bali')
            ->setTitle('Laporan Keuangan Dana Pembangunan Jemaat Bali')
            ->setSubject('Laporan Keuangan & Pengeluaran')
            ->setDescription("Laporan keuangan periode {$fromDate->translatedFormat('d F Y')} s/d {$toDate->translatedFormat('d F Y')}");

        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);

        // 1. Sheet Ringkasan Eksekutif
        $sheetRingkasan = $spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan Laporan');
        $this->buildSheetRingkasan($sheetRingkasan, $fromDate, $toDate, [
            'totalDonasi' => $totalDonasi,
            'omzetKaos' => $omzetKaos,
            'donasiDariKaos' => $donasiDariKaos,
            'ongkir' => $ongkir,
            'totalPenerimaan' => $totalPenerimaan,
            'biayaProduksi' => $biayaProduksi,
            'biayaPengiriman' => $biayaPengiriman,
            'danaBersihKaos' => $danaBersihKaos,
            'belanjaPembangunan' => $belanjaPembangunan,
            'danaPembangunan' => $danaPembangunan,
            'saldoPembangunan' => $saldoPembangunan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKasTotal' => $saldoKasTotal,
        ]);

        // 2. Sheet Buku Kas (Arus Kas Masuk & Keluar)
        $sheetBukuKas = $spreadsheet->createSheet();
        $sheetBukuKas->setTitle('Buku Kas (Arus Kas)');
        $this->buildSheetBukuKas($sheetBukuKas, $fromDate, $toDate, $donasis, $orders, $expenses, $donasiSajaOrders);

        // 3. Sheet Rincian Donasi
        $sheetDonasi = $spreadsheet->createSheet();
        $sheetDonasi->setTitle('Rincian Donasi');
        $this->buildSheetDonasi($sheetDonasi, $fromDate, $toDate, $donasis, $donasiSajaOrders);

        // 4. Sheet Rincian Penjualan Kaos
        $sheetKaos = $spreadsheet->createSheet();
        $sheetKaos->setTitle('Rincian Pesanan Kaos');
        $this->buildSheetKaos($sheetKaos, $fromDate, $toDate, $orders);

        // 5. Sheet Rincian Pengeluaran
        $sheetPengeluaran = $spreadsheet->createSheet();
        $sheetPengeluaran->setTitle('Rincian Pengeluaran');
        $this->buildSheetPengeluaran($sheetPengeluaran, $fromDate, $toDate, $expenses);

        // Set sheet pertama aktif saat dibuka di Excel
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildSheetRingkasan(Worksheet $sheet, Carbon $fromDate, Carbon $toDate, array $metrics): void
    {
        $sheet->setShowGridLines(true);

        // Header Dokumen
        $sheet->setCellValue('A2', 'JEMAAT BALI — ADMINISTRASI KEUANGAN & PEMBANGUNAN');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setBold(true)->getColor()->setRGB(self::COLOR_PRIMARY);

        $sheet->setCellValue('A3', 'LAPORAN KEUANGAN DANA PEMBANGUNAN');
        $sheet->getStyle('A3')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);

        $sheet->setCellValue('A4', 'Periode Laporan: ' . $fromDate->translatedFormat('d F Y') . ' s/d ' . $toDate->translatedFormat('d F Y'));
        $sheet->getStyle('A4')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('78716C');

        $sheet->setCellValue('A5', 'Waktu Unduh: ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WITA | Status: Data Terverifikasi');
        $sheet->getStyle('A5')->getFont()->setSize(9)->getColor()->setRGB('A8A29E');

        // 4 KARTU STATISTIK (Baris 7-9)
        // Kartu 1: Total Penerimaan
        $sheet->mergeCells('A7:B7');
        $sheet->mergeCells('A8:B8');
        $sheet->mergeCells('A9:B9');
        $sheet->setCellValue('A7', 'TOTAL PENERIMAAN KAS');
        $sheet->setCellValue('A8', $metrics['totalPenerimaan']);
        $sheet->setCellValue('A9', 'Donasi + Omzet Kaos + Ongkir');

        // Kartu 2: Belanja Pembangunan
        $sheet->mergeCells('C7:D7');
        $sheet->mergeCells('C8:D8');
        $sheet->mergeCells('C9:D9');
        $sheet->setCellValue('C7', 'BELANJA PEMBANGUNAN');
        $sheet->setCellValue('C8', $metrics['belanjaPembangunan']);
        $sheet->setCellValue('C9', 'Material & Upah Pekerja');

        // Kartu 3: Total Semua Pengeluaran
        $sheet->mergeCells('E7:F7');
        $sheet->mergeCells('E8:F8');
        $sheet->mergeCells('E9:F9');
        $sheet->setCellValue('E7', 'TOTAL PENGELUARAN KAS');
        $sheet->setCellValue('E8', $metrics['totalPengeluaran']);
        $sheet->setCellValue('E9', 'Belanja + Produksi + Ongkir');

        // Kartu 4: Saldo Akhir Pembangunan (Highlight)
        $sheet->mergeCells('G7:H7');
        $sheet->mergeCells('G8:H8');
        $sheet->mergeCells('G9:H9');
        $sheet->setCellValue('G7', 'SALDO AKHIR PEMBANGUNAN');
        $sheet->setCellValue('G8', $metrics['saldoPembangunan']);
        $sheet->setCellValue('G9', 'Siap digunakan untuk tahap fisik');

        // Styling Kartu
        foreach (['A', 'C', 'E'] as $col) {
            $endCol = chr(ord($col) + 1);
            $range = "{$col}7:{$endCol}9";
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_CARD_BG);
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            
            $sheet->getStyle("{$col}7")->getFont()->setSize(8)->setBold(true)->getColor()->setRGB('78716C');
            $sheet->getStyle("{$col}8")->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);
            $sheet->getStyle("{$col}8")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);
            $sheet->getStyle("{$col}9")->getFont()->setSize(8)->setItalic(true)->getColor()->setRGB('A8A29E');
            $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Highlight Kartu 4 (Saldo Pembangunan) dengan warna Forest Green elegan
        $sheet->getStyle('G7:H9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_FOREST);
        $sheet->getStyle('G7:H9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_FOREST);
        $sheet->getStyle('G7')->getFont()->setSize(8)->setBold(true)->getColor()->setRGB('E2E8F0');
        $sheet->getStyle('G8')->getFont()->setSize(15)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('G8')->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);
        $sheet->getStyle('G9')->getFont()->setSize(8)->setItalic(true)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle('G7:H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // TABEL 1: RINCIAN ALOKASI DANA PEMBANGUNAN (Baris 11-20)
        $sheet->setCellValue('A11', '1. REKAPITULASI DANA PEMBANGUNAN (KHUSUS PEMBANGUNAN)');
        $sheet->getStyle('A11')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB(self::COLOR_PRIMARY);

        $sheet->setCellValue('A12', 'Komponen Dana Pembangunan');
        $sheet->setCellValue('D12', 'Jumlah (Rp)');
        $sheet->setCellValue('E12', 'Keterangan');
        $sheet->mergeCells('A12:C12');
        $sheet->mergeCells('E12:H12');

        $sheet->getStyle('A12:H12')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A12:H12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle('A12:H12')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $rowDana = [
            ['Donasi Terverifikasi', $metrics['totalDonasi'], 'Penerimaan murni dari para donatur untuk pembangunan'],
            ['Omzet Penjualan Kaos Fundraiser', $metrics['omzetKaos'], 'Total penjualan kaos program pembangunan'],
            ['Biaya Produksi Kaos (HPP Kaos)', -$metrics['biayaProduksi'], 'Biaya kain, sablon, konveksi kaos fundraiser'],
            ['Dana Bersih Kaos untuk Pembangunan', $metrics['danaBersihKaos'], 'Omzet kaos dikurangi biaya produksi kaos'],
            ['SUBTOTAL DANA PEMBANGUNAN TERKUMPUL', $metrics['danaPembangunan'], 'Total Donasi + Dana Bersih Kaos'],
            ['Belanja Pembangunan (Fisik, Material, Upah)', -$metrics['belanjaPembangunan'], 'Pengeluaran semen, pasir, besi, upah tukang, dll.'],
            ['SALDO AKHIR DANA PEMBANGUNAN', $metrics['saldoPembangunan'], 'Dana bersih yang tersisa untuk kelanjutan pembangunan fisik'],
        ];

        $r = 13;
        foreach ($rowDana as $idx => $item) {
            $sheet->mergeCells("A{$r}:C{$r}");
            $sheet->mergeCells("E{$r}:H{$r}");
            $sheet->setCellValue("A{$r}", $item[0]);
            $sheet->setCellValue("D{$r}", $item[1]);
            $sheet->setCellValue("E{$r}", $item[2]);

            $sheet->getStyle("D{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);
            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Row zebra / highlight
            if ($idx === 4) { // Subtotal Terkumpul
                $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            } elseif ($idx === 6) { // Saldo Akhir Pembangunan
                $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ECFDF5');
                $sheet->getStyle("D{$r}")->getFont()->getColor()->setRGB(self::COLOR_GREEN_TEXT);
            } elseif ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        // TABEL 2: REKAPITULASI ARUS KAS GLOBAL (Semua Penerimaan vs Pengeluaran)
        $r += 2;
        $sheet->setCellValue("A{$r}", '2. REKAPITULASI ARUS KAS PROGRAM SECARA KESELURUHAN');
        $sheet->getStyle("A{$r}")->getFont()->setSize(11)->setBold(true)->getColor()->setRGB(self::COLOR_PRIMARY);
        $r++;

        $sheet->setCellValue("A{$r}", 'Kategori Arus Kas');
        $sheet->setCellValue("D{$r}", 'Jumlah (Rp)');
        $sheet->setCellValue("E{$r}", 'Catatan');
        $sheet->mergeCells("A{$r}:C{$r}");
        $sheet->mergeCells("E{$r}:H{$r}");

        $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle("A{$r}:H{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $r++;

        $rowKas = [
            ['Penerimaan: Donasi Jemaat', $metrics['totalDonasi'], 'Pemasukan donasi terverifikasi'],
            ['Penerimaan: Penjualan Kaos & Donasi Kaos', $metrics['omzetKaos'] + $metrics['donasiDariKaos'], 'Total penerimaan dari transaksi kaos'],
            ['Penerimaan: Ongkos Kirim Kaos (Prabayar)', $metrics['ongkir'], 'Ongkir dari pemesan kaos untuk diteruskan ke ekspedisi'],
            ['TOTAL PENERIMAAN KAS (A)', $metrics['totalPenerimaan'], 'Total seluruh uang masuk ke rekening'],
            ['Pengeluaran: Belanja Pembangunan', -$metrics['belanjaPembangunan'], 'Material semen, pasir, bata, besi, upah tukang, dll.'],
            ['Pengeluaran: Biaya Produksi Kaos', -$metrics['biayaProduksi'], 'Biaya cetak dan jahit kaos'],
            ['Pengeluaran: Biaya Pengiriman / Ekspedisi', -$metrics['biayaPengiriman'], 'Biaya ekspedisi pengiriman paket kaos'],
            ['TOTAL PENGELUARAN KAS (B)', -$metrics['totalPengeluaran'], 'Total seluruh uang keluar dari kas'],
            ['SISA SALDO KAS TUNAI / BANK (A - B)', $metrics['saldoKasTotal'], 'Uang kas fisik/bank yang saat ini tersedia secara riil'],
        ];

        foreach ($rowKas as $idx => $item) {
            $sheet->mergeCells("A{$r}:C{$r}");
            $sheet->mergeCells("E{$r}:H{$r}");
            $sheet->setCellValue("A{$r}", $item[0]);
            $sheet->setCellValue("D{$r}", $item[1]);
            $sheet->setCellValue("E{$r}", $item[2]);

            $sheet->getStyle("D{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);
            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            if ($idx === 3 || $idx === 7) {
                $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            } elseif ($idx === 8) {
                $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
                $sheet->getStyle("D{$r}")->getFont()->getColor()->setRGB('92400E');
            } elseif ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        // Tanda Tangan / Verifikasi Dokumen
        $r += 2;
        $sheet->mergeCells("E{$r}:H{$r}");
        $sheet->setCellValue("E{$r}", 'Denpasar, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $r++;

        $sheet->mergeCells("E{$r}:H{$r}");
        $sheet->setCellValue("E{$r}", 'Mengetahui, Pengurus Pembangunan Jemaat Bali');
        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$r}")->getFont()->setBold(true);

        $r += 4;
        $sheet->mergeCells("E{$r}:H{$r}");
        $sheet->setCellValue("E{$r}", '( Bendahara / Panitia Pembangunan )');
        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$r}")->getFont()->setBold(true);

        // Lebar Kolom Lembar Ringkasan
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(22);
    }

    private function buildSheetBukuKas(Worksheet $sheet, Carbon $fromDate, Carbon $toDate, $donasis, $orders, $expenses, $donasiSajaOrders = null): void
    {
        $sheet->setShowGridLines(true);

        // Header Lembar
        $sheet->setCellValue('A2', 'BUKU KAS UMUM (ARUS KAS MASUK & KELUAR)');
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);

        $sheet->setCellValue('A3', 'Periode: ' . $fromDate->translatedFormat('d F Y') . ' s/d ' . $toDate->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('78716C');

        // Header Kolom Tabel
        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal',
            'C5' => 'No. Referensi / Bukti',
            'D5' => 'Jenis Transaksi',
            'E5' => 'Kategori',
            'F5' => 'Uraian / Keterangan',
            'G5' => 'Penerimaan (Debit)',
            'H5' => 'Pengeluaran (Kredit)',
            'I5' => 'Saldo Kas (Rp)',
            'J5' => 'Catatan Tambahan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:J5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A5:J5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle('A5:J5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Gabungkan dan urutkan transaksi secara kronologis
        $transactions = collect();

        foreach ($donasis as $d) {
            $transactions->push([
                'tanggal' => $d->tanggal ? Carbon::parse($d->tanggal) : Carbon::parse('2026-07-01'),
                'ref' => $d->kode,
                'jenis' => 'Donasi',
                'kategori' => 'Penerimaan Donasi',
                'uraian' => 'Donasi dari ' . $d->nama,
                'debit' => (int) $d->nominal,
                'kredit' => 0,
                'catatan' => $d->catatan ?: ($d->metode_pembayaran ? 'Via ' . $d->metode_pembayaran : '-'),
            ]);
        }

        foreach ($donasiSajaOrders ?? [] as $o) {
            $transactions->push([
                'tanggal' => $o->created_at ? Carbon::parse($o->created_at) : Carbon::parse('2026-07-01'),
                'ref' => $o->nomor_pesanan,
                'jenis' => 'Donasi',
                'kategori' => 'Penerimaan Donasi',
                'uraian' => 'Donasi dari ' . $o->nama,
                'debit' => (int) $o->total,
                'kredit' => 0,
                'catatan' => $o->catatan ?: ($o->metode ? 'Via ' . $o->metode : '-'),
            ]);
        }

        foreach ($orders as $o) {
            $total = (int) $o->totalTagihan();
            $itemDesc = $o->design?->nama ?? 'Kaos Jemaat';
            $transactions->push([
                'tanggal' => $o->created_at ? Carbon::parse($o->created_at) : Carbon::parse('2026-07-01'),
                'ref' => $o->nomor_pesanan,
                'jenis' => 'Pesanan Kaos',
                'kategori' => 'Penjualan Kaos',
                'uraian' => "Pembayaran pesanan {$o->nama} ({$itemDesc}, uk. {$o->ukuran}, {$o->jumlah} pcs)",
                'debit' => $total,
                'kredit' => 0,
                'catatan' => $o->donasi > 0 ? 'Termasuk donasi Rp' . number_format($o->donasi, 0, ',', '.') : '-',
            ]);
        }

        foreach ($expenses as $e) {
            $transactions->push([
                'tanggal' => $e->tanggal ? Carbon::parse($e->tanggal) : Carbon::parse('2026-07-01'),
                'ref' => 'EXP-' . str_pad($e->id, 4, '0', STR_PAD_LEFT),
                'jenis' => 'Pengeluaran',
                'kategori' => $e->kategori,
                'uraian' => $e->nama,
                'debit' => 0,
                'kredit' => (int) $e->jumlah,
                'catatan' => $e->keterangan ?: '-',
            ]);
        }

        $sorted = $transactions->sortBy('tanggal')->values();

        $r = 6;
        $runningBalance = 0;

        foreach ($sorted as $idx => $t) {
            $runningBalance += ($t['debit'] - $t['kredit']);

            $sheet->setCellValue("A{$r}", $idx + 1);
            $sheet->setCellValue("B{$r}", $t['tanggal']->format('d/m/Y'));
            $sheet->setCellValue("C{$r}", $t['ref']);
            $sheet->setCellValue("D{$r}", $t['jenis']);
            $sheet->setCellValue("E{$r}", $t['kategori']);
            $sheet->setCellValue("F{$r}", $t['uraian']);
            $sheet->setCellValue("G{$r}", $t['debit'] > 0 ? $t['debit'] : 0);
            $sheet->setCellValue("H{$r}", $t['kredit'] > 0 ? $t['kredit'] : 0);
            
            // Formula Saldo Berjalan Excel
            if ($r === 6) {
                $sheet->setCellValue("I{$r}", "=G{$r}-H{$r}");
            } else {
                $prev = $r - 1;
                $sheet->setCellValue("I{$r}", "=I{$prev}+G{$r}-H{$r}");
            }

            $sheet->setCellValue("J{$r}", $t['catatan']);

            // Formatting
            $sheet->getStyle("A{$r}:E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("G{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("G{$r}:I{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            // Zebra styling
            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:J{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            // Kategori Badge Color Subtle
            if ($t['jenis'] === 'Donasi') {
                $sheet->getStyle("D{$r}")->getFont()->getColor()->setRGB(self::COLOR_GREEN_TEXT);
            } elseif ($t['jenis'] === 'Pesanan Kaos') {
                $sheet->getStyle("D{$r}")->getFont()->getColor()->setRGB(self::COLOR_PRIMARY);
            } else {
                $sheet->getStyle("D{$r}")->getFont()->getColor()->setRGB('DC2626');
            }

            $sheet->getStyle("A{$r}:J{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        // Baris Total Akhir Buku Kas
        $lastDataRow = $r - 1;
        if ($sorted->count() > 0) {
            $sheet->mergeCells("A{$r}:F{$r}");
            $sheet->setCellValue("A{$r}", 'TOTAL KESELURUHAN (PERIODE INI)');
            $sheet->setCellValue("G{$r}", "=SUM(G6:G{$lastDataRow})");
            $sheet->setCellValue("H{$r}", "=SUM(H6:H{$lastDataRow})");
            $sheet->setCellValue("I{$r}", "=G{$r}-H{$r}");
            $sheet->setCellValue("J{$r}", 'Saldo Kas Akhir');

            $sheet->getStyle("A{$r}:J{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}:J{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$r}:I{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);
            
            $sheet->getStyle("A{$r}:J{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER_DARK);
            $sheet->getStyle("A{$r}:J{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB(self::COLOR_BORDER_DARK);
            $sheet->getStyle("A{$r}:J{$r}")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $sheet->getStyle("A{$r}:J{$r}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
        }

        // Freeze Panes & Auto Column Widths
        $sheet->freezePane('A6');
        $this->autoFitColumns($sheet, [
            'A' => 6,
            'B' => 13,
            'C' => 18,
            'D' => 15,
            'E' => 20,
            'F' => 38,
            'G' => 18,
            'H' => 18,
            'I' => 18,
            'J' => 25,
        ]);
    }

    private function buildSheetDonasi(Worksheet $sheet, Carbon $fromDate, Carbon $toDate, $donasis, $donasiSajaOrders = null): void
    {
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A2', 'RINCIAN PENERIMAAN DONASI TERVERIFIKASI');
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);

        $sheet->setCellValue('A3', 'Periode: ' . $fromDate->translatedFormat('d F Y') . ' s/d ' . $toDate->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('78716C');

        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal Donasi',
            'C5' => 'Kode Donasi',
            'D5' => 'Nama Donatur',
            'E5' => 'No. Telepon / WhatsApp',
            'F5' => 'Metode Pembayaran',
            'G5' => 'Nominal (Rp)',
            'H5' => 'Pesan / Catatan Doa',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle('A5:H5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $allDonasi = $donasis->merge(
            collect($donasiSajaOrders ?? [])->map(fn($o) => (object) [
                'tanggal' => $o->created_at ? Carbon::parse($o->created_at) : Carbon::parse('2026-01-01'),
                'kode' => $o->nomor_pesanan,
                'nama' => $o->nama,
                'telepon' => $o->nomor,
                'metode_pembayaran' => $o->metode,
                'nominal' => (int) $o->total,
                'catatan' => $o->catatan,
            ])
        );

        $r = 6;
        foreach ($allDonasi->sortBy('tanggal') as $idx => $d) {
            $sheet->setCellValue("A{$r}", $idx + 1);
            $sheet->setCellValue("B{$r}", $d->tanggal ? Carbon::parse($d->tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$r}", $d->kode);
            $sheet->setCellValue("D{$r}", $d->nama);
            $sheet->setCellValue("E{$r}", $d->telepon ?: '-');
            $sheet->setCellValue("F{$r}", strtoupper($d->metode_pembayaran ?: 'TRANSFER'));
            $sheet->setCellValue("G{$r}", (int) $d->nominal);
            $sheet->setCellValue("H{$r}", $d->catatan ?: '—');

            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$r}:E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("G{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        $lastDataRow = $r - 1;
        if ($allDonasi->count() > 0) {
            $sheet->mergeCells("A{$r}:F{$r}");
            $sheet->setCellValue("A{$r}", 'TOTAL PENERIMAAN DONASI');
            $sheet->setCellValue("G{$r}", "=SUM(G6:G{$lastDataRow})");
            $sheet->setCellValue("H{$r}", $allDonasi->count() . ' donatur');

            $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER_DARK);
            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB(self::COLOR_BORDER_DARK);
        }

        $sheet->freezePane('A6');
        $this->autoFitColumns($sheet, [
            'A' => 6,
            'B' => 14,
            'C' => 16,
            'D' => 25,
            'E' => 18,
            'F' => 18,
            'G' => 20,
            'H' => 35,
        ]);
    }

    private function buildSheetKaos(Worksheet $sheet, Carbon $fromDate, Carbon $toDate, $orders): void
    {
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A2', 'RINCIAN PENJUALAN KAOS FUNDRAISER TERVERIFIKASI');
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);

        $sheet->setCellValue('A3', 'Periode: ' . $fromDate->translatedFormat('d F Y') . ' s/d ' . $toDate->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('78716C');

        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal Pesan',
            'C5' => 'No. Pesanan',
            'D5' => 'Nama Pemesan',
            'E5' => 'WhatsApp / HP',
            'F5' => 'Desain Kaos',
            'G5' => 'Ukuran',
            'H5' => 'Qty',
            'I5' => 'Subtotal Kaos (Rp)',
            'J5' => 'Donasi Tambahan (Rp)',
            'K5' => 'Ongkir (Rp)',
            'L5' => 'Total Tagihan (Rp)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:L5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A5:L5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle('A5:L5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:L5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $r = 6;
        foreach ($orders as $idx => $o) {
            $sheet->setCellValue("A{$r}", $idx + 1);
            $sheet->setCellValue("B{$r}", $o->created_at ? $o->created_at->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$r}", $o->nomor_pesanan);
            $sheet->setCellValue("D{$r}", $o->nama);
            $sheet->setCellValue("E{$r}", $o->telepon ?: '-');
            $sheet->setCellValue("F{$r}", $o->design?->nama ?? 'Kaos Jemaat');
            $sheet->setCellValue("G{$r}", strtoupper($o->ukuran ?: '-'));
            $sheet->setCellValue("H{$r}", (int) $o->jumlah);
            $sheet->setCellValue("I{$r}", (int) $o->subtotal_kaos);
            $sheet->setCellValue("J{$r}", (int) $o->donasi);
            $sheet->setCellValue("K{$r}", (int) $o->ongkir);
            $sheet->setCellValue("L{$r}", (int) $o->totalTagihan());

            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$r}:F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("G{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$r}:L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("I{$r}:L{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:L{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            $sheet->getStyle("A{$r}:L{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        $lastDataRow = $r - 1;
        if ($orders->count() > 0) {
            $sheet->mergeCells("A{$r}:G{$r}");
            $sheet->setCellValue("A{$r}", 'TOTAL PESANAN KAOS');
            $sheet->setCellValue("H{$r}", "=SUM(H6:H{$lastDataRow})");
            $sheet->setCellValue("I{$r}", "=SUM(I6:I{$lastDataRow})");
            $sheet->setCellValue("J{$r}", "=SUM(J6:J{$lastDataRow})");
            $sheet->setCellValue("K{$r}", "=SUM(K6:K{$lastDataRow})");
            $sheet->setCellValue("L{$r}", "=SUM(L6:L{$lastDataRow})");

            $sheet->getStyle("A{$r}:L{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}:L{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$r}:L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$r}:L{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            $sheet->getStyle("A{$r}:L{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER_DARK);
            $sheet->getStyle("A{$r}:L{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB(self::COLOR_BORDER_DARK);
        }

        $sheet->freezePane('A6');
        $this->autoFitColumns($sheet, [
            'A' => 6,
            'B' => 14,
            'C' => 16,
            'D' => 24,
            'E' => 18,
            'F' => 24,
            'G' => 10,
            'H' => 8,
            'I' => 18,
            'J' => 18,
            'K' => 16,
            'L' => 20,
        ]);
    }

    private function buildSheetPengeluaran(Worksheet $sheet, Carbon $fromDate, Carbon $toDate, $expenses): void
    {
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A2', 'RINCIAN BIAYA PENGELUARAN (PEMBANGUNAN & OPERASIONAL)');
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB(self::COLOR_DARK);

        $sheet->setCellValue('A3', 'Periode: ' . $fromDate->translatedFormat('d F Y') . ' s/d ' . $toDate->translatedFormat('d F Y') . ' | Pengeluaran pembangunan otomatis memotong Saldo Pembangunan');
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('78716C');

        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal Transaksi',
            'C5' => 'Kategori Biaya',
            'D5' => 'Nama Pengeluaran / Uraian Belanja',
            'E5' => 'Jumlah (Rp)',
            'F5' => 'Keterangan / Bukti Nota / Catatan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:F5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A5:F5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_DARK);
        $sheet->getStyle('A5:F5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $r = 6;
        foreach ($expenses as $idx => $e) {
            $sheet->setCellValue("A{$r}", $idx + 1);
            $sheet->setCellValue("B{$r}", $e->tanggal ? Carbon::parse($e->tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$r}", $e->kategori);
            $sheet->setCellValue("D{$r}", $e->nama);
            $sheet->setCellValue("E{$r}", (int) $e->jumlah);
            $sheet->setCellValue("F{$r}", $e->keterangan ?: '—');

            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle("E{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$r}:F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_ZEBRA);
            }

            // Kategori pembangunan diberi penanda warna amber/sage
            if (!in_array($e->kategori, ['Produksi kaos', 'Pengiriman'])) {
                $sheet->getStyle("C{$r}")->getFont()->setBold(true)->getColor()->setRGB(self::COLOR_PRIMARY);
            }

            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER);
            $r++;
        }

        $lastDataRow = $r - 1;
        if ($expenses->count() > 0) {
            $sheet->mergeCells("A{$r}:D{$r}");
            $sheet->setCellValue("A{$r}", 'TOTAL SELURUH PENGELUARAN');
            $sheet->setCellValue("E{$r}", "=SUM(E6:E{$lastDataRow})");
            $sheet->setCellValue("F{$r}", $expenses->count() . ' catatan transaksi');

            $sheet->getStyle("A{$r}:F{$r}")->getFont()->setBold(true);
            $sheet->getStyle("A{$r}:F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$r}")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY);

            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::COLOR_BORDER_DARK);
            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB(self::COLOR_BORDER_DARK);
        }

        $sheet->freezePane('A6');
        $this->autoFitColumns($sheet, [
            'A' => 6,
            'B' => 14,
            'C' => 24,
            'D' => 38,
            'E' => 20,
            'F' => 35,
        ]);
    }

    private function autoFitColumns(Worksheet $sheet, array $minWidths = []): void
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            
            if (isset($minWidths[$colLetter])) {
                $sheet->getColumnDimension($colLetter)->setWidth($minWidths[$colLetter]);
                $sheet->getColumnDimension($colLetter)->setAutoSize(false);
            }
        }
    }
}
