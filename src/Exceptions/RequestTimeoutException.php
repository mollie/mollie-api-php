<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

/**
 * Exception thrown when a request times out (either during connection or while waiting for response).
 */
class RequestTimeoutException extends NetworkRequestException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response->getPendingRequest(),
            null,
            'The request took too long to complete. '.
                sprintf('Error executing API call (%d: %s): %s', 408, $body->title, $body->detail)
        );
    }
}
