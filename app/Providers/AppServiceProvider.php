<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Custom public path for Hostinger (parallel public_html folder)
        $publicHtmlPath = base_path('../public_html');
        if (is_dir($publicHtmlPath)) {
            $this->app->usePublicPath($publicHtmlPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Heavy data exports: 10 per minute per user (own bucket, not shared with other throttles).
        \Illuminate\Support\Facades\RateLimiter::for('exports', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)
                ->by('export:' . ($request->user()?->getAuthIdentifier() ?? $request->ip()));
        });

        // Share dynamic WhatsApp links and settings across views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('pengaturanApp', \App\Models\PengaturanPendaftaran::instance());
        });

        // Register Google Sheets Sync Observers
        \App\Models\Akun::observe(\App\Observers\GoogleSheetObserver::class);
        \App\Models\Peserta::observe(\App\Observers\GoogleSheetObserver::class);
        \App\Models\Nilai::observe(\App\Observers\NilaiObserver::class);
        \App\Models\Nilai::observe(\App\Observers\GoogleSheetObserver::class);
        \App\Models\Berkas::observe(\App\Observers\GoogleSheetObserver::class);
        \App\Models\Daftar::observe(\App\Observers\GoogleSheetObserver::class);
    }
}
