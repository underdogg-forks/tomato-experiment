<?php

namespace App\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BaseHttpClient implements ExternalApiClient
{
    public function __construct(
        private string $baseUrl,
        private ?string $username = null,
        private ?string $token = null,
    ) {}

    public function request(string $method, string $uri, array $options = []): Response
    {
        return $this->buildClient()->send($method, $uri, $options);
    }

    protected function buildClient(): PendingRequest
    {
        $client = Http::baseUrl($this->baseUrl);

        if ($this->username && $this->token) {
            $client->withBasicAuth($this->username, $this->token);
        }

        return $client;
    }
}
