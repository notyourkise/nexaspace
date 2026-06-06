<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTenantPasswords extends Command
{
    protected $signature   = 'tenants:reset-passwords {password=password}';
    protected $description = 'Reset semua password anak kos ke nilai default (hanya untuk development)';

    public function handle(): int
    {
        $password = $this->argument('password');
        $count    = User::where('role', 'tenant')->count();

        if (! $this->confirm("Reset password {$count} akun anak kos ke \"{$password}\"?", true)) {
            $this->info('Dibatalkan.');
            return self::FAILURE;
        }

        User::where('role', 'tenant')->each(function (User $user) use ($password) {
            $user->update(['password' => $password]);
        });

        $this->info("Selesai. {$count} akun anak kos di-reset ke password: {$password}");

        return self::SUCCESS;
    }
}
