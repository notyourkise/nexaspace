<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\JuraganProfilePage;
use App\Models\User;
use Filament\Widgets\Widget;

class JuraganOnboardingWidget extends Widget
{
    protected string $view = 'filament.widgets.juragan-onboarding-widget';

    protected static ?int $sort = 0; // tampil paling atas

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user?->isJuragan()) {
            return false;
        }

        // Sembunyikan jika semua setup sudah selesai.
        return static::hasPendingSetup($user);
    }

    private static function hasPendingSetup(User $juragan): bool
    {
        // Masih ada anak kos tanpa monthly_rate.
        if ($juragan->anakKos()->where('monthly_rate', 0)->exists()) {
            return true;
        }

        // Belum mengisi rekening bank.
        return empty($juragan->bank_accounts);
    }

    protected function getViewData(): array
    {
        $juragan = auth()->user();

        $totalRooms    = $juragan->anakKos()->count();
        $noRateRooms   = $juragan->anakKos()->where('monthly_rate', 0)->get(['name', 'room_number']);
        $hasAllRates   = $noRateRooms->isEmpty();

        $hasMikrotik   = ! empty(config('services.mikrotik.host'));

        $bankAccounts  = $juragan->bank_accounts ?? [];
        $hasBank       = ! empty($bankAccounts);

        $steps = [
            [
                'done'  => $totalRooms > 0,
                'label' => 'Akun anak kos sudah dibuat',
                'note'  => $totalRooms > 0
                    ? "{$totalRooms} anak kos terdaftar"
                    : 'Belum ada anak kos — hubungi NexaSpace',
            ],
            [
                'done'  => $hasAllRates,
                'label' => 'Semua kamar sudah punya tarif bulanan',
                'note'  => $hasAllRates
                    ? 'Semua kamar sudah dikonfigurasi'
                    : $noRateRooms->count() . ' kamar belum punya tarif — edit di menu Anak Kos',
            ],
            [
                'done'  => $hasBank,
                'label' => 'Rekening bank sudah diisi',
                'note'  => $hasBank
                    ? count($bankAccounts) . ' rekening terdaftar'
                    : 'Isi rekening di menu Profil & Rekening agar anak kos bisa bayar',
            ],
            [
                'done'  => $hasMikrotik,
                'label' => 'Konfigurasi MikroTik tersedia',
                'note'  => $hasMikrotik
                    ? 'Pengaturan router sudah dikonfigurasi'
                    : 'Hubungi NexaSpace untuk konfigurasi MikroTik',
            ],
        ];

        $completedCount = collect($steps)->where('done', true)->count();

        $profileUrl = JuraganProfilePage::getUrl();

        return compact('steps', 'completedCount', 'noRateRooms', 'hasBank', 'profileUrl');
    }
}
