<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Contracts\MollieHttpAdapterPickerContract;
use Mollie\Api\Exceptions\UnrecognizedClientException;

class MollieHttpAdapterPicker implements MollieHttpAdapterPickerContract
{
    /**
     * @param  \GuzzleHttp\ClientInterface|HttpAdapterContract|null|\stdClass  $httpClient
     *
     * @throws \Mollie\Api\Exceptions\UnrecognizedClientException
     */
    public function pickHttpAdapter($httpClient): HttpAdapterContract
    {
        if (! $httpClient) {
            return $this->createDefaultAdapter();
        }

        if ($httpClient instanceof HttpAdapterContract) {
            return $httpClient;
        }

        if ($httpClient instanceof \GuzzleHttp\ClientInterface) {
            return new GuzzleMollieHttpAdapter($httpClient);
        }

        throw new UnrecognizedClientException('The provided http client or adapter was not recognized.');
    }

    private function createDefaultAdapter(): HttpAdapterContract
    {
        if ($this->guzzleIsDetected()) {
            return GuzzleMollieHttpAdapter::createClient();
        }

        return new CurlMollieHttpAdapter;
    }

    private function guzzleIsDetected(): bool
    {
        return interface_exists('\\'.\GuzzleHttp\ClientInterface::class);
    }
}
