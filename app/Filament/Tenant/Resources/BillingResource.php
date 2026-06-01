<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\BillingResource\Pages;
use App\Models\Billing;
use App\Services\MidtransService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'My Bills';

    protected static ?string $modelLabel = 'Bill';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('billing_month')
                    ->date('F Y')
                    ->label('Month')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->label('Due Date')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                        'danger'  => 'throttled',
                    ]),

                TextColumn::make('payment_receipt')
                    ->label('Receipt')
                    ->formatStateUsing(fn ($state) => $state ? 'Uploaded' : 'None')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->actions([
                Action::make('pay_now')
                    ->label('Pay Now')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->modalHeading('Complete Payment')
                    ->modalDescription(fn (Billing $record) => 'Amount due: Rp ' . number_format($record->amount, 0, ',', '.'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function (Billing $record): \Illuminate\View\View {
                        $record->load('user');
                        $snapToken = app(MidtransService::class)->getSnapToken($record);
                        $isProduction = config('services.midtrans.is_production');

                        return view('tenant.billing.pay-now', [
                            'snap_token' => $snapToken,
                            'client_key' => config('services.midtrans.client_key'),
                            'snap_url'   => $isProduction
                                ? 'https://app.midtrans.com/snap/snap.js'
                                : 'https://app.sandbox.midtrans.com/snap/snap.js',
                        ]);
                    })
                    ->visible(fn (Billing $record): bool => in_array($record->status, ['unpaid', 'throttled'])),

                Action::make('upload_receipt')
                    ->label('Upload Receipt')
                    ->icon('heroicon-o-paper-clip')
                    ->color('warning')
                    ->modalHeading('Upload Payment Receipt')
                    ->modalDescription('Upload a photo or scan of your bank transfer receipt.')
                    ->form([
                        FileUpload::make('payment_receipt')
                            ->label('Receipt Image')
                            ->disk('public')
                            ->directory('receipts')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(3072)
                            ->required(),
                    ])
                    ->fillForm(fn (Billing $record): array => [
                        'payment_receipt' => $record->payment_receipt,
                    ])
                    ->action(function (Billing $record, array $data): void {
                        $record->update(['payment_receipt' => $data['payment_receipt']]);

                        Notification::make()
                            ->title('Receipt uploaded successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Billing $record): bool => in_array($record->status, ['unpaid', 'throttled'])),
            ])
            ->defaultSort('billing_month', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->where('user_id', auth()->id()));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillings::route('/'),
        ];
    }
}
