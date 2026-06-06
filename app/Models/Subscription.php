<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['juragan_id', 'amount', 'subscription_month', 'due_date', 'status', 'payment_receipt'])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'subscription_month' => 'date',
            'due_date'           => 'date',
            'amount'             => 'integer',
        ];
    }

    public function juragan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'juragan_id');
    }
}
