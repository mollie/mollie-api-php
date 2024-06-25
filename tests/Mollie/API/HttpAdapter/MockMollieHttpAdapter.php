<?php

namespace Tests\Mollie\API\HttpAdapter;

use Mollie\Api\Contracts\ResponseContract;

class MockMollieHttpAdapter implements \Mollie\Api\Contracts\MollieHttpAdapterContract
{
    /**
     * @inheritDoc
     */
    public function send(string $method, string $url, $headers, ?string $body): ResponseContract
    {
        return new class implements ResponseContract {
            public function json(): \stdClass
            {
                return (object) ['foo' => 'bar'];
            }

            public function status(): int
            {
                return 200;
            }

            public function body(): string
            {
                return 'foo';
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function version(): string
    {
        return 'mock-client/1.0';
    }
}
