<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexNowTest extends TestCase
{
    use RefreshDatabase;

    private function enable(): void
    {
        config(['services.indexnow.enabled' => true, 'services.indexnow.key' => 'testkey123']);
        Http::fake(['api.indexnow.org/*' => Http::response('', 200)]);
    }

    private function publishPost(string $slug): BlogPost
    {
        return BlogPost::create([
            'title' => 'Judul ' . $slug,
            'slug' => $slug,
            'content' => '<p>Isi</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
    }

    public function test_published_posts_are_sent_together_after_the_request(): void
    {
        $this->enable();

        $this->publishPost('artikel-satu');
        $this->publishPost('artikel-dua');
        Http::assertNothingSent();

        $this->app->terminate();

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            return str_starts_with($request->url(), 'https://api.indexnow.org/indexnow')
                && $request['key'] === 'testkey123'
                && $request['urlList'] === [url('/blog/artikel-satu'), url('/blog/artikel-dua')];
        });
    }

    public function test_drafts_are_not_sent(): void
    {
        $this->enable();

        BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'content' => '<p>x</p>', 'status' => 'draft']);
        $this->app->terminate();

        Http::assertNothingSent();
    }

    public function test_nothing_is_sent_when_disabled(): void
    {
        config(['services.indexnow.enabled' => false]);
        Http::fake();

        $this->publishPost('artikel-satu');
        $this->app->terminate();

        Http::assertNothingSent();
    }

    public function test_command_submits_every_sitemap_url(): void
    {
        $this->enable();
        $this->publishPost('artikel-satu');

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertSent(fn (Request $request) => in_array(url('/blog/artikel-satu'), $request['urlList'], true)
            && in_array(route('home'), $request['urlList'], true));
    }

    public function test_committed_key_file_matches_the_default_key(): void
    {
        $key = config('services.indexnow.key');

        $this->assertFileExists(public_path("{$key}.txt"));
        $this->assertSame($key, file_get_contents(public_path("{$key}.txt")));
    }
}
