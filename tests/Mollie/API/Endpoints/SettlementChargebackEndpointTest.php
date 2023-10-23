<?php
declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Settlement;

class SettlementChargebackEndpointTest extends BaseEndpointTest
{
    public function testListSettlementChargebacks()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/settlements/stl_jDk30akdN/chargebacks?limit=5&foo=bar'
            ),
            new Response(
                200,
                [],
                '{
                    "count": 1,
                    "_embedded": {
                        "chargebacks": [
                            {
                                "resource": "chargeback",
                                "id": "chb_n9z0tp",
                                "amount": {
                                    "value": "43.38",
                                    "currency": "USD"
                                },
                                "settlementAmount": {
                                    "value": "-37.14",
                                    "currency": "EUR"
                                },
                                "createdAt": "2018-03-14T17:00:52.0Z",
                                "reversedAt": null,
                                "paymentId": "tr_WDqYK6vllg",
                                "settlementId": "stl_jDk30akdN",
                                "_links": {
                                     "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/chargebacks/chb_n9z0tp",
                                        "type": "application/hal+json"
                                     },
                                     "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                                        "type": "application/hal+json"
                                     },
                                     "settlement": {
                                         "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN",
                                         "type": "application/hal+json"
                                     }
                                }
                            }
                        ]
                    },
                    "_links": {
                         "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlement-chargebacks",
                            "type": "text/html"
                         },
                         "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN/chargebacks",
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

        $chargebacks = $settlement->chargebacks(5, ['foo' => 'bar']);

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertCount(1, $chargebacks);

        $chargeback = $chargebacks[0];
        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals("chb_n9z0tp", $chargeback->id);
    }
}
