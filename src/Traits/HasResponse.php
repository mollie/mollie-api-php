<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

trait HasResponse
{
    protected ?Response $response = null;

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->response
            ? $this->response->getPendingRequest()
            : throw new \Exception('Response is not set');
    }
}
