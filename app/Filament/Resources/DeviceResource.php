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
                ->label('Tenant')
                ->options(User::where('role', 'tenant')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('device_name')
                ->required()
                ->maxLength(255),

            TextInput::make('mac_address')
                ->label('MAC Address')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(17)
                ->placeholder('AA:BB:CC:DD:EE:FF'),

            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'throttled' => 'Throttled',
                    'blocked' => 'Blocked',
                ])
                ->required()
                ->default('active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mac_address')
                    ->label('MAC Address')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.room_number')
                    ->label('Room')
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
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'throttled'])),

                BulkAction::make('unblock')
                    ->label('Set Active')
                    ->icon('heroicon-o-signal')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'active'])),

                BulkAction::make('block')
                    ->label('Block Selected')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'blocked'])),

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
