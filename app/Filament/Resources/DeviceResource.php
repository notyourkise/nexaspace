<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use App\Models\User;
use App\Services\MikroTikService;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Anak Kos')
                ->options(function () {
                    $query = User::where('role', 'tenant');
                    $user  = auth()->user();
                    if ($user && $user->isJuragan()) {
                        $query->where('juragan_id', $user->id);
                    }
                    return $query
                        ->orderByRaw('CAST(room_number AS UNSIGNED), room_number')
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (User $tenant): array => [
                            $tenant->id => trim(collect([
                                $tenant->room_number ? "Kamar {$tenant->room_number}" : null,
                                $tenant->name,
                                $tenant->email,
                            ])->filter()->implode(' - ')),
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->helperText('Pilih anak kos yang akan didaftarkan perangkatnya.'),

            TextInput::make('device_name')
                ->label('Nama Device')
                ->placeholder('Contoh: HP Android, Laptop, iPhone')
                ->required()
                ->maxLength(255),

            TextInput::make('mac_address')
                ->label('MAC Address')
                ->required()
                ->unique(ignoreRecord: true)
                ->regex('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/')
                ->maxLength(17)
                ->placeholder('AA:BB:CC:DD:EE:FF')
                ->helperText('Gunakan format 6 pasang karakter, contoh: AA:BB:CC:DD:EE:FF.'),

            Select::make('status')
                ->label('Status Device')
                ->options([
                    'active' => 'Active / Normal',
                    'throttled' => 'Throttled / Dibatasi',
                    'blocked' => 'Blocked / Diblokir',
                ])
                ->required()
                ->default('active')
                ->helperText('Status awal biasanya Active. Pilih Throttled atau Blocked jika perangkat perlu dibatasi sejak awal.'),
        ]);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->isDeveloper() || $user?->isJuragan();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                \Filament\Actions\Action::make('create_device')
                    ->label('Tambah Device')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->visible(fn (): bool => static::canCreate())
                    ->url(fn () => static::getUrl('create')),
            ])
            ->columns([
                TextColumn::make('device_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mac_address')
                    ->label('MAC Address')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Anak Kos')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.room_number')
                    ->label('Kamar')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'throttled',
                        'danger' => 'blocked',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'throttled' => 'Throttled',
                        'blocked' => 'Blocked',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('throttle')
                    ->label('Throttle Selected')
                    ->icon('heroicon-o-signal-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'throttled']))
                    ->successNotificationTitle('Perangkat di-throttle'),

                BulkAction::make('unblock')
                    ->label('Set Active')
                    ->icon('heroicon-o-signal')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'active']))
                    ->successNotificationTitle('Perangkat diaktifkan'),

                BulkAction::make('block')
                    ->label('Block Selected')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'blocked']))
                    ->successNotificationTitle('Perangkat diblokir'),

                BulkAction::make('sync_mikrotik')
                    ->label('Sync to MikroTik')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Sync selected devices to MikroTik?')
                    ->modalDescription('This will push the current DB status of each device to the router via API. Active devices will have their rate-limit cleared; throttled devices will be set to 256k/256k.')
                    ->action(function (Collection $records): void {
                        $mikrotik = app(MikroTikService::class);
                        $synced = 0;
                        $failed = 0;

                        foreach ($records as $device) {
                            $ok = match ($device->status) {
                                'active'    => $mikrotik->unthrottleDevice($device->mac_address),
                                'throttled' => $mikrotik->throttleDevice($device->mac_address),
                                default     => true, // 'blocked' is handled manually on router
                            };

                            $ok ? $synced++ : $failed++;
                        }

                        Notification::make()
                            ->title('MikroTik Sync Complete')
                            ->body("Synced: {$synced} | Failed: {$failed}")
                            ->when($failed > 0, fn ($n) => $n->warning(), fn ($n) => $n->success())
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('user'));
    }

    /**
     * Data isolation: a juragan only sees devices belonging to their own anak kos.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && $user->isJuragan()) {
            $query->whereHas('user', fn (Builder $q) => $q->where('juragan_id', $user->id));
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
