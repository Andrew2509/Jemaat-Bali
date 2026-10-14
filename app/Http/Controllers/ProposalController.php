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
        $programInfo = \App\Models\ProgramInfo::first();
        $isCanva = $programInfo ? $programInfo->isCanvaProposal() : false;
        $canvaEmbedUrl = $programInfo?->canva_embed_url;

        return view('proposal', [
            'adaFile' => file_exists($path),
            'ukuran' => file_exists($path) ? filesize($path) : 0,
            'namaFile' => basename(self::FILE),
            'url' => asset(self::FILE) . (file_exists($path) ? '?v=' . filemtime($path) : ''),
            'updatedAt' => file_exists($path) ? date('d M Y, H:i', filemtime($path)) : null,
            'isCanva' => $isCanva,
            'canvaEmbedUrl' => $canvaEmbedUrl,
            'programInfo' => $programInfo,
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

        if ($info = \App\Models\ProgramInfo::first()) {
            $info->update(['proposal_tipe' => 'pdf']);
            cache()->forget('program_info');
        }

        return redirect()->route('admin.content')->with('success', 'File proposal berhasil diperbarui dan mode berkas PDF diaktifkan.');
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'proposal_tipe' => ['required', 'in:canva,pdf'],
            'proposal_canva_url' => ['nullable', 'string', 'max:3000'],
            'judul_proposal' => ['nullable', 'string', 'max:120'],
        ]);

        $info = \App\Models\ProgramInfo::first();
        if ($info) {
            $info->update([
                'proposal_tipe' => $data['proposal_tipe'],
                'proposal_canva_url' => $data['proposal_canva_url'],
                'judul_proposal' => !empty($data['judul_proposal']) ? $data['judul_proposal'] : $info->judul_proposal,
            ]);
        } else {
            \App\Models\ProgramInfo::create([
                'nama_program' => 'Pembangunan Jemaat Bali',
                'deskripsi' => 'Penggalangan dana pembangunan bersama.',
                'whatsapp' => '0812-0000-0000',
                'email' => 'admin@jemaatbali.or.id',
                'judul_proposal' => $data['judul_proposal'] ?: 'Proposal pembangunan',
                'proposal_tipe' => $data['proposal_tipe'],
                'proposal_canva_url' => $data['proposal_canva_url'],
            ]);
        }

        cache()->forget('program_info');

        return redirect()->route('admin.content')->with('success', 'Pengaturan proposal berhasil disimpan.');
    }

    /** Streaming file agar nama unduhan tetap rapi. */
    public function download()
    {
        $path = public_path(self::FILE);

        abort_unless(file_exists($path), 404, 'File proposal belum tersedia.');

        return response()->download($path, 'Proposal-Pembangunan-Jemaat-Bali.pdf');
    }
}
