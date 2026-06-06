<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * A juragan can only ever create anak kos owned by themselves.
     * Enforced server-side so it cannot be bypassed via the UI.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->isJuragan()) {
            $data['role']       = 'tenant';
            $data['juragan_id'] = $user->id;
        }

        return $data;
    }
}
