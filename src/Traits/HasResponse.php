<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\ResourceOrigin;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

trait HasResponse
{
    protected Response $response;

    protected ?ResourceOrigin $origin = null;

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
        $this->origin = $response;

        return $this;
    }

    public function getOrigin(): ?ResourceOrigin
    {
        return $this->origin;
    }

    /**
     * @return $this
     */
    public function setOrigin(?ResourceOrigin $origin)
    {
        $this->origin = $origin;

        // Mirror onto $this->response whenever origin is an HTTP Response so
        // code that still reads $this->response directly keeps working on
        // HTTP-origin resources during the transitional period. Non-Response
        // origins (e.g. webhooks) only populate $this->origin.
        if ($origin instanceof Response) {
            $this->response = $origin;
        }

        return $this;
    }

    public function getPendingRequest(): ?PendingRequest
    {
        return $this->origin instanceof Response
            ? $this->origin->getPendingRequest()
            : null;
    }
}
