<?php

namespace App\Jobs;

use App\Mail\TenantProvisionedMail;
use App\Models\ActivityLog;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(public readonly Registration $registration) {}

    public function handle(): void
    {
        $reg = $this->registration->fresh();

        // Idempotency: never provision the same registration twice.
        if (! $reg || $reg->status === 'active') {
            return;
        }

        $quota = $this->quotaForPlan($reg->plan, $reg->room_count);
        $slug  = $this->uniqueSlug($reg->kos_name);

        // All-or-nothing: if anything (including the email) fails, roll back the
        // accounts so a retry starts clean and we never half-provision.
        DB::transaction(function () use ($reg, $quota, $slug): void {
            $defaultPassword = 'password';

            $juragan = User::create([
                'name'          => $reg->name,
                'email'         => "owner@{$slug}.com",
                'contact_email' => $reg->email,
                'password'      => $defaultPassword,
                'role'          => 'juragan',
                'kos_name'      => $reg->kos_name,
                'kos_slug'      => $slug,
                'plan'          => $reg->plan,
                'room_quota'    => $quota,
                'phone_number'  => $reg->phone,
            ]);

            $anakKos = [];

            for ($i = 1; $i <= $quota; $i++) {
                User::create([
                    'name'        => "Kamar {$i} — {$reg->kos_name}",
                    'email'       => "room{$i}@{$slug}.com",
                    'password'    => $defaultPassword,
                    'role'        => 'tenant',
                    'juragan_id'  => $juragan->id,
                    'room_number' => (string) $i,
                ]);

                $anakKos[] = ['email' => "room{$i}@{$slug}.com", 'password' => $defaultPassword];
            }

            Mail::to($reg->email)->send(
                new TenantProvisionedMail($juragan, $defaultPassword, $anakKos)
            );

            $reg->update(['status' => 'active']);

            ActivityLog::record(
                event: 'juragan.provisioned',
                description: "Akun juragan {$juragan->kos_name} + {$quota} anak kos berhasil dibuat dari registrasi #{$reg->id}.",
                subject: $reg,
                properties: ['juragan_id' => $juragan->id, 'quota' => $quota, 'plan' => $reg->plan, 'slug' => $slug],
            );
        });
    }

    private function quotaForPlan(string $plan, int $roomCount): int
    {
        return match ($plan) {
            'lite'   => 20,
            'pro'    => 40,
            'custom' => max(50, $roomCount),
            default  => max(1, $roomCount),
        };
    }

    /** Generate a kos slug unique across all users (mutiara → mutiara-2 → …). */
    private function uniqueSlug(string $kosName): string
    {
        $base = Str::slug($kosName) ?: 'kos';
        $slug = $base;
        $n    = 2;

        while (User::where('kos_slug', $slug)->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
