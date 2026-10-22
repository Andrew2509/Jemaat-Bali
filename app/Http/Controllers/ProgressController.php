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
            'entries' => $semua,
            'total' => $semua->count(),
            'fotoCount' => $semua->where('tipe', 'foto')->count(),
            'videoCount' => $semua->where('tipe', 'video')->count(),
            'programInfo' => ProgramInfo::first(),
        ]);
    }
}
