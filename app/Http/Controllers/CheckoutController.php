<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Services\FonnteService;
use App\Support\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /** Halaman Pemesanan kaos (form data + pengiriman + metode pembayaran). */
    public function create(Request $request)
    {
        $designs = Design::where('aktif', true)->orderBy('harga')->get();

        $selected = $designs->firstWhere('id', (int) $request->query('desain'));
        if (! $selected) {
            $selected = $designs->first();
        }

        if (! $selected) {
            abort(404, 'Desain kaos belum tersedia.');
        }

        $tipe = $request->query('tipe', 'kaos_saja');

        return view('checkout.pemesanan', [
            'designs' => $designs,
            'selected' => $selected,
            'provinsi' => Provinsi::all(),
            'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'tipe' => $tipe,
        ]);
    }

    /** Simpan pesanan kaos (dengan atau tanpa donasi sukarela) lalu lanjut ke halaman Pembayaran. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'in:'.implode(',', Provinsi::all())],
            'kabupaten' => ['required', 'string', 'max:120'],
            'kecamatan' => ['required', 'string', 'max:120'],
            'kode_pos' => ['required', 'string', 'max:10'],
            'design_id' => ['required', 'integer', 'exists:designs,id'],
            'ukuran' => ['required', 'string', 'in:S,M,L,XL,XXL'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
            'donasi' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'metode' => ['required', 'in:qris'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
        ]);

        $design = Design::where('aktif', true)->find($data['design_id']);
        if (! $design) {
            throw ValidationException::withMessages([
                'design_id' => 'Desain kaos yang dipilih tidak tersedia.',
            ]);
        }

        $subtotal = $design->harga * (int) $data['jumlah'];
        $donasi = max(0, (int) ($data['donasi'] ?? 0));
        $total = $subtotal + $donasi;

        $order = Order::create([
            'nomor_pesanan' => $this->nomorPesanan(),
            'jenis' => 'kaos_donasi',
            'nama' => $data['nama'],
            'nomor' => $data['nomor'],
            'email' => $data['email'] ?? null,
            'alamat' => $data['alamat'],
            'provinsi' => $data['provinsi'],
            'kabupaten' => $data['kabupaten'],
            'kecamatan' => $data['kecamatan'],
            'kode_pos' => $data['kode_pos'],
            'ukuran' => $data['ukuran'],
            'jumlah' => (int) $data['jumlah'],
            'design_id' => $design->id,
            'donasi' => $donasi,
            'subtotal_kaos' => $subtotal,
            'ongkir' => 0,
            'total' => $total,
            'metode' => $data['metode'],
            'catatan' => $data['catatan'] ?? null,
            'status_pembayaran' => 'belum_bayar',
            'status_pengiriman' => 'belum_diproduksi',
        ]);

        try {
            app(\App\Services\FonnteService::class)->sendOrderNotification($order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fonnte sendOrderNotification failed: ' . $e->getMessage());
        }

        return redirect()->route('checkout.bayar', $order);
    }

    /** Halaman Pembayaran kaos (QRIS + unggah bukti). */
    public function bayar(Order $order)
    {
        abort_unless($this->isKaos($order), 404);
        abort_unless($order->status_pembayaran === 'belum_bayar', 403);

        return view('checkout.bayar', [
            'order' => $order->load('design'),
            'program' => ProgramInfo::first(),
        ]);
    }

    /** Simpan bukti pembayaran lalu tampilkan halaman Menunggu konfirmasi. */
    public function bayarStore(Request $request, Order $order): RedirectResponse
    {
        abort_unless($this->isKaos($order), 404);
        abort_unless($order->status_pembayaran === 'belum_bayar', 403);

        $data = $request->validate([
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'bukti.required' => 'Pilih file bukti pembayaran (JPG, PNG, WebP, atau PDF).',
            'bukti.mimes' => 'File harus berformat JPG, PNG, WebP, atau PDF.',
            'bukti.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if ($order->bukti_path) {
            Storage::disk('public')->delete($order->bukti_path);
        }

        $order->bukti_path = $request->file('bukti')->store('bukti-pembayaran', 'public');
        $order->status_pembayaran = 'menunggu_verifikasi';
        $order->save();

        // Otomatis kirim notifikasi WhatsApp ke pemesan
        try {
            $sendRes = app(FonnteService::class)->sendWaitingNotification($order);
            if ($sendRes && ($sendRes['success'] ?? false)) {
                Cache::put("wa_waiting_sent_{$order->id}", true, now()->addDays(7));
            }
        } catch (\Throwable $e) {
            Log::warning('Fonnte sendWaitingNotification failed: ' . $e->getMessage());
        }

        return redirect()->route('checkout.menunggu', $order);
    }

    /** Halaman Menunggu konfirmasi pesanan kaos. */
    public function menunggu(Order $order)
    {
        abort_unless($this->isKaos($order), 404);

        // Jika status menunggu_verifikasi dan belum tercatat dikirim, coba kirimkan pesan WA
        if ($order->status_pembayaran === 'menunggu_verifikasi' && !Cache::has("wa_waiting_sent_{$order->id}")) {
            try {
                $sendRes = app(FonnteService::class)->sendWaitingNotification($order);
                if ($sendRes && ($sendRes['success'] ?? false)) {
                    Cache::put("wa_waiting_sent_{$order->id}", true, now()->addDays(7));
                }
            } catch (\Throwable $e) {
                Log::warning('Fonnte sendWaitingNotification fallback failed: ' . $e->getMessage());
            }
        }

        return view('checkout.menunggu', [
            'order' => $order->load('design'),
            'program' => ProgramInfo::first(),
            'waNotificationSent' => Cache::has("wa_waiting_sent_{$order->id}"),
        ]);
    }

    protected function isKaos(Order $order): bool
    {
        return $order->jenis === 'kaos_donasi' || $order->design_id !== null;
    }

    protected function nomorPesanan(): string
    {
        $urut = (int) Order::max('id') + 1;

        return 'KS-' . now()->format('ymd') . '-' . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}
