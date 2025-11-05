<?php

namespace TomatoPHP\FilamentTenancy;

use Filament\Panel;
use Filament\Panel\Plugin;

class FilamentTenancyAppPlugin extends Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-tenancy-app';
    }

    public function register(Panel $panel): void
    {
        // Keep default behaviour while ensuring the plugin can be resolved.
    }

    public function boot(Panel $panel): void
    {
        // Intentionally left blank. The inline package simply exposes the plugin entry point.
    }
}
