<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Daftar perintah cache yang dijalankan oleh `php artisan optimize`.
     */
    private const OPTIMIZE_COMMANDS = [
        'config' => 'config:cache',
        'events' => 'event:cache',
        'routes' => 'route:cache',
        'views' => 'view:cache',
    ];

    /**
     * Halaman status cache & optimasi sistem (khusus Super Admin).
     */
    public function index(): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.system.optimize', [
            'status' => $this->cacheStatus(),
            'environment' => $this->environmentInfo(),
            'history' => $this->lastOptimization(),
        ]);
    }

    /**
     * Jalankan `php artisan optimize` (config, event, route, view cache).
     */
    public function optimize(): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $startedAt = microtime(true);
        $steps = [];
        $failed = 0;

        foreach (self::OPTIMIZE_COMMANDS as $label => $command) {
            try {
                $exitCode = Artisan::call($command);
                $output = trim(Artisan::output());
                $failed += $exitCode === 0 ? 0 : 1;
            } catch (\Throwable $e) {
                $exitCode = 1;
                $output = $e->getMessage();
                $failed++;
            }

            $steps[] = [
                'label' => $label,
                'command' => $command,
                'success' => $exitCode === 0,
                'output' => $output,
            ];
        }

        $duration = $this->recordOptimization('optimize', $failed === 0, $steps, $startedAt);

        return redirect()->route('admin.system.optimize')
            ->with($failed === 0 ? 'success' : 'error', $failed === 0
                ? 'Optimasi selesai dalam ' . $duration . ' ms. Cache config, event, route, dan view sudah di-build ulang.'
                : 'Optimasi selesai dengan ' . $failed . ' langkah gagal. Lihat detail hasil di bawah.')
            ->with('system_steps', $steps);
    }

    /**
     * Bersihkan seluruh cache sistem (`php artisan optimize:clear`).
     */
    public function clearCache(): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $startedAt = microtime(true);

        try {
            $exitCode = Artisan::call('optimize:clear');
            $output = trim(Artisan::output());
            $success = $exitCode === 0;
        } catch (\Throwable $e) {
            $output = $e->getMessage();
            $success = false;
        }

        $steps = [[
            'label' => 'cache',
            'command' => 'optimize:clear',
            'success' => $success,
            'output' => $output,
        ]];

        $this->recordOptimization('optimize:clear', $success, $steps, $startedAt);

        return redirect()->route('admin.system.optimize')
            ->with($success ? 'success' : 'error', $success
                ? 'Seluruh cache sistem (config, event, route, view) berhasil dibersihkan.'
                : 'Gagal membersihkan cache. Lihat detail hasil di bawah.')
            ->with('system_steps', $steps);
    }

    /**
     * Sinkronkan database + hak akses + bersihkan cache (deploy Hostinger tanpa SSH).
     */
    public function setupDatabase(): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $startedAt = microtime(true);
        $steps = [];

        foreach ([
            'migrate' => ['migrate', ['--force' => true]],
            'seed' => ['db:seed', ['--class' => 'RoleAndPermissionSeeder', '--force' => true]],
            'cache' => ['optimize:clear', []],
        ] as $label => [$command, $parameters]) {
            try {
                $exitCode = Artisan::call($command, $parameters);
                $output = trim(Artisan::output());
                $success = $exitCode === 0;
            } catch (\Throwable $e) {
                $output = $e->getMessage();
                $success = false;
            }

            $steps[] = [
                'label' => $label,
                'command' => $command,
                'success' => $success,
                'output' => $output,
            ];
        }

        $failed = count(array_filter($steps, fn ($step) => ! $step['success']));

        $this->recordOptimization('setup-database', $failed === 0, $steps, $startedAt);

        return redirect()->route('admin.system.optimize')
            ->with($failed === 0 ? 'success' : 'error', $failed === 0
                ? 'Database, hak akses, dan seluruh cache berhasil disinkronkan di server!'
                : 'Sinkronisasi database selesai dengan ' . $failed . ' langkah gagal. Lihat detail hasil di bawah.')
            ->with('system_steps', $steps);
    }

    /**
     * Status cache aktif (config, event, route, view).
     */
    private function cacheStatus(): array
    {
        return [
            [
                'key' => 'config',
                'label' => 'Configuration Cache',
                'command' => 'config:cache',
                'active' => app()->configurationIsCached(),
                'note' => 'Nilai .env dikunci ke config.',
            ],
            [
                'key' => 'events',
                'label' => 'Event Cache',
                'command' => 'event:cache',
                'active' => app()->eventsAreCached(),
                'note' => 'Daftar event ter-discover lebih cepat.',
            ],
            [
                'key' => 'routes',
                'label' => 'Route Cache',
                'command' => 'route:cache',
                'active' => app()->routesAreCached(),
                'note' => 'Peta route tidak dihitung ulang tiap request.',
            ],
            [
                'key' => 'views',
                'label' => 'View Cache',
                'command' => 'view:cache',
                'active' => $this->compiledViewsCount() > 0,
                'note' => $this->compiledViewsCount() . ' template Blade terkompilasi.',
            ],
        ];
    }

    /**
     * Informasi environment server (real-time).
     */
    private function environmentInfo(): array
    {
        return [
            ['label' => 'APP_ENV', 'value' => config('app.env')],
            ['label' => 'APP_DEBUG', 'value' => config('app.debug') ? 'true (aktif)' : 'false (nonaktif)'],
            ['label' => 'CACHE_STORE', 'value' => config('cache.default')],
            ['label' => 'SESSION_DRIVER', 'value' => config('session.driver')],
            ['label' => 'QUEUE_CONNECTION', 'value' => config('queue.default')],
            ['label' => 'DATABASE', 'value' => config('database.default')],
            ['label' => 'PHP', 'value' => PHP_VERSION],
            ['label' => 'Laravel', 'value' => app()->version()],
            ['label' => 'Maintenance Mode', 'value' => app()->isDownForMaintenance() ? 'AKTIF' : 'nonaktif'],
            ['label' => 'Waktu Server', 'value' => now()->setTimezone(config('app.timezone'))->format('d/m/Y H:i:s') . ' (' . config('app.timezone') . ')'],
        ];
    }

    /**
     * Riwayat optimasi terakhir (disimpan di file agar tidak hilang saat cache dibersihkan).
     */
    private function lastOptimization(): ?array
    {
        $file = $this->historyFile();

        if (! File::exists($file)) {
            return null;
        }

        $decoded = json_decode((string) File::get($file), true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Simpan hasil optimasi terakhir ke storage/app/system.
     */
    private function recordOptimization(string $action, bool $success, array $steps, float $startedAt): int
    {
        $duration = (int) round((microtime(true) - $startedAt) * 1000);

        $payload = [
            'action' => $action,
            'success' => $success,
            'at' => now()->toDateTimeString(),
            'duration_ms' => $duration,
            'steps' => $steps,
        ];

        try {
            $directory = storage_path('app/system');
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            File::put($this->historyFile(), json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            // Riwayat bersifat informatif, kegagalan menulis tidak boleh menghentikan proses.
        }

        return $duration;
    }

    private function historyFile(): string
    {
        return storage_path('app/system/last_optimize.json');
    }

    private function compiledViewsCount(): int
    {
        $files = glob(storage_path('framework/views/*.php'));

        return is_array($files) ? count($files) : 0;
    }

    /**
     * Hanya Super Admin yang boleh menjalankan optimasi sistem.
     */
    private function authorizeSuperAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang berhak menjalankan optimasi sistem.');
        }
    }
}
