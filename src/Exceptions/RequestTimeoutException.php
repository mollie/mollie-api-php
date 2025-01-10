<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;

class RequestTimeoutException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            'The request took too long to complete. ' .
                sprintf('Error executing API call (%d: %s): %s', 408, $body->title, $body->detail),
            408,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
