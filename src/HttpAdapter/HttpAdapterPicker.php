<?php

namespace Mollie\Api\HttpAdapter;

class HttpAdapterPicker
{
    /**
     * @param \GuzzleHttp\ClientInterface|MollieHttpAdapter $httpClient
     *
     * @return \Mollie\Api\HttpAdapter\MollieHttpAdapter
     */
    public function pickHttpAdapter($httpClient)
    {
        if (! $httpClient) {
            // Detect and try to instantiate a Guzzle adapter
            if (interface_exists("\GuzzleHttp\ClientInterface")) {
                return Guzzle6And7MollieHttpAdapter::createDefault();
            }
        }

        if ($httpClient instanceof MollieHttpAdapter) {
            return $httpClient;
        }

        if ($httpClient instanceof \GuzzleHttp\ClientInterface) {
            return new Guzzle6And7MollieHttpAdapter($httpClient);
        }

        return new CurlMollieHttpAdapter;
    }
}
