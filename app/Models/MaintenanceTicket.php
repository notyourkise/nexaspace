<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tenant_id', 'juragan_id', 'category', 'title', 'description', 'evidence_path', 'status', 'progress_note', 'reviewed_at', 'resolved_at'])]
class MaintenanceTicket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_RESOLVED = 'resolved';

    public const CATEGORY_WIFI = 'wifi';

    public const CATEGORY_WATER = 'water';

    public const CATEGORY_ELECTRICITY = 'electricity';

    public const CATEGORY_ROOM_DAMAGE = 'room_damage';

    public const CATEGORY_OTHER = 'other';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'Menunggu Review',
            self::STATUS_REVIEWED => 'Direview',
            self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            self::STATUS_RESOLVED => 'Selesai',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_WIFI => 'WiFi / Internet',
            self::CATEGORY_WATER => 'Air',
            self::CATEGORY_ELECTRICITY => 'Listrik',
            self::CATEGORY_ROOM_DAMAGE => 'Kerusakan Kamar',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function juragan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'juragan_id');
    }
}
