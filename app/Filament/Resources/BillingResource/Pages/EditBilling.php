<?php

namespace App\Filament\Resources\BillingResource\Pages;

use App\Filament\Resources\BillingResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBilling extends EditRecord
{
    protected static string $resource = BillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Persist move_in_date to the tenant user record, then strip from billing data.
        if (isset($data['user_id']) && array_key_exists('move_in_date', $data)) {
            User::where('id', $data['user_id'])->update(['move_in_date' => $data['move_in_date']]);
        }
        unset($data['move_in_date']);

        return $data;
    }
}
