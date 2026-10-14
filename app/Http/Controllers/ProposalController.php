<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /** Lokasi file proposal di dalam folder public. */
    public const FILE = 'uploads/proposal/proposal-pembangunan.pdf';

    public function index()
    {
        $path = public_path(self::FILE);

        return view('proposal', [
            'adaFile' => file_exists($path),
            'ukuran' => file_exists($path) ? filesize($path) : 0,
            'namaFile' => basename(self::FILE),
            'url' => asset(self::FILE) . (file_exists($path) ? '?v=' . filemtime($path) : ''),
            'updatedAt' => file_exists($path) ? date('d M Y, H:i', filemtime($path)) : null,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'proposal' => ['required', 'file', 'mimes:pdf', 'max:25600'],
        ], [
            'proposal.required' => 'Pilih file PDF yang ingin diunggah.',
            'proposal.mimes' => 'File harus berformat PDF (.pdf).',
            'proposal.max' => 'Ukuran file PDF maksimal 25 MB.',
        ]);

        $dir = public_path('uploads/proposal');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $request->file('proposal')->move($dir, 'proposal-pembangunan.pdf');

        return redirect()->route('admin.content')->with('success', 'File proposal berhasil diperbarui dan langsung digunakan di website.');
    }

    /** Streaming file agar nama unduhan tetap rapi. */
    public function download()
    {
        $path = public_path(self::FILE);

        abort_unless(file_exists($path), 404, 'File proposal belum tersedia.');

        return response()->download($path, 'Proposal-Pembangunan-Jemaat-Bali.pdf');
    }
}
