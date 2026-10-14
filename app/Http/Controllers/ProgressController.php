<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\ProgramInfo;

class ProgressController extends Controller
{
    public function index()
    {
        $semua = Progress::where('status', 'terbit')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return view('progres', [
            'fotos' => $semua->where('tipe', 'foto'),
            'videos' => $semua->where('tipe', 'video'),
            'total' => $semua->count(),
            'programInfo' => ProgramInfo::first(),
        ]);
    }
}
