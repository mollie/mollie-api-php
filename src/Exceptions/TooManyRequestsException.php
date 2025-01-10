<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class TooManyRequestsException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'Your request exceeded the rate limit. ' .
                sprintf('Error executing API call (%d: %s): %s', 429, $body->title, $body->detail),
            429,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
