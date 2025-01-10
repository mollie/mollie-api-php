<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class NotFoundException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'The object referenced by your API request does not exist. ' .
                sprintf('Error executing API call (%d: %s): %s', 404, $body->title, $body->detail),
            404,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
