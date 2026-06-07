<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\Widget;

class QrisWidget extends Widget
{
    protected string $view = 'filament.tenant.widgets.qris-widget';

    protected static ?int $sort = 5;

    // Digantikan oleh TenantDashboardWidget yang sudah menyertakan blok QRIS.
    public static function canView(): bool
    {
        return false;
    }

    protected function getViewData(): array
    {
        $juragan = auth()->user()->juragan;

        return [
            'qrisUrl'  => asset('storage/' . $juragan->qris_image),
            'kosName'  => $juragan->kos_name,
            'phone'    => $juragan->phone_number,
        ];
    }
}
