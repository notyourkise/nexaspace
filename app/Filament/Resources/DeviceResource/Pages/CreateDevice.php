<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateDevice extends CreateRecord
{
    protected static string $resource = DeviceResource::class;

    /**
     * Defense in depth: a juragan may only attach a device to one of their
     * own anak kos, even if the user_id is tampered with client-side.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->isJuragan()) {
            $owns = User::where('id', $data['user_id'] ?? null)
                ->where('juragan_id', $user->id)
                ->exists();

            abort_unless($owns, 403);
        }

        return $data;
    }
}
