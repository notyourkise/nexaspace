<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'contact_email', 'password', 'role', 'juragan_id', 'kos_name', 'kos_slug', 'plan', 'room_quota', 'room_number', 'phone_number', 'monthly_rate', 'suspended_at', 'mikrotik_host', 'mikrotik_port', 'mikrotik_user', 'mikrotik_pass'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'room_quota'        => 'integer',
            'monthly_rate'      => 'integer',
            'suspended_at'      => 'datetime',
            'mikrotik_port'     => 'integer',
        ];
    }

    // ── Role helpers ──────────────────────────────────────────────────────────
    // developer = NexaSpace super admin (platform owner)
    // juragan   = boarding-house owner, a paying SaaS customer
    // tenant    = anak kos, belongs to exactly one juragan

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isJuragan(): bool
    {
        return $this->role === 'juragan';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            // Developer & juragan share the /admin panel; RBAC scoping happens per-resource.
            // Suspended juragan are blocked; developer is never suspended.
            'admin'  => in_array($this->role, ['developer', 'juragan'], true)
                && ($this->isDeveloper() || $this->suspended_at === null),
            // Anak kos lose access when their juragan is suspended.
            'tenant' => $this->role === 'tenant'
                && $this->juragan?->suspended_at === null,
            default  => false,
        };
    }

    // ── Relationships ───────────────────────────────────────────────────────────

    /** The juragan this anak kos belongs to (null for developer/juragan rows). */
    public function juragan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'juragan_id');
    }

    /** All anak kos managed by this juragan. */
    public function anakKos(): HasMany
    {
        return $this->hasMany(User::class, 'juragan_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }
}
