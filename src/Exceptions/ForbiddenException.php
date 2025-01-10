<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class ForbiddenException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'Your request was understood but not allowed. ' .
                sprintf('Error executing API call (%d: %s): %s', 403, $body->title, $body->detail),
            403,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
