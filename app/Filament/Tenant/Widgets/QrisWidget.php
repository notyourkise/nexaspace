<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\Widget;

class QrisWidget extends Widget
{
    protected string $view = 'filament.tenant.widgets.qris-widget';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $juragan = auth()->user()?->juragan;
        return $juragan && filled($juragan->qris_image);
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
