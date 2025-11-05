<?php

namespace TomatoPHP\FilamentUsers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class FilamentUsersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/filament-users.php', 'filament-users');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/filament-users.php' => config_path('filament-users.php'),
        ], 'filament-users-config');

        $this->publishes([
            __DIR__.'/resources/lang' => lang_path('vendor/filament-users'),
        ], 'filament-users-translations');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'filament-users');
        Lang::addNamespace('filament-users', __DIR__.'/resources/lang');
    }
}
