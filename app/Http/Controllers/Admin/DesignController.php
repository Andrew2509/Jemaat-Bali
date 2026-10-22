<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Design;
use App\Models\ProgramInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DesignController extends Controller
{
    /**
     * Display a listing of t-shirt designs.
     */
    public function index(Request $request)
    {
        $query = Design::withCount('orders')->latest('id');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->status === 'aktif' ? 1 : 0);
        }

        $designs = $query->paginate(12)->withQueryString();

        $totalDesigns = Design::count();
        $totalAktif = Design::where('aktif', 1)->count();
        $totalNonaktif = Design::where('aktif', 0)->count();

        $selectedId = $request->query('selected_id');
        $selectedDesign = $selectedId ? Design::find($selectedId) : $designs->first();

        return view('admin.designs.index', compact(
            'designs',
            'selectedDesign',
            'totalDesigns',
            'totalAktif',
            'totalNonaktif'
        ));
    }

    /**
     * Show size chart table editor for the collection or specific design.
     */
    public function sizeChart(Request $request)
    {
        $programInfo = ProgramInfo::first() ?? new ProgramInfo();
        $design = null;
        if ($request->filled('design_id')) {
            $design = Design::find($request->query('design_id'));
        }

        $gender = $request->query('gender') === 'wanita' ? 'wanita' : 'pria';
        $sizeChart = $design ? $design->getSizeChartFor($gender) : $programInfo->getSizeChartFor($gender);
        $designs = Design::latest('id')->get();

        return view('admin.designs.size-chart', compact('sizeChart', 'programInfo', 'design', 'designs', 'gender'));
    }

    /**
     * Update size chart table (columns, rows, title, note).
     */
    public function updateSizeChart(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:500'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['required', 'string', 'max:100'],
            'rows' => ['required', 'array', 'min:1'],
            'rows.*' => ['required', 'array'],
            'gender' => ['nullable', 'in:pria,wanita'],
        ]);

        $gender = $request->input('gender') === 'wanita' ? 'wanita' : 'pria';

        $columns = array_values(array_map('trim', $request->input('columns', [])));
        $numCols = count($columns);

        $rawRows = (array) $request->input('rows', []);
        $cleanRows = [];

        foreach ($rawRows as $row) {
            $rowValues = is_array($row) ? array_values($row) : [];
            $normalizedRow = [];
            for ($i = 0; $i < $numCols; $i++) {
                $normalizedRow[] = isset($rowValues[$i]) ? trim((string) $rowValues[$i]) : '';
            }
            $cleanRows[] = $normalizedRow;
        }

        $chartData = [
            'title' => trim($request->input('title', 'Panduan Ukuran Kaos (cm)')),
            'note' => trim($request->input('note', 'Toleransi ukuran ±1 cm')),
            'columns' => $columns,
            'rows' => $cleanRows,
        ];

        if ($request->filled('design_id')) {
            $design = Design::findOrFail($request->input('design_id'));
            $redirectParams = ['design_id' => $design->id];
            if ($gender === 'wanita') {
                $redirectParams['gender'] = 'wanita';
            }

            if ($request->boolean('reset_to_global')) {
                $design->size_chart = null;
                $design->save();
                return redirect()->route('admin.designs.size_chart', $redirectParams)
                    ->with('success', "Tabel ukuran kaos \"{$design->nama}\" direset mengikuti tabel standar koleksi.");
            }

            $existing = is_array($design->size_chart) ? $design->size_chart : [];
            // Migrasi data lama (satu tabel di root) menjadi versi Pria
            if (isset($existing['columns']) || isset($existing['rows'])) {
                $existing = ['pria' => $existing];
            }
            $existing[$gender] = $chartData;

            $design->size_chart = $existing;
            $design->save();

            $genderLabel = $gender === 'wanita' ? 'Cewe' : 'Pria';
            return redirect()->route('admin.designs.size_chart', $redirectParams)
                ->with('success', "Tabel ukuran kaos {$genderLabel} khusus \"{$design->nama}\" berhasil diperbarui!");
        }

        $programInfo = ProgramInfo::first();
        if (! $programInfo) {
            $programInfo = ProgramInfo::create([
                'nama_program' => 'Pembangunan Gereja',
                'deskripsi' => 'Program penggalangan dana pembangunan',
                'whatsapp' => '08120000000',
                'email' => 'admin@jemaatbali.or.id',
                'judul_proposal' => 'Proposal Pembangunan',
            ]);
        }

        $existing = is_array($programInfo->size_chart) ? $programInfo->size_chart : [];
        // Migrasi data lama (satu tabel di root) menjadi versi Pria
        if (isset($existing['columns']) || isset($existing['rows'])) {
            $existing = ['pria' => $existing];
        }
        $existing[$gender] = $chartData;

        $programInfo->size_chart = $existing;
        $programInfo->save();

        $redirectParams = $gender === 'wanita' ? ['gender' => 'wanita'] : [];
        $genderLabel = $gender === 'wanita' ? 'Cewe' : 'Pria';

        return redirect()->route('admin.designs.size_chart', $redirectParams)
            ->with('success', "Tabel ukuran standar Koleksi Kaos {$genderLabel} berhasil diperbarui dan diterapkan ke website!");
    }

    /**
     * Show form to upload and create a new t-shirt design.
     */
    public function create()
    {
        return view('admin.designs.create', [
            'design' => new Design([
                'harga' => 150000,
                'aktif' => true,
                'ukuran' => Design::ALL_UKURAN,
            ]),
        ]);
    }

    /**
     * Store a newly created t-shirt design.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'harga' => ['required', 'integer', 'min:10000'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'aktif' => ['required', 'boolean'],
            'ukuran' => ['nullable', 'array'],
            'ukuran.*' => ['string', Rule::in(Design::ALL_UKURAN)],
            'gambar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'video_360' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ]);

        $data['ukuran'] = $request->has('ukuran') ? array_values((array) $request->input('ukuran')) : [];

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'design-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage public/designs
            $path = $file->storeAs('designs', $filename, 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        if ($request->hasFile('video_360')) {
            $videoFile = $request->file('video_360');
            $videoName = 'video360-' . time() . '-' . uniqid() . '.' . $videoFile->getClientOriginalExtension();
            $videoPath = $videoFile->storeAs('designs/videos', $videoName, 'public');
            $data['video_360'] = 'storage/' . $videoPath;
        }

        $design = Design::create($data);

        return redirect()->route('admin.designs', ['selected_id' => $design->id])
            ->with('success', "Desain kaos \"{$design->nama}\" berhasil diunggah dan disimpan ke koleksi!");
    }

    /**
     * Show form to edit an existing t-shirt design.
     */
    public function edit(Design $design)
    {
        return view('admin.designs.create', compact('design'));
    }

    /**
     * Update the specified t-shirt design.
     */
    public function update(Request $request, Design $design)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'harga' => ['required', 'integer', 'min:10000'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'aktif' => ['required', 'boolean'],
            'ukuran' => ['nullable', 'array'],
            'ukuran.*' => ['string', Rule::in(Design::ALL_UKURAN)],
            'gambar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'video_360' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ]);

        $data['ukuran'] = $request->has('ukuran') ? array_values((array) $request->input('ukuran')) : [];

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'design-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('designs', $filename, 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        if ($request->hasFile('video_360')) {
            $videoFile = $request->file('video_360');
            $videoName = 'video360-' . time() . '-' . uniqid() . '.' . $videoFile->getClientOriginalExtension();
            $videoPath = $videoFile->storeAs('designs/videos', $videoName, 'public');
            $data['video_360'] = 'storage/' . $videoPath;
        }

        $design->update($data);

        return redirect()->route('admin.designs', ['selected_id' => $design->id])
            ->with('success', "Desain kaos \"{$design->nama}\" berhasil diperbarui.");
    }

    /**
     * Toggle active status in public website catalog.
     */
    public function toggleStatus(Design $design)
    {
        $design->aktif = ! $design->aktif;
        $design->save();

        $statusText = $design->aktif ? 'diaktifkan di website' : 'dinonaktifkan';
        return back()->with('success', "Koleksi kaos \"{$design->nama}\" berhasil {$statusText}.");
    }

    /**
     * Remove the specified design from storage if no orders exist.
     */
    public function destroy(Design $design)
    {
        if ($design->orders()->count() > 0) {
            $design->aktif = false;
            $design->save();

            return back()->with('success', "Desain kaos memiliki riwayat pesanan, status dialihkan menjadi nonaktif.");
        }

        $nama = $design->nama;
        $design->delete();

        return redirect()->route('admin.designs')
            ->with('success', "Desain kaos \"{$nama}\" berhasil dihapus dari koleksi.");
    }
}
