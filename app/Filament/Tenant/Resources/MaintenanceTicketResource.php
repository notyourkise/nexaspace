<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MaintenanceTicketResource\Pages;
use App\Models\MaintenanceTicket;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MaintenanceTicketResource extends Resource
{
    protected static ?string $model = MaintenanceTicket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Kerusakan';

    protected static ?string $pluralModelLabel = 'Laporan Kerusakan';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category')
                ->label('Jenis Gangguan')
                ->options(MaintenanceTicket::categoryOptions())
                ->default(MaintenanceTicket::CATEGORY_OTHER)
                ->required()
                ->native(false),

            TextInput::make('title')
                ->label('Judul Laporan')
                ->placeholder('Contoh: WiFi kamar 12 mati')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Detail Laporan')
                ->placeholder('Jelaskan lokasi, kronologi, dan kondisi terakhir.')
                ->required()
                ->rows(5)
                ->maxLength(2000)
                ->columnSpanFull(),

            FileUpload::make('evidence_path')
                ->label('Bukti Gambar')
                ->disk('public')
                ->directory('maintenance-reports')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(3072)
                ->required()
                ->helperText('Upload foto bukti gangguan. Format JPG, PNG, atau WebP. Maks 3 MB.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => MaintenanceTicket::categoryOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Laporan')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => MaintenanceTicket::statusOptions()[$state] ?? $state)
                    ->colors([
                        'warning' => MaintenanceTicket::STATUS_OPEN,
                        'info' => MaintenanceTicket::STATUS_REVIEWED,
                        'primary' => MaintenanceTicket::STATUS_IN_PROGRESS,
                        'success' => MaintenanceTicket::STATUS_RESOLVED,
                    ]),

                TextColumn::make('progress_note')
                    ->label('Progress')
                    ->placeholder('-')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                IconColumn::make('evidence_path')
                    ->label('Bukti')
                    ->icon(fn ($state) => $state ? 'heroicon-o-photo' : 'heroicon-o-minus')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? 'Klik untuk lihat bukti' : 'Belum ada bukti')
                    ->url(fn (MaintenanceTicket $record): ?string => $record->evidence_path
                        ? Storage::url($record->evidence_path)
                        : null
                    )
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(MaintenanceTicket::statusOptions()),

                SelectFilter::make('category')
                    ->label('Jenis Gangguan')
                    ->options(MaintenanceTicket::categoryOptions()),
            ])
            ->actions([
                Action::make('lihat_bukti')
                    ->label('Bukti')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->url(fn (MaintenanceTicket $record): ?string => $record->evidence_path
                        ? Storage::url($record->evidence_path)
                        : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (MaintenanceTicket $record): bool => filled($record->evidence_path)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceTickets::route('/'),
            'create' => Pages\CreateMaintenanceTicket::route('/create'),
        ];
    }
}
