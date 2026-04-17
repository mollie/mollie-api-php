<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Response;

interface IsResponseAware extends ViableResponse
{
    /**
     * Returns the HTTP `Response` this resource was hydrated from, or
     * `null` when the resource was hydrated from a non-HTTP origin
     * (for example a signed webhook envelope). Use
     * {@see \Mollie\Api\Contracts\ResourceOrigin} via
     * `$resource->getOrigin()` to inspect provenance across both cases.
     */
    public function getResponse(): ?Response;

    /**
     * @return $this
     */
    public function setResponse(Response $response);
}
