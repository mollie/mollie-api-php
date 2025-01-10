<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class ServiceUnavailableException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'The service is temporarily unavailable. ' .
                sprintf('Error executing API call (%d: %s): %s', 503, $body->title, $body->detail),
            503,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
