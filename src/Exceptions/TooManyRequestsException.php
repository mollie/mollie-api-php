<?php

namespace Mollie\Api\Exceptions;

use DateTimeImmutable;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use Throwable;

class TooManyRequestsException extends ApiException
{
    private ?int $retryAfterSeconds;

    public function __construct(
        Response $response,
        string $message,
        int $code,
        ?int $retryAfterSeconds = null,
        ?Throwable $previous = null
    ) {
        $this->retryAfterSeconds = $retryAfterSeconds;

        parent::__construct($response, $message, $code, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            $response,
            'Your request exceeded the rate limit. '.
                sprintf('Error executing API call (%d: %s): %s', ResponseStatusCode::HTTP_TOO_MANY_REQUESTS, $body->title, $body->detail),
            ResponseStatusCode::HTTP_TOO_MANY_REQUESTS,
            self::parseRetryAfter($response->header('Retry-After'))
        );
    }

    public function getRetryAfterSeconds(): ?int
    {
        return $this->retryAfterSeconds;
    }

    /**
     * Parse integer seconds or an HTTP-date from Retry-After.
     */
    private static function parseRetryAfter(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        try {
            $retryAt = new DateTimeImmutable($value);
        } catch (\Exception $exception) {
            return null;
        }

        return max(0, $retryAt->getTimestamp() - (new DateTimeImmutable)->getTimestamp());
    }
}
