<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\NeiraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_falls_back_to_default_when_admin_env_is_empty(): void
    {
        Storage::fake('public');
        putenv('ADMIN_EMAIL=');
        putenv('ADMIN_PASSWORD=');
        $_ENV['ADMIN_EMAIL'] = $_SERVER['ADMIN_EMAIL'] = '';
        $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = '';

        $this->seed(NeiraSeeder::class);

        $admin = User::where('email', 'admin@neiraenergy.com')->firstOrFail();
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_make_admin_command_creates_and_resets_password(): void
    {
        $this->artisan('neira:admin', ['email' => 'bos@neira.test', 'password' => 'rahasia123'])->assertSuccessful();
        $this->assertTrue(Hash::check('rahasia123', User::where('email', 'bos@neira.test')->value('password')));

        $this->artisan('neira:admin', ['email' => 'bos@neira.test', 'password' => 'baru12345'])->assertSuccessful();
        $user = User::where('email', 'bos@neira.test')->first();
        $this->assertTrue(Hash::check('baru12345', $user->password));
        $this->assertSame('super_admin', $user->role);

        $this->artisan('neira:admin', ['email' => 'x@neira.test', 'password' => 'pendek'])->assertFailed();
    }

    public function test_admin_can_log_in_through_panel(): void
    {
        $this->artisan('neira:admin', ['email' => 'bos@neira.test', 'password' => 'rahasia123']);

        \Livewire\Livewire::test(\Filament\Pages\Auth\Login::class)
            ->fillForm(['email' => 'bos@neira.test', 'password' => 'rahasia123'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticated();
    }
}
