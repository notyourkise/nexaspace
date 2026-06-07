<?php

namespace App\Filament\Widgets;

use App\Services\MikroTikService;
use Filament\Widgets\Widget;

class MikroTikStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.mikrotik-status-widget';

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 1;

    // Refresh the widget every 5 minutes (300 seconds) to reflect cache TTL.
    protected static ?string $pollingInterval = '300s';

    public static function canView(): bool
    {
        return false; // MikroTik status is rendered inside DeveloperWelcomeWidget
    }

    protected function getViewData(): array
    {
        $mikrotik   = app(MikroTikService::class);
        $configured = $mikrotik->isConfigured();
        $connected  = $configured && $mikrotik->isConnected();
        $info       = $mikrotik->connectionInfo();

        return [
            'configured' => $configured,
            'connected'  => $connected,
            'host'       => $info['host'] ?: '—',
            'port'       => $info['port'],
            'user'       => $info['user'],
        ];
    }
}
