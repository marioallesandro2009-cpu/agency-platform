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

Artisan::command('mail:test {to? : Alamat tujuan, bawaannya Email Notifikasi Admin}', function (?string $to = null) {
    $to = $to ?: \App\Models\Setting::get('admin_notification_email');
    if (! $to) {
        $this->error('Belum ada alamat tujuan. Isi "Email Notifikasi Admin" di admin Settings atau beri alamat sebagai argumen.');
        return 1;
    }

    $mailer = config('mail.default');
    $this->line("Mailer: {$mailer} | host: " . (config("mail.mailers.{$mailer}.host") ?? '-') . ' | dari: ' . (config('mail.from.address') ?? '-'));

    try {
        \Illuminate\Support\Facades\Mail::raw(
            'Email uji dari ' . config('app.url') . '. Kalau ini sampai, notifikasi lead juga akan sampai.',
            fn ($message) => $message->to($to)->subject('Tes email notifikasi lead')
        );
    } catch (\Throwable $e) {
        $this->error('Gagal kirim: ' . $e->getMessage());
        return 1;
    }

    if (in_array($mailer, ['log', 'array'], true)) {
        $this->warn("MAIL_MAILER={$mailer}: email hanya ditulis ke log, tidak benar-benar terkirim. Ganti ke smtp di .env.");
        return 1;
    }

    $this->info("Terkirim ke {$to}. Cek kotak masuk dan folder spam.");
    return 0;
})->purpose('Kirim email uji lewat konfigurasi mail saat ini, untuk memastikan notifikasi lead sampai');

Artisan::command('blog:import', function () {
    $result = \App\Support\WeeklyBlogImporter::run();

    $this->info($result['imported'] . ' artikel diimpor atau diperbarui.');
    foreach ($result['skipped'] as $reason) {
        $this->warn('Dilewati: ' . $reason);
    }

    return 0;
})->purpose('Impor artikel mingguan dari database/seeders/data/blog-mingguan/*.json (aman dijalankan berulang)');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
