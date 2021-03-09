<?php

namespace Mollie\Api\HttpAdapter;

class CurlMollieHttpAdapter implements MollieHttpAdapterInterface
{
    /**
     * @param $httpMethod
     * @param $url
     * @param $headers
     * @param $httpBody
     * @return \stdClass|void|null
     */
    public function send($httpMethod, $url, $headers, $httpBody)
    {
        // TODO: Implement send() method.
    }

    /**
     * The version number for the underlying http client, if available.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString()
    {
        return 'Curl/*';
    }
}
