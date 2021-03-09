<?php

namespace Mollie\Api\HttpAdapter;

class MollieHttpAdapterPicker
{
    /**
     * @param \GuzzleHttp\ClientInterface|\Mollie\Api\HttpAdapter\MollieHttpAdapterInterface $httpClient
     *
     * @return \Mollie\Api\HttpAdapter\MollieHttpAdapterInterface
     */
    public function pickHttpAdapter($httpClient)
    {
        if (! $httpClient) {
            if ($this->guzzleIsDetected()) {
                $guzzleVersion = $this->guzzleMajorVersionNumber();
                
                if ($guzzleVersion && in_array($guzzleVersion, [6, 7])) {
                    return Guzzle6And7MollieHttpAdapter::createDefault();
                }
            }
        }

        if ($httpClient instanceof MollieHttpAdapterInterface) {
            return $httpClient;
        }

        if ($httpClient instanceof \GuzzleHttp\ClientInterface) {
            return new Guzzle6And7MollieHttpAdapter($httpClient);
        }

        return new CurlMollieHttpAdapter;
    }

    /**
     * @return bool
     */
    private function guzzleIsDetected()
    {
        return interface_exists("\GuzzleHttp\ClientInterface");
    }

    /**
     * @return int|null
     */
    private function guzzleMajorVersionNumber()
    {
        // Guzzle 7
        if (defined('\GuzzleHttp\ClientInterface::MAJOR_VERSION')) {
            return (int) \GuzzleHttp\ClientInterface::MAJOR_VERSION;
        }

        // Before Guzzle 7
        if (defined('\GuzzleHttp\ClientInterface::VERSION')) {
            return (int) \GuzzleHttp\ClientInterface::VERSION[0];
        }

        return null;
    }
}
