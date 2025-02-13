<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class ForbiddenException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'Your request was understood but not allowed. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_FORBIDDEN, $body->title, $body->detail),
            ResponseStatusCode::HTTP_FORBIDDEN
        );
    }
}
