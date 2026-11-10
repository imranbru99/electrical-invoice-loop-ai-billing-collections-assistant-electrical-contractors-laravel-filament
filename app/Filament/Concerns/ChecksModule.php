<?php

namespace App\Filament\Concerns;

use App\Support\Modules;

trait ChecksModule
{
    public static function shouldRegisterNavigation(): bool
    {
        $module = static::$moduleKey ?? null;

        return $module ? Modules::enabled($module) : true;
    }

    public static function canAccess(): bool
    {
        $module = static::$moduleKey ?? null;

        return $module ? Modules::enabled($module) : true;
    }
}
