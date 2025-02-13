<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class ServiceUnavailableException extends ServerException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'The service is temporarily unavailable. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE, $body->title, $body->detail),
            ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
