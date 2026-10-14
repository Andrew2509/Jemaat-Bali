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

        // Aktivitas terbaru matching design
        $aktivitas = collect([
            [
                'waktu' => '09.15',
                'tipe' => 'doc',
                'judul' => 'Bukti donasi Clara Wijaya diterima',
                'sub' => 'DN-010 · Rp500.000 · menunggu verifikasi',
            ],
            [
                'waktu' => '08.40',
                'tipe' => 'box',
                'judul' => 'Pesanan Agatha siap dikirim',
                'sub' => 'KS-001 · 2 kaos Keluarga Kudus · pembayaran terverifikasi',
            ],
            [
                'waktu' => '08.10',
                'tipe' => 'check',
                'judul' => 'Donasi Agatha diverifikasi panitia',
                'sub' => 'DN-004 · Rp300.000 · bagian dari KS-001',
            ],
            [
                'waktu' => '07.45',
                'tipe' => 'article',
                'judul' => 'Entri progres fondasi diperbarui',
                'sub' => 'Pemasangan fondasi bangunan utama · terbit',
            ],
        ]);

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
