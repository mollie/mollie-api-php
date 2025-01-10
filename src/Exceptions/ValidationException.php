<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ValidationException extends ApiException
{
    private ?string $field;

    public function __construct(
        string $message,
        int $code,
        ?string $field,
        ?RequestInterface $request,
        ?ResponseInterface $response
    ) {
        parent::__construct($message, $code, $request, $response);
        $this->field = $field;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $field = ! empty($body->field) ? $body->field : null;

        return new self(
            'We could not process your request due to validation errors. ' .
                sprintf('Error executing API call (%d: %s): %s', 422, $body->title, $body->detail),
            422,
            $field,
            $response->getPsrRequest(),
            $response->getPsrResponse()
        );
    }
}
