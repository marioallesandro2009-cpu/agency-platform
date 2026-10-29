<?php

namespace App\Support;

class ReadingTime
{
    // Kept in sync with resources/js/utils/seoAnalyzer.js's READING_WPM —
    // both the admin analyzer and the public blog pages (Vue + this
    // Blade-side port) must agree on the same "estimasi waktu baca" number.
    private const WPM = 200;

    public static function minutes(?string $content): int
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($content ?? '')));
        $words = $text === '' ? 0 : count(array_filter(explode(' ', $text)));

        return max(1, (int) ceil($words / self::WPM));
    }
}
