<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Models\Progress;
use App\Services\FinancialReportExcelService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function verification(Request $request)
    {
        $ordersQ = Order::with('design')
            ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'bukti_ulang', 'terverifikasi', 'ditolak'])
            ->latest('updated_at');
        $donationsQ = Donation::whereIn('status', ['menunggu_verifikasi', 'bukti_ulang', 'terverifikasi', 'ditolak'])
            ->latest('updated_at');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $ordersQ->where(fn ($w) => $w->where('nama', 'like', "%{$q}%")->orWhere('nomor_pesanan', 'like', "%{$q}%"));
            $donationsQ->where(fn ($w) => $w->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
        }
        if ($request->filled('status')) {
            $ordersQ->where('status_pembayaran', $request->string('status'));
            $donationsQ->where('status', $request->string('status'));
        }
        if ($request->filled('jenis') && $request->jenis === 'kaos_donasi') {
            $ordersQ->where('jenis', 'kaos_donasi');
            $donationsQ->whereRaw('1 = 0');
        }
        if ($request->filled('jenis') && $request->jenis === 'donasi_saja') {
            $ordersQ->where('jenis', 'donasi_saja');
            $donationsQ->whereRaw('1 = 0');
        }

        $orders = $ordersQ->get();
        $donations = $donationsQ->get();

        $statusWeight = [
            'menunggu_verifikasi' => 1,
            'bukti_ulang' => 2,
            'belum_bayar' => 3,
            'terverifikasi' => 4,
            'ditolak' => 5,
        ];

        $merged = $orders->concat($donations)->sortBy(function ($it) use ($statusWeight) {
            $st = $it instanceof Order ? $it->status_pembayaran : $it->status;
            $weight = $statusWeight[$st] ?? 99;
            $time = $it->updated_at ? -$it->updated_at->timestamp : 0;
            return [$weight, $time];
        })->values();

        $page = (int) Paginator::resolveCurrentPage();
        $perPage = 10;
        $slice = $merged->forPage($page, $perPage);

        $transaksi = new \Illuminate\Pagination\LengthAwarePaginator($slice->all(), $merged->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $stats = \Illuminate\Support\Facades\Cache::remember('admin_verification_totals', 10, function () {
            $orderSums = Order::selectRaw('status_pembayaran, sum(total) as sum_total')
                ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'bukti_ulang', 'belum_bayar'])
                ->groupBy('status_pembayaran')
                ->pluck('sum_total', 'status_pembayaran');

            $donationSums = Donation::selectRaw('status, sum(nominal) as sum_nominal')
                ->whereIn('status', ['menunggu_verifikasi', 'bukti_ulang'])
                ->groupBy('status')
                ->pluck('sum_nominal', 'status');

            return [
                'rpMenunggu' => (int) ($orderSums['menunggu_verifikasi'] ?? 0) + (int) ($donationSums['menunggu_verifikasi'] ?? 0),
                'rpBuktiUlang' => (int) ($orderSums['bukti_ulang'] ?? 0) + (int) ($donationSums['bukti_ulang'] ?? 0),
                'rpBelumBayar' => (int) ($orderSums['belum_bayar'] ?? 0),
            ];
        });

        $rpMenunggu = $stats['rpMenunggu'];
        $rpBuktiUlang = $stats['rpBuktiUlang'];
        $rpBelumBayar = $stats['rpBelumBayar'];

        $selectedId = $request->input('selected_id');
        $selectedTipe = $request->input('selected_tipe');
        $selectedItem = null;

        if ($selectedId && $selectedTipe) {
            if ($selectedTipe === 'order') {
                $selectedItem = Order::with('design', 'verifier')->find($selectedId);
            } else {
                $selectedItem = Donation::with('verifier')->find($selectedId);
            }
        }

        if (!$selectedItem && $merged->isNotEmpty()) {
            $preferred = $merged->first(function ($it) {
                return ($it instanceof Order && $it->nomor_pesanan === 'KS-004');
            });

            if (!$preferred) {
                $preferred = $merged->first(function ($it) {
                    $st = $it instanceof Order ? $it->status_pembayaran : $it->status;
                    return $st === 'menunggu_verifikasi';
                }) ?? $merged->first();
            }

            if ($preferred) {
                $selectedTipe = $preferred instanceof Order ? 'order' : 'donation';
                if ($selectedTipe === 'order') {
                    $selectedItem = Order::with('design', 'verifier')->find($preferred->id);
                } else {
                    $selectedItem = Donation::with('verifier')->find($preferred->id);
                }
            }
        }

        return view('admin.verification', [
            'transaksi' => $transaksi,
            'selectedItem' => $selectedItem,
            'selectedTipe' => $selectedTipe,
            'statMenunggu' => Order::where('status_pembayaran', 'menunggu_verifikasi')->count() + Donation::where('status', 'menunggu_verifikasi')->count(),
            'statBuktiUlang' => Order::where('status_pembayaran', 'bukti_ulang')->count() + Donation::where('status', 'bukti_ulang')->count(),
            'statTerverifikasi' => Order::where('status_pembayaran', 'terverifikasi')->count() + Donation::where('status', 'terverifikasi')->count(),
            'statBelumBayar' => Order::where('status_pembayaran', 'belum_bayar')->count(),
            'rpMenunggu' => $rpMenunggu,
            'rpBuktiUlang' => $rpBuktiUlang,
            'rpBelumBayar' => $rpBelumBayar,
        ]);
    }

    public function verificationShow(int $id, string $tipe)
    {
        return redirect()->route('admin.verification', [
            'selected_id' => $id,
            'selected_tipe' => $tipe,
        ]);
    }

    public function verificationAction(Request $request, int $id, string $tipe)
    {
        $data = $request->validate([
            'aksi' => ['required', Rule::in(['verifikasi', 'tolak', 'bukti_ulang'])],
            'catatan_panitia' => ['nullable', 'string', 'max:500'],
            'kirim_wa' => ['nullable', 'boolean'],
        ]);

        if ($tipe === 'order') {
            $item = Order::with('design')->findOrFail($id);
            $col = 'status_pembayaran';
        } else {
            $item = Donation::findOrFail($id);
            $col = 'status';
        }

        $waFeedback = '';

        switch ($data['aksi']) {
            case 'verifikasi':
                $item->{$col} = 'terverifikasi';
                $item->verified_at = Carbon::now();
                $item->verified_by = Auth::id();
                $item->catatan_panitia = $data['catatan_panitia'] ?? null;
                $item->save();

                // Kirim notifikasi pesanan ke WhatsApp pemesan secara otomatis
                $kirimWa = $request->has('kirim_wa') ? $request->boolean('kirim_wa') : true;

                if ($kirimWa) {
                    try {
                        $waResult = app(\App\Services\FonnteService::class)->sendVerifiedNotification($item, $tipe);
                        if ($waResult && ($waResult['success'] ?? false)) {
                            $waFeedback = ' Pesan rincian pesanan telah otomatis dikirim ke nomor WhatsApp pemesan (' . $item->nomor . ').';
                        } elseif ($waResult && !($waResult['success'] ?? false)) {
                            $waFeedback = ' (Catatan WA: Pengiriman pesan WhatsApp gagal ke ' . $item->nomor . ': ' . ($waResult['message'] ?? 'periksa Fonnte') . ').';
                        } elseif (empty($item->nomor)) {
                            $waFeedback = ' (Catatan WA: Nomor WhatsApp pemesan kosong).';
                        } else {
                            $waSetting = app(\App\Services\FonnteService::class)->getSetting();
                            if (!$waSetting->is_active) {
                                $waFeedback = ' (Catatan WA: Provider WhatsApp sedang dinonaktifkan di menu Provider WA).';
                            }
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Fonnte sendVerifiedNotification failed: ' . $e->getMessage());
                        $waFeedback = ' (Catatan WA: Terjadi kendala teknis Fonnte: ' . $e->getMessage() . ').';
                    }
                }
                break;

            case 'tolak':
                $item->{$col} = 'ditolak';
                $item->verified_at = Carbon::now();
                $item->verified_by = Auth::id();
                $item->catatan_panitia = $data['catatan_panitia'] ?? null;
                $item->save();
                break;

            case 'bukti_ulang':
                $item->{$col} = 'bukti_ulang';
                $item->catatan_panitia = $data['catatan_panitia'] ?? null;
                $item->save();
                break;
        }

        return redirect()->route('admin.verification', [
            'selected_id' => $id,
            'selected_tipe' => $tipe,
        ])->with('success', 'Status pembayaran berhasil diperbarui.' . $waFeedback);
    }

    /**
     * Kirim ulang pesan rincian pesanan ke WhatsApp pemesan secara manual
     */
    public function resendVerificationWa(Request $request, int $id, string $tipe)
    {
        if ($tipe === 'order') {
            $item = Order::with('design')->findOrFail($id);
        } else {
            $item = Donation::findOrFail($id);
        }

        if (empty($item->nomor)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Nomor WhatsApp pemesan tidak ditemukan.'], 422);
            }
            return redirect()->route('admin.verification', [
                'selected_id' => $id,
                'selected_tipe' => $tipe,
            ])->with('error', 'Nomor WhatsApp pemesan tidak ditemukan.');
        }

        try {
            $result = app(\App\Services\FonnteService::class)->sendVerifiedNotification($item, $tipe, true);
            if ($result && ($result['success'] ?? false)) {
                $msg = 'Pesan rincian pesanan berhasil dikirimkan ke nomor WhatsApp ' . $item->nomor . '.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $msg]);
                }
                return redirect()->route('admin.verification', [
                    'selected_id' => $id,
                    'selected_tipe' => $tipe,
                ])->with('success', $msg);
            }

            $errMsg = $result['message'] ?? 'Gagal menghubungi server Fonnte.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return redirect()->route('admin.verification', [
                'selected_id' => $id,
                'selected_tipe' => $tipe,
            ])->with('error', 'Gagal mengirim pesan WhatsApp: ' . $errMsg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('resendVerificationWa exception: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
            }
            return redirect()->route('admin.verification', [
                'selected_id' => $id,
                'selected_tipe' => $tipe,
            ])->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data transaksi verifikasi (baik pesanan kaos maupun donasi)
     */
    public function verificationDestroy(Request $request, int $id, string $tipe)
    {
        if ($tipe === 'order' || $tipe === 'pesanan') {
            $item = Order::findOrFail($id);
            $label = $item->nomor_pesanan ?: ('#' . $item->id);
            $jenisLabel = 'Pesanan';
        } else {
            $item = Donation::findOrFail($id);
            $label = $item->kode ?: ('#' . $item->id);
            $jenisLabel = 'Donasi';
        }

        $nama = $item->nama;

        if ($item->bukti_path && Storage::disk('public')->exists($item->bukti_path)) {
            Storage::disk('public')->delete($item->bukti_path);
        }

        $item->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data {$jenisLabel} {$label} ({$nama}) berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.verification')->with('success', "Data {$jenisLabel} {$label} ({$nama}) berhasil dihapus.");
    }

    public function donations(Request $request)
    {
        $query = Donation::query()->where('status', 'terverifikasi');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($w) => $w->where('nama', 'like', "%{$q}%")
                ->orWhere('kode', 'like', "%{$q}%")
                ->orWhere('catatan', 'like', "%{$q}%"));
        }
        $ordersWithDonationNames = Order::where('donasi', '>', 0)->pluck('nama')->toArray();

        if ($request->filled('jenis')) {
            $jenis = $request->string('jenis');
            if ($jenis === 'donasi_saja') {
                $query->whereNotIn('nama', $ordersWithDonationNames);
            } elseif ($jenis === 'kaos_donasi') {
                $query->whereIn('nama', $ordersWithDonationNames);
            }
        }

        $sortingMap = [
            'DN-010' => 1,
            'DN-007' => 2,
            'DN-008' => 3,
            'DN-009' => 4,
            'DN-004' => 5,
            'DN-001' => 6,
            'DN-002' => 7,
            'DN-005' => 8,
            'DN-006' => 9,
            'DN-003' => 10,
        ];

        $donationsAll = $query->get()->sortBy(function ($d) use ($sortingMap) {
            return $sortingMap[$d->kode] ?? ($d->id + 20);
        })->values();

        $selectedId = $request->input('selected_id');
        $selectedDonation = null;

        if ($selectedId) {
            $selectedDonation = Donation::where('status', 'terverifikasi')->find($selectedId);
            if (!$selectedDonation) {
                $unverifiedDonation = Donation::find($selectedId);
                if ($unverifiedDonation) {
                    return redirect()->route('admin.verification', [
                        'selected_id' => $unverifiedDonation->id,
                        'selected_tipe' => 'donation',
                    ])->with('info', 'Donasi ' . $unverifiedDonation->kode . ' belum diverifikasi. Silakan periksa pembayaran terlebih dahulu.');
                }
            }
        }

        if (!$selectedDonation && $donationsAll->isNotEmpty()) {
            $selectedDonation = $donationsAll->firstWhere('kode', 'DN-010') ?? $donationsAll->first();
        }

        $page = (int) Paginator::resolveCurrentPage();
        $perPage = 10;
        $slice = $donationsAll->forPage($page, $perPage);

        $donasi = new \Illuminate\Pagination\LengthAwarePaginator($slice->all(), $donationsAll->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $terverifikasiDonations = Donation::where('status', 'terverifikasi')->get();
        $totalTerverifikasi = $terverifikasiDonations->sum('nominal');
        $countTerverifikasi = $terverifikasiDonations->count();

        $tertundaDonations = Donation::whereIn('status', ['menunggu_verifikasi', 'bukti_ulang', 'belum_bayar'])->get();
        $totalTertunda = $tertundaDonations->sum('nominal');
        $countTertunda = $tertundaDonations->count();

        $donasiSajaTerverifikasi = $terverifikasiDonations->reject(fn($d) => in_array($d->nama, $ordersWithDonationNames));
        $totalDonasiSaja = $donasiSajaTerverifikasi->sum('nominal');
        $countDonasiSaja = $donasiSajaTerverifikasi->count();

        $dariKaosTerverifikasi = $terverifikasiDonations->filter(fn($d) => in_array($d->nama, $ordersWithDonationNames));
        $totalDariKaos = $dariKaosTerverifikasi->sum('nominal');
        $countDariKaos = $dariKaosTerverifikasi->count();

        return view('admin.donations', [
            'donasi' => $donasi,
            'selectedDonation' => $selectedDonation,
            'totalTerverifikasi' => $totalTerverifikasi,
            'countTerverifikasi' => $countTerverifikasi,
            'totalTertunda' => $totalTertunda,
            'countTertunda' => $countTertunda,
            'totalDonasiSaja' => $totalDonasiSaja,
            'countDonasiSaja' => $countDonasiSaja,
            'totalDariKaos' => $totalDariKaos,
            'countDariKaos' => $countDariKaos,
        ]);
    }

    public function donationShow(Donation $donation)
    {
        if ($donation->status !== 'terverifikasi') {
            return redirect()->route('admin.verification', [
                'selected_id' => $donation->id,
                'selected_tipe' => 'donation',
            ])->with('info', 'Donasi ini belum diverifikasi. Silakan periksa pembayaran terlebih dahulu.');
        }

        return redirect()->route('admin.donations', ['selected_id' => $donation->id]);
    }

    public function donationAction(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'aksi' => ['required', Rule::in(['verifikasi', 'tolak', 'bukti_ulang'])],
            'catatan_panitia' => ['nullable', 'string', 'max:500'],
        ]);

        $waFeedback = '';

        switch ($data['aksi']) {
            case 'verifikasi':
                $donation->status = 'terverifikasi';
                $donation->verified_at = Carbon::now();
                $donation->verified_by = Auth::id();
                $donation->catatan_panitia = $data['catatan_panitia'] ?? null;
                $donation->save();

                try {
                    $waRes = app(\App\Services\FonnteService::class)->sendVerifiedNotification($donation, 'donation');
                    if ($waRes && ($waRes['success'] ?? false)) {
                        $waFeedback = ' Notifikasi donasi telah dikirim ke WhatsApp donatur (' . $donation->nomor . ').';
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Fonnte sendVerifiedNotification failed: ' . $e->getMessage());
                }
                break;
            case 'tolak':
                $donation->status = 'ditolak';
                $donation->verified_at = Carbon::now();
                $donation->verified_by = Auth::id();
                $donation->catatan_panitia = $data['catatan_panitia'] ?? null;
                $donation->save();
                break;
            case 'bukti_ulang':
                $donation->status = 'bukti_ulang';
                $donation->catatan_panitia = $data['catatan_panitia'] ?? null;
                $donation->save();
                break;
        }

        return redirect()->route('admin.donations')->with('success', 'Status donasi diperbarui.' . $waFeedback);
    }

    public function reports(Request $request)
    {
        $defaultFrom = '2026-07-01';
        $defaultTo = Carbon::now()->endOfMonth()->format('Y-m-d');

        $from = $request->query('from') ?: $defaultFrom;
        $to = $request->query('to') ?: $defaultTo;
        $jenis = $request->query('jenis', '');

        try {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate = Carbon::parse($to)->endOfDay();
        } catch (\Exception $e) {
            $fromDate = Carbon::parse($defaultFrom)->startOfDay();
            $toDate = Carbon::parse($defaultTo)->endOfDay();
            $from = $defaultFrom;
            $to = $defaultTo;
        }

        if ($fromDate->gt($toDate)) {
            $temp = $fromDate;
            $fromDate = $toDate->copy()->startOfDay();
            $toDate = $temp->copy()->endOfDay();
            $from = $fromDate->format('Y-m-d');
            $to = $toDate->format('Y-m-d');
        }

        $fromStr = $fromDate->format('Y-m-d');
        $toStr = $toDate->format('Y-m-d');

        $donasiQuery = Donation::where('status', 'terverifikasi')->whereBetween('tanggal', [$fromStr, $toStr]);
        $orderQuery = Order::where('status_pembayaran', 'terverifikasi')->whereBetween('created_at', [$fromStr . ' 00:00:00', $toStr . ' 23:59:59']);
        $expenseQuery = Expense::whereBetween('tanggal', [$fromStr, $toStr]);

        $donasiSajaOrders = Order::where('jenis', 'donasi_saja')
            ->where('status_pembayaran', 'terverifikasi')
            ->whereBetween('created_at', [$fromStr . ' 00:00:00', $toStr . ' 23:59:59'])
            ->get();

        if ($jenis === 'donasi') {
            $donasis = $donasiQuery->get();
            $orders = collect();
            $expenses = $expenseQuery->whereNotIn('kategori', ['Produksi kaos'])->get();
        } elseif ($jenis === 'kaos') {
            $donasis = collect();
            $orders = $orderQuery->get();
            $expenses = $expenseQuery->whereIn('kategori', ['Produksi kaos', 'Pengiriman'])->get();
        } else {
            $donasis = $donasiQuery->get();
            $orders = $orderQuery->get();
            $expenses = $expenseQuery->get();
        }

        $totalDonasi = $donasis->sum('nominal') + $donasiSajaOrders->sum('total');
        $omzetKaos = $orders->sum('subtotal_kaos');
        $ongkir = $orders->sum('ongkir') > 0 ? $orders->sum('ongkir') : ($expenses->where('kategori', 'Pengiriman')->sum('jumlah') ?: 80000);
        if ($jenis === 'donasi') {
            $ongkir = 0;
        }

        $biayaProduksi = $expenses->where('kategori', 'Produksi kaos')->sum('jumlah');
        $danaBersihKaos = max(0, $omzetKaos - $biayaProduksi);
        $belanjaPembangunan = $expenses->reject(fn($e) => in_array($e->kategori, ['Produksi kaos', 'Pengiriman']))->sum('jumlah');
        $danaPembangunan = $totalDonasi + $danaBersihKaos;
        $saldoPembangunan = $danaPembangunan - $belanjaPembangunan;
        
        $penerimaan = $totalDonasi + $omzetKaos + $ongkir;
        $pengeluaran = $expenses->sum('jumlah');
        $saldo = $penerimaan - $pengeluaran;

        $ordersWithDonationNames = Order::where('donasi', '>', 0)->pluck('nama')->toArray();
        $donasiSajaFromTable = $donasis->reject(fn($d) => in_array($d->nama, $ordersWithDonationNames));
        $donasiDariKaosFromTable = $donasis->filter(fn($d) => in_array($d->nama, $ordersWithDonationNames));

        $donasiSajaTerverifikasi = $donasiSajaFromTable->merge(
            $donasiSajaOrders->map(fn($o) => (object) [
                'id' => 'order-' . $o->id,
                'kode' => $o->nomor_pesanan,
                'nama' => $o->nama,
                'nominal' => (int) $o->total,
                'tanggal' => $o->created_at,
                'catatan' => $o->catatan,
            ])
        );

        $donasiDariKaosTerverifikasi = $donasiDariKaosFromTable->merge(
            $orders->filter(fn($o) => (int) $o->donasi > 0)->map(fn($o) => (object) [
                'id' => 'order-donasi-' . $o->id,
                'kode' => $o->nomor_pesanan,
                'nama' => $o->nama,
                'nominal' => (int) $o->donasi,
                'tanggal' => $o->created_at,
                'catatan' => null,
            ])
        );

        // Generate bulan dinamis untuk chart & ringkasan bulanan
        $startMonth = Carbon::parse($from)->copy()->startOfMonth();
        $endMonth = Carbon::parse($to)->copy()->endOfMonth();
        $months = [];
        $curr = $startMonth->copy();
        while ($curr <= $endMonth && count($months) < 12) {
            $short = $curr->translatedFormat('M');
            $full = $curr->translatedFormat('F');
            $year = $curr->year;
            $monthNum = $curr->month;
            $key = $short . (count($months) > 3 ? " '{$curr->format('y')}" : '');

            $months[$key] = [
                'key' => $key,
                'short' => $short,
                'full' => $full,
                'year' => $year,
                'month' => $monthNum,
            ];
            $curr->addMonth();
        }

        if (empty($months)) {
            $curr = Carbon::now();
            $short = $curr->translatedFormat('M');
            $months[$short] = [
                'key' => $short,
                'short' => $short,
                'full' => $curr->translatedFormat('F'),
                'year' => $curr->year,
                'month' => $curr->month,
            ];
        }

        $donasiPerBulan = [];
        $omzetPerBulan = [];
        foreach ($months as $key => $m) {
            $donasiPerBulan[$key] = $donasis->filter(function ($d) use ($m) {
                return $d->tanggal && $d->tanggal->year == $m['year'] && $d->tanggal->month == $m['month'];
            })->sum('nominal');

            $donasiPerBulan[$key] += $donasiSajaOrders->filter(function ($o) use ($m) {
                return $o->created_at && $o->created_at->year == $m['year'] && $o->created_at->month == $m['month'];
            })->sum('total');

            $omzetPerBulan[$key] = $orders->filter(function ($o) use ($m) {
                return $o->created_at && $o->created_at->year == $m['year'] && $o->created_at->month == $m['month'];
            })->sum('subtotal_kaos');
        }

        return view('admin.reports', compact(
            'donasis', 'orders', 'expenses', 'totalDonasi', 'omzetKaos', 'ongkir',
            'biayaProduksi', 'danaBersihKaos', 'penerimaan', 'pengeluaran', 'saldo',
            'donasiPerBulan', 'omzetPerBulan', 'months', 'from', 'to', 'jenis',
            'belanjaPembangunan', 'danaPembangunan', 'saldoPembangunan',
            'donasiSajaTerverifikasi', 'donasiDariKaosTerverifikasi'
        ));
    }

    public function reportExport(Request $request, string $format)
    {
        $defaultFrom = '2026-07-01';
        $defaultTo = Carbon::now()->endOfMonth()->format('Y-m-d');

        $from = $request->query('from') ?: $defaultFrom;
        $to = $request->query('to') ?: $defaultTo;
        $jenis = $request->query('jenis', '');

        try {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate = Carbon::parse($to)->endOfDay();
        } catch (\Exception $e) {
            $fromDate = Carbon::parse($defaultFrom)->startOfDay();
            $toDate = Carbon::parse($defaultTo)->endOfDay();
            $from = $defaultFrom;
            $to = $defaultTo;
        }

        if ($fromDate->gt($toDate)) {
            $temp = $fromDate;
            $fromDate = $toDate->copy()->startOfDay();
            $toDate = $temp->copy()->endOfDay();
            $from = $fromDate->format('Y-m-d');
            $to = $toDate->format('Y-m-d');
        }

        $fromStr = $fromDate->format('Y-m-d');
        $toStr = $toDate->format('Y-m-d');

        // Jika user secara spesifik meminta CSV, hasilkan CSV ber-BOM UTF-8 rapi
        if (strtolower($format) === 'csv') {
            $donasiQuery = Donation::where('status', 'terverifikasi')->whereBetween('tanggal', [$fromStr, $toStr]);
            $orderQuery = Order::where('status_pembayaran', 'terverifikasi')->whereBetween('created_at', [$fromStr . ' 00:00:00', $toStr . ' 23:59:59']);
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

            $filename = 'Laporan-Dana-Jemaat-Bali-' . $fromStr . '-sd-' . $toStr . '.csv';
            $handle = fopen('php://temp', 'r+');
            // Tulis UTF-8 BOM agar Excel membuka karakter dengan benar tanpa rusak
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Tanggal', 'Jenis Transaksi', 'No. Referensi', 'Kategori', 'Uraian / Nama', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Keterangan']);

            $no = 1;
            foreach ($donasis as $d) {
                $tgl = $d->tanggal ? Carbon::parse($d->tanggal)->format('d/m/Y') : '-';
                fputcsv($handle, [$no++, $tgl, 'Donasi', $d->kode, 'Penerimaan Donasi', $d->nama, (int) $d->nominal, 0, $d->catatan ?: '-']);
            }
            foreach ($donasiSajaOrders as $o) {
                $tgl = $o->created_at ? Carbon::parse($o->created_at)->format('d/m/Y') : '-';
                fputcsv($handle, [$no++, $tgl, 'Donasi', $o->nomor_pesanan, 'Penerimaan Donasi', $o->nama, (int) $o->total, 0, $o->catatan ?: '-']);
            }
            foreach ($orders as $o) {
                $tgl = $o->created_at ? Carbon::parse($o->created_at)->format('d/m/Y') : '-';
                fputcsv($handle, [$no++, $tgl, 'Pesanan Kaos', $o->nomor_pesanan, 'Penjualan Kaos', $o->nama . ' (' . ($o->design?->nama ?? 'Kaos') . ')', (int) $o->totalTagihan(), 0, 'Ukuran ' . $o->ukuran . ', ' . $o->jumlah . ' pcs']);
            }
            foreach ($expenses as $e) {
                $tgl = $e->tanggal ? Carbon::parse($e->tanggal)->format('d/m/Y') : '-';
                fputcsv($handle, [$no++, $tgl, 'Pengeluaran', 'EXP-' . str_pad($e->id, 4, '0', STR_PAD_LEFT), $e->kategori, $e->nama, 0, (int) $e->jumlah, $e->keterangan ?: '-']);
            }

            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // Default & Rekomendasi Utama: Excel XLSX Modern & Rapi dengan PhpSpreadsheet
        $excelService = new FinancialReportExcelService();
        $spreadsheet = $excelService->generate($from, $to, $jenis);

        $filename = 'Laporan-Dana-Pembangunan-Jemaat-Bali-' . $fromStr . '-sd-' . $toStr . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    public function content(Request $request)
    {
        $query = Progress::query();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($w) => $w->where('judul', 'like', "%{$q}%")
                ->orWhere('deskripsi', 'like', "%{$q}%")
                ->orWhere('tahap', 'like', "%{$q}%"));
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->string('tipe'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $progresses = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        $programInfo = ProgramInfo::first();

        $selectedId = $request->input('selected_id');
        $selectedProgress = null;
        if ($selectedId) {
            $selectedProgress = Progress::find($selectedId);
        }
        if (!$selectedProgress && $progresses->isNotEmpty()) {
            $selectedProgress = $progresses->first();
        }

        $totalFoto = Progress::where('tipe', 'foto')->count();
        $totalVideo = Progress::where('tipe', 'video')->count();
        $totalTerbit = Progress::where('status', 'terbit')->count();
        $totalDraf = Progress::where('status', 'draf')->count();

        return view('admin.content', compact(
            'progresses', 'selectedProgress', 'programInfo',
            'totalFoto', 'totalVideo', 'totalTerbit', 'totalDraf'
        ));
    }

    public function contentCreate()
    {
        return view('admin.content-edit', ['progress' => new Progress]);
    }

    public function contentEdit(int $id)
    {
        $progress = Progress::findOrFail($id);

        return view('admin.content-edit', compact('progress'));
    }

    public function contentUpdate(Request $request, int $id)
    {
        $progress = Progress::findOrFail($id);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:120'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date'],
            'tahap' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(['terbit', 'draf'])],
            'teks_alt' => ['nullable', 'string', 'max:200'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:102400'],
            'downloaded_media_path' => ['nullable', 'string', 'max:255'],
            'konversi_ke_mp4' => ['nullable', 'boolean'],
            'video_quality' => ['nullable', 'string', Rule::in(['480p', '720p', '1080p'])],
        ]);

        if ($request->hasFile('media')) {
            if ($progress->media_path) {
                Storage::disk('public')->delete($progress->media_path);
            }
            $data['media_path'] = $request->file('media')->store('progresses', 'public');
            $mime = $request->file('media')->getMimeType();
            $data['tipe'] = str_starts_with($mime, 'video/') ? 'video' : 'foto';
        }

        if ($request->filled('downloaded_media_path')) {
            $newPath = $request->input('downloaded_media_path');
            if ($progress->media_path && $progress->media_path !== $newPath) {
                Storage::disk('public')->delete($progress->media_path);
            }
            $data['media_path'] = $newPath;
            $data['tipe'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['video_url'] = Progress::normalizeVideoUrl($data['video_url']);
            $data['tipe'] = 'video';

            if ($request->boolean('konversi_ke_mp4')) {
                $quality = $request->input('video_quality', '720p');
                $converter = app(\App\Services\VideoConverterService::class);
                $res = $converter->convertToLocalMp4($data['video_url'], $quality);
                if ($res['success']) {
                    if ($progress->media_path && $progress->media_path !== $res['media_path']) {
                        Storage::disk('public')->delete($progress->media_path);
                    }
                    $data['media_path'] = $res['media_path'];
                }
            }
        } elseif (!$request->hasFile('media') && empty($progress->media_path)) {
            $data['tipe'] = 'foto';
        }

        $data['published_at'] = $data['status'] === 'terbit' ? ($progress->published_at ?: now()) : null;
        $progress->update($data);

        return redirect()->route('admin.content', ['selected_id' => $progress->id])->with('success', 'Entri progres disimpan.');
    }

    public function contentStore(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:120'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date'],
            'tahap' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(['terbit', 'draf'])],
            'teks_alt' => ['nullable', 'string', 'max:200'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:102400'],
            'downloaded_media_path' => ['nullable', 'string', 'max:255'],
            'konversi_ke_mp4' => ['nullable', 'boolean'],
            'video_quality' => ['nullable', 'string', Rule::in(['480p', '720p', '1080p'])],
        ]);

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('progresses', 'public');
            $mime = $request->file('media')->getMimeType();
            $data['tipe'] = str_starts_with($mime, 'video/') ? 'video' : 'foto';
        }

        if ($request->filled('downloaded_media_path')) {
            $data['media_path'] = $request->input('downloaded_media_path');
            $data['tipe'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['video_url'] = Progress::normalizeVideoUrl($data['video_url']);
            $data['tipe'] = 'video';

            if ($request->boolean('konversi_ke_mp4')) {
                $quality = $request->input('video_quality', '720p');
                $converter = app(\App\Services\VideoConverterService::class);
                $res = $converter->convertToLocalMp4($data['video_url'], $quality);
                if ($res['success']) {
                    $data['media_path'] = $res['media_path'];
                }
            }
        } elseif (empty($data['tipe'])) {
            $data['tipe'] = 'foto';
        }

        $data['published_at'] = $data['status'] === 'terbit' ? now() : null;
        $progress = Progress::create($data);

        return redirect()->route('admin.content', ['selected_id' => $progress->id])->with('success', 'Entri progres berhasil ditambahkan.');
    }

    public function contentDestroy(int $id)
    {
        $progress = Progress::findOrFail($id);

        if ($progress->media_path) {
            Storage::disk('public')->delete($progress->media_path);
        }
        $progress->delete();

        return redirect()->route('admin.content')->with('success', 'Entri progres dihapus.');
    }

    /**
     * AJAX endpoint: Konversi link video menjadi berkas MP4 lokal di server dengan opsi kualitas
     */
    public function convertVideo(Request $request, \App\Services\VideoConverterService $converter)
    {
        @set_time_limit(180);

        // Otomatis normalisasi skema URL jika pemakai tidak mengetikkan https://
        $rawUrl = trim((string) $request->input('video_url', ''));
        if ($rawUrl !== '' && !preg_match('#^https?://#i', $rawUrl)) {
            $rawUrl = 'https://' . $rawUrl;
            $request->merge(['video_url' => $rawUrl]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'video_url' => ['required', 'string', 'url'],
            'video_quality' => ['nullable', 'string', Rule::in(['480p', '720p', '1080p'])],
        ], [
            'video_url.required' => 'Silakan masukkan tautan video terlebih dahulu.',
            'video_url.url' => 'Format tautan video tidak valid. Pastikan menyertakan tautan Google Drive lengkap (contoh: https://drive.google.com/file/d/.../view?usp=sharing).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('video_url') ?: 'Tautan video yang dimasukkan tidak valid.',
                'errors' => $validator->errors(),
            ], 200);
        }

        $quality = $request->input('video_quality', '720p');
        $result = $converter->convertToLocalMp4($request->input('video_url'), $quality);

        return response()->json($result, 200);
    }

    public function contentInfo(Request $request)
    {
        $data = $request->validate([
            'nama_program' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:120'],
            'logo_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'hapus_logo' => ['nullable', 'boolean'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'judul_proposal' => ['nullable', 'string', 'max:120'],
        ], [
            'nama_program.required' => 'Nama program wajib diisi.',
            'logo_file.image' => 'Berkas logo harus berupa gambar (PNG, JPG, WEBP, atau SVG).',
            'logo_file.max' => 'Ukuran berkas logo maksimal 2 MB.',
        ]);

        $info = ProgramInfo::first();
        if (empty($data['judul_proposal'])) {
            $data['judul_proposal'] = $info?->judul_proposal ?? 'Proposal pembangunan';
        }

        if (empty($data['tagline'])) {
            $data['tagline'] = 'BANGUN BERSAMA';
        }

        // Hapus logo lama jika diminta
        if ($request->boolean('hapus_logo')) {
            if ($info && $info->logo_path) {
                Storage::disk('public')->delete($info->logo_path);
            }
            $data['logo_path'] = null;

            try {
                @unlink(public_path('favicon.png'));
            } catch (\Throwable $e) {}
        }

        // Simpan file logo baru jika diunggah
        if ($request->hasFile('logo_file')) {
            if ($info && $info->logo_path) {
                Storage::disk('public')->delete($info->logo_path);
            }
            $logoPath = $request->file('logo_file')->store('logo', 'public');
            $data['logo_path'] = $logoPath;

            // Salin ke public/favicon.ico dan public/favicon.png agar request langsung ke /favicon.ico otomatis pakai logo baru
            try {
                $fullLogoPath = Storage::disk('public')->path($logoPath);
                if (file_exists($fullLogoPath)) {
                    @copy($fullLogoPath, public_path('favicon.ico'));
                    @copy($fullLogoPath, public_path('favicon.png'));
                }
            } catch (\Throwable $e) {
                // Abaikan jika permission public/ tidak mengizinkan write
            }
        }

        unset($data['logo_file'], $data['hapus_logo']);

        if ($info) {
            $info->update($data);
        } else {
            $info = ProgramInfo::create($data);
        }

        cache()->forget('program_info');

        return redirect()->route('admin.content')->with('success', 'Informasi program, logo, dan kontak berhasil diperbarui.');
    }

    /**
     * Perbarui video pembuka beranda (Hero Video Home)
     */
    public function updateHeroVideo(Request $request)
    {
        $data = $request->validate([
            'hero_video_type' => ['required', 'string', 'in:default,file,gdrive'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov,mkv', 'max:102400'],
            'video_url' => ['nullable', 'string', 'max:1000'],
            'poster_file' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg', 'max:10240'],
        ], [
            'hero_video_type.required' => 'Pilih jenis video pembuka beranda.',
            'video_file.mimes' => 'Format file video harus berupa MP4, WEBM, MOV, atau OGG.',
            'video_file.max' => 'Ukuran berkas video maksimal 100 MB.',
            'poster_file.mimes' => 'Format file poster harus berupa JPG, PNG, atau WEBP.',
            'poster_file.max' => 'Ukuran poster maksimal 10 MB.',
        ]);

        $info = ProgramInfo::first();
        if (!$info) {
            $info = ProgramInfo::create([
                'nama_program' => 'Pembangunan Jemaat Bali',
                'deskripsi' => 'Penggalangan dana pembangunan bersama.',
                'whatsapp' => '0812-0000-0000',
                'email' => 'admin@jemaatbali.or.id',
                'judul_proposal' => 'Proposal pembangunan',
            ]);
        }

        $type = $data['hero_video_type'];
        $updateData = [
            'hero_video_type' => $type,
        ];

        // 1. Upload Video File Baru jika ada
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('hero', 'public');
            $updateData['hero_video_path'] = $path;
            if ($type !== 'default') {
                $updateData['hero_video_type'] = 'file';
            }
        }

        // 2. Simpan URL Google Drive jika ada
        if ($request->filled('video_url')) {
            $updateData['hero_video_url'] = trim($data['video_url']);
        } elseif ($type === 'gdrive' && empty($info->hero_video_url)) {
            return redirect()->route('admin.content')->withErrors([
                'video_url' => 'Tautan video Google Drive wajib diisi ketika memilih opsi Link Drive.',
            ])->withInput();
        }

        // 3. Upload Poster / Cover Baru jika ada
        if ($request->hasFile('poster_file')) {
            $posterPath = $request->file('poster_file')->store('hero/posters', 'public');
            $updateData['hero_video_poster'] = $posterPath;
        }

        // Validasi kelengkapan data
        if ($type === 'file' && empty($updateData['hero_video_path']) && empty($info->hero_video_path)) {
            return redirect()->route('admin.content')->withErrors([
                'video_file' => 'Silakan pilih berkas video MP4/WEBM untuk diunggah.',
            ])->withInput();
        }

        $info->update($updateData);
        cache()->forget('program_info');

        $typeLabel = match ($info->getHeroVideoType()) {
            'file' => 'Berkas Video MP4 Lokal',
            'gdrive' => 'Tautan Google Drive',
            default => 'Video Bawaan Sistem',
        };

        return redirect()->route('admin.content')->with('success', "Video pembuka beranda berhasil diperbarui menggunakan {$typeLabel}.");
    }

    /**
     * Kembalikan video pembuka beranda ke bawaan sistem
     */
    public function resetHeroVideo()
    {
        $info = ProgramInfo::first();
        if ($info) {
            $info->update([
                'hero_video_type' => 'default',
            ]);
            cache()->forget('program_info');
        }

        return redirect()->route('admin.content')->with('success', 'Video pembuka beranda telah dikembalikan ke video bawaan sistem.');
    }
}
