<?php

namespace Database\Seeders;

use App\Models\TechStack;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TechStackSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ["name" => "Laravel", "slug" => "laravel", "hex" => "FF2D20", "link_url" => "https://laravel.com", "order" => 1],
            ["name" => "Vue.js", "slug" => "vuedotjs", "hex" => "4FC08D", "link_url" => "https://vuejs.org", "order" => 2],
            ["name" => "Inertia.js", "slug" => "inertia", "hex" => "9553E9", "link_url" => "https://inertiajs.com", "order" => 3],
            ["name" => "Tailwind CSS", "slug" => "tailwindcss", "hex" => "06B6D4", "link_url" => "https://tailwindcss.com", "order" => 4],
            ["name" => "MySQL", "slug" => "mysql", "hex" => "4479A1", "link_url" => "https://www.mysql.com", "order" => 5],
            ["name" => "Git", "slug" => "git", "hex" => "F03C2E", "link_url" => "https://git-scm.com", "order" => 6],
            ["name" => "GitHub", "slug" => "github", "hex" => "181717", "link_url" => "https://github.com", "order" => 7],
            ["name" => "cPanel", "slug" => "cpanel", "hex" => "FF6C2C", "link_url" => "https://cpanel.net", "order" => 8],
        ];

        foreach ($items as $item) {
            $iconUrl = $this->downloadIcon($item["slug"], $item["hex"]);

            TechStack::updateOrCreate(
                ["name" => $item["name"]],
                [
                    "icon_url" => $iconUrl,
                    "link_url" => $item["link_url"],
                    "order" => $item["order"],
                    "is_active" => true,
                ]
            );
        }
    }

    /**
     * Downloads the official brand SVG from simple-icons at seed time rather
     * than committing the SVGs to the repo, so icons stay in sync with
     * upstream without a manual asset-update step. Falls back to an existing
     * previously-downloaded file if the CDN is unreachable, so re-seeding
     * offline doesn't wipe out an already-working icon.
     */
    private function downloadIcon(string $slug, string $hex): string
    {
        $path = "icons/{$slug}.svg";

        try {
            $response = Http::timeout(10)
                ->withOptions(["allow_redirects" => false])
                ->get("https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/{$slug}.svg");

            if ($response->successful() && str_contains((string) $response->header("Content-Type"), "svg")) {
                // simple-icons ships path data with no fill attribute (callers
                // are expected to colorize it themselves), which renders as
                // plain black — bake the official brand hex directly into the
                // root <svg> tag so it displays in brand color even though
                // LogoLoop references the file via <img> (external SVGs
                // loaded that way can't be recolored with CSS currentColor).
                $svg = preg_replace('/^<svg/', '<svg fill="#' . $hex . '"', $response->body(), 1);
                Storage::disk("public")->put($path, $svg);
            } elseif (!Storage::disk("public")->exists($path)) {
                throw new \RuntimeException("Failed to download icon for '{$slug}': HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            if (!Storage::disk("public")->exists($path)) {
                throw new \RuntimeException("Failed to download icon for '{$slug}' and no cached copy exists.", previous: $e);
            }
        }

        return Storage::disk("public")->url($path);
    }
}
