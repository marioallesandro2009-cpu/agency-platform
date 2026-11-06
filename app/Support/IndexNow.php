<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Tells search engines that use IndexNow (Bing, Yandex, Seznam, ...) right
 * away when a public page is published or changed, instead of waiting for
 * their next crawl. Google does not use IndexNow; it relies on the sitemap.
 *
 * The key is public by design: search engines verify it by fetching
 * /{key}.txt, which is committed under public/.
 */
class IndexNow
{
    public const ENDPOINT = 'https://api.indexnow.org/indexnow';

    /** @var array<string, true> URLs collected during this request/command */
    private static array $pending = [];

    public static function enabled(): bool
    {
        return (bool) config('services.indexnow.enabled') && (bool) config('services.indexnow.key');
    }

    /**
     * Queue a URL and send everything collected in one request once the
     * response (or artisan command) has finished, so saving twelve posts in a
     * seeder makes one call and never slows down the admin's save.
     */
    public static function queue(string $url): void
    {
        if (! self::enabled()) {
            return;
        }

        if (self::$pending === []) {
            app()->terminating(function () {
                $urls = array_keys(self::$pending);
                self::$pending = [];
                self::submit($urls);
            });
        }

        self::$pending[$url] = true;
    }

    /** Send URLs now. Returns false when disabled, empty or rejected; never throws. */
    public static function submit(array $urls): bool
    {
        $urls = array_values(array_unique(array_filter($urls)));

        if (! self::enabled() || $urls === []) {
            return false;
        }

        $key = config('services.indexnow.key');
        $ok = true;

        foreach (array_chunk($urls, 10000) as $chunk) {
            try {
                $response = Http::timeout(10)->acceptJson()->post(self::ENDPOINT, [
                    'host' => parse_url(url('/'), PHP_URL_HOST),
                    'key' => $key,
                    'keyLocation' => url("/{$key}.txt"),
                    'urlList' => $chunk,
                ]);

                if (! $response->successful()) {
                    $ok = false;
                    Log::warning('IndexNow rejected the submission', ['status' => $response->status(), 'body' => $response->body()]);
                }
            } catch (Throwable $e) {
                $ok = false;
                Log::warning('IndexNow request failed', ['error' => $e->getMessage()]);
            }
        }

        return $ok;
    }
}
