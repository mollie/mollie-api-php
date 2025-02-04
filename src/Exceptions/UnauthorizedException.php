<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class UnauthorizedException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'Your request wasn\'t executed due to failed authentication. Check your API key. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_UNAUTHORIZED, $body->title, $body->detail),
            ResponseStatusCode::HTTP_UNAUTHORIZED
        );
    }
}
