<?php

namespace Mollie\Api\HttpAdapter;

interface MollieHttpAdapterInterface
{
    /**
     * Send a request to the specified Mollie api url.
     *
     * @param string $meethod
     * @param string $url
     * @param string|array $headers
     * @param ?string $body
     * @return \stdClass|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function send(string $meethod, string $url, $headers, ?string $body): ?\stdClass;

    /**
     * The version number for the underlying http client, if available.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString(): ?string;
}
