<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Utils\Arr;

class SetUserAgent
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $userAgent = Arr::join($pendingRequest->getConnector()->getVersionStrings(), ' ');

        $authenticator = $pendingRequest->getConnector()->getAuthenticator();

        if ($authenticator instanceof AccessTokenAuthenticator) {
            $userAgent .= ' Auth/Token';
        }

        $pendingRequest->headers()->add('User-Agent', $userAgent);

        return $pendingRequest;
    }
}
