<?php
declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\Settlement;

class SettlementCaptureEndpointTest extends BaseEndpointTest
{
    /** @test */
    public function testListSettlementCaptures()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/settlements/stl_jDk30akdN/captures?limit=5&foo=bar'
            ),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "captures": [
                            {
                                "resource": "capture",
                                "id": "cpt_4qqhO89gsT",
                                "mode": "live",
                                "amount": {
                                    "value": "1027.99",
                                    "currency": "EUR"
                                },
                                "settlementAmount": {
                                    "value": "399.00",
                                    "currency": "EUR"
                                },
                                "paymentId": "tr_WDqYK6vllg",
                                "shipmentId": "shp_3wmsgCJN4U",
                                "settlementId": "stl_jDk30akdN",
                                "createdAt": "2018-08-02T09:29:56+00:00",
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/captures/cpt_4qqhO89gsT",
                                        "type": "application/hal+json"
                                    },
                                    "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                                        "type": "application/hal+json"
                                    },
                                    "shipment": {
                                        "href": "https://api.mollie.com/v2/orders/ord_8wmqcHMN4U/shipments/shp_3wmsgCJN4U",
                                        "type": "application/hal+json"
                                    },
                                    "settlement": {
                                        "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/captures-api/get-capture",
                                        "type": "text/html"
                                    }
                                }
                            }
                        ]
                    },
                    "count": 1,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlement-captures",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN/captures?limit=50",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null
                    }
                }'
            )
        );

        $settlement = new Settlement($this->apiClient);
        $settlement->id = 'stl_jDk30akdN';

        $captures = $settlement->captures(5, ['foo' => 'bar']);

        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertCount(1, $captures);

        $capture = $captures[0];
        $this->assertInstanceOf(Capture::class, $capture);
        $this->assertEquals("cpt_4qqhO89gsT", $capture->id);
    }
}
