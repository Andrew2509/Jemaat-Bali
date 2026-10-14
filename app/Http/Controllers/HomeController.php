<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Progress;

class HomeController extends Controller
{
    public function index()
    {
        $proposalPath = public_path(ProposalController::FILE);

        return view('home', [
            'progressTerbaru' => Progress::latest('tanggal')->latest('id')->take(6)->get(),
            'designs' => Design::where('aktif', true)->latest('id')->get(),
            'adaFile' => file_exists($proposalPath),
            'url' => asset(ProposalController::FILE) . (file_exists($proposalPath) ? '?v=' . filemtime($proposalPath) : ''),
        ]);
    }
}
