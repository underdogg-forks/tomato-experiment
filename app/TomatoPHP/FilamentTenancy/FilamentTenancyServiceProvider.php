<?php

namespace TomatoPHP\FilamentTenancy;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class FilamentTenancyServiceProvider extends ServiceProvider
{
    public const TENANCY_IDENTIFICATION = 'filament.tenancy.identification';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/filament-tenancy.php', 'filament-tenancy');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/filament-tenancy.php' => config_path('filament-tenancy.php'),
        ], 'filament-tenancy-config');

        $this->publishes([
            __DIR__ . '/resources/lang' => lang_path('vendor/filament-tenancy'),
        ], 'filament-tenancy-translations');

        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'filament-tenancy');

        $this->registerMiddleware();
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make('router');

        $router->middlewareGroup(self::TENANCY_IDENTIFICATION, [
            InitializeTenancyByDomain::class,
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);
    }
}
