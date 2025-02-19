<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class ServiceUnavailableException extends ServerException
{
    public static function fromResponse(Response $response): self
    {
        $message = 'The service is temporarily unavailable.';
        $body = $response->body();

        if (! $response->isEmpty()) {
            $message .= sprintf(' Server response: %s', $body);
        }

        return new self(
            $response,
            $message,
            ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
