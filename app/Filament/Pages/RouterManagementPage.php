<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Services\MikroTikService;
use Filament\Pages\Page;

class RouterManagementPage extends Page
{
    protected string $view = 'filament.pages.router-management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    protected static ?string $navigationLabel = 'Router MikroTik';

    protected static ?int $navigationSort = 5;

    /** Selected juragan_id (developer use; juragan sees only their own). */
    public ?int $selectedJuraganId = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->isJuragan()) {
            $this->selectedJuraganId = $user->id;
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->isDeveloper() || $user?->isJuragan();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getTitle(): string
    {
        return 'Manajemen Router MikroTik';
    }

    /** @return array<int, User> */
    public function getJuraganOptions(): array
    {
        return User::where('role', 'juragan')
            ->orderBy('kos_name')
            ->get(['id', 'kos_name', 'mikrotik_host'])
            ->all();
    }

    /** The currently selected juragan (null = none selected). */
    public function selectedJuragan(): ?User
    {
        if ($this->selectedJuraganId === null) {
            return null;
        }

        return User::find($this->selectedJuraganId);
    }

    /** Retrieve DHCP leases from the selected juragan's router. */
    public function getLeases(): array
    {
        $juragan = $this->selectedJuragan();

        if ($juragan === null) {
            return [];
        }

        $service = MikroTikService::forJuragan($juragan);

        if (! $service->isConfigured()) {
            return [];
        }

        return $service->getLeases();
    }

    /** State for the view. */
    protected function getViewData(): array
    {
        $user    = auth()->user();
        $juragan = $this->selectedJuragan();
        $leases  = $juragan ? $this->getLeases() : [];

        $service    = $juragan ? MikroTikService::forJuragan($juragan) : null;
        $configured = $service?->isConfigured() ?? false;
        $connected  = $configured ? $service->isConnected() : false;

        return [
            'isDeveloper'        => $user->isDeveloper(),
            'juragan'            => $juragan,
            'leases'             => $leases,
            'configured'         => $configured,
            'connected'          => $connected,
            'juraganOptions'     => $user->isDeveloper() ? $this->getJuraganOptions() : [],
        ];
    }
}
