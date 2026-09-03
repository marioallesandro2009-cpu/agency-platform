<?php

use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Adds a "why custom code, not WordPress/template" item to the existing
     * "why_us" home section instead of introducing a new section_type —
     * keeps the messaging inside the same admin-editable content.items
     * repeater the section already supports.
     */
    public function up(): void
    {
        $section = PageSection::where('page', 'home')->where('section_type', 'why_us')->first();

        if (! $section) {
            return;
        }

        $items = $section->content['items'] ?? [];
        $alreadyPresent = collect($items)->contains(fn ($item) => ($item['title'] ?? null) === 'Kode Custom, Bukan Template');

        if ($alreadyPresent) {
            return;
        }

        $items[] = [
            'title' => 'Kode Custom, Bukan Template',
            'description' => 'Dibangun dari kode asli tanpa tumpukan plugin pihak ketiga, sehingga performa lebih cepat, celah keamanan lebih sedikit, dan fitur bisa disesuaikan penuh dengan kebutuhan bisnis Anda.',
            'icon' => 'code',
        ];

        $section->update(['content' => array_merge($section->content ?? [], ['items' => $items])]);
    }

    public function down(): void
    {
        $section = PageSection::where('page', 'home')->where('section_type', 'why_us')->first();

        if (! $section) {
            return;
        }

        $items = collect($section->content['items'] ?? [])
            ->reject(fn ($item) => ($item['title'] ?? null) === 'Kode Custom, Bukan Template')
            ->values()
            ->all();

        $section->update(['content' => array_merge($section->content ?? [], ['items' => $items])]);
    }
};
