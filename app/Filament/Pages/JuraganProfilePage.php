<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class JuraganProfilePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.juragan-profile';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profil & Rekening';

    protected static ?string $title = 'Profil & Rekening Bank';

    protected static ?int $navigationSort = 90;

    public ?array $infoData     = [];
    public ?array $passwordData = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isJuragan() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isJuragan() ?? false;
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->infoForm->fill([
            'name'          => $user->name,
            'phone_number'  => $user->phone_number,
            'contact_email' => $user->contact_email,
            'bank_accounts' => $user->bank_accounts ?? [],
            'qris_image'    => $user->qris_image,
        ]);
    }

    public function infoForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Pemilik / PIC')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_number')
                            ->label('Nomor HP (WhatsApp)')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+6281234567890'),

                        TextInput::make('contact_email')
                            ->label('Email Nyata (untuk notifikasi)')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('contoh@gmail.com')
                            ->helperText('Email ini dipakai untuk menerima notifikasi sistem, bukan untuk login.'),
                    ])
                    ->columns(2),

                Section::make('Rekening Bank')
                    ->description('Daftarkan rekening bank kamu di sini. Anak kos akan memilih rekening ini saat melakukan pembayaran tagihan.')
                    ->schema([
                        Repeater::make('bank_accounts')
                            ->label('')
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label('Nama Bank')
                                    ->placeholder('Contoh: BCA, BRI, Mandiri, GoPay')
                                    ->required()
                                    ->maxLength(50),

                                TextInput::make('account_number')
                                    ->label('Nomor Rekening / No. HP')
                                    ->placeholder('Contoh: 1234567890')
                                    ->required()
                                    ->maxLength(30),

                                TextInput::make('account_name')
                                    ->label('Atas Nama')
                                    ->placeholder('Nama sesuai rekening')
                                    ->required()
                                    ->maxLength(100),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Tambah Rekening')
                            ->defaultItems(0)
                            ->reorderable(false),
                    ]),

                Section::make('QRIS Pembayaran')
                    ->description('Upload kode QRIS statis. Akan ditampilkan di portal anak kos dan invoice PDF.')
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
                    ->collapsed(fn () => auth()->user()?->qris_image === null),
            ])
            ->statePath('infoData');
    }

    public function passwordForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ganti Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::min(8))
                            ->different('current_password'),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ])
                    ->columns(1),
            ])
            ->statePath('passwordData');
    }

    protected function getForms(): array
    {
        return ['infoForm', 'passwordForm'];
    }

    public function saveInfo(): void
    {
        $data = $this->infoForm->getState();

        auth()->user()->update([
            'name'          => $data['name'],
            'phone_number'  => $data['phone_number'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'bank_accounts' => $data['bank_accounts'] ?? [],
            'qris_image'    => $data['qris_image'] ?? null,
        ]);

        Notification::make()
            ->title('Profil & rekening berhasil disimpan')
            ->success()
            ->send();
    }

    public function savePassword(): void
    {
        $data = $this->passwordForm->getState();

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->passwordData = [];

        Notification::make()
            ->title('Password berhasil diubah')
            ->success()
            ->send();
    }

    /**
     * Hapus QRIS yang sedang tersimpan agar juragan bisa mengganti gambar.
     * Menghapus file dari storage, mengosongkan kolom di DB, dan membersihkan
     * preview pada FileUpload tanpa mengganggu field lain yang sedang diisi.
     */
    public function deleteQris(): void
    {
        $user = auth()->user();

        if ($user->qris_image) {
            Storage::disk('public')->delete($user->qris_image);
        }

        $user->update(['qris_image' => null]);

        $this->infoData['qris_image'] = null;

        Notification::make()
            ->title('QRIS berhasil dihapus')
            ->body('Silakan upload gambar QRIS baru jika ingin menggantinya.')
            ->success()
            ->send();
    }
}
