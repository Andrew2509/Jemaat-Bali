<?php

namespace App\Http\Controllers;

use App\Models\Design;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::where('aktif', true)
            ->withCount('orders')
            ->orderBy('harga')
            ->get();

        // Cari produk yang memiliki aset 360 jika tidak ada parameter desain eksplisit
        $selected = null;
        if (request()->has('desain')) {
            $selected = $designs->firstWhere('id', (int) request('desain'));
        }
        if (! $selected) {
            $selected = $designs->first(fn (Design $d) => $this->resolve360Config($d) !== null) ?? $designs->first();
        }

        $catalog = $designs->map(function (Design $d) {
            $viewer360 = $this->resolve360Config($d);

            return [
                'id' => $d->id,
                'nama' => str_replace('"', '', $d->nama),
                'deskripsi' => $d->deskripsi,
                'harga' => $d->harga,
                'harga_format' => number_format($d->harga, 0, ',', '.'),
                'gambar' => asset($d->gambar),
                'pesanan' => $d->orders_count,
                'url' => route('checkout.pemesanan', ['desain' => $d->id]),
                'has_360' => $viewer360 !== null,
                'viewer_config' => $viewer360,
            ];
        })->values();

        $catalogJson = json_encode(
            $catalog,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );

        $selectedViewerConfig = $selected ? $this->resolve360Config($selected) : null;

        return view('design-kaos', [
            'designs' => $designs,
            'selected' => $selected,
            'selectedViewerConfig' => $selectedViewerConfig,
            'catalogJson' => $catalogJson,
        ]);
    }

    /**
     * Video 360 garment dipakai semua desain pada halaman Design Kaos.
     * Digeser dengan kursor: ke kanan = durasi maju, ke kiri = durasi mundur.
     */
    protected function resolve360Config(Design $d): ?array
    {
        $videoFile = 'videos/Garment_rotating_360_degrees_1080p_20261012065448.mp4';

        if (! file_exists(public_path($videoFile))) {
            return null;
        }

        return [
            'productName' => str_replace('"', '', $d->nama),
            'videoMp4' => asset($videoFile),
            'videoWebm' => null,
            'imagePath' => null,
            'initialAngle' => 0,
            'sensitivity' => 1.0,
            'showHint' => true,
        ];
    }
}
