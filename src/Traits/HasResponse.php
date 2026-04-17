<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\ResourceOrigin;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

trait HasResponse
{
    protected ?ResourceOrigin $origin = null;

    /**
     * Returns the HTTP `Response` backing this resource, or `null`
     * when the resource was hydrated from a non-HTTP origin (e.g. a
     * signed webhook envelope). Callers that previously assumed a
     * non-null return should null-check or read provenance from
     * {@see self::getOrigin()} instead.
     */
    public function getResponse(): ?Response
    {
        return $this->origin instanceof Response ? $this->origin : null;
    }

    /**
     * @return $this
     */
    public function setResponse(Response $response)
    {
        return $this->setOrigin($response);
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

        return $this;
    }

    public function getPendingRequest(): ?PendingRequest
    {
        return $this->origin instanceof Response
            ? $this->origin->getPendingRequest()
            : null;
    }
}
