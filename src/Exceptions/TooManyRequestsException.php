<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class TooManyRequestsException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'Your request exceeded the rate limit. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_TOO_MANY_REQUESTS, $body->title, $body->detail),
            ResponseStatusCode::HTTP_TOO_MANY_REQUESTS
        );
    }
}
