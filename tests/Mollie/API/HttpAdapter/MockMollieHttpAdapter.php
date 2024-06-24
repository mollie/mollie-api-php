<?php

namespace Tests\Mollie\API\HttpAdapter;

class MockMollieHttpAdapter implements \Mollie\Api\HttpAdapter\MollieHttpAdapterInterface
{
    /**
     * @inheritDoc
     */
    public function send(string $meethod, string $url, $headers, ?string $body): \stdClass
    {
        return (object) ['foo' => 'bar'];
    }

    /**
     * @inheritDoc
     */
    public function versionString(): string
    {
        return 'mock-client/1.0';
    }
}
