<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Design;
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
     * Show form to upload and create a new t-shirt design.
     */
    public function create()
    {
        return view('admin.designs.create', [
            'design' => new Design([
                'harga' => 150000,
                'aktif' => true,
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
            'gambar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'video_360' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ]);

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
            'gambar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            'video_360' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ]);

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
