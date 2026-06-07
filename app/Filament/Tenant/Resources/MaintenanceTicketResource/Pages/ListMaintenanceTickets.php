<?php

namespace App\Filament\Tenant\Resources\MaintenanceTicketResource\Pages;

use App\Filament\Tenant\Resources\MaintenanceTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceTickets extends ListRecords
{
    protected static string $resource = MaintenanceTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Laporan'),
        ];
    }
}
