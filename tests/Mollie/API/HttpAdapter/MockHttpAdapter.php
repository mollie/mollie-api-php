<?php

namespace Tests\Mollie\API\HttpAdapter;

class MockHttpAdapter implements \Mollie\Api\HttpAdapter\MollieHttpAdapterInterface
{
    /**
     * @inheritDoc
     */
    public function send($httpMethod, $url, $headers, $httpBody)
    {
        return (object) ['foo' => 'bar'];
    }

    /**
     * @inheritDoc
     */
    public function versionString()
    {
        return 'mock-client/1.0';
    }
}