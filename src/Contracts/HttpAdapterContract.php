<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Factories;

interface HttpAdapterContract
{
    public function factories(): Factories;

    /**
     * Send a request to the specified Mollie api url.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response;

    /**
     * The version number for the underlying http client, if available.
     *
     * @example Guzzle/6.3
     */
    public function version(): ?string;
}
