<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class UnauthorizedException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'Your request wasn\'t executed due to failed authentication. Check your API key. ' .
                sprintf('Error executing API call (%d: %s): %s', 401, $body->title, $body->detail),
            401,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
