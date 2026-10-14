<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    /**
     * Dapatkan daftar provinsi
     */
    public function provinces(RajaOngkirService $service): JsonResponse
    {
        $provinces = $service->getProvinces();

        return response()->json([
            'success' => true,
            'is_live' => $service->isConfigured(),
            'data' => $provinces,
        ]);
    }

    /**
     * Dapatkan daftar kota / kabupaten berdasarkan province_id
     */
    public function cities(Request $request, RajaOngkirService $service): JsonResponse
    {
        $provinceId = $request->query('province_id');
        if (!$provinceId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter province_id wajib diisi.',
                'data' => [],
            ], 400);
        }

        $cities = $service->getCities($provinceId);

        return response()->json([
            'success' => true,
            'is_live' => $service->isConfigured(),
            'data' => $cities,
        ]);
    }

    /**
     * Dapatkan daftar kecamatan (districts) berdasarkan city_id
     * Termasuk informasi zip_code untuk otomatisasi kode pos
     */
    public function districts(Request $request, RajaOngkirService $service): JsonResponse
    {
        $cityId = $request->query('city_id');
        if (!$cityId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter city_id wajib diisi.',
                'data' => [],
            ], 400);
        }

        $districts = $service->getDistricts($cityId);

        return response()->json([
            'success' => true,
            'is_live' => $service->isConfigured(),
            'data' => $districts,
        ]);
    }

    /**
     * Dapatkan daftar kelurahan / desa (subdistricts) berdasarkan district_id
     */
    public function subdistricts(Request $request, RajaOngkirService $service): JsonResponse
    {
        $districtId = $request->query('district_id');
        if (!$districtId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter district_id wajib diisi.',
                'data' => [],
            ], 400);
        }

        $subdistricts = $service->getSubDistricts($districtId);

        return response()->json([
            'success' => true,
            'is_live' => $service->isConfigured(),
            'data' => $subdistricts,
        ]);
    }
}
