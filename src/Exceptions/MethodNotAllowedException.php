<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class MethodNotAllowedException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'The HTTP method is not supported. ' .
                sprintf('Error executing API call (%d: %s): %s', 405, $body->title, $body->detail),
            405,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
