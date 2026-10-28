<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, string $currency = 'USD'): string
    {
        $value = is_numeric($amount) ? (float) $amount : 0.0;

        if (class_exists('NumberFormatter') && extension_loaded('intl')) {
            $formatter = new \NumberFormatter(app()->getLocale(), \NumberFormatter::CURRENCY);

            return $formatter->formatCurrency($value, strtoupper($currency));
        }

        $symbol = match (strtoupper($currency)) {
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