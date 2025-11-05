<?php

namespace TomatoPHP\FilamentTenancy;

use Filament\Panel;
use Filament\Panel\Plugin;

class FilamentTenancyPlugin extends Plugin
{
    protected bool $impersonation = false;

    protected ?string $targetPanel = null;

    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-tenancy';
    }

    public function allowImpersonate(bool $condition = true): static
    {
        $this->impersonation = $condition;

        return $this;
    }

    public function panel(string $panel): static
    {
        $this->targetPanel = $panel;

        return $this;
    }

    public function register(Panel $panel): void
    {
        // No-op registration to keep compatibility with the original package API.
    }

    public function boot(Panel $panel): void
    {
        // The original package decorates the panel during boot. In this inline
        // variant we simply ensure the plugin can be attached safely.
    }
}
