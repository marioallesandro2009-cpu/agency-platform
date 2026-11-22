<?php

namespace App\Support;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Carbon\Carbon;

/**
 * Imports the weekly blog articles from database/seeders/data/blog-mingguan/*.json.
 * Safe to run repeatedly (rows are matched by slug). An article without its image
 * file or with an unknown category is skipped, never published half-finished.
 */
class WeeklyBlogImporter
{
    public const DIR = 'database/seeders/data/blog-mingguan';

    /** @return array{imported: int, skipped: array<int, string>} */
    public static function run(?string $dir = null): array
    {
        $dir = $dir ?? base_path(self::DIR);
        $categories = BlogCategory::pluck('id', 'slug')->all();
        $authorId = optional(User::first())->id;

        $imported = 0;
        $skipped = [];

        foreach (glob(rtrim($dir, '/\\') . '/*.json') ?: [] as $file) {
            $posts = json_decode((string) file_get_contents($file), true);

            if (! is_array($posts)) {
                $skipped[] = basename($file) . ': JSON tidak valid';
                continue;
            }

            foreach ($posts as $post) {
                $slug = $post['slug'] ?? '(tanpa slug)';

                foreach (['title', 'slug', 'category', 'content', 'image', 'image_alt', 'published_at'] as $field) {
                    if (empty($post[$field])) {
                        $skipped[] = "{$slug}: kolom {$field} kosong";
                        continue 2;
                    }
                }

                if (! isset($categories[$post['category']])) {
                    $skipped[] = "{$slug}: kategori {$post['category']} tidak ada";
                    continue;
                }

                $image = '/images/blog/' . $post['image'] . '.jpg';
                if (! is_file(public_path($image))) {
                    $skipped[] = "{$slug}: gambar {$image} tidak ditemukan";
                    continue;
                }

                BlogPost::updateOrCreate(['slug' => $post['slug']], [
                    'title' => $post['title'],
                    'seo_title' => $post['seo_title'] ?? $post['title'],
                    'seo_description' => $post['seo_description'] ?? null,
                    'focus_keyword' => $post['focus_keyword'] ?? null,
                    'excerpt' => $post['excerpt'] ?? null,
                    // The blog template uses whitespace-pre-line: newlines between tags would show as blank lines.
                    'content' => preg_replace("/\s*\n\s*/", '', $post['content']),
                    'featured_image' => $image,
                    'featured_image_alt' => $post['image_alt'],
                    'blog_category_id' => $categories[$post['category']],
                    'author_id' => $authorId,
                    'status' => 'published',
                    'published_at' => Carbon::parse($post['published_at']),
                ]);

                $imported++;
            }
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
