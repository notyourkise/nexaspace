<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Jobs\ProvisionTenantJob;
use App\Models\Registration;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Pendaftaran';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Registrations are platform-level subscription requests — developer only.
     */
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

    public static function getNavigationBadge(): ?string
    {
        $count = Registration::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Juragan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kos_name')
                    ->label('Nama Kos')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('Nomor WA')
                    ->url(fn ($state) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', ltrim((string) $state, '+')))
                    ->openUrlInNewTab(),

                BadgeColumn::make('plan')
                    ->label('Paket')
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                    ->colors([
                        'gray'    => 'lite',
                        'warning' => 'pro',
                        'info'    => 'custom',
                    ]),

                TextColumn::make('room_count')
                    ->label('Kamar')
                    ->sortable()
                    ->suffix(' kamar'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'approved',
                        'success' => 'active',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'active'   => 'Aktif',
                        'rejected' => 'Ditolak',
                        default    => $state,
                    }),

                BadgeColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'paid' ? 'Lunas' : 'Belum Bayar'),

                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('plan')
                    ->label('Paket')
                    ->options([
                        'lite'   => 'LITE',
                        'pro'    => 'PRO',
                        'custom' => 'CUSTOM',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'active'   => 'Aktif',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('approve_provision')
                    ->label('Setujui & Buat Akun')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui pendaftaran & buat akun?')
                    ->modalDescription('Sistem akan membuat akun juragan + akun anak kos sesuai kuota paket, lalu mengirim kredensial ke email juragan. Tindakan ini tidak bisa dibatalkan.')
                    ->action(function (Collection $records): void {
                        $provisioned = 0;
                        $skipped     = 0;

                        foreach ($records as $registration) {
                            if ($registration->status === 'active') {
                                $skipped++;
                                continue;
                            }

                            ProvisionTenantJob::dispatchSync($registration);
                            $provisioned++;
                        }

                        Notification::make()
                            ->title('Provisioning selesai')
                            ->body("Diproses: {$provisioned} | Dilewati (sudah aktif): {$skipped}")
                            ->success()
                            ->send();
                    }),

                BulkAction::make('mark_rejected')
                    ->label('Tandai: Ditolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected']))
                    ->successNotificationTitle('Pendaftaran ditolak'),

                BulkAction::make('mark_payment_paid')
                    ->label('Tandai Pembayaran: Lunas')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['payment_status' => 'paid']))
                    ->successNotificationTitle('Pembayaran ditandai lunas'),

                BulkAction::make('mark_payment_unpaid')
                    ->label('Tandai Pembayaran: Belum Bayar')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['payment_status' => 'unpaid']))
                    ->successNotificationTitle('Pembayaran ditandai belum bayar'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
        ];
    }
}
