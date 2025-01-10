<?php

namespace Mollie\Api\Http\Adapter;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

class CurlConnectionErrorException extends CurlException implements NetworkExceptionInterface
{
    private RequestInterface $request;

    public function __construct(
        string $message,
        int $curlErrorNumber,
        RequestInterface $request,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $curlErrorNumber, $previous);
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
