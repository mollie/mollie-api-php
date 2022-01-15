<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Route;

class PaymentRouteEndpointTest extends BaseEndpointTest
{
    public function testUpdateReleaseDateForPaymentId()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/payments/tr_2qkhcMzypH/routes/rt_9dk4al1n",
                [],
                '{
                    "releaseDate": "2021-09-14",
                    "testmode": false
                }'
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "route",
                    "id": "rt_9dk4al1n",
                    "createdAt": "2021-08-28T14:02:29+00:00",
                    "amount": {
                        "value": "7.50",
                        "currency": "EUR"
                    },
                    "destination": {
                        "type": "organization",
                        "organizationId": "org_23456"
                    },
                    "releaseDate": "2021-09-14"
                }'
            )
        );

        $route = $this->apiClient->paymentRoutes->updateReleaseDateForPaymentId('tr_2qkhcMzypH', 'rt_9dk4al1n', '2021-09-14');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('rt_9dk4al1n', $route->id);
        $this->assertEquals("2021-09-14", $route->releaseDate);
    }
}
