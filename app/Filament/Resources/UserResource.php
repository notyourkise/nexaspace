<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->maxLength(255),

            Select::make('role')
                ->options([
                    'developer' => 'Developer (Super Admin)',
                    'juragan'   => 'Juragan (Pemilik Kos)',
                    'tenant'    => 'Anak Kos',
                ])
                ->required()
                ->live()
                ->default('tenant')
                // Only the developer may choose a role; juragan can only create anak kos.
                ->visible(fn (): bool => auth()->user()?->isDeveloper() ?? false),

            // ── Juragan fields (developer only) ──
            TextInput::make('kos_name')
                ->label('Nama Kos')
                ->maxLength(255)
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'juragan'),

            TextInput::make('kos_slug')
                ->label('Slug Kos (untuk domain email)')
                ->helperText('Contoh: "mutiara" → akun anak kos jadi room1@mutiara.com')
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'juragan'),

            Select::make('plan')
                ->label('Paket Langganan')
                ->options([
                    'lite'   => 'LITE (maks 20 kamar)',
                    'pro'    => 'PRO (maks 40 kamar)',
                    'custom' => 'CUSTOM (50+ kamar)',
                ])
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'juragan'),

            TextInput::make('room_quota')
                ->label('Kuota Akun Anak Kos')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'juragan'),

            // ── Anak kos fields ──
            // Developer picks the owning juragan; for a juragan it is forced to self
            // server-side (see CreateUser::mutateFormDataBeforeCreate), so hide it.
            Select::make('juragan_id')
                ->label('Juragan')
                ->relationship('juragan', 'name', fn ($query) => $query->where('role', 'juragan'))
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'tenant'),

            TextInput::make('room_number')
                ->label('Nomor Kamar')
                ->maxLength(10)
                ->visible(fn (Get $get): bool => $get('role') === 'tenant'),

            TextInput::make('phone_number')
                ->tel()
                ->maxLength(20),

            TextInput::make('monthly_rate')
                ->label('Rate Bulanan (Rp)')
                ->numeric()
                ->prefix('Rp')
                ->minValue(0)
                ->default(0)
                ->step(1000)
                ->helperText('Nominal tagihan bulanan. Tagihan digenerate otomatis sesuai tanggal masuk.')
                ->visible(fn (Get $get): bool => $get('role') === 'tenant'),

            DatePicker::make('move_in_date')
                ->label('Tanggal Masuk')
                ->displayFormat('d F Y')
                ->helperText('Tagihan bulanan auto-generate setiap tanggal ini. Update kapanpun ada pergantian penghuni.')
                ->nullable()
                ->visible(fn (Get $get): bool => $get('role') === 'tenant'),

            // ── MikroTik router config (developer only, for juragan) ──
            Section::make('Konfigurasi Router MikroTik')
                ->description('Kosongkan semua field untuk memakai konfigurasi global (.env). Isi jika juragan ini memiliki router sendiri.')
                ->schema([
                    TextInput::make('mikrotik_host')
                        ->label('Host / IP Router')
                        ->placeholder('192.168.1.1')
                        ->maxLength(255),

                    TextInput::make('mikrotik_port')
                        ->label('Port API (default 8728)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(65535)
                        ->placeholder('8728'),

                    TextInput::make('mikrotik_user')
                        ->label('Username Router')
                        ->placeholder('admin')
                        ->maxLength(255),

                    TextInput::make('mikrotik_pass')
                        ->label('Password Router')
                        ->password()
                        ->revealable()
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->visible(fn (Get $get): bool => (auth()->user()?->isDeveloper() ?? false) && $get('role') === 'juragan'),

            // ── QRIS (developer or juragan) ──
            Section::make('QRIS Pembayaran')
                ->description('Upload kode QRIS statis juragan. Akan ditampilkan di portal anak kos dan invoice PDF sebagai opsi pembayaran.')
                ->schema([
                    FileUpload::make('qris_image')
                        ->label('Gambar QRIS')
                        ->disk('public')
                        ->directory('qris')
                        ->image()
                        ->imagePreviewHeight('200')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->helperText('Format: JPG, PNG, atau WebP. Maks 2 MB.'),
                ])
                ->collapsible()
                ->collapsed(fn ($record) => $record?->qris_image === null)
                ->visible(fn (Get $get): bool => $get('role') === 'juragan'),

            // ── Rekening bank (juragan) ──
            Section::make('Rekening Bank')
                ->description('Daftar rekening tujuan transfer untuk anak kos. Akan muncul sebagai pilihan saat anak kos melakukan pembayaran tagihan.')
                ->schema([
                    Repeater::make('bank_accounts')
                        ->label('')
                        ->schema([
                            TextInput::make('bank_name')
                                ->label('Nama Bank')
                                ->placeholder('Contoh: BCA, BRI, Mandiri')
                                ->required()
                                ->maxLength(50),

                            TextInput::make('account_number')
                                ->label('Nomor Rekening')
                                ->placeholder('Contoh: 1234567890')
                                ->required()
                                ->maxLength(30),

                            TextInput::make('account_name')
                                ->label('Atas Nama')
                                ->placeholder('Nama pemilik rekening')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Tambah Rekening')
                        ->defaultItems(0)
                        ->reorderable(false),
                ])
                ->collapsible()
                ->collapsed(fn ($record) => empty($record?->bank_accounts))
                ->visible(fn (Get $get): bool => $get('role') === 'juragan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('role')
                    ->colors([
                        'danger'  => 'developer',
                        'warning' => 'juragan',
                        'success' => 'tenant',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'developer' => 'Developer',
                        'juragan'   => 'Juragan',
                        'tenant'    => 'Anak Kos',
                        default     => $state,
                    }),

                TextColumn::make('kos_name')
                    ->label('Kos')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('room_number')
                    ->label('Room')
                    ->sortable(),

                TextColumn::make('phone_number')
                    ->label('Phone'),

                TextColumn::make('monthly_rate')
                    ->label('Rate/Bln')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '—')
                    ->sortable(),

                TextColumn::make('devices_count')
                    ->label('Devices')
                    ->counts('devices')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'developer' => 'Developer',
                        'juragan'   => 'Juragan',
                        'tenant'    => 'Anak Kos',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->withCount('devices'));
    }

    /**
     * Data isolation: a juragan only ever sees their own anak kos.
     * The developer sees every user across the platform.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && $user->isJuragan()) {
            $query->where('juragan_id', $user->id)->where('role', 'tenant');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
