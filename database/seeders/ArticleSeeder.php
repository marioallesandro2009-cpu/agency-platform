<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Artikel blog lanjutan dari database/seeders/data/blog-artikel.php.
 * Hanya mengisi artikel, jadi aman dijalankan berulang di production:
 *   php artisan db:seed --class=ArticleSeeder --force
 */
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BlogCategorySeeder::class);

        $categories = BlogCategory::pluck('id', 'slug')->all();
        $authorId = optional(User::first())->id;

        foreach (require __DIR__ . '/data/blog-artikel.php' as $post) {
            if (! isset($categories[$post['category']])) {
                $this->command?->warn("Kategori {$post['category']} belum ada, jalankan CorporateContentSeeder dulu. Dilewati: {$post['slug']}");
                continue;
            }

            $graphic = '<img src="/images/blog/' . $post['slug'] . '-infografik.webp" alt="' . e($post['graphic_alt']) . '" width="1200" height="675" loading="lazy">';

            BlogPost::updateOrCreate(['slug' => $post['slug']], [
                'title' => $post['title'],
                'seo_title' => $post['seo_title'],
                'seo_description' => $post['seo_description'],
                'focus_keyword' => $post['focus_keyword'],
                'excerpt' => $post['excerpt'],
                // The blog template uses whitespace-pre-line: newlines between
                // tags would render as blank lines, so they are dropped here.
                'content' => str_replace('[[infografik]]', $graphic, preg_replace("/\s*\n\s*/", '', $post['content'])),
                'featured_image' => '/images/blog/' . $post['image'] . '.jpg',
                'featured_image_alt' => $post['image_alt'],
                'blog_category_id' => $categories[$post['category']],
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::parse($post['published_at']),
            ]);
        }
    }
}
