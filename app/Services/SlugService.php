<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Local equivalent of imrandevbd/laravel-universal-slug.
 */
class SlugService
{
    public function make(string $value, ?string $fallback = null): string
    {
        $slug = Str::slug($value);
        if ($slug === '' && $fallback) {
            $slug = Str::slug($fallback);
        }

        return $slug !== '' ? $slug : Str::lower(Str::random(8));
    }
}
