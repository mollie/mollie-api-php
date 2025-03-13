<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\PendingRequest;
use Throwable;

/**
 * Exception thrown when CURL initialization or configuration fails.
 * This indicates a local configuration issue rather than a network problem.
 */
class CurlInitializationException extends MollieException
{
    protected PendingRequest $pendingRequest;

    protected string $plainMessage;

    public function __construct(
        PendingRequest $pendingRequest,
        string $message,
        ?Throwable $previous = null
    ) {
        $this->pendingRequest = $pendingRequest;
        $this->plainMessage = $message;

        parent::__construct($message, 0, $previous);
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    public function getPlainMessage(): string
    {
        return $this->plainMessage;
    }
}
