<?php

namespace App\Support;

use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Schema;

class Modules
{
    public static function enabled(string $key): bool
    {
        if (Schema::hasTable('module_settings')) {
            $row = ModuleSetting::query()->where('key', $key)->first();
            if ($row) {
                return (bool) $row->enabled;
            }
        }

        return (bool) data_get(config('modules'), "{$key}.enabled", false);
    }

    public static function all(): array
    {
        $config = config('modules', []);

        if (! Schema::hasTable('module_settings')) {
            return collect($config)->map(fn ($meta, $key) => [
                'key' => $key,
                'label' => $meta['label'] ?? $key,
                'description' => $meta['description'] ?? '',
                'enabled' => (bool) ($meta['enabled'] ?? false),
                'core' => (bool) ($meta['core'] ?? false),
            ])->values()->all();
        }

        return ModuleSetting::query()->orderByDesc('is_core')->orderBy('label')->get()->map(fn (ModuleSetting $m) => [
            'key' => $m->key,
            'label' => $m->label,
            'description' => $m->description,
            'enabled' => (bool) $m->enabled,
            'core' => (bool) $m->is_core,
        ])->all();
    }
}
