<?php

namespace Mollie\Api\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class RequestException extends MollieException implements ClientExceptionInterface
{
    protected RequestInterface $request;

    public function __construct(
        string $message,
        RequestInterface $request,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
