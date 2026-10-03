<?php

use App\Services\SettingsRepository;

if (! function_exists('settings')) {
    /**
     * Read a store setting (falls back to config/store.php defaults).
     */
    function settings(?string $key = null, mixed $default = null): mixed
    {
        /** @var SettingsRepository $repo */
        $repo = app(SettingsRepository::class);

        if ($key === null) {
            return $repo;
        }

        return $repo->get($key, $default ?? config('store.'.$key));
    }
}

if (! function_exists('money')) {
    /**
     * Format an amount using the store currency.
     */
    function money(float|int|string|null $amount): string
    {
        $currency = settings('currency', 'AED');

        return $currency.' '.number_format((float) $amount, 2);
    }
}

if (! function_exists('media')) {
    /**
     * URL for an uploaded file on the "public" disk.
     * Works with or without the storage:link symlink (routes through /media/…).
     * Absolute URLs and empty values are returned untouched.
     */
    function media(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return url('media/'.ltrim($path, '/'));
    }
}

if (! function_exists('whatsapp_link')) {
    /**
     * Build a click-to-chat WhatsApp URL for the configured store number.
     */
    function whatsapp_link(string $message = ''): string
    {
        $number = preg_replace('/\D+/', '',
            (string) settings('whatsapp_country_code', '971').settings('whatsapp_phone', '')
        );

        $url = 'https://wa.me/'.$number;

        if ($message !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}
