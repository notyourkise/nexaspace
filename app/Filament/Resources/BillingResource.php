<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingResource\Pages;
use App\Models\Billing;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

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
                    return $query->orderBy('room_number')->pluck('name', 'id');
                })
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    if (! $state) {
                        $set('move_in_date', null);
                        return;
                    }

                    $tenant = User::find($state);
                    if (! $tenant) return;

                    // Auto-fill move_in_date from tenant record
                    $set('move_in_date', $tenant->move_in_date?->format('Y-m-d'));

                    // Auto-fill amount from monthly_rate
                    if ($tenant->monthly_rate > 0) {
                        $set('amount', number_format((int) $tenant->monthly_rate, 0, ',', '.'));
                    }

                    // Auto-fill billing_month to current month
                    $set('billing_month', Carbon::now()->startOfMonth()->format('Y-m-d'));

                    // Auto-fill due_date: 7 days after the move_in_date's day in current month
                    if ($tenant->move_in_date) {
                        $day     = $tenant->move_in_date->day;
                        $maxDay  = Carbon::now()->daysInMonth;
                        $dueDate = Carbon::now()->startOfMonth()->setDay(min($day, $maxDay))->addDays(7);
                        $set('due_date', $dueDate->format('Y-m-d'));
                    }
                }),

            DatePicker::make('move_in_date')
                ->label('Tanggal Masuk Anak Kos')
                ->displayFormat('d F Y')
                ->helperText('Tagihan bulanan auto-generate setiap tanggal ini. Simpan perubahan untuk memperbarui data anak kos.')
                ->nullable()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    if (! $state) return;
                    $day     = Carbon::parse($state)->day;
                    $maxDay  = Carbon::now()->daysInMonth;
                    $dueDate = Carbon::now()->startOfMonth()->setDay(min($day, $maxDay))->addDays(7);
                    $set('due_date', $dueDate->format('Y-m-d'));
                })
                ->afterStateHydrated(function ($component, ?Billing $record): void {
                    if ($record?->user?->move_in_date) {
                        $component->state($record->user->move_in_date->format('Y-m-d'));
                    }
                }),

            TextInput::make('amount')
                ->label('Nominal Tagihan')
                ->prefix('Rp')
                ->required()
                ->placeholder('0')
                ->inputMode('numeric')
                ->extraInputAttributes([
                    'x-on:input' => '$el.value = $el.value.replace(/[^0-9]/g,"").replace(/\B(?=(\d{3})+(?!\d))/g,".")',
                    'x-on:focus' => '$el.value = $el.value.replace(/[^0-9]/g,"").replace(/\B(?=(\d{3})+(?!\d))/g,".")',
                ])
                ->formatStateUsing(fn ($state) => $state ? number_format((int) $state, 0, ',', '.') : '')
                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/[^0-9]/', '', (string) $state))
                ->rules(['required', 'integer', 'min:0']),

            DatePicker::make('billing_month')
                ->required()
                ->displayFormat('F Y')
                ->label('Bulan Tagihan')
                ->default(fn () => Carbon::now()->startOfMonth()->format('Y-m-d')),

            DatePicker::make('due_date')
                ->required()
                ->label('Jatuh Tempo'),

            Select::make('status')
                ->label('Status')
                ->options([
                    'unpaid'    => 'Belum Lunas',
                    'paid'      => 'Lunas',
                    'throttled' => 'Di-throttle',
                ])
                ->required()
                ->default('unpaid'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.room_number')
                    ->label('Room')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('billing_month')
                    ->date('F Y')
                    ->label('Month')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->label('Due Date')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                        'danger'  => 'throttled',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid'      => 'Lunas',
                        'unpaid'    => 'Belum Lunas',
                        'throttled' => 'Dibatasi',
                        default     => $state,
                    }),

                IconColumn::make('payment_receipt')
                    ->label('Bukti')
                    ->icon(fn ($state) => $state ? 'heroicon-o-photo' : 'heroicon-o-minus')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? 'Ada bukti bayar — klik untuk lihat' : 'Belum ada bukti bayar')
                    ->url(fn (Billing $record): ?string => $record->payment_receipt
                        ? Storage::url($record->payment_receipt)
                        : null
                    )
                    ->openUrlInNewTab(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid'    => 'Belum Lunas',
                        'paid'      => 'Lunas',
                        'throttled' => 'Dibatasi',
                    ]),

                SelectFilter::make('has_receipt')
                    ->label('Bukti Bayar')
                    ->options([
                        'yes' => 'Ada bukti',
                        'no'  => 'Belum ada',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'yes' => $query->whereNotNull('payment_receipt'),
                        'no'  => $query->whereNull('payment_receipt'),
                        default => $query,
                    }),
            ])
            ->actions([
                Action::make('download_invoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Billing $record): string => route('invoice.billing', $record))
                    ->openUrlInNewTab(),

                DeleteAction::make()
                    ->modalHeading('Hapus Tagihan')
                    ->modalDescription('Tagihan yang dihapus tidak dapat dikembalikan. Lanjutkan?')
                    ->successNotificationTitle('Tagihan berhasil dihapus'),
            ])
            ->bulkActions([
                BulkAction::make('download_merged_invoice')
                    ->label('Invoice Gabungan (PDF)')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Invoice Gabungan')
                    ->modalDescription('Buat satu file PDF yang menggabungkan semua tagihan yang dipilih. Semua tagihan harus milik satu anak kos yang sama.')
                    ->action(function (Collection $records): void {
                        $uniqueTenants = $records->pluck('user_id')->unique();

                        if ($uniqueTenants->count() > 1) {
                            Notification::make()
                                ->title('Pilih tagihan dari satu anak kos')
                                ->body('Invoice gabungan hanya bisa dibuat untuk tagihan dari satu anak kos dalam satu waktu.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $ids = $records->pluck('id')->join(',');
                        $url = route('invoice.billing.merged', ['ids' => $ids]);

                        Notification::make()
                            ->title('Invoice Gabungan Siap')
                            ->body($records->count() . ' tagihan dipilih.')
                            ->success()
                            ->persistent()
                            ->actions([
                                Action::make('download')
                                    ->label('Download PDF →')
                                    ->url($url)
                                    ->openUrlInNewTab()
                                    ->button(),
                            ])
                            ->send();
                    }),

                BulkAction::make('mark_paid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'paid']))
                    ->successNotificationTitle('Tagihan ditandai lunas'),

                BulkAction::make('mark_unpaid')
                    ->label('Tandai Belum Lunas')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'unpaid']))
                    ->successNotificationTitle('Tagihan ditandai belum lunas'),

                DeleteBulkAction::make()
                    ->modalHeading('Hapus Tagihan yang Dipilih')
                    ->modalDescription('Semua tagihan yang dipilih akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.')
                    ->successNotificationTitle('Tagihan berhasil dihapus'),
            ])
            ->defaultSort('billing_month', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('user'));
    }

    /**
     * Data isolation: a juragan only sees bills belonging to their own anak kos.
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
            'index' => Pages\ListBillings::route('/'),
            'create' => Pages\CreateBilling::route('/create'),
            'edit' => Pages\EditBilling::route('/{record}/edit'),
        ];
    }
}
