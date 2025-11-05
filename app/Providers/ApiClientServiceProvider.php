<?php

namespace App\Providers;

use App\Clients\BaseHttpClient;
use App\Clients\Decorators\HttpClientExceptionHandler;
use App\Clients\Decorators\RequestLogger;
use App\Clients\ExternalApiClient;
use Illuminate\Support\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the ExternalApiClient with decorators
        // BaseHttpClient -> RequestLogger -> HttpClientExceptionHandler
        $this->app->singleton(ExternalApiClient::class, function ($app) {
            // Create base HTTP client with GitHub configuration
            $baseClient = new BaseHttpClient(
                baseUrl: 'https://api.github.com/',
                username: config('services.github.username'),
                token: config('services.github.token'),
            );

            // Decorate with RequestLogger
            $loggedClient = new RequestLogger($baseClient);

            // Decorate with HttpClientExceptionHandler
            return new HttpClientExceptionHandler($loggedClient);
        });
    }
}
