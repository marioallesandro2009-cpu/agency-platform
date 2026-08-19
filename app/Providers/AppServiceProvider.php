<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * Lazily generated once per request, cached for the rest of it (each
         * request boots a fresh container under php-fpm/artisan serve, so this
         * is never reused across visitors). Registered here — rather than in
         * middleware — so it's available no matter which code path renders the
         * response: normal routed requests, AND the custom NotFoundHttpException
         * render callback in bootstrap/app.php, which builds its Inertia/Blade
         * response *outside* the "web" middleware group entirely (see that
         * callback's own comment) and would otherwise crash every 404 trying
         * to resolve a nonce no middleware ever bound.
         */
        $this->app->singleton('csp-nonce', fn () => base64_encode(random_bytes(16)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
