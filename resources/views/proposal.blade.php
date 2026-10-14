@extends('layouts.app')

@section('title', ($programInfo->judul_proposal ?? 'Proposal Pembangunan') . ' — ' . ($programInfo->nama_program ?? 'Jemaat Bali'))
@section('description', 'Baca langsung dokumen proposal program ' . ($programInfo->nama_program ?? 'Jemaat Bali') . ' dalam tampilan buku digital.')

@section('content')
    {{-- Navbar tetap putih dari layouts.app (sticky). Isi reader ada di partial agar bisa dipakai ulang di Home. --}}
    @include('partials.proposal-reader')

    @if ($adaFile && session('success'))
        <div class="mx-auto max-w-6xl px-3 sm:px-6">
            <div class="mx-auto mt-4 flex max-w-2xl items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-200 text-xs font-bold">✓</span>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif
@endsection
