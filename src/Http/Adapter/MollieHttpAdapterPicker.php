<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\MollieHttpAdapterPickerContract;
use Mollie\Api\Exceptions\UnrecognizedClientException;

class MollieHttpAdapterPicker implements MollieHttpAdapterPickerContract
{
    /**
     * @param \GuzzleHttp\ClientInterface|MollieHttpAdapterContract|null|\stdClass $httpClient
     *
     * @return MollieHttpAdapterContract
     * @throws \Mollie\Api\Exceptions\UnrecognizedClientException
     */
    public function pickHttpAdapter($httpClient): MollieHttpAdapterContract
    {
        if (! $httpClient) {
            if ($this->guzzleIsDetected()) {
                $guzzleVersion = $this->guzzleMajorVersionNumber();

                if ($guzzleVersion && in_array($guzzleVersion, [6, 7])) {
                    return GuzzleMollieHttpAdapter::createDefault();
                }
            }

            return new CurlMollieHttpAdapter;
        }

        if ($httpClient instanceof MollieHttpAdapterContract) {
            return $httpClient;
        }

        if ($httpClient instanceof \GuzzleHttp\ClientInterface) {
            return new GuzzleMollieHttpAdapter($httpClient);
        }

        throw new UnrecognizedClientException('The provided http client or adapter was not recognized.');
    }

    /**
     * @return bool
     */
    private function guzzleIsDetected(): bool
    {
        return interface_exists('\\' . \GuzzleHttp\ClientInterface::class);
    }

    /**
     * @return int|null
     */
    private function guzzleMajorVersionNumber(): ?int
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
