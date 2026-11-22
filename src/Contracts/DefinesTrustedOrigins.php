<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface DefinesTrustedOrigins
{
    /**
     * URLs whose origin (scheme, user info, host and port) may receive
     * authenticated requests from this connector.
     *
     * Any other origin is refused before credentials are attached, so links
     * taken from response or webhook data cannot redirect the API key or
     * access token to a third party.
     *
     * @return string[]
     */
    public function trustedOrigins(): array;
}
