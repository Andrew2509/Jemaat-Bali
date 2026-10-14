<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class BuktiController extends Controller
{
    public function show(string $type, int $id)
    {
        $model = match ($type) {
            'pesanan' => Order::find($id),
            'donasi' => Donation::find($id),
            default => null,
        };

        abort_unless($model && $model->bukti_path, 404);
        abort_unless(Storage::disk('public')->exists($model->bukti_path), 404);

        return Storage::disk('public')->response($model->bukti_path);
    }
}
