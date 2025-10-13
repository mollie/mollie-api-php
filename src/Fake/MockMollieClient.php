<?php

namespace Mollie\Api\Fake;

use Mollie\Api\MollieApiClient;

/**
 * @property MockMollieHttpAdapter $httpClient
 */
class MockMollieClient extends MollieApiClient
{
    public function __construct(array $expectedResponses = [], bool $retainRequests = false)
    {
        parent::__construct();

        $this->httpClient = new MockMollieHttpAdapter($expectedResponses, $retainRequests);

        $this->setAccessToken('access_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    /**
     * @param  string|callable  $callback
     */
    public function assertSent($callback): void
    {
        $this->httpClient->assertSent($callback);
    }

    public function assertSentCount(int $count): void
    {
        $this->httpClient->assertSentCount($count);
    }
}
