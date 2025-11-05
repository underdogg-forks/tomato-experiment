<?php

namespace TomatoPHP\FilamentAccounts;

use Filament\Panel;
use Filament\Panel\Plugin;

class FilamentAccountsSaaSPlugin extends Plugin
{
    protected bool $registerProfilePage = true;

    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-accounts-saas';
    }

    public function withoutProfilePage(): static
    {
        $this->registerProfilePage = false;

        return $this;
    }

    /**
     * @param Panel $panel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function register(Panel $panel): void
    {
        // The original package registers panel pages here. The inline
        // implementation keeps the structure configurable without
        // requiring the underlying vendor package.
    }

    /**
     * @param Panel $panel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(Panel $panel): void
    {
        // Nothing to bootstrap for the simplified inline plugin.
    }
}
