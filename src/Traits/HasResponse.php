<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

trait HasResponse
{
    protected Response $response;

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    public function getPendingRequest(): PendingRequest
    {
        return $this->response->getPendingRequest();
    }
}
