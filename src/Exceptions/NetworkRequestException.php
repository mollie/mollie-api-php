<?php

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\ResponseStatusCode;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * Exception thrown when a request cannot be completed due to network-level errors.
 * This includes connection failures, DNS issues, timeouts, etc.
 */
class NetworkRequestException extends MollieException implements NetworkExceptionInterface
{
    protected PendingRequest $pendingRequest;

    protected string $plainMessage;

    public function __construct(
        PendingRequest $pendingRequest,
        ?Throwable $previous = null,
        ?string $message = null,
        int $code = ResponseStatusCode::HTTP_GATEWAY_TIMEOUT
    ) {
        $this->pendingRequest = $pendingRequest;
        $this->plainMessage = $message ?? 'The request failed due to network issues.';

        parent::__construct($this->plainMessage, $code, $previous);
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    public function getPlainMessage(): string
    {
        return $this->plainMessage;
    }

    public function getRequest(): RequestInterface
    {
        return $this->pendingRequest->createPsrRequest();
    }
}
