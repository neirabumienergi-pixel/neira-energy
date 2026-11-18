<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'super_admin' => 'Super Admin',
        'manager' => 'Manajer',
        'sales' => 'BD / Sales',
        'marketing' => 'Marketing / Konten',
    ];

    /**
     * Modul yang boleh diakses tiap role. Super admin selalu boleh semua.
     * Kunci = nama grup/modul yang dipakai di Resource::canAccess().
     */
    public const PERMISSIONS = [
        'manager' => ['content', 'products', 'promos', 'settings', 'crm', 'sales'],
        'sales' => ['products.view', 'promos.view', 'crm', 'sales'],
        'marketing' => ['content', 'products', 'promos'],
    ];

    protected $attributes = ['role' => 'marketing', 'is_active' => true];

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Cek izin modul. 'products' juga memberi 'products.view'. */
    public function canModule(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        $perms = self::PERMISSIONS[$this->role] ?? [];
        $base = explode('.', $module)[0];

        return in_array($module, $perms, true) || in_array($base, $perms, true);
    }
}
