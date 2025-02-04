<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class NotFoundException extends ApiException
{
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'The object referenced by your API request does not exist. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_NOT_FOUND, $body->title, $body->detail),
            ResponseStatusCode::HTTP_NOT_FOUND
        );
    }
}
