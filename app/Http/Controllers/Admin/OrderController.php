<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class OrderController extends Controller
{
    public function dashboard()
    {
        $donasiTerverifikasi = Donation::where('status', 'terverifikasi')->sum('nominal')
            + Order::where('status_pembayaran', 'terverifikasi')->sum('donasi');
        $omzetKaos = Order::where('status_pembayaran', 'terverifikasi')->sum('subtotal_kaos');
        $biayaProduksi = Expense::where('kategori', 'Produksi kaos')->sum('jumlah');
        $danaBersihKaos = $omzetKaos - $biayaProduksi;
        $pengeluaran = Expense::sum('jumlah');
        $saldo = $donasiTerverifikasi + $danaBersihKaos - $pengeluaran;

        // Pembangunan: Donasi terverifikasi + dana bersih kaos
        $totalPembangunan = $donasiTerverifikasi + $danaBersihKaos;
        $pengeluaranPembangunan = Expense::whereNotIn('kategori', ['Produksi kaos', 'Pengiriman'])->sum('jumlah');
        $saldoPembangunan = $totalPembangunan - $pengeluaranPembangunan;

        $perluVerifikasiCount = Order::where('status_pembayaran', 'menunggu_verifikasi')->count()
            + Donation::where('status', 'menunggu_verifikasi')->count();
        $buktiUlangCount = Order::where('status_pembayaran', 'bukti_ulang')->count()
            + Donation::where('status', 'bukti_ulang')->count();
        $belumBayarCount = Order::where('status_pembayaran', 'belum_bayar')->count();
        $pesananBelumDikirimCount = Order::whereIn('status_pengiriman', ['siap_kirim', 'produksi', 'belum_diproduksi'])->where('status_pembayaran', 'terverifikasi')->count() ?: Order::where('status_pengiriman', 'siap_kirim')->count();

        // Nominal perlu verifikasi
        $rpPerluVerifikasi = Order::where('status_pembayaran', 'menunggu_verifikasi')->sum('total')
            + Donation::where('status', 'menunggu_verifikasi')->sum('nominal');

        $donasiTertunda = Donation::where('status', 'menunggu_verifikasi')->sum('nominal');

        // Daftar pembayaran prioritas gabungan
        $prioritasOrders = Order::with('design')
            ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'bukti_ulang'])
            ->latest('updated_at')
            ->get();
        $donasiPrioritas = Donation::whereIn('status', ['menunggu_verifikasi', 'bukti_ulang'])
            ->latest('updated_at')
            ->get();

        $daftarPrioritas = collect();
        foreach ($prioritasOrders as $o) {
            $daftarPrioritas->push([
                'id' => $o->id,
                'tipe' => 'order',
                'nama' => $o->nama,
                'kode' => $o->nomor_pesanan,
                'sub' => $o->nomor_pesanan . ' · ' . ($o->jenis === 'kaos_donasi' ? ($o->donasi > 0 ? 'Kaos + Donasi' : 'Pesan Kaos') : 'Donasi Saja'),
                'total' => $o->totalTagihan(),
                'tanggal_jam' => $o->updated_at ? $o->updated_at->translatedFormat('d M, H.i') : '11 Okt, 16.40',
                'keterangan_waktu' => $o->updated_at ? $o->updated_at->diffForHumans() : '17 jam lalu',
                'status' => $o->status_pembayaran,
                'catatan_panitia' => $o->catatan_panitia,
                'timestamp' => $o->updated_at?->timestamp ?? 0,
            ]);
        }
        foreach ($donasiPrioritas as $d) {
            $daftarPrioritas->push([
                'id' => $d->id,
                'tipe' => 'donation',
                'nama' => $d->nama,
                'kode' => $d->kode,
                'sub' => $d->kode . ' · Donasi Saja',
                'total' => $d->nominal,
                'tanggal_jam' => $d->updated_at ? $d->updated_at->translatedFormat('d M, H.i') : '12 Okt, 09.15',
                'keterangan_waktu' => $d->status === 'bukti_ulang' && $d->catatan_panitia ? $d->catatan_panitia : ($d->updated_at ? $d->updated_at->diffForHumans() : '45 menit lalu'),
                'status' => $d->status,
                'catatan_panitia' => $d->catatan_panitia,
                'timestamp' => $d->updated_at?->timestamp ?? 0,
            ]);
        }
        $daftarPrioritas = $daftarPrioritas->sortByDesc('timestamp')->values();

        // Aktivitas terbaru dari database secara dinamis
        $rawAktivitas = collect();

        // 1. Donasi terbaru
        $recentDonations = Donation::with('verifier')->latest('updated_at')->take(8)->get();
        foreach ($recentDonations as $d) {
            $ts = $d->updated_at?->timestamp ?? $d->created_at?->timestamp ?? 0;
            $timeObj = $d->updated_at ?? $d->created_at;
            $nominalFmt = 'Rp' . number_format($d->nominal, 0, ',', '.');

            if ($d->status === 'terverifikasi') {
                $verTs = $d->verified_at?->timestamp ?? $ts;
                $rawAktivitas->push([
                    'timestamp' => $verTs,
                    'time' => $d->verified_at ?? $timeObj,
                    'tipe' => 'check',
                    'judul' => 'Donasi ' . $d->nama . ' diverifikasi' . ($d->verifier ? ' (' . $d->verifier->name . ')' : ''),
                    'sub' => $d->kode . ' · ' . $nominalFmt . ' · terverifikasi',
                    'url' => route('admin.donations.show', $d->id),
                ]);
            } elseif ($d->status === 'menunggu_verifikasi') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'doc',
                    'judul' => 'Bukti donasi ' . $d->nama . ' diterima',
                    'sub' => $d->kode . ' · ' . $nominalFmt . ' · menunggu verifikasi',
                    'url' => route('admin.verification.show', ['id' => $d->id, 'tipe' => 'donasi']),
                ]);
            } elseif ($d->status === 'bukti_ulang') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'doc',
                    'judul' => 'Minta bukti ulang donasi ' . $d->nama,
                    'sub' => $d->kode . ' · ' . ($d->catatan_panitia ?: 'Bukti kurang jelas'),
                    'url' => route('admin.verification.show', ['id' => $d->id, 'tipe' => 'donasi']),
                ]);
            } elseif ($d->status === 'ditolak') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'doc',
                    'judul' => 'Donasi ' . $d->nama . ' ditolak',
                    'sub' => $d->kode . ' · ' . $nominalFmt . ($d->catatan_panitia ? ' · ' . $d->catatan_panitia : ''),
                    'url' => route('admin.donations.show', $d->id),
                ]);
            }
        }

        // 2. Pesanan kaos terbaru
        $recentOrders = Order::with(['design', 'verifier'])->latest('updated_at')->take(8)->get();
        foreach ($recentOrders as $o) {
            $ts = $o->updated_at?->timestamp ?? $o->created_at?->timestamp ?? 0;
            $timeObj = $o->updated_at ?? $o->created_at;
            $designName = $o->design?->nama ?? 'Kaos';

            if ($o->status_pengiriman === 'terkirim') {
                $shipTs = $o->dikirim_at?->timestamp ?? $ts;
                $rawAktivitas->push([
                    'timestamp' => $shipTs,
                    'time' => $o->dikirim_at ?? $timeObj,
                    'tipe' => 'box',
                    'judul' => 'Pesanan ' . $o->nama . ' telah dikirim',
                    'sub' => $o->nomor_pesanan . ' · Resi: ' . ($o->resi ?: '-') . ' · ' . $o->jumlah . ' kaos',
                    'url' => route('admin.orders.show', $o->id),
                ]);
            } elseif ($o->status_pengiriman === 'siap_kirim') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'box',
                    'judul' => 'Pesanan ' . $o->nama . ' siap dikirim',
                    'sub' => $o->nomor_pesanan . ' · ' . $o->jumlah . ' kaos ' . $designName . ' · siap kirim',
                    'url' => route('admin.orders.show', $o->id),
                ]);
            } elseif ($o->status_pengiriman === 'produksi') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'box',
                    'judul' => 'Pesanan ' . $o->nama . ' dalam produksi',
                    'sub' => $o->nomor_pesanan . ' · ' . $o->jumlah . ' kaos ' . $designName . ' · proses produksi',
                    'url' => route('admin.orders.show', $o->id),
                ]);
            } elseif ($o->status_pembayaran === 'terverifikasi') {
                $verTs = $o->verified_at?->timestamp ?? $ts;
                $rawAktivitas->push([
                    'timestamp' => $verTs,
                    'time' => $o->verified_at ?? $timeObj,
                    'tipe' => 'check',
                    'judul' => 'Pembayaran ' . $o->nama . ' diverifikasi' . ($o->verifier ? ' (' . $o->verifier->name . ')' : ''),
                    'sub' => $o->nomor_pesanan . ' · Rp' . number_format($o->totalTagihan(), 0, ',', '.') . ' · terverifikasi',
                    'url' => route('admin.orders.show', $o->id),
                ]);
            } elseif ($o->status_pembayaran === 'menunggu_verifikasi') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'doc',
                    'judul' => 'Bukti pembayaran ' . $o->nama . ' diterima',
                    'sub' => $o->nomor_pesanan . ' · Rp' . number_format($o->totalTagihan(), 0, ',', '.') . ' · menunggu verifikasi',
                    'url' => route('admin.verification.show', ['id' => $o->id, 'tipe' => 'pesanan']),
                ]);
            } elseif ($o->status_pembayaran === 'bukti_ulang') {
                $rawAktivitas->push([
                    'timestamp' => $ts,
                    'time' => $timeObj,
                    'tipe' => 'doc',
                    'judul' => 'Minta bukti ulang pesanan ' . $o->nama,
                    'sub' => $o->nomor_pesanan . ' · ' . ($o->catatan_panitia ?: 'Bukti kurang jelas'),
                    'url' => route('admin.verification.show', ['id' => $o->id, 'tipe' => 'pesanan']),
                ]);
            }
        }

        // 3. Progres pembangunan terbaru
        $recentProgress = Progress::latest('updated_at')->take(4)->get();
        foreach ($recentProgress as $p) {
            $ts = $p->published_at?->timestamp ?? $p->updated_at?->timestamp ?? $p->created_at?->timestamp ?? 0;
            $timeObj = $p->published_at ?? $p->updated_at ?? $p->created_at;
            $rawAktivitas->push([
                'timestamp' => $ts,
                'time' => $timeObj,
                'tipe' => 'article',
                'judul' => 'Entri progres ' . ($p->status === 'terbit' ? 'diterbitkan' : 'diperbarui'),
                'sub' => $p->judul . ($p->tahap ? ' · Tahap ' . $p->tahap : ''),
                'url' => route('admin.content.edit', $p->id),
            ]);
        }

        // 4. Pengeluaran terbaru
        $recentExpenses = Expense::latest('updated_at')->take(4)->get();
        foreach ($recentExpenses as $e) {
            $ts = $e->updated_at?->timestamp ?? $e->created_at?->timestamp ?? 0;
            $timeObj = $e->updated_at ?? $e->created_at;
            $rawAktivitas->push([
                'timestamp' => $ts,
                'time' => $timeObj,
                'tipe' => 'doc',
                'judul' => 'Pengeluaran: ' . $e->nama,
                'sub' => 'Rp' . number_format($e->jumlah, 0, ',', '.') . ' · ' . $e->kategori,
                'url' => route('admin.expenses.index'),
            ]);
        }

        // Urutkan dan format waktu
        $aktivitas = $rawAktivitas
            ->sortByDesc('timestamp')
            ->take(8)
            ->map(function ($item) {
                $time = $item['time'] ?? null;
                if ($time instanceof \Illuminate\Support\Carbon) {
                    if ($time->isToday()) {
                        $item['waktu'] = $time->format('H.i');
                    } elseif ($time->isYesterday()) {
                        $item['waktu'] = 'Kmrn';
                    } else {
                        $item['waktu'] = $time->translatedFormat('d M');
                    }
                    $item['tanggal_lengkap'] = $time->translatedFormat('d M Y, H.i');
                } else {
                    $item['waktu'] = '—';
                    $item['tanggal_lengkap'] = '';
                }
                return $item;
            })
            ->values();

        return view('admin.dashboard', [
            'donasiTerverifikasi' => $donasiTerverifikasi,
            'danaBersihKaos' => $danaBersihKaos,
            'omzetKaos' => $omzetKaos,
            'biayaProduksi' => $biayaProduksi,
            'pengeluaran' => $pengeluaran,
            'saldo' => $saldo,
            'totalPembangunan' => $totalPembangunan,
            'pengeluaranPembangunan' => $pengeluaranPembangunan,
            'saldoPembangunan' => $saldoPembangunan,
            'perluVerifikasi' => $perluVerifikasiCount,
            'buktiUlang' => $buktiUlangCount,
            'belumBayar' => $belumBayarCount,
            'pesananBelumDikirim' => $pesananBelumDikirimCount,
            'rpPerluVerifikasi' => $rpPerluVerifikasi,
            'donasiTertunda' => $donasiTertunda,
            'daftarPrioritas' => $daftarPrioritas,
            'aktivitas' => $aktivitas,
        ]);
    }

    public function index(Request $request)
    {
        $query = Order::with('design')->where('status_pembayaran', 'terverifikasi');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($w) => $w->where('nama', 'like', "%{$q}%")
                ->orWhere('nomor_pesanan', 'like', "%{$q}%"));
        }

        if ($request->filled('pengiriman')) {
            $query->where('status_pengiriman', $request->string('pengiriman'));
        }

        $orderSorting = [
            'KS-001' => 1,
            'KS-004' => 2,
            'KS-005' => 3,
            'KS-002' => 4,
            'KS-003' => 5,
        ];

        $pesananAll = $query->get()->sortBy(function ($o) use ($orderSorting) {
            return $orderSorting[$o->nomor_pesanan] ?? ($o->id + 10);
        })->values();

        $selectedId = $request->input('selected_id');
        $selectedOrder = null;

        if ($selectedId) {
            $selectedOrder = Order::with('design', 'verifier')
                ->where('status_pembayaran', 'terverifikasi')
                ->find($selectedId);

            if (!$selectedOrder) {
                $unverifiedOrder = Order::find($selectedId);
                if ($unverifiedOrder) {
                    return redirect()->route('admin.verification', [
                        'selected_id' => $unverifiedOrder->id,
                        'selected_tipe' => 'order',
                    ])->with('info', 'Pesanan ' . $unverifiedOrder->nomor_pesanan . ' belum diverifikasi. Silakan periksa pembayaran terlebih dahulu.');
                }
            }
        }

        if (!$selectedOrder && $pesananAll->isNotEmpty()) {
            $selectedOrder = $pesananAll->firstWhere('nomor_pesanan', 'KS-001') ?? $pesananAll->first();
        }

        $page = (int) Paginator::resolveCurrentPage();
        $perPage = 10;
        $slice = $pesananAll->forPage($page, $perPage);

        $pesanan = new \Illuminate\Pagination\LengthAwarePaginator($slice->all(), $pesananAll->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $rekapKaos = Order::with('design')
            ->where('status_pembayaran', 'terverifikasi')
            ->where('jenis', 'kaos_donasi')
            ->get()
            ->sortBy(function ($o) use ($orderSorting) {
                return $orderSorting[$o->nomor_pesanan] ?? $o->id;
            })
            ->values();

        return view('admin.orders', [
            'pesanan' => $pesanan,
            'selectedOrder' => $selectedOrder,
            'rekapKaos' => $rekapKaos,
            'semuaPesanan' => Order::where('status_pembayaran', 'terverifikasi')->count(),
            'siapKirim' => Order::where('status_pembayaran', 'terverifikasi')->where('status_pengiriman', 'siap_kirim')->count(),
            'dalamProduksi' => Order::where('status_pembayaran', 'terverifikasi')->where('status_pengiriman', 'produksi')->count(),
            'menungguPembayaran' => Order::whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'bukti_ulang'])->count(),
        ]);
    }

    public function show(Order $order)
    {
        if ($order->status_pembayaran !== 'terverifikasi') {
            return redirect()->route('admin.verification', [
                'selected_id' => $order->id,
                'selected_tipe' => 'order',
            ])->with('info', 'Pesanan ini belum diverifikasi. Silakan periksa pembayaran terlebih dahulu.');
        }

        return redirect()->route('admin.orders', ['selected_id' => $order->id]);
    }

    public function ship(Request $request, Order $order)
    {
        $data = $request->validate([
            'status_pengiriman' => ['required', 'in:produksi,siap_kirim,terkirim,belum_diproduksi'],
            'resi' => ['nullable', 'string', 'max:80'],
            'catatan_panitia' => ['nullable', 'string', 'max:500'],
        ]);

        $order->status_pengiriman = $data['status_pengiriman'];
        if (array_key_exists('resi', $data)) {
            $order->resi = $data['resi'];
        }
        if (array_key_exists('catatan_panitia', $data)) {
            $order->catatan_panitia = $data['catatan_panitia'];
        }

        if ($data['status_pengiriman'] === 'terkirim' && ! $order->dikirim_at) {
            $order->dikirim_at = now();
        }

        $order->save();

        return redirect()->route('admin.orders', ['selected_id' => $order->id])->with('success', 'Status pengiriman berhasil diperbarui.');
    }
}
