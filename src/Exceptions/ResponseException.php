<?php

namespace Mollie\Api\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseException extends ClientException
{
    private ?ResponseInterface $response;
    private ?RequestInterface $request;
    private ?string $field;

    public function __construct(
        string $message,
        ?ResponseInterface $response = null,
        ?RequestInterface $request = null,
        ?string $field = null
    ) {
        $this->response = $response;
        $this->request = $request;
        $this->field = $field;
        parent::__construct($message);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}
