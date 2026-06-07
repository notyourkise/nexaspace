<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class JuraganQuotaWidget extends Widget
{
    protected string $view = 'filament.widgets.juragan-quota-widget';

    protected static ?int $sort = 2;

    /** Only render for juragan role. */
    public static function canView(): bool
    {
        return false;
    }

    protected function getViewData(): array
    {
        $juragan = auth()->user();

        $tenants = User::where('juragan_id', $juragan->id)
            ->where('role', 'tenant')
            ->orderBy('room_number')
            ->get(['id', 'name', 'room_number', 'monthly_rate']);

        $quota       = (int) ($juragan->room_quota ?: 0);
        $used        = $tenants->count();
        $sisa        = max(0, $quota - $used);
        $pct         = $quota > 0 ? round(($used / $quota) * 100) : 0;
        $noRate      = $tenants->filter(fn ($t) => (int) $t->monthly_rate === 0);

        return [
            'juragan'   => $juragan,
            'tenants'   => $tenants,
            'quota'     => $quota,
            'used'      => $used,
            'sisa'      => $sisa,
            'pct'       => $pct,
            'noRate'    => $noRate,
        ];
    }
}
