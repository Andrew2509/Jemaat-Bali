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
    /** Langkah 1 & 2: Pemilihan Kaos & Data + Alamat Pengiriman. */
    public function create(Request $request, RajaOngkirService $rajaOngkir)
    {
        $data = $request->session()->get('checkout.data', []);
        $designs = Design::where('aktif', true)->orderBy('harga')->get();
        $selected = $designs->firstWhere('id', (int) ($request->query('desain') ?? ($data['design_id'] ?? 0))) ?? $designs->first();

        if (! $selected) {
            abort(404, 'Desain kaos belum tersedia.');
        }

        $heroShirts = $designs->take(2)->values();

        return view('checkout.data.pemesan', [
            'designs' => $designs,
            'selected' => $selected,
            'heroShirts' => $heroShirts,
            'provinsi' => $rajaOngkir->getProvinces(),
            'isRajaOngkirLive' => $rajaOngkir->isConfigured(),
            'ukuran' => ['S', 'M', 'L', 'XL', 'XXL'],
            'draft' => array_merge([
                'design_id' => $selected->id,
                'tipe' => $request->query('tipe', $data['tipe'] ?? 'kaos_saja'),
                'ukuran' => $request->query('ukuran', $data['ukuran'] ?? ''),
                'jumlah' => $request->query('jumlah', $data['jumlah'] ?? 1),
            ], $data),
        ]);
    }

    /** Simpan langkah 1 & 2 atau lanjut ke pembayaran langsung. */
    public function dataStore(Request $request): RedirectResponse
    {
        $rules = [
            'nama' => ['required', 'string', 'max:120'],
            'nomor' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,}$/'],
            'email' => ['required', 'email', 'max:150'],
            'design_id' => ['required_without:items', 'integer', 'exists:designs,id'],
            'ukuran' => ['nullable', 'string', 'in:S,M,L,XL,XXL'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:100'],
            'items' => ['nullable', 'array'],
            'items.*.design_id' => ['required_with:items', 'integer', 'exists:designs,id'],
            'items.*.ukuran' => ['required_with:items', 'string', 'in:S,M,L,XL,XXL'],
            'items.*.jumlah' => ['required_with:items', 'integer', 'min:1', 'max:100'],
            'items.*.gender' => ['nullable', 'string', 'in:pria,wanita'],
            'tipe' => ['nullable', 'in:kaos_saja,kaos_donasi'],
            'metode' => ['nullable', 'string', 'in:qris,transfer'],
        ];

        // Jika alamat terisi (alur baru Data & Alamat jadi 1 step), sertakan validasi alamat
        if ($request->filled('alamat') || $request->filled('provinsi')) {
            $rules = array_merge($rules, [
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
                'catatan' => ['nullable', 'string', 'max:500'],
            ]);
        }

        $data = $request->validate($rules, [
            'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
            'provinsi.required' => 'Pilih provinsi.',
            'kabupaten.required' => 'Pilih kota / kabupaten.',
            'kecamatan.required' => 'Pilih kecamatan.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi untuk pengiriman bukti dan konfirmasi.',
        ]);

        $session = $request->session()->get('checkout.data', []);
        $session['nama'] = $data['nama'];
        $session['nomor'] = $data['nomor'];
        $session['email'] = $data['email'] ?? '';
        $session['tipe'] = $data['tipe'] ?? 'kaos_saja';
        $session['metode'] = in_array($request->input('metode'), ['transfer', 'qris'], true) ? $request->input('metode') : 'qris';

        if (!empty($data['items'])) {
            $session['items'] = $data['items'];
            $session['design_id'] = $data['items'][0]['design_id'];
        } else {
            $session['items'] = [[
                'design_id' => (int) $data['design_id'],
                'ukuran' => $data['ukuran'] ?? 'M',
                'gender' => 'pria',
                'jumlah' => (int) ($data['jumlah'] ?? 1),
            ]];
            $session['design_id'] = (int) $data['design_id'];
        }

        $this->activeDesign($session['design_id']);

        // Jika alamat terisi, langsung buat pesanan dan ke halaman pembayaran
        if (!empty($data['alamat'])) {
            $orderData = array_merge($session, [
                'alamat' => $data['alamat'],
                'provinsi' => $data['provinsi'],
                'kabupaten' => $data['kabupaten'],
                'kecamatan' => $data['kecamatan'],
                'kelurahan' => $data['kelurahan'] ?? null,
                'kode_pos' => $data['kode_pos'],
                'catatan' => $data['catatan'] ?? null,
                'donasi' => 0,
                'metode' => in_array($request->input('metode'), ['transfer', 'qris'], true) ? $request->input('metode') : 'qris',
            ]);

            $order = $this->createOrder($orderData);
            $request->session()->forget('checkout.data');

            return redirect()->route('checkout.bayar', $order);
        }

        $request->session()->put('checkout.data', $session);

        return redirect()->route('checkout.pengiriman');
    }

    /** Langkah 2: alamat, produk, dan donasi. */
    public function pengiriman(Request $request, RajaOngkirService $rajaOngkir)
    {
        $draft = $request->session()->get('checkout.data');
        if (! $draft || empty($draft['nama']) || empty($draft['design_id'])) {
            return redirect()->route('checkout.pemesanan');
        }

        $designs = Design::where('aktif', true)->orderBy('harga')->get();

        return view('checkout.pengiriman.alamat', [
            'selected' => $this->activeDesign($draft['design_id']),
            'designs' => $designs,
            'designsJson' => $designs->map(fn ($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'harga' => $d->harga,
                'sizes' => $d->pilihanUkuran(),
            ])->values(),
            'initialItems' => old('items', $draft['items'] ?? [[
                'design_id' => $this->activeDesign($draft['design_id'])->id,
                'ukuran' => $draft['ukuran'] ?? '',
                'jumlah' => $draft['jumlah'] ?? 1,
            ]]),
            'provinsi' => $rajaOngkir->getProvinces(),
            'isRajaOngkirLive' => $rajaOngkir->isConfigured(),
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
        $metode = in_array($request->input('metode', $identity['metode'] ?? 'qris'), ['transfer', 'qris'], true)
            ? $request->input('metode', $identity['metode'] ?? 'qris')
            : 'qris';
        $data = array_merge($identity, $data, ['metode' => $metode]);

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
            'ukuran' => ['required', 'string', 'in:S,M,L,XL,XXL'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
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
            'donasi' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'metode' => ['required', 'in:qris'],
        ]), [
            'nomor.regex' => 'Nomor HP tidak valid. Gunakan angka saja, contoh 08123456789.',
        ]);

        // Convert to items array for multi-item storage
        $data['items'] = [[
            'design_id' => $data['design_id'],
            'ukuran' => $data['ukuran'],
            'jumlah' => $data['jumlah'],
        ]];

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

        if ($request->filled('metode') && in_array($request->input('metode'), ['qris', 'transfer'], true)) {
            $order->metode = $request->input('metode');
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.design_id' => ['required', 'integer', 'exists:designs,id'],
            'items.*.ukuran' => ['required', 'string', 'in:S,M,L,XL,XXL'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:100'],
            'donasi' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'metode' => ['nullable', 'string', 'in:qris,transfer'],
        ];
    }

    private function createOrder(array $data): Order
    {
        $items = [];
        $totalKaos = 0;

        $designIds = array_column($data['items'], 'design_id');
        $designs = Design::where('aktif', true)->whereIn('id', $designIds)->get()->keyBy('id');

        foreach ($data['items'] as $itemData) {
            $design = $designs->get($itemData['design_id']);
            if (!$design) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu desain kaos tidak tersedia atau sudah tidak aktif.',
                ]);
            }

            $jumlah = (int) $itemData['jumlah'];
            $subtotal = $design->harga * $jumlah;
            $totalKaos += $subtotal;

            $items[] = [
                'design_id' => $design->id,
                'nama' => $design->nama,
                'ukuran' => $itemData['ukuran'],
                'jumlah' => $jumlah,
                'harga' => $design->harga,
                'subtotal' => $subtotal,
            ];
        }

        $donasi = max(0, (int) ($data['donasi'] ?? 0));
        $firstItem = $items[0] ?? ['ukuran' => null, 'jumlah' => 0, 'design_id' => null];

        return Order::create([
            'nomor_pesanan' => $this->nomorPesanan(),
            'jenis' => 'kaos_donasi',
            'nama' => $data['nama'], 'nomor' => $data['nomor'], 'email' => $data['email'] ?? null,
            'alamat' => $data['alamat'], 'provinsi' => $data['provinsi'], 'kabupaten' => $data['kabupaten'],
            'kecamatan' => $data['kecamatan'], 'kelurahan' => $data['kelurahan'] ?? null, 'kode_pos' => $data['kode_pos'],
            'ukuran' => $firstItem['ukuran'], 'jumlah' => $firstItem['jumlah'], 'design_id' => $firstItem['design_id'],
            'donasi' => $donasi, 'subtotal_kaos' => $totalKaos, 'ongkir' => 0, 'total' => $totalKaos + $donasi,
            'metode' => $data['metode'], 'catatan' => $data['catatan'] ?? null,
            'status_pembayaran' => 'belum_bayar', 'status_pengiriman' => 'belum_diproduksi',
            'items' => $items,
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
