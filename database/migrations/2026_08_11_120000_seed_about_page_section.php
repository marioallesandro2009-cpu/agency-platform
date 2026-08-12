<?php

use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Make room right after "hero" (order 1) for the new "about" section.
        PageSection::where("page", "home")
            ->where("order", ">=", 2)
            ->where("section_type", "!=", "about")
            ->increment("order");

        PageSection::updateOrCreate(
            ["page" => "home", "section_type" => "about"],
            [
                "title" => "Kenalan Lebih Dekat dengan Kami",
                "content" => [
                    "description" => "Kami adalah agensi pengembangan website yang fokus membantu bisnis dan UMKM di Indonesia tampil profesional secara online. Setiap proyek kami kerjakan dengan pendekatan personal, bukan template massal, supaya website benar-benar mencerminkan identitas dan tujuan bisnis Anda.",
                    "button_text" => "Lihat Selengkapnya",
                    "button_url" => "/tentang-kami",
                ],
                "is_visible" => true,
                "order" => 2,
            ]
        );
    }

    public function down(): void
    {
        PageSection::where("page", "home")->where("section_type", "about")->delete();
    }
};
