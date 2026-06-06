<?php

namespace App\Filament\Resources\BillingResource\Pages;

use App\Filament\Resources\BillingResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateBilling extends CreateRecord
{
    protected static string $resource = BillingResource::class;

    /**
     * Defense in depth: a juragan may only create a bill for one of their
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
