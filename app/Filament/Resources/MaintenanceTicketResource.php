<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceTicketResource\Pages;
use App\Models\MaintenanceTicket;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class MaintenanceTicketResource extends Resource
{
    protected static ?string $model = MaintenanceTicket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Kerusakan';

    protected static ?string $pluralModelLabel = 'Laporan Kerusakan';

    protected static ?int $navigationSort = 8;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Laporan')
                ->schema([
                    Placeholder::make('tenant_name')
                        ->label('Anak Kos')
                        ->content(fn (?MaintenanceTicket $record): string => $record?->tenant?->name ?? '-'),

                    Placeholder::make('room_number')
                        ->label('Kamar')
                        ->content(fn (?MaintenanceTicket $record): string => (string) ($record?->tenant?->room_number ?? '-')),

                    Placeholder::make('category_label')
                        ->label('Jenis Gangguan')
                        ->content(fn (?MaintenanceTicket $record): string => $record
                            ? (MaintenanceTicket::categoryOptions()[$record->category] ?? $record->category)
                            : '-'
                        ),

                    Placeholder::make('reported_at')
                        ->label('Tanggal Laporan')
                        ->content(fn (?MaintenanceTicket $record): string => $record?->created_at?->format('d M Y H:i') ?? '-'),

                    Placeholder::make('title_label')
                        ->label('Judul')
                        ->content(fn (?MaintenanceTicket $record): string => $record?->title ?? '-')
                        ->columnSpanFull(),

                    Placeholder::make('description_label')
                        ->label('Detail')
                        ->content(fn (?MaintenanceTicket $record): string => $record?->description ?? '-')
                        ->columnSpanFull(),

                    Placeholder::make('evidence_link')
                        ->label('Bukti Gambar')
                        ->content(fn (?MaintenanceTicket $record): HtmlString => new HtmlString(
                            $record?->evidence_path
                                ? '<a class="font-bold text-primary-400 underline" href="' . e(Storage::url($record->evidence_path)) . '" target="_blank" rel="noopener">Buka bukti gambar</a>'
                                : '-'
                        )),

                    FileUpload::make('evidence_path')
                        ->label('Preview Bukti')
                        ->disk('public')
                        ->directory('maintenance-reports')
                        ->image()
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Progress Laporan')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(MaintenanceTicket::statusOptions())
                        ->required()
                        ->native(false),

                    Textarea::make('progress_note')
                        ->label('Catatan Progress')
                        ->placeholder('Contoh: Teknisi akan cek router sore ini.')
                        ->rows(4)
                        ->maxLength(2000)
                        ->columnSpanFull(),
                ]),
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

                TextColumn::make('tenant.name')
                    ->label('Anak Kos')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.room_number')
                    ->label('Kamar')
                    ->sortable(),

                TextColumn::make('tenant.juragan.kos_name')
                    ->label('Kos')
                    ->toggleable(isToggledHiddenByDefault: ! (auth()->user()?->isDeveloper() ?? false)),

                TextColumn::make('category')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => MaintenanceTicket::categoryOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Laporan')
                    ->searchable()
                    ->sortable()
                    ->limit(42),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => MaintenanceTicket::statusOptions()[$state] ?? $state)
                    ->colors([
                        'warning' => MaintenanceTicket::STATUS_OPEN,
                        'info' => MaintenanceTicket::STATUS_REVIEWED,
                        'primary' => MaintenanceTicket::STATUS_IN_PROGRESS,
                        'success' => MaintenanceTicket::STATUS_RESOLVED,
                    ]),

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
                Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->url(fn (MaintenanceTicket $record): string => static::getUrl('edit', ['record' => $record])),

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
            ->bulkActions([
                BulkAction::make('mark_in_progress')
                    ->label('Tandai Sedang Dikerjakan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update([
                        'status' => MaintenanceTicket::STATUS_IN_PROGRESS,
                        'reviewed_at' => now(),
                        'resolved_at' => null,
                    ]))
                    ->successNotificationTitle('Laporan ditandai sedang dikerjakan'),

                BulkAction::make('mark_resolved')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update([
                        'status' => MaintenanceTicket::STATUS_RESOLVED,
                        'reviewed_at' => now(),
                        'resolved_at' => now(),
                    ]))
                    ->successNotificationTitle('Laporan ditandai selesai'),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['tenant.juragan']));
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->isJuragan()) {
            $query->where('juragan_id', $user->id);
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
            'index' => Pages\ListMaintenanceTickets::route('/'),
            'edit' => Pages\EditMaintenanceTicket::route('/{record}/edit'),
        ];
    }
}
