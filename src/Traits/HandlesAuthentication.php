<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Authenticator;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\Http\Auth\TokenValidator;

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

    /**
     * @param  string  $token  API key (test_/live_) or OAuth access token (access_)
     *
     * @throws ApiException
     */
    public function setToken(string $token): self
    {
        if (TokenValidator::isAccessToken($token)) {
            return $this->setAccessToken($token);
        }

        return $this->setApiKey($token);
    }

    public function getAuthenticator(): ?Authenticator
    {
        return $this->authenticator;
    }
}
