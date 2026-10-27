<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use App\Models\WhatsappSetting;
use App\Services\FonnteService;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProviderController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan Provider (WhatsApp Fonnte & RajaOngkir)
     */
    public function index(FonnteService $service, RajaOngkirService $rajaOngkir, Request $request): View
    {
        $setting = $service->getSetting();
        $shippingSetting = $rajaOngkir->getSetting();

        // Auto refresh hanya jika ada parameter refresh atau jika belum pernah dicek sama sekali
        if ($request->has('refresh') || ($setting->isConfigured() && !$setting->last_checked_at)) {
            $service->checkDevice();
            $setting->refresh();
        }

        return view('admin.provider', compact('setting', 'shippingSetting'));
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
            'auto_notify_waiting' => ['nullable', 'boolean'],
            'auto_notify_verified' => ['nullable', 'boolean'],
            'template_waiting' => ['nullable', 'string', 'max:3000'],
            'template_verified' => ['nullable', 'string', 'max:3000'],
        ]);

        $setting = $service->getSetting();

        $data['is_active'] = $request->boolean('is_active');
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

    private function sanitizeApiKey(?string $key): ?string
    {
        if ($key === null) {
            return null;
        }
        $key = trim($key);
        // Hapus jika ada prefix 'Key:' atau 'key:' atau 'Header:' atau kutip yang tidak sengaja tertempel
        $key = preg_replace('/^(key|header\s*:\s*key)\s*:\s*/i', '', $key);
        $key = trim($key, " \t\n\r\0\x0B\"'");
        return $key === '' ? null : $key;
    }

    /**
     * Simpan konfigurasi API Key RajaOngkir
     */
    public function updateRajaOngkir(Request $request, RajaOngkirService $service): RedirectResponse
    {
        $data = $request->validate([
            'api_key' => ['nullable', 'string', 'max:500'],
            'base_url' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $sanitizedKey = $this->sanitizeApiKey($data['api_key'] ?? null);
        $data['api_key'] = $sanitizedKey;
        $data['is_active'] = $request->boolean('is_active');
        $data['base_url'] = rtrim($data['base_url'], '/');

        $shippingSetting = $service->getSetting();
        $oldKey = $shippingSetting->getEffectiveApiKey();
        $keyChanged = ($sanitizedKey ?? '') !== ($oldKey ?? '');

        // Persist ke database dan backup JSON
        $shippingSetting->saveSettings($data);
        $service->setSetting($shippingSetting);
        Cache::forget('rajaongkir_provinces');

        // Langsung tes koneksi jika API key terisi
        $effectiveKey = $shippingSetting->getEffectiveApiKey();
        if (!empty($effectiveKey) && ($keyChanged || !$shippingSetting->isConnected())) {
            $testResult = $service->testConnection($effectiveKey, $shippingSetting->getEffectiveBaseUrl());
            if ($testResult['success']) {
                return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
                    ->with('success', 'API Key RajaOngkir berhasil disimpan & terhubung! ' . $testResult['message']);
            } else {
                return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
                    ->with('warning', 'API Key berhasil disimpan di sistem, namun tes respon RajaOngkir: ' . $testResult['message']);
            }
        }

        return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
            ->with('success', 'Pengaturan RajaOngkir berhasil disimpan.');
    }

    /**
     * Cek & Uji koneksi API RajaOngkir
     */
    public function testRajaOngkir(Request $request, RajaOngkirService $service): JsonResponse|RedirectResponse
    {
        $result = $service->testConnection();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
            ->with('error', $result['message']);
    }
}

