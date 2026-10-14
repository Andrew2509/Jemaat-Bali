<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Order;
use App\Services\FonnteService;
use App\Services\RajaOngkirService;
use App\Support\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    public function index(Request $request, RajaOngkirService $rajaOngkir)
    {
        $designs = Design::where('aktif', true)->orderBy('harga')->get();
        $selectedDesign = $designs->firstWhere('id', (int) $request->query('desain')) ?? $designs->first();
        $tab = $request->query('tab', 'kaos_donasi');
        if (!in_array($tab, ['kaos_donasi', 'donasi_saja'])) {
            $tab = 'kaos_donasi';
        }

        return view('shop', [
            'designs' => $designs,
            'selectedDesign' => $selectedDesign,
            'provinsi' => $rajaOngkir->getProvinces(),
            'isRajaOngkirLive' => $rajaOngkir->isConfigured(),
            'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'tab' => $tab,
            'berhasil' => $request->boolean('sukses'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $jenis = $request->input('jenis', 'donasi_saja');

        if ($jenis === 'kaos_donasi') {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:120'],
                'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
                'email' => ['required', 'email', 'max:150'],
                'alamat' => ['required', 'string', 'max:255'],
                'provinsi' => [
                    'required',
                    'string',
                    'max:120',
                    function ($attribute, $value, $fail) {
                        $normalized = strtoupper(trim((string) $value));
                        $valid = array_map('strtoupper', Provinsi::all());
                        if (! in_array($normalized, $valid, true)) {
                            $fail('Provinsi yang dipilih tidak valid.');
                        }
                    },
                ],
                'kabupaten' => ['required', 'string', 'max:120'],
                'kecamatan' => ['required', 'string', 'max:120'],
                'kelurahan' => ['nullable', 'string', 'max:120'],
                'kode_pos' => ['required', 'string', 'max:10'],
                'design_id' => ['required', 'integer', 'exists:designs,id'],
                'ukuran' => ['required', 'string', 'in:S,M,L,XL,XXL'],
                'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
                'donasi' => ['required', 'integer', 'min:200000', 'max:100000000'],
                'catatan' => ['nullable', 'string', 'max:500'],
            ], [
                'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
                'donasi.min' => 'Nominal donasi untuk Kaos + Donasi minimal Rp 200.000.',
            ]);

            $design = Design::where('aktif', true)->find($data['design_id']);
            if (!$design) {
                throw ValidationException::withMessages([
                    'design_id' => 'Desain kaos yang dipilih tidak tersedia.',
                ]);
            }

            $subtotal = $design->harga * (int) $data['jumlah'];
            $donasi = max(0, (int) ($data['donasi'] ?? 0));
            $total = $subtotal + $donasi;
            $nomorPesanan = 'KD-' . now()->format('ymd') . '-' . str_pad((string) (Order::max('id') + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'nomor_pesanan' => $nomorPesanan,
                'jenis' => 'kaos_donasi',
                'nama' => $data['nama'],
                'nomor' => $data['nomor'],
                'email' => $data['email'] ?? null,
                'alamat' => $data['alamat'],
                'provinsi' => $data['provinsi'],
                'kabupaten' => $data['kabupaten'],
                'kecamatan' => $data['kecamatan'],
                'kelurahan' => $data['kelurahan'] ?? null,
                'kode_pos' => $data['kode_pos'],
                'ukuran' => $data['ukuran'],
                'jumlah' => (int) $data['jumlah'],
                'design_id' => $design->id,
                'donasi' => $donasi,
                'subtotal_kaos' => $subtotal,
                'ongkir' => 0,
                'total' => $total,
                'metode' => 'qris',
                'catatan' => $data['catatan'] ?? null,
                'status_pembayaran' => 'belum_bayar',
                'status_pengiriman' => 'belum_diproduksi',
            ]);
        } else {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:120'],
                'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
                'email' => ['required', 'email', 'max:150'],
                'donasi' => ['required', 'integer', 'min:1000', 'max:100000000'],
                'catatan' => ['nullable', 'string', 'max:500'],
            ], [
                'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
                'donasi.min' => 'Nominal donasi minimal Rp 1.000.',
            ]);

            $donasi = (int) $data['donasi'];
            $nomorPesanan = 'DN-' . now()->format('ymd') . '-' . str_pad((string) (Order::max('id') + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'nomor_pesanan' => $nomorPesanan,
                'jenis' => 'donasi_saja',
                'nama' => $data['nama'],
                'nomor' => $data['nomor'],
                'email' => $data['email'],
                'donasi' => $donasi,
                'total' => $donasi,
                'catatan' => $data['catatan'] ?? null,
                'status_pembayaran' => 'belum_bayar',
                'status_pengiriman' => 'belum_diproduksi',
            ]);
        }

        try {
            app(FonnteService::class)->sendOrderNotification($order);
        } catch (\Throwable $e) {
            Log::warning('Fonnte sendOrderNotification failed: ' . $e->getMessage());
        }

        return redirect()->route('shop.pemesanan', $order);
    }

    public function pemesanan(Order $order)
    {
        return view('pemesanan', [
            'order' => $order->load('design'),
        ]);
    }

    public function payment(Order $order)
    {
        abort_unless($order->status_pembayaran === 'belum_bayar' || $order->status_pembayaran === 'bukti_ulang', 403);

        return view('payment', [
            'order' => $order->load('design'),
        ]);
    }

    public function paymentStore(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->status_pembayaran === 'belum_bayar' || $order->status_pembayaran === 'bukti_ulang', 403);

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

        return redirect()->route('shop.waiting', $order);
    }

    public function waiting(Order $order)
    {
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

        return view('waiting', [
            'order' => $order->load('design'),
            'waNotificationSent' => Cache::has("wa_waiting_sent_{$order->id}"),
        ]);
    }
}
