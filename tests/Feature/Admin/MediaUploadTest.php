<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_upload_a_valid_image(): void
    {
        $admin = User::factory()->create();
        // create() instead of image() — this CLI's php.ini has no GD
        // extension, and create() fakes size/mimetype without needing to
        // render real pixel data, which Laravel's "image" rule accepts.
        $file = UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin)->post('/admin/media', ['file' => $file]);

        $response->assertRedirect();
        $this->assertDatabaseHas('media', ['name' => 'photo.jpg']);
    }

    public function test_svg_uploads_are_rejected(): void
    {
        $admin = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent(
            'evil.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $response = $this->actingAs($admin)->post('/admin/media', ['file' => $file]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('media', ['name' => 'evil.svg']);
    }

    public function test_guest_cannot_upload_media(): void
    {
        $file = UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg');

        $this->post('/admin/media', ['file' => $file])->assertRedirect('/login');
    }
}
