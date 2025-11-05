<?php

namespace App\Services\Twitter;

final readonly class NullTwitter implements TwitterInterface
{
    public function tweet(string $status): ?array
    {
        return null;
    }
}
