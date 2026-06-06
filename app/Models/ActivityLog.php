<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null; // only created_at

    protected $fillable = [
        'causer_id',
        'causer_name',
        'subject_type',
        'subject_id',
        'event',
        'description',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties'  => 'array',
            'created_at'  => 'datetime',
        ];
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Record an activity. Causer defaults to the currently authenticated user.
     *
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        string $event,
        string $description,
        ?Model $subject = null,
        array  $properties = [],
        ?User  $causer = null,
    ): self {
        $actor = $causer ?? auth()->user();

        return static::create([
            'causer_id'    => $actor?->id,
            'causer_name'  => $actor?->name,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'event'        => $event,
            'description'  => $description,
            'properties'   => $properties ?: null,
        ]);
    }
}
