<?php
declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\Settlement;

class SettlementRefundEndpointTest extends Mollie\Api\Endpoints\BaseEndpointTest
{
    /** @test */
    public function testListSettlementRefunds()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/settlements/stl_jDk30akdN/refunds?limit=5&foo=bar'
            ),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "refunds": [
                            {
                                "resource": "refund",
                                "id": "re_3aKhkUNigy",
                                "amount": {
                                    "value": "10.00",
                                    "currency": "EUR"
                                },
                                "status": "refunded",
                                "createdAt": "2018-08-30T07:59:02+00:00",
                                "description": "Order #33",
                                "paymentId": "tr_maJaG2j8OM",
                                "settlementAmount": {
                                    "value": "-10.00",
                                    "currency": "EUR"
                                },
                                "settlementId": "stl_jDk30akdN",
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_maJaG2j8OM/refunds/re_3aKhkUNigy",
                                        "type": "application/hal+json"
                                    },
                                    "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_maJaG2j8OM",
                                        "type": "application/hal+json"
                                    },
                                    "settlement": {
                                        "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN",
                                        "type": "application/hal+json"
                                    }
                                }
                            },
                            { }
                        ]
                    },
                    "count": 1,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlement-refunds",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN/refunds?limit=50",
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

        $refunds = $settlement->refunds(5, ['foo' => 'bar']);

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertCount(2, $refunds);

        $refund = $refunds[0];
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_3aKhkUNigy", $refund->id);
    }
}
