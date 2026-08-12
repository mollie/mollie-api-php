<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class RequestException extends MollieException implements RequestExceptionInterface
{
    public function __construct(
        public readonly Response $response,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message ?? '', $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->response->getPsrRequest();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->response->getPendingRequest();
    }

    public function getStatusCode(): int
    {
        return $this->response->status();
    }
}
