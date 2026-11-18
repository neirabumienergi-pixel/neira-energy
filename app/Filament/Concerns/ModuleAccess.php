<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Batasi akses resource berdasarkan role user (lihat User::PERMISSIONS).
 * Set `protected static string $module = 'content';` di resource.
 */
trait ModuleAccess
{
    protected static function userCan(string $suffix = ''): bool
    {
        $user = auth()->user();

        return $user && $user->canModule(static::$module.$suffix);
    }

    public static function canViewAny(): bool
    {
        return static::userCan() || static::userCan('.view');
    }

    public static function canCreate(): bool
    {
        return static::userCan();
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCan();
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCan();
    }

    public static function canDeleteAny(): bool
    {
        return static::userCan();
    }
}
