<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Settlement;

class SettlementPaymentsEndpointTest extends BaseEndpointTest
{
    public function testListSettlementPayments()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/settlements/stl_jDk30akdN/payments?limit=5&foo=bar'
            ),
            new Response(
                200,
                [],
                '{
                    "count": 1,
                    "_embedded": {
                        "payments": [
                            {
                                "resource": "payment",
                                "id": "tr_7UhSN1zuXS",
                                "mode": "test",
                                "createdAt": "2018-02-12T11:58:35.0Z",
                                "expiresAt": "2018-02-12T12:13:35.0Z",
                                "status": "open",
                                "isCancelable": false,
                                "amount": {
                                    "value": "75.00",
                                    "currency": "GBP"
                                },
                                "description": "Order #12345",
                                "method": "ideal",
                                "metadata": null,
                                "details": null,
                                "profileId": "pfl_QkEhN94Ba",
                                "settlementId": "stl_jDk30akdN",
                                "redirectUrl": "https://webshop.example.org/order/12345/",
                                "_links": {
                                     "self": {
                                         "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS",
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
                        "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN/payments?limit=5",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": {
                            "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN/payments?from=tr_SDkzMggpvx&limit=5",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlement-payments",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $settlement = new Settlement($this->apiClient);
        $settlement->id = 'stl_jDk30akdN';

        $payments = $settlement->payments(5, ['foo' => 'bar']);

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertCount(1, $payments);

        $payment = $payments[0];
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals("tr_7UhSN1zuXS", $payment->id);
    }
}
