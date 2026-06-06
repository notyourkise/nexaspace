<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Langganan';

    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return auth()->user()?->isDeveloper() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        $isDeveloper = auth()->user()?->isDeveloper() ?? false;

        return $table
            ->columns(array_filter([
                $isDeveloper
                    ? TextColumn::make('juragan.name')
                        ->label('Juragan')
                        ->searchable()
                        ->sortable()
                    : null,

                $isDeveloper
                    ? BadgeColumn::make('juragan.plan')
                        ->label('Paket')
                        ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                        ->colors([
                            'gray'    => 'lite',
                            'warning' => 'pro',
                            'info'    => 'custom',
                        ])
                    : null,

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('subscription_month')
                    ->label('Bulan')
                    ->date('M Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                        'danger'  => 'overdue',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid'   => 'Lunas',
                        'unpaid' => 'Belum Lunas',
                        'overdue' => 'Menunggak',
                        default  => $state,
                    }),
            ]))
            ->defaultSort('subscription_month', 'desc')
            ->filters(array_filter([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid'  => 'Belum Lunas',
                        'paid'    => 'Lunas',
                        'overdue' => 'Menunggak',
                    ]),

                $isDeveloper
                    ? SelectFilter::make('plan')
                        ->label('Paket')
                        ->options([
                            'lite'   => 'LITE',
                            'pro'    => 'PRO',
                            'custom' => 'CUSTOM',
                        ])
                        ->query(fn (Builder $query, array $data): Builder =>
                            isset($data['value']) && $data['value']
                                ? $query->whereHas('juragan', fn (Builder $q) => $q->where('plan', $data['value']))
                                : $query
                        )
                    : null,

                SelectFilter::make('subscription_month')
                    ->label('Bulan')
                    ->options(function (): array {
                        $query = Subscription::query()
                            ->selectRaw('DATE_FORMAT(subscription_month, "%Y-%m-01") as val')
                            ->groupByRaw('DATE_FORMAT(subscription_month, "%Y-%m-01")')
                            ->orderByRaw('DATE_FORMAT(subscription_month, "%Y-%m-01") DESC');

                        $user = auth()->user();
                        if ($user && $user->isJuragan()) {
                            $query->where('juragan_id', $user->id);
                        }

                        return $query->pluck('val')
                            ->mapWithKeys(fn (string $val) => [
                                $val => Carbon::parse($val)->locale('id')->isoFormat('MMMM Y'),
                            ])
                            ->toArray();
                    })
                    ->query(fn (Builder $query, array $data): Builder =>
                        isset($data['value']) && $data['value']
                            ? $query->whereYear('subscription_month', Carbon::parse($data['value'])->year)
                                    ->whereMonth('subscription_month', Carbon::parse($data['value'])->month)
                            : $query
                    ),
            ]))
            ->actions([])
            ->bulkActions(array_filter([
                $isDeveloper
                    ? BulkAction::make('mark_paid')
                        ->label('Tandai: Lunas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'paid']))
                        ->successNotificationTitle('Tagihan ditandai lunas')
                    : null,

                $isDeveloper
                    ? BulkAction::make('mark_unpaid')
                        ->label('Tandai: Belum Lunas')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'unpaid']))
                        ->successNotificationTitle('Tagihan ditandai belum lunas')
                    : null,
            ]));
    }

    /**
     * Data isolation: developer sees all subscriptions; juragan sees only their own.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

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
            'index' => Pages\ListSubscriptions::route('/'),
        ];
    }
}
