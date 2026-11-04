<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, ?string $currency = null): string
    {
        $value = is_numeric($amount) ? (float) $amount : 0.0;
        $configuredCurrency = strtoupper((string) config('artiva.currency', 'PHP'));
        $currency = strtoupper($currency ?? $configuredCurrency);

        $symbol = $currency === $configuredCurrency
            ? config('artiva.currency_symbol', '₱')
            : match ($currency) {
                'PHP' => '₱',
                'EUR' => '€',
                'GBP' => '£',
                'JPY' => '¥',
                default => '$',
            };

        $formatted = number_format(abs($value), 2, '.', ',');

        return ($value < 0 ? '-' : '').$symbol.$formatted;
    }
}

if (! function_exists('artiva_preview')) {
    function artiva_preview(?\App\Models\Artwork $artwork): ?string
    {
        if ($artwork === null || blank($artwork->preview_path)) {
            return null;
        }

        $path = ltrim($artwork->preview_path, '/');

        if (str_starts_with($path, 'images/')) {
            return file_exists(public_path($path))
                ? asset($path)
                : null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}