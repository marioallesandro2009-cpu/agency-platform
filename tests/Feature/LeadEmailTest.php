<?php

namespace Tests\Feature;

use App\Mail\NewLeadNotification;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadEmailTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        "name" => "PT Uji Email",
        "whatsapp" => "081234567890",
        "source" => "konsultasi",
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Setting::set("admin_notification_email", "admin@example.test", "notifications");
    }

    public function test_notification_is_sent_immediately_and_not_queued(): void
    {
        Mail::fake();

        $this->post("/leads", $this->payload);

        Mail::assertSent(NewLeadNotification::class, fn ($mail) => $mail->hasTo("admin@example.test"));
        Mail::assertNothingQueued();
    }

    public function test_a_mail_failure_does_not_break_the_form(): void
    {
        Log::spy();
        Mail::shouldReceive("to")->andReturnSelf();
        Mail::shouldReceive("sendNow")->andThrow(new \RuntimeException("smtp down"));

        $this->postJson("/leads", $this->payload)->assertOk();

        $this->assertDatabaseHas("leads", ["name" => "PT Uji Email"]);
        Log::shouldHaveReceived("error")->once();
    }

    public function test_mail_test_command_warns_when_mail_is_only_logged(): void
    {
        $this->artisan("mail:test", ["to" => "admin@example.test"])
            ->expectsOutputToContain("hanya ditulis ke log")
            ->assertExitCode(1);
    }

    public function test_cleanup_migration_fixes_old_values_and_keeps_edited_ones(): void
    {
        Setting::set("phone", "851-7788-6018", "contact");
        Setting::set("footer_disclaimer", "Beberapa elemen visual terinspirasi dari sumber ilustrasi umum di internet.", "general");

        (require database_path("migrations/2026_11_22_000000_clean_up_contact_and_footer_settings.php"))->up();

        $this->assertSame("+62 851-7788-6018", DB::table("settings")->where("key", "phone")->value("value"));
        $this->assertSame("", DB::table("settings")->where("key", "footer_disclaimer")->value("value"));

        Setting::set("phone", "021-5551234", "contact");
        Setting::set("footer_disclaimer", "Teks yang sudah saya tulis sendiri.", "general");

        (require database_path("migrations/2026_11_22_000000_clean_up_contact_and_footer_settings.php"))->up();

        $this->assertSame("021-5551234", DB::table("settings")->where("key", "phone")->value("value"));
        $this->assertSame("Teks yang sudah saya tulis sendiri.", DB::table("settings")->where("key", "footer_disclaimer")->value("value"));
    }

    public function test_phone_link_on_contact_page_has_only_dialable_characters(): void
    {
        Setting::set("phone", "+62 851-7788-6018", "contact");

        $this->get("/kontak")->assertOk()->assertSee('href="tel:+6285177886018"', false);
    }
}
