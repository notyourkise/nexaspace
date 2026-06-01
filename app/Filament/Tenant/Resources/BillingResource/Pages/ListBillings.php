<?php

namespace App\Filament\Tenant\Resources\BillingResource\Pages;

use App\Filament\Tenant\Resources\BillingResource;
use Filament\Resources\Pages\ListRecords;

class ListBillings extends ListRecords
{
    protected static string $resource = BillingResource::class;

    /** No "New Bill" button — bills are created by admin only. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
