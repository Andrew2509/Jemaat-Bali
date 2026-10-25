<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Order;
use App\Models\ProgramInfo;
use App\Services\FonnteService;
use App\Services\RajaOngkirService;
use App\Support\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /** Langkah 1: data pemesan. */
    public function create(Request $request)
    {
        $data = $request->session()->get('checkout.data', []);
        $designs = Design::where('aktif', true)->orderBy('harga')->get();
        $selected = $designs->firstWhere('id', (int) ($request->query('desain') ?? ($data['design_id'] ?? 0))) ?? $designs->first();

        if (! $selected) {
            abort(404, 'Desain kaos belum tersedia.');
        }

        return view('checkout.data.pemesan', [
            'selected' => $selected,
            'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'draft' => array_merge([
                'design_id' => $selected->id,
                'tipe' => $request->query('tipe', $data['tipe'] ?? 'kaos_saja'),
                'ukuran' => $request->query('ukuran', $data['ukuran'] ?? ''),
                'jumlah' => $request->query('jumlah', $data['jumlah'] ?? 1),
            ], $data),
        ]);
    }

    /** Simpan langkah 1 ke session. */
    public function dataStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'design_id' => ['required', 'integer', 'exists:designs,id'],
            'tipe' => ['nullable', 'in:kaos_saja,kaos_donasi'],
            'ukuran' => ['nullable', 'in:S,M,L,XL,XXL'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
        ]);

        $this->activeDesign($data['design_id']);
        $request->session()->put('checkout.data', array_merge(
            $request->session()->get('checkout.data', []),
            $data,
        ));

        return redirect()->route('checkout.pengiriman');
    }

    /** Langkah 2: alamat, produk, dan donasi. */
    public function pengiriman(Request $request, RajaOngkirService $rajaOngkir)
    {
        $draft = $request->session()->get('checkout.data');
        if (! $draft || empty($draft['nama']) || empty($draft['design_id'])) {
            return redirect()->route('checkout.pemesanan');
        }

        return view('checkout.pengiriman.alamat', [
            'selected' => $this->activeDesign($draft['design_id']),
            'provinsi' => $rajaOngkir->getProvinces(),
            'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'draft' => $draft,
            'tipe' => $draft['tipe'] ?? 'kaos_saja',
        ]);
    }

    /** Simpan langkah 2, buat pesanan, lalu masuk halaman pembayaran. */
    public function pengirimanStore(Request $request): RedirectResponse
    {
        $identity = $request->session()->get('checkout.data');
        if (! $identity || empty($identity['nama']) || empty($identity['design_id'])) {
            return redirect()->route('checkout.pemesanan');
        }

        $data = $request->validate($this->shippingRules(), [
            'provinsi.required' => 'Pilih provinsi.',
        ]);
        $data = array_merge($identity, $data, ['metode' => 'qris']);

        $order = $this->createOrder($data);
        $request->session()->forget('checkout.data');

        return redirect()->route('checkout.bayar', $order);
    }

    /** Endpoint lama. Tetap menerima form lengkap agar tautan/integrasi lama tidak putus. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(array_merge([
            'nama' => ['required', 'string', 'max:120'],
            'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'design_id' => ['required', 'integer', 'exists:designs,id'],
        ], $this->shippingRules(), [
            'metode' => ['required', 'in:qris'],
        ]), [
            'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
        ]);

        return redirect()->route('checkout.bayar', $this->createOrder($data));
    }

    /** Halaman pembayaran QRIS dan unggah bukti. */
    public function bayar(Order $order)
    {
        abort_unless($this->isKaos($order), 404);
        abort_unless($order->status_pembayaran === 'belum_bayar', 403);

        return view('checkout.bayar', [
            'order' => $order->load('design'),
            'program' => ProgramInfo::first(),
        ]);
    }

    /** Simpan bukti pembayaran lalu tampilkan halaman menunggu. */
    public function bayarStore(Request $request, Order $order): RedirectResponse
    {
        abort_unless($this->isKaos($order), 404);
        abort_unless($order->status_pembayaran === 'belum_bayar', 403);

        $request->validate([
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

    /** Halaman menunggu konfirmasi pesanan kaos. */
    public function menunggu(Order $order)
    {
        abort_unless($this->isKaos($order), 404);

        if ($order->status_pembayaran === 'menunggu_verifikasi' && ! Cache::has("wa_waiting_sent_{$order->id}")) {
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

    private function shippingRules(): array
    {
        return [
            'alamat' => ['required', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:120', function ($attribute, $value, $fail) {
                if (! in_array(strtoupper(trim((string) $value)), array_map('strtoupper', Provinsi::all()), true)) {
                    $fail('Provinsi yang dipilih tidak valid.');
                }
            }],
            'kabupaten' => ['required', 'string', 'max:120'],
            'kecamatan' => ['required', 'string', 'max:120'],
            'kelurahan' => ['nullable', 'string', 'max:120'],
            'kode_pos' => ['required', 'string', 'max:10'],
            'ukuran' => ['required', 'string', 'in:S,M,L,XL,XXL'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
            'donasi' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function createOrder(array $data): Order
    {
        $design = $this->activeDesign($data['design_id']);
        $subtotal = $design->harga * (int) $data['jumlah'];
        $donasi = max(0, (int) ($data['donasi'] ?? 0));

        return Order::create([
            'nomor_pesanan' => $this->nomorPesanan(),
            'jenis' => 'kaos_donasi',
            'nama' => $data['nama'], 'nomor' => $data['nomor'], 'email' => $data['email'] ?? null,
            'alamat' => $data['alamat'], 'provinsi' => $data['provinsi'], 'kabupaten' => $data['kabupaten'],
            'kecamatan' => $data['kecamatan'], 'kelurahan' => $data['kelurahan'] ?? null, 'kode_pos' => $data['kode_pos'],
            'ukuran' => $data['ukuran'], 'jumlah' => (int) $data['jumlah'], 'design_id' => $design->id,
            'donasi' => $donasi, 'subtotal_kaos' => $subtotal, 'ongkir' => 0, 'total' => $subtotal + $donasi,
            'metode' => $data['metode'], 'catatan' => $data['catatan'] ?? null,
            'status_pembayaran' => 'belum_bayar', 'status_pengiriman' => 'belum_diproduksi',
        ]);
    }

    private function activeDesign(int $id): Design
    {
        $design = Design::where('aktif', true)->find($id);
        if (! $design) {
            throw ValidationException::withMessages(['design_id' => 'Desain kaos yang dipilih tidak tersedia.']);
        }

        return $design;
    }

    protected function isKaos(Order $order): bool
    {
        return $order->jenis === 'kaos_donasi' || $order->design_id !== null;
    }

    protected function nomorPesanan(): string
    {
        return 'KS-' . now()->format('ymd') . '-' . str_pad((string) ((int) Order::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
}
