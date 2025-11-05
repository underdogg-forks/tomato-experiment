<?php

namespace TomatoPHP\FilamentAccounts;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class FilamentAccountsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FilamentAccountsManager::class, function (): FilamentAccountsManager {
            return new FilamentAccountsManager(config('filament-accounts'));
        });

        $this->app->alias(FilamentAccountsManager::class, 'filament-accounts');

        $this->mergeConfigFrom(__DIR__.'/config/filament-accounts.php', 'filament-accounts');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/filament-accounts.php' => config_path('filament-accounts.php'),
        ], 'filament-accounts-config');

        $this->publishes([
            __DIR__.'/resources/lang' => lang_path('vendor/filament-accounts'),
        ], 'filament-accounts-translations');

        if (is_dir(__DIR__.'/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        }

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'filament-accounts');
    }
}
    }
}
