<?php

declare(strict_types=1);

namespace Tests\Mollie\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\PaymentCollection;
use Tests\Mollie\Api\Endpoints\BaseEndpointTest;

class SubscriptionPaymentEndpointTest extends BaseEndpointTest
{
    /** @test */
    public function testListSubscriptionPayments(): void
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/customers/cst_stTC2WHAuS/subscriptions/sub_8JfGzs6v3K/payments?limit=25'
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
                            "mode": "live",
                            "amount": {
                              "currency": "EUR",
                              "value": "25.00"
                            },
                            "description": "Quarterly payment",
                            "method": "creditcard",
                            "sequenceType": "recurring",
                            "status": "paid",
                            "isCancelable": false,
                            "webhookUrl": "https://webshop.example.org/payments/webhook",
                            "profileId": "pfl_QkEhN94Ba",
                            "customerId": "cst_stTC2WHAuS",
                            "mandateId": "mdt_38HS4fsS",
                            "createdAt": "2023-09-01T03:58:35.0Z",
                            "paidAt": "2023-09-01T04:02:01.0Z",
                            "_links": {
                              "self": {
                              "href": "...",
                                "type": "application/hal+json"
                              },
                              "dashboard": {
                                  "href": "https://www.mollie.com/dashboard/org_12345678/payments/tr_7UhSN1zuXS",
                                "type": "text/html"
                              }
                            }
                          }
                        ]
                      },
                      "_links": {
                        "self": {
                          "href": "https://api.mollie.com/v2/customers/cst_stTC2WHAuS/subscriptions/sub_8JfGzs6v3K/payments?limit=25",
                          "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null,
                        "documentation": {
                          "href": "https://docs.mollie.com/reference/list-subscription-payments",
                          "type": "text/html"
                        }
                      }
                    }'
            )
        );

        $response = $this->apiClient->subscriptionPayments->pageForIds(
            'cst_stTC2WHAuS',
            'sub_8JfGzs6v3K',
            null,
            25
        );

        $this->assertInstanceOf(PaymentCollection::class, $response);
    }
}
