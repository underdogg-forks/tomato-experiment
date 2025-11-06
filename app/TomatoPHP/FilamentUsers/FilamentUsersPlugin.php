<?php

namespace TomatoPHP\FilamentUsers;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentUsersPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-users';
    }

    public function register(Panel $panel): void
    {
        // Inline plugin keeps registration lightweight.
    }

    public function boot(Panel $panel): void
    {
        // Nothing to bootstrap for the simplified plugin.
    }
}
