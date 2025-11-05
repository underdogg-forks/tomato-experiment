<?php

namespace App\Clients\Decorators;

use App\Clients\ExternalApiClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class HttpClientExceptionHandler implements ExternalApiClient
{
    public function __construct(
        private ExternalApiClient $client
    ) {}

    public function request(string $method, string $uri, array $options = []): Response
    {
        try {
            return $this->client->request($method, $uri, $options);
        } catch (Throwable $e) {
            Log::error('External API request failed', [
                'method'  => $method,
                'uri'     => $uri,
                'options' => $options,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
