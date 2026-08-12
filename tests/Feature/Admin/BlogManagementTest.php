<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_a_post(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/blog', [
            'title' => 'Cara Memilih Domain',
            'content' => '<p>Isi artikel</p>',
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Cara Memilih Domain',
            'slug' => 'cara-memilih-domain',
            'status' => 'published',
        ]);

        $post = BlogPost::where('title', 'Cara Memilih Domain')->first();
        $this->assertNotNull($post->published_at, 'publishing a post should stamp published_at automatically');
    }

    public function test_script_tags_are_stripped_from_post_content(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post('/admin/blog', [
            'title' => 'XSS Test',
            'content' => '<p>Halo</p><script>alert(1)</script><img src=x onerror="alert(2)">',
            'status' => 'draft',
        ]);

        $post = BlogPost::where('title', 'XSS Test')->first();

        $this->assertNotNull($post);
        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringNotContainsString('onerror', $post->content);
        $this->assertStringContainsString('<p>Halo</p>', $post->content);
    }

    public function test_admin_can_update_a_post_by_id(): void
    {
        $admin = User::factory()->create();
        $post = BlogPost::create(['title' => 'Judul Lama', 'slug' => 'judul-lama', 'status' => 'draft']);

        $response = $this->actingAs($admin)->put("/admin/blog/{$post->id}", [
            'title' => 'Judul Baru',
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('blog_posts', ['id' => $post->id, 'title' => 'Judul Baru']);
    }

    public function test_admin_can_delete_a_post_by_id(): void
    {
        $admin = User::factory()->create();
        $post = BlogPost::create(['title' => 'To Delete', 'slug' => 'to-delete', 'status' => 'draft']);

        $this->actingAs($admin)->delete("/admin/blog/{$post->id}")->assertRedirect();

        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
    }
}
