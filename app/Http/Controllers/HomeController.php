<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\ProgramInfo;
use App\Models\Progress;

class HomeController extends Controller
{
    public function index()
    {
        $proposalPath = public_path(ProposalController::FILE);
        $programInfo = ProgramInfo::first();
        $isCanva = $programInfo ? $programInfo->isCanvaProposal() : false;
        $canvaEmbedUrl = $programInfo?->canva_embed_url;

        return view('home', [
            'progressTerbaru' => Progress::published()->latest('tanggal')->latest('id')->take(6)->get(),
            'designs' => Design::where('aktif', true)->latest('id')->get(),
            'adaFile' => file_exists($proposalPath),
            'url' => asset(ProposalController::FILE) . (file_exists($proposalPath) ? '?v=' . filemtime($proposalPath) : ''),
            'programInfo' => $programInfo,
            'isCanva' => $isCanva,
            'canvaEmbedUrl' => $canvaEmbedUrl,
        ]);
    }
}
