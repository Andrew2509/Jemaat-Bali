<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSetting;
use App\Services\FonnteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan Provider WhatsApp (Fonnte)
     */
    public function index(FonnteService $service, Request $request): View
    {
        $setting = $service->getSetting();

        // Auto refresh jika ada parameter refresh atau jika sudah lebih dari 1 menit sejak cek terakhir
        if ($request->has('refresh') || ($setting->isConfigured() && (!$setting->last_checked_at || $setting->last_checked_at->diffInMinutes(now()) >= 1))) {
            $service->checkDevice();
            $setting->refresh();
        }

        return view('admin.provider', compact('setting'));
    }

    /**
     * Simpan konfigurasi API token & template pesan
     */
    public function update(Request $request, FonnteService $service): RedirectResponse
    {
        $data = $request->validate([
            'api_token' => ['nullable', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'auto_notify_order' => ['nullable', 'boolean'],
            'auto_notify_waiting' => ['nullable', 'boolean'],
            'auto_notify_verified' => ['nullable', 'boolean'],
            'template_order' => ['nullable', 'string', 'max:3000'],
            'template_waiting' => ['nullable', 'string', 'max:3000'],
            'template_verified' => ['nullable', 'string', 'max:3000'],
        ]);

        $setting = $service->getSetting();

        $data['is_active'] = $request->boolean('is_active');
        $data['auto_notify_order'] = $request->boolean('auto_notify_order');
        $data['auto_notify_waiting'] = $request->boolean('auto_notify_waiting');
        $data['auto_notify_verified'] = $request->boolean('auto_notify_verified');

        $tokenChanged = isset($data['api_token']) && $data['api_token'] !== $setting->api_token;

        $setting->update($data);

        // Langsung cek status koneksi ke Fonnte jika token diisi atau diubah
        if (!empty($setting->api_token) && ($tokenChanged || !$setting->isConnected())) {
            $checkResult = $service->checkDevice($setting->api_token);
            if ($checkResult['success']) {
                return redirect()->route('admin.provider')
                    ->with('success', 'Pengaturan berhasil disimpan. Perangkat Fonnte terhubung!');
            } else {
                return redirect()->route('admin.provider')
                    ->with('warning', 'Pengaturan disimpan, tetapi status perangkat: ' . $checkResult['message']);
            }
        }

        return redirect()->route('admin.provider')
            ->with('success', 'Pengaturan provider WhatsApp berhasil disimpan.');
    }

    /**
     * Cek status koneksi perangkat ke Fonnte API
     */
    public function checkStatus(FonnteService $service, Request $request): JsonResponse|RedirectResponse
    {
        $result = $service->checkDevice();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('admin.provider')
                ->with('success', 'Status perangkat diperbarui: ' . $result['message']);
        }

        return redirect()->route('admin.provider')
            ->with('error', 'Status perangkat: ' . $result['message']);
    }

    /**
     * Uji kirim pesan WhatsApp melalui Fonnte
     */
    public function testSend(Request $request, FonnteService $service): JsonResponse|RedirectResponse
    {
        $request->validate([
            'target' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $result = $service->sendMessage(
            $request->input('target'),
            $request->input('message')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()->route('admin.provider')
                ->with('success', 'Uji coba berhasil: ' . $result['message']);
        }

        return redirect()->route('admin.provider')
            ->with('error', 'Gagal mengirim pesan: ' . $result['message']);
    }
}
