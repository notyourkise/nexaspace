<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /** Show the dedicated registration page for a given plan. */
    public function show(string $plan = 'pro'): View
    {
        $plan = in_array($plan, ['lite', 'pro', 'custom']) ? $plan : 'pro';

        $plans = $this->plans();

        return view('daftar', [
            'selectedPlan' => $plan,
            'planData'     => $plans[$plan],
        ]);
    }

    /** Store a new registration and return a manual payment URL (or WhatsApp URL for CUSTOM). */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'kos_name'   => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'room_count' => ['required', 'integer', 'min:1', 'max:999'],
            'plan'       => ['required', 'in:lite,pro,custom'],
            'message'    => ['nullable', 'string', 'max:1000'],
        ]);

        $registration = Registration::create($data + ['status' => 'pending']);

        // CUSTOM plan has no upfront payment — redirect to WhatsApp for consultation.
        if ($registration->plan === 'custom') {
            $waText = $this->buildWhatsAppMessage($data);

            return response()->json([
                'payment_url' => null,
                'wa_url'      => 'https://wa.me/6285249678700?text=' . rawurlencode($waText),
            ]);
        }

        return response()->json([
            'payment_url' => URL::signedRoute('daftar.payment', $registration),
            'wa_url'      => null,
        ]);
    }

    /** Show manual transfer instructions for a LITE/PRO registration. */
    public function payment(Registration $registration): View
    {
        $plans = $this->plans();

        abort_if($registration->plan === 'custom', 404);
        abort_unless(isset($plans[$registration->plan]), 404);

        return view('daftar-pembayaran', [
            'registration'    => $registration,
            'planData'        => $plans[$registration->plan],
            'amount'          => $this->amountForPlan($registration->plan),
            'banks'           => $this->manualPaymentBanks(),
            'confirmationUrl' => 'https://wa.me/6285249678700?text='
                . rawurlencode($this->buildPaymentConfirmationMessage($registration)),
        ]);
    }

    private function buildWhatsAppMessage(array $data): string
    {
        $planLabel = match ($data['plan']) {
            'lite'   => 'LITE (Rp 199.000/bln, maks 20 kamar)',
            'pro'    => 'PRO (Rp 499.000/bln, maks 40 kamar)',
            'custom' => 'CUSTOM (50+ kamar)',
        };

        $lines = [
            'Halo NexaSpace! Saya ingin berlangganan.',
            '',
            "Paket      : *{$planLabel}*",
            '',
            "Nama       : {$data['name']}",
            "Nama Kos   : {$data['kos_name']}",
            "Email      : {$data['email']}",
            "Nomor WA   : {$data['phone']}",
            "Jml Kamar  : {$data['room_count']} kamar",
        ];

        if (! empty($data['message'])) {
            $lines[] = '';
            $lines[] = "Pesan: {$data['message']}";
        }

        $lines[] = '';
        $lines[] = 'Mohon info langkah konfirmasi & aktivasi akun. Terima kasih!';

        return implode("\n", $lines);
    }

    private function buildPaymentConfirmationMessage(Registration $registration): string
    {
        $amount = $this->amountForPlan($registration->plan);

        return implode("\n", [
            'Halo NexaSpace! Saya sudah melakukan pembayaran pendaftaran.',
            '',
            "ID Pendaftaran : REG-{$registration->id}",
            'Paket          : ' . strtoupper($registration->plan),
            'Nominal        : Rp ' . number_format($amount, 0, ',', '.'),
            '',
            "Nama           : {$registration->name}",
            "Nama Kos       : {$registration->kos_name}",
            "Email          : {$registration->email}",
            "Nomor WA       : {$registration->phone}",
            "Jumlah Kamar   : {$registration->room_count} kamar",
            '',
            'Saya akan mengirimkan bukti transfer melalui chat ini. Mohon dibantu verifikasi dan aktivasi akun.',
        ]);
    }

    private function amountForPlan(string $plan): int
    {
        return match ($plan) {
            'lite' => 199_000,
            'pro'  => 499_000,
            default => 0,
        };
    }

    /**
     * @return array<int, array{name: string, logo: string, logo_class: string, account_number: string, account_name: string}>
     */
    private function manualPaymentBanks(): array
    {
        return [
            [
                'name'           => 'Bank Central Asia (BCA)',
                'logo'           => 'BCA',
                'logo_class'     => 'text-[#0b56a4]',
                'account_number' => '1234567890',
                'account_name'   => 'PT NexaSpace Teknologi Indonesia',
            ],
            [
                'name'           => 'Bank Mandiri',
                'logo'           => 'mandiri',
                'logo_class'     => 'text-[#1d4f91]',
                'account_number' => '1440012345678',
                'account_name'   => 'PT NexaSpace Teknologi Indonesia',
            ],
            [
                'name'           => 'Bank Rakyat Indonesia (BRI)',
                'logo'           => 'BRI',
                'logo_class'     => 'text-[#064ea4]',
                'account_number' => '002201234567890',
                'account_name'   => 'PT NexaSpace Teknologi Indonesia',
            ],
            [
                'name'           => 'Bank Negara Indonesia (BNI)',
                'logo'           => 'BNI',
                'logo_class'     => 'text-[#006b7a]',
                'account_number' => '0098765432',
                'account_name'   => 'PT NexaSpace Teknologi Indonesia',
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, price: string, period: string, quota: string, features: array<int, string>, highlight: bool}>
     */
    private function plans(): array
    {
        return [
            'lite' => [
                'label'    => 'LITE',
                'price'    => 'Rp 199.000',
                'period'   => 'per bulan',
                'quota'    => 'Maksimal 20 Kamar',
                'features' => [
                    'Pencatatan Terpusat',
                    'Portal Penyewa Mandiri',
                    'Dukungan via WhatsApp',
                    'Onboarding & Migrasi Data',
                ],
                'highlight' => false,
            ],
            'pro' => [
                'label'    => 'PRO',
                'price'    => 'Rp 499.000',
                'period'   => 'per bulan',
                'quota'    => 'Maksimal 40 Kamar',
                'features' => [
                    'Semua fitur LITE',
                    'Tagihan Otomatis Penyewa',
                    'Smart WiFi Auto-Block',
                    'Gratis Peminjaman MikroTik & Switch',
                    'Gratis Instalasi (senilai Rp 1.500.000)',
                ],
                'highlight' => true,
            ],
            'custom' => [
                'label'    => 'CUSTOM',
                'price'    => 'Hubungi Kami',
                'period'   => 'harga disesuaikan',
                'quota'    => '50+ Kamar',
                'features' => [
                    'Semua fitur PRO',
                    'Topologi Jaringan Khusus',
                    'Dukungan Teknis Prioritas',
                    'SLA & Kontrak Khusus',
                ],
                'highlight' => false,
            ],
        ];
    }
}
