<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?int $navigationSort = 99;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->timezone('Asia/Makassar'),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'suspended')    => 'danger',
                        str_contains($state, 'provisioned')  => 'success',
                        str_contains($state, 'unsuspended')  => 'success',
                        str_contains($state, 'paid')         => 'success',
                        str_contains($state, 'throttled')    => 'warning',
                        default                              => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('causer_name')
                    ->label('Oleh')
                    ->default('Sistem')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Jenis Event')
                    ->options([
                        'billing.status_changed'       => 'Tagihan',
                        'subscription.status_changed'  => 'Langganan',
                        'juragan.suspended'            => 'Juragan Disuspend',
                        'juragan.unsuspended'          => 'Juragan Dipulihkan',
                        'juragan.provisioned'          => 'Provisioning',
                    ]),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
