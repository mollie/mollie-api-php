<?php

namespace Tests\Fixtures;

use Mollie\Api\MollieApiClient;
use Tests\Http\Adapter\MockMollieHttpAdapter;

class MockClient extends MollieApiClient
{
    public function __construct(array $expectedResponses = [])
    {
        parent::__construct();

        $this->httpClient = new MockMollieHttpAdapter($expectedResponses);

        $this->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }
}
