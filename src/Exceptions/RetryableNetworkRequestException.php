<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\ResponseStatusCode;
use Throwable;

/**
 * Exception thrown when a request fails due to a temporary network issue that may resolve on retry.
 * This includes timeouts, temporary DNS issues, connection drops, etc.
 */
class RetryableNetworkRequestException extends NetworkRequestException
{
    public function __construct(
        PendingRequest $pendingRequest,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $pendingRequest,
            $previous,
            $message ?? 'The request failed due to a temporary network issue. Retrying may resolve this.',
            ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
