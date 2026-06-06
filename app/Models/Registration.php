<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'name',
        'kos_name',
        'email',
        'phone',
        'room_count',
        'plan',
        'message',
        'status',
        'payment_status',
        'midtrans_order_id',
    ];

    protected function casts(): array
    {
        return [
            'room_count' => 'integer',
            'plan'       => 'string',
            'status'     => 'string',
        ];
    }

    public function getPlanLabelAttribute(): string
    {
        return match ($this->plan) {
            'lite'   => 'LITE — Rp 199.000/bln',
            'pro'    => 'PRO — Rp 499.000/bln',
            'custom' => 'CUSTOM',
            default  => strtoupper((string) $this->plan),
        };
    }
}
