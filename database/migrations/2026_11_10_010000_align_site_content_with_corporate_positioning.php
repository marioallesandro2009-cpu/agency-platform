<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Brings the database-managed copy in line with the corporate positioning
     * during a normal deploy. Only text that still carries the old message is
     * replaced, so anything already rewritten in the admin panel is kept.
     * Safe on an empty database: missing rows are skipped.
     */
    public function up(): void
    {
        $this->updateSection("home", "about", [
            "description" => "Kami membangun website dan software custom untuk perusahaan di Indonesia. Setiap proyek dimulai dari memahami cara kerja tim Anda, supaya hasilnya benar-benar dipakai dan mendukung tujuan bisnis.",
        ]);

        $this->updateSection("about", "about_main", [
            "description" => "Exodine Web adalah studio pengembangan website dan software custom untuk perusahaan di Indonesia. Kami membantu tim merapikan pekerjaan yang masih bergantung pada spreadsheet, chat, dan proses manual, mulai dari website perusahaan sampai sistem operasional yang mengikuti alur kerja Anda.",
            "mission" => "Membangun website dan software yang mudah dipakai, tepat sasaran, dan bisa dikembangkan bertahap sesuai kebutuhan tiap perusahaan.",
            "vision" => "Menjadi mitra teknologi terpercaya bagi perusahaan di Indonesia yang ingin bekerja lebih rapi dan terukur.",
        ]);

        $this->updateSoftwareService();
    }

    /** The previous copy is not kept, so this cannot be undone automatically. */
    public function down(): void
    {
    }

    private function updateSection(string $page, string $type, array $replacements): void
    {
        $section = DB::table("page_sections")->where("page", $page)->where("section_type", $type)->first();
        if (! $section) {
            return;
        }

        $content = json_decode($section->content ?? "[]", true) ?: [];

        foreach ($replacements as $key => $text) {
            if (preg_match("/UMKM|terjangkau/i", $content[$key] ?? "")) {
                $content[$key] = $text;
            }
        }

        // The rating stat no longer exists; drop it from the admin form too.
        if (isset($content["stats"])) {
            $content["stats"] = array_values(array_filter($content["stats"], fn ($stat) => ($stat["key"] ?? "") !== "rating"));
        }

        DB::table("page_sections")->where("id", $section->id)->update(["content" => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }

    private function updateSoftwareService(): void
    {
        $service = DB::table("services")->where("slug", "jasa-software-custom")->first();
        if (! $service || ! str_contains($service->short_description ?? "", "sistem deteksi")) {
            return;
        }

        $replacedFaqs = [
            "Apakah bisa membuat sistem deteksi atau pemantauan?" => [
                "question" => "Apakah sistem bisa memberi peringatan otomatis?",
                "answer" => "Bisa, misalnya saat data tidak cocok, tagihan atau kontrak mendekati jatuh tempo, atau persetujuan tertahan terlalu lama. Aturan peringatannya kami tetapkan bersama pada tahap pemetaan.",
            ],
            "Apakah bisa membuat aplikasi Android dan iOS?" => [
                "question" => "Apakah sistemnya bisa dibuka dari ponsel?",
                "answer" => "Bisa. Sistem dibangun sebagai aplikasi web yang bisa dibuka dari browser ponsel dan dipasang di layar utama (PWA), sehingga satu sistem melayani pengguna kantor dan lapangan.",
            ],
        ];

        $faqs = array_map(
            fn ($faq) => $replacedFaqs[$faq["question"] ?? ""] ?? $faq,
            json_decode($service->faqs ?? "[]", true) ?: []
        );

        DB::table("services")->where("id", $service->id)->update([
            "short_description" => "Dari sistem operasional dan persetujuan otomatis hingga portal klien, dibangun untuk kebutuhan yang tidak ada produk jadinya.",
            "description" => "Sistem operasional, otomasi alur kerja, portal, integrasi, dan dashboard yang dirancang sesuai kebutuhan perusahaan Anda.",
            "seo_description" => "Jasa pembuatan software custom untuk sistem operasional, otomasi alur kerja, portal klien, integrasi, dan dashboard, dirancang mengikuti kebutuhan perusahaan.",
            "features" => json_encode([
                "Peringatan otomatis untuk selisih data, jatuh tempo, dan persetujuan tertahan",
                "Otomasi alur kerja, chatbot internal, dan pembacaan dokumen",
                "ERP, HRIS, CRM, dan portal web sesuai alur kerja Anda",
            ], JSON_UNESCAPED_UNICODE),
            "faqs" => json_encode($faqs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
};
