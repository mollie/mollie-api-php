<?php

namespace Tests\Mollie\API\HttpAdapter;

class MockMollieHttpAdapter implements \Mollie\Api\HttpAdapter\MollieHttpAdapterInterface
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
