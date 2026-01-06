<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use Throwable;

class ValidationException extends ApiException
{
    private string $field;

    public function __construct(
        Response $response,
        string $field,
        string $message,
        int $code = ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null
    ) {
        $this->field = $field;

        parent::__construct($response, $message, $code, $previous);
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            $body->field ?? '',
            'We could not process your request due to validation errors. '.
                sprintf('Error executing API call (%d: %s): %s', 422, $body->title, $body->detail),
            ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
