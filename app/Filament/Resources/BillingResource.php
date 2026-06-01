<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingResource\Pages;
use App\Models\Billing;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Tenant')
                ->options(User::where('role', 'tenant')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('amount')
                ->numeric()
                ->required()
                ->prefix('Rp')
                ->minValue(0),

            DatePicker::make('billing_month')
                ->required()
                ->displayFormat('F Y')
                ->label('Billing Month'),

            DatePicker::make('due_date')
                ->required()
                ->label('Due Date'),

            Select::make('status')
                ->options([
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                    'throttled' => 'Throttled',
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
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                        'danger' => 'throttled',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'throttled' => 'Throttled',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'paid'])),

                BulkAction::make('mark_unpaid')
                    ->label('Mark as Unpaid')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'unpaid'])),
            ])
            ->defaultSort('billing_month', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('user'));
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
