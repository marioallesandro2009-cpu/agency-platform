<?php

namespace App\Providers;

use App\Http\Controllers\SitemapController;
use App\Models\BlogPost;
use App\Models\MascotAsset;
use App\Models\NavItem;
use App\Models\Service;
use App\Models\Setting;
use App\Support\IndexNow;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
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
        // The prefetch helper is an inline <script>; without the nonce the
        // CSP blocks it (console error). Public pages turn prefetch off.
        Vite::useCspNonce(app('csp-nonce'));
        Vite::prefetch(concurrency: 3);

        // Let Bing & co. know as soon as public content goes live or changes.
        BlogPost::saved(function (BlogPost $post) {
            $live = $post->status === 'published' && (! $post->published_at || $post->published_at->lte(now()));
            if ($live && ($post->wasRecentlyCreated || $post->wasChanged(['status', 'slug', 'title', 'content', 'excerpt', 'published_at', 'featured_image']))) {
                IndexNow::queue(route('blog.show', $post));
            }
        });
        Service::saved(function (Service $service) {
            $route = SitemapController::SERVICE_ROUTES[$service->slug] ?? null;
            if ($route && $service->is_published && ($service->wasRecentlyCreated || $service->wasChanged())) {
                IndexNow::queue(route($route));
            }
        });

        // Feeds the same navbar/footer/mascot data the Inertia pages get via
        // HandleInertiaRequests::share() to the hybrid-Blade public layout
        // and its navbar/footer partials — those aren't Inertia responses,
        // so that middleware's shared props never reach them. Keep the
        // public setting groups list in sync with
        // HandleInertiaRequests::PUBLIC_SETTING_GROUPS if it ever changes.
        View::composer(
            ['components.layouts.public', 'partials.public.navbar', 'partials.public.footer', 'pages.*'],
            function ($view) {
                // The composer fires once per matching view (layout, navbar,
                // footer, page...), so the queries are memoized with once()
                // to run a single time per request instead of 4x.
                $shared = once(function () {
                    $publicGroups = ['general', 'contact', 'social', 'branding', 'integrations'];
                    $settingsGrouped = Setting::allGrouped();

                    return [
                        'settings' => collect($publicGroups)
                            ->mapWithKeys(fn (string $group) => [$group => $settingsGrouped[$group] ?? []])
                            ->toArray(),
                        'navServices' => Service::published()->ordered()->get(['id', 'name', 'slug']),
                        'navMenu' => NavItem::visible()->ordered()->get(['id', 'label', 'url']),
                        'mascots' => MascotAsset::where('is_active', true)->get()->keyBy('placement_key')
                            ->map(fn (MascotAsset $m) => [
                                'image_path' => $m->image_path,
                                'alt_text' => $m->alt_text,
                                'caption' => $m->caption,
                            ]),
                    ];
                });

                $view->with($shared);
            }
        );
    }
}
