<?php

namespace App\Filament\Resources\MaintenanceTicketResource\Pages;

use App\Filament\Resources\MaintenanceTicketResource;
use App\Models\MaintenanceTicket;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceTicket extends EditRecord
{
    protected static string $resource = MaintenanceTicketResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $status = $data['status'] ?? $this->record->status;

        if (in_array($status, [MaintenanceTicket::STATUS_REVIEWED, MaintenanceTicket::STATUS_IN_PROGRESS, MaintenanceTicket::STATUS_RESOLVED], true)) {
            $data['reviewed_at'] = $this->record->reviewed_at ?? now();
        } else {
            $data['reviewed_at'] = null;
        }

        if ($status === MaintenanceTicket::STATUS_RESOLVED) {
            $data['resolved_at'] = $this->record->resolved_at ?? now();
        } elseif ($this->record->status === MaintenanceTicket::STATUS_RESOLVED && $status !== MaintenanceTicket::STATUS_RESOLVED) {
            $data['resolved_at'] = null;
        }

        return $data;
    }
}
