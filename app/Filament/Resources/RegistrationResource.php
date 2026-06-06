<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Jobs\ProvisionTenantJob;
use App\Models\Registration;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
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

    public static function getNavigationBadge(): ?string
    {
        $count = Registration::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pendaftar')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Juragan / PIC')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('kos_name')
                        ->label('Nama Kos')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Nomor WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(20)
                        ->placeholder('08xxxxxxxxxx'),
                ])
                ->columns(2),

            Section::make('Detail Paket')
                ->schema([
                    Select::make('plan')
                        ->label('Paket')
                        ->options([
                            'lite'   => 'LITE — Rp 199.000/bln (maks 20 kamar)',
                            'pro'    => 'PRO — Rp 499.000/bln (maks 40 kamar)',
                            'custom' => 'CUSTOM — 50+ kamar',
                        ])
                        ->required()
                        ->live()
                        ->native(false),

                    TextInput::make('room_count')
                        ->label('Jumlah Kamar')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(fn (Get $get): int => match ($get('plan')) {
                            'lite'  => 20,
                            'pro'   => 40,
                            default => 999,
                        })
                        ->helperText(fn (Get $get): string => match ($get('plan')) {
                            'lite'  => 'Maks. 20 kamar untuk paket LITE',
                            'pro'   => 'Maks. 40 kamar untuk paket PRO',
                            default => 'Disesuaikan kebutuhan',
                        }),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'  => 'Menunggu Persetujuan',
                            'approved' => 'Disetujui',
                            'active'   => 'Aktif (sudah diprovisioning)',
                            'rejected' => 'Ditolak',
                        ])
                        ->required()
                        ->default('pending')
                        ->native(false),

                    Select::make('payment_status')
                        ->label('Status Pembayaran')
                        ->options([
                            'unpaid' => 'Belum Bayar',
                            'paid'   => 'Lunas',
                        ])
                        ->required()
                        ->default('unpaid')
                        ->native(false),
                ])
                ->columns(2),

            Section::make('Pesan / Catatan')
                ->schema([
                    Textarea::make('message')
                        ->label('Pesan dari Pendaftar')
                        ->rows(4)
                        ->maxLength(1000)
                        ->placeholder('Kosong jika tidak ada pesan.')
                        ->nullable(),
                ])
                ->collapsible()
                ->collapsed(fn ($record) => empty($record?->message)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->prefix('REG-')
                    ->sortable()
                    ->width('70px'),

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
                        'pending'  => 'Menunggu',
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

                SelectFilter::make('payment_status')
                    ->label('Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'paid'   => 'Lunas',
                    ]),
            ])
            ->actions([
                Action::make('provision')
                    ->label('Setujui & Buat Akun')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui & provisioning akun?')
                    ->modalDescription('Sistem akan membuat akun juragan + anak kos sesuai kuota paket, lalu mengirim kredensial ke email juragan. Tindakan ini tidak dapat dibatalkan.')
                    ->action(function (Registration $record): void {
                        if ($record->status === 'active') {
                            Notification::make()->title('Sudah aktif')->warning()->send();
                            return;
                        }
                        ProvisionTenantJob::dispatchSync($record);
                        Notification::make()->title('Akun berhasil dibuat')->success()->send();
                    })
                    ->visible(fn (Registration $record): bool => $record->status !== 'active'),

                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->url(fn (Registration $record): string => static::getUrl('edit', ['record' => $record])),

                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus pendaftaran?')
                    ->modalDescription('Data pendaftaran ini akan dihapus permanen. Akun yang sudah diprovisioning tidak terpengaruh.')
                    ->action(function (Registration $record): void {
                        $record->delete();
                        Notification::make()->title('Pendaftaran dihapus')->success()->send();
                    }),
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

                BulkAction::make('mark_payment_paid')
                    ->label('Pembayaran: Lunas')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['payment_status' => 'paid']))
                    ->successNotificationTitle('Pembayaran ditandai lunas'),

                BulkAction::make('mark_payment_unpaid')
                    ->label('Pembayaran: Belum Bayar')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['payment_status' => 'unpaid']))
                    ->successNotificationTitle('Pembayaran ditandai belum bayar'),

                BulkAction::make('mark_rejected')
                    ->label('Tandai: Ditolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected']))
                    ->successNotificationTitle('Pendaftaran ditolak'),

                BulkAction::make('delete_bulk')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus pendaftaran terpilih?')
                    ->modalDescription('Semua pendaftaran yang dipilih akan dihapus permanen. Akun yang sudah diprovisioning tidak terpengaruh.')
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->successNotificationTitle('Pendaftaran dihapus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit'   => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}
