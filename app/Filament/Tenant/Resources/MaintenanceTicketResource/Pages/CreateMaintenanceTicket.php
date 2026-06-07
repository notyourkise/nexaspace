<?php

namespace App\Filament\Tenant\Resources\MaintenanceTicketResource\Pages;

use App\Filament\Tenant\Resources\MaintenanceTicketResource;
use App\Models\MaintenanceTicket;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceTicket extends CreateRecord
{
    protected static string $resource = MaintenanceTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = auth()->user();

        abort_unless($tenant?->isTenant() && filled($tenant->juragan_id), 403);

        $data['tenant_id'] = $tenant->id;
        $data['juragan_id'] = $tenant->juragan_id;
        $data['status'] = MaintenanceTicket::STATUS_OPEN;
        $data['progress_note'] = null;
        $data['reviewed_at'] = null;
        $data['resolved_at'] = null;

        return $data;
    }
}
