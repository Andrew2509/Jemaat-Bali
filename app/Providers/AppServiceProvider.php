<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/components'));

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
        });

        \Illuminate\Support\Facades\Gate::after(function ($user, $ability, $result) {
            if ($result !== null) {
                return $result;
            }
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
            return false;
        });

        \Illuminate\Support\Facades\Blade::directive('money', function ($expression) {
            return "<?php echo 'Rp' . number_format($expression, 0, ',', '.'); ?>";
        });

        if (! class_exists('Donation', false)) {
            class_alias(\App\Models\Donation::class, 'Donation');
        }
        if (! class_exists('Order', false)) {
            class_alias(\App\Models\Order::class, 'Order');
        }
        if (! class_exists('Expense', false)) {
            class_alias(\App\Models\Expense::class, 'Expense');
        }

        // Bagikan informasi program & kontak ke semua tampilan blade (navbar, footer, sidebar, dll)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $info = \App\Models\ProgramInfo::first();
                $view->with('programInfo', $info);
                $view->with('program', $info);
            } catch (\Throwable $e) {
                // Abaikan jika migrasi belum selesai
            }
        });
    }
}
