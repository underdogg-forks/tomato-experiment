<?php

namespace App\Services\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

final readonly class OAuthTwitter implements TwitterInterface
{
    public function __construct(
        private TwitterOAuth $twitter,
    ) {
        $this->twitter->setApiVersion('2');
    }

    public function tweet(string $status): ?array
    {
        return (array) $this->twitter->post('tweets', ['text' => $status], true);
    }
}
