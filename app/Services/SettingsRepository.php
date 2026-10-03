<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    private const CACHE_KEY = 'settings.all';

    /** @var array<string,mixed>|null */
    private ?array $items = null;

    public function all(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }

        return $this->items = Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::query()->get()->mapWithKeys(fn (Setting $s) => [
                $s->key => $s->castedValue(),
            ])->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        $stored = match ($type) {
            'bool' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type],
        );

        $this->flush();
    }

    public function flush(): void
    {
        $this->items = null;
        Cache::forget(self::CACHE_KEY);
    }
}
