<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('media')) {
    /** URL publik file upload; mendukung URL absolut & path aset statis (images/...). */
    function media(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback ? asset($fallback) : null;
        }
        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('rupiah')) {
    function rupiah(?float $value, bool $prefix = true): string
    {
        if ($value === null) {
            return '-';
        }

        return ($prefix ? 'Rp ' : '').number_format($value, 0, ',', '.');
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $message = null, ?string $number = null): string
    {
        $number = preg_replace('/\D/', '', $number ?? (string) setting('contact.whatsapp', ''));
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }
        $message ??= setting('contact.wa_message', 'Halo Neira Energy, saya ingin konsultasi.');

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}
