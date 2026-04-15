<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

use DateTimeImmutable;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use Throwable;

class TooManyRequestsException extends ApiException
{
    public function __construct(
        Response $response,
        string $message,
        int $code,
        public readonly ?int $retryAfterSeconds = null,
        ?Throwable $previous = null
    ) {
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

    /**
     * Get the value of the Retry-After header converted to seconds from now,
     * or null if the header is missing or unparseable.
     */
    public function getRetryAfterSeconds(): ?int
    {
        return $this->retryAfterSeconds;
    }

    /**
     * Parse a Retry-After header value.
     *
     * Supports:
     *   - Integer seconds (RFC 7231), e.g. "120"
     *   - HTTP-date, e.g. "Wed, 21 Oct 2015 07:28:00 GMT"
     *
     * Returns null when the header is absent or cannot be parsed.
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
        } catch (\Exception $e) {
            return null;
        }

        $seconds = $retryAt->getTimestamp() - (new DateTimeImmutable)->getTimestamp();

        return max(0, $seconds);
    }
}
