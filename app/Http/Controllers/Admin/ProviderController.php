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

        // Auto refresh jika ada parameter refresh atau jika sudah lebih dari 1 menit sejak cek terakhir
        if ($request->has('refresh') || ($setting->isConfigured() && (!$setting->last_checked_at || $setting->last_checked_at->diffInMinutes(now()) >= 1))) {
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

    /**
     * Simpan konfigurasi API Key RajaOngkir
     */
    public function updateRajaOngkir(Request $request, RajaOngkirService $service): RedirectResponse
    {
        $data = $request->validate([
            'api_key' => ['nullable', 'string', 'max:255'],
            'base_url' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $shippingSetting = $service->getSetting();

        $data['is_active'] = $request->boolean('is_active');
        $data['base_url'] = rtrim($data['base_url'], '/');

        $keyChanged = isset($data['api_key']) && $data['api_key'] !== $shippingSetting->api_key;

        $shippingSetting->fill($data);
        if ($shippingSetting->exists) {
            $shippingSetting->save();
        }

        // Langsung tes koneksi jika API key terisi
        if (!empty($shippingSetting->getEffectiveApiKey()) && ($keyChanged || !$shippingSetting->isConnected())) {
            $testResult = $service->testConnection($shippingSetting->getEffectiveApiKey(), $shippingSetting->getEffectiveBaseUrl());
            if ($testResult['success']) {
                return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
                    ->with('success', 'Konfigurasi RajaOngkir disimpan! ' . $testResult['message']);
            } else {
                return redirect()->route('admin.provider', ['tab' => 'rajaongkir'])
                    ->with('warning', 'Konfigurasi disimpan, namun koneksi: ' . $testResult['message']);
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

