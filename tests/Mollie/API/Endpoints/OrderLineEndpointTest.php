<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Client;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Order;

class OrderLineEndpointTest extends BaseEndpointTest
{
    public function testCancelLinesRequiresLinesArray()
    {
        $this->expectException(ApiException::class);

        $this->guzzleClient = $this->createMock(Client::class);
        $this->apiClient = new MollieApiClient($this->guzzleClient);

        $this->apiClient->orderLines->cancelFor(new Order($this->apiClient), []);
    }
}
