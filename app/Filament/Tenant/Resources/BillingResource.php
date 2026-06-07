<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\BillingResource\Pages;
use App\Models\Billing;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Tagihan Saya';

    protected static ?string $modelLabel = 'Tagihan';

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
                    ->label('Bulan')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->label('Jatuh Tempo')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Nominal')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid'      => 'Lunas',
                        'unpaid'    => 'Belum Bayar',
                        'throttled' => 'Dibatasi',
                        default     => $state,
                    })
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                        'danger'  => 'throttled',
                    ]),

                TextColumn::make('payment_receipt')
                    ->label('Bukti')
                    ->formatStateUsing(fn ($state) => $state ? 'Terupload' : '—')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->actions([
                Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-o-credit-card')
                    ->color('primary')
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Lakukan transfer ke rekening juragan, lalu isi form di bawah dan upload bukti transfer.')
                    ->form(function (Billing $record): array {
                        $juragan     = $record->user?->juragan;
                        $bankAccounts = $juragan?->bank_accounts ?? [];
                        $qrisImage    = $juragan?->qris_image;

                        $bankOptions = collect($bankAccounts)
                            ->mapWithKeys(fn ($bank, $i) => [
                                $i => "{$bank['bank_name']} — {$bank['account_number']} a.n. {$bank['account_name']}",
                            ])
                            ->all();

                        // Tambah opsi QRIS jika juragan sudah mengunggah QRIS.
                        if (filled($qrisImage)) {
                            $bankOptions['qris'] = 'QRIS — Scan untuk bayar';
                        }

                        $fields = [];

                        if (! empty($bankOptions)) {
                            $fields[] = Select::make('bank_index')
                                ->label('Metode Pembayaran')
                                ->options($bankOptions)
                                ->required()
                                ->live()
                                ->helperText('Pilih rekening tujuan transfer atau QRIS.')
                                ->native(false);
                        }

                        // Preview QRIS muncul saat opsi QRIS dipilih.
                        if (filled($qrisImage)) {
                            $qrisUrl = asset('storage/' . $qrisImage);

                            $fields[] = Placeholder::make('qris_preview')
                                ->label('Scan QRIS untuk Membayar')
                                ->visible(fn (Get $get): bool => $get('bank_index') === 'qris')
                                ->content(new HtmlString(
                                    '<a href="' . e($qrisUrl) . '" target="_blank" rel="noopener" title="Buka QRIS ukuran penuh">'
                                    . '<img src="' . e($qrisUrl) . '" alt="QRIS" '
                                    . 'style="display:block;width:16rem;max-width:100%;height:auto;background:#fff;padding:0.6rem;border-radius:0.6rem;border:1px solid rgba(0,0,0,0.1);" />'
                                    . '</a>'
                                ));
                        }

                        $fields[] = FileUpload::make('payment_receipt')
                            ->label('Bukti Transfer')
                            ->disk('public')
                            ->directory('receipts')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(3072)
                            ->required()
                            ->helperText('Format: JPG, PNG, WebP, atau PDF. Maks 3 MB.');

                        return $fields;
                    })
                    ->action(function (Billing $record, array $data): void {
                        $record->update([
                            'payment_receipt' => $data['payment_receipt'],
                            'status'          => 'paid',
                        ]);

                        Notification::make()
                            ->title('Pembayaran berhasil!')
                            ->body('Tagihan bulan ' . \Carbon\Carbon::parse($record->billing_month)->locale('id')->isoFormat('MMMM Y') . ' telah lunas.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Billing $record): bool => in_array($record->status, ['unpaid', 'throttled'])),

                Action::make('download_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Billing $record): string => route('invoice.billing', $record))
                    ->openUrlInNewTab(),
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
