<?php

namespace App\Clients;

use Illuminate\Http\Client\Response;

class GitHub
{
    public function __construct(
        private ExternalApiClient $client
    ) {}

    public function request(string $method, string $uri, array $options = []): Response
    {
        return $this->client->request($method, $uri, $options);
    }
}
