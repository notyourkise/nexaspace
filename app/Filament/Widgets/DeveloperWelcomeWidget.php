<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class DeveloperWelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.developer-welcome-widget';

    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    protected function getViewData(): array
    {
        $failedJobs = DB::table('failed_jobs')->count();

        $dbHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception) {
            $dbHealthy = false;
        }

        return [
            'userName'   => auth()->user()?->name ?? 'Developer',
            'failedJobs' => $failedJobs,
            'dbHealthy'  => $dbHealthy,
        ];
    }
}
