<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('indexnow:submit', function () {
    if (! \App\Support\IndexNow::enabled()) {
        $this->warn('IndexNow nonaktif. Set INDEXNOW_ENABLED=true (otomatis aktif bila APP_ENV=production).');
        return 1;
    }

    $urls = collect(\App\Http\Controllers\SitemapController::urls())->pluck('loc')->all();
    $ok = \App\Support\IndexNow::submit($urls);
    $ok ? $this->info(count($urls) . ' URL dikirim ke IndexNow.') : $this->error('Pengiriman gagal, cek storage/logs/laravel.log.');

    return $ok ? 0 : 1;
})->purpose('Kirim semua URL di sitemap ke IndexNow (Bing dan lainnya)');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
