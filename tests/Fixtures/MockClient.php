<?php

namespace Tests\Fixtures;

use Mollie\Api\MollieApiClient;
use Tests\Http\Adapter\MockMollieHttpAdapter;

/**
 * @property MockMollieHttpAdapter $httpClient
 */
class MockClient extends MollieApiClient
{
    public function __construct(array $expectedResponses = [])
    {
        parent::__construct();

        $this->httpClient = new MockMollieHttpAdapter($expectedResponses);

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
