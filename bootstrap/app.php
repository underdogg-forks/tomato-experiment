<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withProviders([
        \TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider::class,
        \TomatoPHP\FilamentAccounts\FilamentAccountsServiceProvider::class,
        \TomatoPHP\FilamentUsers\FilamentUsersServiceProvider::class,
    ])
    ->withCommands([
        \App\Console\Commands\EnsureCrawlableRepos::class,
        \App\Console\Commands\PreloadRepoData::class,
        \App\Console\Commands\TweetAboutNewIssues::class,
        \App\Console\Commands\UpdateOpenSource::class,
        \App\Console\Commands\MoveOldTenantsToAccounts::class,
        \App\Console\Commands\GoogleIndex::class,
        \App\Console\Commands\IndexNowIndex::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('universal', [
            InitializeTenancyByDomain::class,
            InitializeTenancyBySubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        \TomatoPHP\FilamentDiscord\Helpers\DiscordErrorReporter::make($exceptions);
    })->create();
