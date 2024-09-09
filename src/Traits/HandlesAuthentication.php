<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Authenticator;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;

trait HandlesAuthentication
{
    protected ?Authenticator $authenticator = null;

    /**
     * @param  string  $apiKey  The Mollie API key, starting with 'test_' or 'live_'
     *
     * @throws ApiException
     */
    public function setApiKey(string $apiKey): self
    {
        $this->authenticator = new ApiKeyAuthenticator($apiKey);

        return $this;
    }

    /**
     * @param  string  $accessToken  OAuth access token, starting with 'access_'
     *
     * @throws ApiException
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->authenticator = new AccessTokenAuthenticator($accessToken);

        return $this;
    }

    public function getAuthenticator(): ?Authenticator
    {
        return $this->authenticator;
    }
}
