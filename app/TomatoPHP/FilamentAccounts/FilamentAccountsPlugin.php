<?php

namespace TomatoPHP\FilamentAccounts;

use Filament\Panel;
use Filament\Panel\Plugin;

class FilamentAccountsPlugin extends Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-accounts';
    }

    public function register(Panel $panel): void
    {
        // Registration handled by the host application.
    }

    public function boot(Panel $panel): void
    {
        // Intentionally left blank to mirror package expectations.
    }
}
