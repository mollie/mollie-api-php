<?php

namespace Mollie\Api\Contracts;

interface MollieHttpAdapterContract
{
    /**
     * Send a request to the specified Mollie api url.
     *
     * @param string $method
     * @param string $url
     * @param string|array $headers
     * @param ?string $body
     * @return ResponseContract
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function send(string $method, string $url, $headers, ?string $body): ResponseContract;

    /**
     * The version number for the underlying http client, if available.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function version(): ?string;
}
