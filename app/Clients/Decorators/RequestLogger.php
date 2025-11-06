<?php

namespace App\Clients\Decorators;

use App\Clients\ExternalApiClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class RequestLogger implements ExternalApiClient
{
    public function __construct(
        private ExternalApiClient $client
    ) {}

    public function request(string $method, string $uri, array $options = []): Response
    {
        Log::info('External API request', [
            'method'  => $method,
            'uri'     => $uri,
            'options' => $options,
        ]);

        $response = $this->client->request($method, $uri, $options);

        Log::info('External API response', [
            'method' => $method,
            'uri'    => $uri,
            'status' => $response->status(),
        ]);

        return $response;
    }
}
