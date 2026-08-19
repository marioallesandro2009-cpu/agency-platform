<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaviconSettingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(["role" => "admin"]);
    }

    /**
     * This dev environment's PHP has no GD extension (php.ini disables it),
     * so UploadedFile::fake()->image() — which shells out to GD — can't be
     * used here. Hand-rolls a minimal valid solid-color PNG (uncompressed
     * IDAT via zlib_compress, which only needs the always-available zlib
     * extension) so dimension validation can still be exercised for real.
     */
    private function solidPng(int $width, int $height): string
    {
        $signature = "\x89PNG\r\n\x1a\n";

        $chunk = function (string $type, string $data) {
            return pack("N", strlen($data)).$type.$data.pack("N", crc32($type.$data));
        };

        $ihdr = pack("NNCCCCC", $width, $height, 8, 2, 0, 0, 0);

        $raw = "";
        for ($y = 0; $y < $height; $y++) {
            $raw .= "\x00".str_repeat("\xAA\xBB\xCC", $width);
        }
        $idat = gzcompress($raw);

        return $signature.$chunk("IHDR", $ihdr).$chunk("IDAT", $idat).$chunk("IEND", "");
    }

    private function fakePng(int $width, int $height): UploadedFile
    {
        return UploadedFile::fake()->createWithContent("favicon.png", $this->solidPng($width, $height));
    }

    /**
     * Modern ICO containers can embed a PNG directly instead of a raw BMP
     * DIB, which is far simpler to hand-roll: ICONDIR (6 bytes) + one
     * ICONDIRENTRY (16 bytes) + the PNG bytes themselves.
     */
    private function fakeIco(int $size): UploadedFile
    {
        $png = $this->solidPng($size, $size);

        $dir = pack("vvv", 0, 1, 1);
        $entry = pack("CCCCvvVV", $size, $size, 0, 0, 1, 32, strlen($png), 6 + 16);

        return UploadedFile::fake()->createWithContent("favicon.ico", $dir.$entry.$png);
    }

    public function test_admin_can_upload_a_square_png_favicon(): void
    {
        Storage::fake("public");

        $response = $this->actingAs($this->admin())
            ->post(route("admin.settings.favicon.update"), ["favicon" => $this->fakePng(192, 192)]);

        $response->assertSessionHas("success");
        $this->assertNotNull(Setting::get("favicon"));
    }

    public function test_non_square_favicon_is_rejected(): void
    {
        Storage::fake("public");

        $response = $this->actingAs($this->admin())
            ->post(route("admin.settings.favicon.update"), ["favicon" => $this->fakePng(300, 180)]);

        $response->assertSessionHasErrors("favicon");
        $this->assertNull(Setting::get("favicon"));
    }

    public function test_too_small_favicon_is_rejected(): void
    {
        Storage::fake("public");

        $response = $this->actingAs($this->admin())
            ->post(route("admin.settings.favicon.update"), ["favicon" => $this->fakePng(64, 64)]);

        $response->assertSessionHasErrors("favicon");
        $this->assertNull(Setting::get("favicon"));
    }

    public function test_large_enough_ico_file_is_accepted(): void
    {
        Storage::fake("public");

        $response = $this->actingAs($this->admin())
            ->post(route("admin.settings.favicon.update"), ["favicon" => $this->fakeIco(192)]);

        $response->assertSessionHas("success");
        $this->assertNotNull(Setting::get("favicon"));
    }

    /**
     * The repo's static public/favicon.ico (used as the fallback when no
     * admin favicon has been uploaded) only has a 48x48 largest frame —
     * below the 180px floor this endpoint enforces for *uploads*. That's
     * fine: the static fallback bypasses this validation entirely (it's
     * just a file in public/, never routed through here), and this test
     * documents that admins can't re-upload that same file as a "custom"
     * favicon without hitting the same minimum-size safeguard everyone else
     * does.
     */
    public function test_the_static_fallback_favicon_is_too_small_to_pass_upload_validation(): void
    {
        Storage::fake("public");

        $icoPath = base_path("public/favicon.ico");
        $file = new UploadedFile($icoPath, "favicon.ico", "image/x-icon", null, true);

        $response = $this->actingAs($this->admin())
            ->post(route("admin.settings.favicon.update"), ["favicon" => $file]);

        $response->assertSessionHasErrors("favicon");
        $this->assertNull(Setting::get("favicon"));
    }

    public function test_admin_can_remove_custom_favicon(): void
    {
        Storage::fake("public");
        Setting::set("favicon", "/storage/favicon/xyz.png", "general");

        $response = $this->actingAs($this->admin())
            ->delete(route("admin.settings.favicon.destroy"));

        $response->assertSessionHas("success");
        $this->assertNull(Setting::get("favicon"));
    }
}
