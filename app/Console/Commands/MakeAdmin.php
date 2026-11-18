<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'neira:admin {email} {password?} {--name=Super Admin}';

    protected $description = 'Buat atau reset akun Super Admin panel';

    public function handle(): int
    {
        $password = $this->argument('password') ?: $this->secret('Password baru');
        if (blank($password) || strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => $this->argument('email')], [
            'name' => User::where('email', $this->argument('email'))->value('name') ?? $this->option('name'),
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->info(($user->wasRecentlyCreated ? 'Akun dibuat: ' : 'Password direset: ').$user->email);

        return self::SUCCESS;
    }
}
