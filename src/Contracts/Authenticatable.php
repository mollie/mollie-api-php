<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface Authenticatable
{
    public function setApiKey(string $apiKey): self;

    public function setAccessToken(string $accessToken): self;

    public function getAuthenticator(): ?Authenticator;
}
