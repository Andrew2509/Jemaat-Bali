<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $kategori = $request->query('kategori');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Expense::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($from) {
            $query->whereDate('tanggal', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal', '<=', $to);
        }

        $expenses = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        // Metrik Keuangan Global untuk Pemantauan Saldo Pembangunan
        $allExpenses = Expense::all();
        $totalPengeluaran = $allExpenses->sum('jumlah');
        $belanjaPembangunan = $allExpenses->reject(fn($e) => in_array($e->kategori, ['Produksi kaos', 'Pengiriman']))->sum('jumlah');
        $biayaProduksi = $allExpenses->where('kategori', 'Produksi kaos')->sum('jumlah');
        $biayaPengiriman = $allExpenses->where('kategori', 'Pengiriman')->sum('jumlah');

        $totalDonasi = Donation::where('status', 'terverifikasi')->sum('nominal');
        $omzetKaos = Order::where('status_pembayaran', 'terverifikasi')->sum('subtotal_kaos');
        $danaBersihKaos = max(0, $omzetKaos - $biayaProduksi);
        $danaPembangunan = $totalDonasi + $danaBersihKaos;
        $saldoPembangunan = $danaPembangunan - $belanjaPembangunan;

        $kategoriList = [
            'Material fondasi',
            'Transportasi material',
            'Material bangunan',
            'Upah tukang & pekerja',
            'Alat & perlengkapan',
            'Produksi kaos',
            'Pengiriman',
            'Operasional & lain-lain',
        ];

        return view('admin.expenses', compact(
            'expenses',
            'totalPengeluaran',
            'belanjaPembangunan',
            'biayaProduksi',
            'biayaPengiriman',
            'danaPembangunan',
            'saldoPembangunan',
            'kategoriList',
            'search',
            'kategori',
            'from',
            'to'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:120',
            'kategori' => 'required|string|max:60',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['jumlah'] = (int) $validated['jumlah'];

        $expense = Expense::create($validated);

        return redirect()->back()
            ->with('success', "Pengeluaran '{$expense->nama}' sebesar Rp" . number_format($expense->jumlah, 0, ',', '.') . " berhasil dicatat. Saldo pembangunan otomatis terpotong.");
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:120',
            'kategori' => 'required|string|max:60',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['jumlah'] = (int) $validated['jumlah'];

        $expense->update($validated);

        return redirect()->back()
            ->with('success', "Data pengeluaran '{$expense->nama}' berhasil diperbarui.");
    }

    public function destroy(Expense $expense)
    {
        $nama = $expense->nama;
        $jumlah = $expense->jumlah;
        $expense->delete();

        return redirect()->back()
            ->with('success', "Pengeluaran '{$nama}' (Rp" . number_format($jumlah, 0, ',', '.') . ") berhasil dihapus.");
    }
}
