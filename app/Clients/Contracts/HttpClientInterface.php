<?php

namespace App\Clients\Contracts;

use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    public function request(string $method, string $uri, array $options = []): Response;
}
