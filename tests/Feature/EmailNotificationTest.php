<?php

namespace Tests\Feature;

use App\Jobs\SendBillingReminderJob;
use App\Jobs\SendSubscriptionReminderJob;
use App\Jobs\SuspendOverdueJuraganJob;
use App\Mail\BillingCreatedMail;
use App\Mail\BillingReminderMail;
use App\Mail\BillingThrottledMail;
use App\Mail\JuraganSuspendedMail;
use App\Mail\SubscriptionReminderMail;
use App\Models\Billing;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    // ── BillingCreatedMail ────────────────────────────────────────────────────

    /** generateMonthlyBills() mengirim BillingCreatedMail ke juragan yang punya contact_email. */
    public function test_billing_created_mail_sent_to_juragan_on_generate(): void
    {
        Mail::fake();
        $this->mock(\App\Services\MikroTikService::class);

        $juragan = User::factory()->juragan()->create([
            'contact_email' => 'owner@example.com',
        ]);
        User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
            'monthly_rate' => 200000,
        ]);

        app(BillingService::class)->generateMonthlyBills();

        Mail::assertSent(BillingCreatedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }

    /** generateMonthlyBills() TIDAK mengirim email ke juragan tanpa contact_email. */
    public function test_billing_created_mail_not_sent_when_no_contact_email(): void
    {
        Mail::fake();
        $this->mock(\App\Services\MikroTikService::class);

        $juragan = User::factory()->juragan()->create(['contact_email' => null]);
        User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
            'monthly_rate' => 150000,
        ]);

        app(BillingService::class)->generateMonthlyBills();

        Mail::assertNotSent(BillingCreatedMail::class);
    }

    /** Dua juragan berbeda masing-masing menerima satu email BillingCreated. */
    public function test_billing_created_mail_sent_once_per_juragan(): void
    {
        Mail::fake();
        $this->mock(\App\Services\MikroTikService::class);

        $juraganA = User::factory()->juragan()->create(['contact_email' => 'a@example.com']);
        $juraganB = User::factory()->juragan()->create(['contact_email' => 'b@example.com']);

        User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id, 'monthly_rate' => 200000]);
        User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganA->id, 'monthly_rate' => 200000]);
        User::factory()->create(['role' => 'tenant', 'juragan_id' => $juraganB->id, 'monthly_rate' => 150000]);

        app(BillingService::class)->generateMonthlyBills();

        Mail::assertSent(BillingCreatedMail::class, 2); // satu per juragan
        Mail::assertSent(BillingCreatedMail::class, fn ($mail) => $mail->hasTo('a@example.com'));
        Mail::assertSent(BillingCreatedMail::class, fn ($mail) => $mail->hasTo('b@example.com'));
    }

    // ── BillingThrottledMail ──────────────────────────────────────────────────

    /** checkAndThrottleOverdue() mengirim BillingThrottledMail ke juragan setelah throttle. */
    public function test_billing_throttled_mail_sent_after_throttle(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);
        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->subDays(3),
        ]);

        $this->mock(\App\Services\MikroTikService::class)
            ->shouldReceive('throttleDevice')
            ->andReturn(true);

        app(BillingService::class)->checkAndThrottleOverdue();

        Mail::assertSent(BillingThrottledMail::class, fn ($mail) => $mail->hasTo('juragan@example.com'));
    }

    /** checkAndThrottleOverdue() TIDAK mengirim email jika tidak ada yang di-throttle. */
    public function test_billing_throttled_mail_not_sent_when_nothing_throttled(): void
    {
        Mail::fake();
        $this->mock(\App\Services\MikroTikService::class);

        $tenant = User::factory()->create(['role' => 'tenant']);
        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today(), // dalam grace
        ]);

        app(BillingService::class)->checkAndThrottleOverdue();

        Mail::assertNotSent(BillingThrottledMail::class);
    }

    // ── BillingReminderMail ───────────────────────────────────────────────────

    /** SendBillingReminderJob mengirim BillingReminderMail ke juragan H-3 jatuh tempo. */
    public function test_billing_reminder_mail_sent_three_days_before_due(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->addDays(3),
        ]);

        (new SendBillingReminderJob)->handle();

        Mail::assertSent(BillingReminderMail::class, fn ($mail) => $mail->hasTo('juragan@example.com'));
    }

    /** Tagihan yang due dalam 5 hari (bukan H-3) tidak mendapat reminder. */
    public function test_billing_reminder_not_sent_for_wrong_due_date(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'unpaid',
            'due_date' => Carbon::today()->addDays(5), // bukan H-3
        ]);

        (new SendBillingReminderJob)->handle();

        Mail::assertNotSent(BillingReminderMail::class);
    }

    /** Tagihan yang sudah 'paid' tidak mendapat reminder meskipun due_date = H-3. */
    public function test_billing_reminder_not_sent_for_paid_billing(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        $tenant  = User::factory()->create(['role' => 'tenant', 'juragan_id' => $juragan->id]);

        Billing::factory()->create([
            'user_id'  => $tenant->id,
            'status'   => 'paid',
            'due_date' => Carbon::today()->addDays(3),
        ]);

        (new SendBillingReminderJob)->handle();

        Mail::assertNotSent(BillingReminderMail::class);
    }

    // ── SubscriptionReminderMail ──────────────────────────────────────────────

    /** SendSubscriptionReminderJob mengirim SubscriptionReminderMail ke juragan H-3. */
    public function test_subscription_reminder_mail_sent_three_days_before_due(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->addDays(3),
        ]);

        (new SendSubscriptionReminderJob)->handle();

        Mail::assertSent(SubscriptionReminderMail::class, fn ($mail) => $mail->hasTo('juragan@example.com'));
    }

    /** Subscription yang already paid tidak mendapat reminder. */
    public function test_subscription_reminder_not_sent_for_paid_subscription(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create(['contact_email' => 'juragan@example.com']);
        Subscription::factory()->paid()->create([
            'juragan_id' => $juragan->id,
            'due_date'   => Carbon::today()->addDays(3),
        ]);

        (new SendSubscriptionReminderJob)->handle();

        Mail::assertNotSent(SubscriptionReminderMail::class);
    }

    // ── JuraganSuspendedMail ──────────────────────────────────────────────────

    /** SuspendOverdueJuraganJob mengirim JuraganSuspendedMail saat suspend. */
    public function test_juragan_suspended_mail_sent_on_suspension(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create([
            'contact_email' => 'juragan@example.com',
            'suspended_at'  => null,
        ]);

        $graceCutoff = Carbon::today()->subDays(3);
        Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => $graceCutoff->toDateString(),
        ]);

        (new SuspendOverdueJuraganJob)->handle();

        Mail::assertSent(JuraganSuspendedMail::class, fn ($mail) => $mail->hasTo('juragan@example.com'));
    }

    /** Juragan yang sudah di-suspend sebelumnya tidak mendapat email ulang. */
    public function test_juragan_suspended_mail_not_sent_if_already_suspended(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create([
            'contact_email' => 'juragan@example.com',
            'suspended_at'  => Carbon::now()->subDay(), // sudah di-suspend sebelumnya
        ]);

        Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDays(3)->toDateString(),
        ]);

        (new SuspendOverdueJuraganJob)->handle();

        Mail::assertNotSent(JuraganSuspendedMail::class);
    }

    /** Juragan tanpa contact_email tidak mendapat email suspension. */
    public function test_juragan_suspended_mail_not_sent_when_no_contact_email(): void
    {
        Mail::fake();

        $juragan = User::factory()->juragan()->create([
            'contact_email' => null,
            'suspended_at'  => null,
        ]);

        Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDays(3)->toDateString(),
        ]);

        (new SuspendOverdueJuraganJob)->handle();

        Mail::assertNotSent(JuraganSuspendedMail::class);
    }
}
