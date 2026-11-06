<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_articles_are_published_and_render(): void
    {
        BlogCategory::create(['name' => 'Custom Software', 'slug' => 'custom-software']);

        $this->seed(ArticleSeeder::class);
        $this->seed(ArticleSeeder::class); // safe to run twice

        $post = BlogPost::where('slug', 'monitoring-suhu-cold-chain')->sole();
        $this->assertSame('published', $post->status);
        $this->assertStringNotContainsString("\n", $post->content);
        $this->assertFileExists(public_path(ltrim($post->featured_image, '/')));

        $this->get('/blog/monitoring-suhu-cold-chain')
            ->assertOk()
            ->assertSee('id="komponen"', false)
            ->assertSee('monitoring-suhu-cold-chain-infografik.webp', false);
    }
}
