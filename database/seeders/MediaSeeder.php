<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Installs the stock images the other seeders point at. Uploads live in
 * storage/app/public, which is gitignored, so a fresh server would have
 * every /storage/media/... path broken; the images are bundled in
 * database/seeders/media (resized to 1600px) and copied over here.
 *
 * Safe to re-run on a live site: existing files are never overwritten and
 * no content row is touched, except the About image when it is empty or
 * points at a file that does not exist.
 *
 * All photos are from Pexels (free for commercial use, no attribution
 * required); sources are noted next to each path in BlogPostSeeder and
 * PortfolioProjectSeeder. about-team.jpg: photo by fauxels,
 * https://www.pexels.com/photo/3184418/
 */
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $target = storage_path('app/public/media');
        File::ensureDirectoryExists($target);

        foreach (File::files(database_path('seeders/media')) as $file) {
            $destination = $target . DIRECTORY_SEPARATOR . $file->getFilename();
            if (! File::exists($destination)) {
                File::copy($file->getPathname(), $destination);
            }
        }

        $about = PageSection::where('page', 'about')->where('section_type', 'about_main')->first();
        if ($about && ! $this->exists($about->image)) {
            $about->update(['image' => '/storage/media/about-team.jpg']);
        }
    }

    private function exists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        // External URLs are the admin's choice; only local uploads can be checked.
        return ! str_starts_with($path, '/storage/')
            || File::exists(storage_path('app/public/' . substr($path, strlen('/storage/'))));
    }
}
