<?php

namespace TomatoPHP\FilamentAccounts;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAccountsPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-accounts';
    }

    public function canLogin(): self
    {
        return $this;
    }

    public function canBlocked(): self
    {
        return $this;
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
