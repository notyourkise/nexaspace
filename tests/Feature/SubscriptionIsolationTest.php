<?php

namespace Tests\Feature;

use App\Filament\Resources\SubscriptionResource;
use App\Jobs\SuspendOverdueJuraganJob;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SubscriptionIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** Build two independent juragan, each with one subscription. */
    private function seedTwoJuragan(): array
    {
        $juraganA = User::factory()->juragan()->create(['plan' => 'lite']);
        $juraganB = User::factory()->juragan()->create(['plan' => 'pro']);

        $subA = Subscription::factory()->create(['juragan_id' => $juraganA->id, 'amount' => 199_000]);
        $subB = Subscription::factory()->create(['juragan_id' => $juraganB->id, 'amount' => 499_000]);

        return [$juraganA, $juraganB, $subA, $subB];
    }

    // ── Query isolation ────────────────────────────────────────────────────────

    public function test_juragan_only_sees_own_subscription(): void
    {
        [$juraganA, $juraganB, $subA, $subB] = $this->seedTwoJuragan();

        $this->actingAs($juraganA);
        $ids = SubscriptionResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($ids->contains($subA->id));
        $this->assertFalse($ids->contains($subB->id));
        $this->assertSame(1, $ids->count());
    }

    public function test_developer_sees_all_subscriptions(): void
    {
        $this->seedTwoJuragan();
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer);
        $this->assertSame(2, SubscriptionResource::getEloquentQuery()->count());
    }

    public function test_juragan_a_subscription_invisible_to_juragan_b(): void
    {
        [$juraganA, $juraganB, $subA, $subB] = $this->seedTwoJuragan();

        $this->actingAs($juraganB);
        $ids = SubscriptionResource::getEloquentQuery()->pluck('id');

        $this->assertFalse($ids->contains($subA->id));
        $this->assertTrue($ids->contains($subB->id));
    }

    // ── SuspendOverdueJuraganJob isolation ────────────────────────────────────

    public function test_suspend_job_only_suspends_overdue_juragan(): void
    {
        $juraganA = User::factory()->juragan()->create(['plan' => 'lite']);
        $juraganB = User::factory()->juragan()->create(['plan' => 'pro']);

        // A is overdue (due_date 3 days ago, no grace left)
        Subscription::factory()->create([
            'juragan_id' => $juraganA->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDays(3),
        ]);

        // B is within grace period (due yesterday)
        Subscription::factory()->create([
            'juragan_id' => $juraganB->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDay(),
        ]);

        SuspendOverdueJuraganJob::dispatchSync();

        $this->assertNotNull($juraganA->fresh()->suspended_at);
        $this->assertNull($juraganB->fresh()->suspended_at);
    }

    public function test_suspend_job_marks_subscription_overdue_not_paid(): void
    {
        $juragan = User::factory()->juragan()->create(['plan' => 'lite']);

        $sub = Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDays(3),
        ]);

        SuspendOverdueJuraganJob::dispatchSync();

        $this->assertSame('overdue', $sub->fresh()->status);
    }

    public function test_suspend_job_does_not_re_suspend_already_suspended_juragan(): void
    {
        $suspendedAt = Carbon::now()->subHour();
        $juragan     = User::factory()->juragan()->create([
            'plan'         => 'lite',
            'suspended_at' => $suspendedAt,
        ]);

        Subscription::factory()->create([
            'juragan_id' => $juragan->id,
            'status'     => 'unpaid',
            'due_date'   => Carbon::today()->subDays(3),
        ]);

        SuspendOverdueJuraganJob::dispatchSync();

        // suspended_at should not have changed (still the original timestamp)
        $this->assertEquals(
            $suspendedAt->toDateTimeString(),
            $juragan->fresh()->suspended_at->toDateTimeString()
        );
    }

    // ── SubscriptionObserver isolation ────────────────────────────────────────

    public function test_paying_subscription_clears_suspended_at_for_correct_juragan_only(): void
    {
        $juraganA = User::factory()->juragan()->create(['suspended_at' => now()]);
        $juraganB = User::factory()->juragan()->create(['suspended_at' => now()]);

        $subA = Subscription::factory()->create([
            'juragan_id' => $juraganA->id,
            'status'     => 'overdue',
        ]);

        // Only A's subscription is paid
        $subA->update(['status' => 'paid']);

        // A should be unsuspended, B should remain suspended
        $this->assertNull($juraganA->fresh()->suspended_at);
        $this->assertNotNull($juraganB->fresh()->suspended_at);
    }

    // ── Panel access with suspension ─────────────────────────────────────────

    public function test_suspended_juragan_cannot_access_admin_panel(): void
    {
        $juragan = User::factory()->juragan()->create(['suspended_at' => now()]);

        $adminPanel = app(\Filament\Panel::class)::make()->id('admin');
        $this->assertFalse($juragan->canAccessPanel($adminPanel));
    }

    public function test_unsuspended_juragan_can_access_admin_panel(): void
    {
        $juragan = User::factory()->juragan()->create(['suspended_at' => null]);

        $adminPanel = \Filament\Facades\Filament::getPanel('admin');
        $this->assertTrue($juragan->canAccessPanel($adminPanel));
    }

    public function test_anak_kos_blocked_when_juragan_suspended(): void
    {
        $juragan = User::factory()->juragan()->create(['suspended_at' => now()]);
        $anakKos = User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
        ]);

        $tenantPanel = \Filament\Facades\Filament::getPanel('tenant');
        $this->assertFalse($anakKos->canAccessPanel($tenantPanel));
    }

    public function test_anak_kos_accessible_when_juragan_not_suspended(): void
    {
        $juragan = User::factory()->juragan()->create(['suspended_at' => null]);
        $anakKos = User::factory()->create([
            'role'       => 'tenant',
            'juragan_id' => $juragan->id,
        ]);

        $tenantPanel = \Filament\Facades\Filament::getPanel('tenant');
        $this->assertTrue($anakKos->canAccessPanel($tenantPanel));
    }
}
