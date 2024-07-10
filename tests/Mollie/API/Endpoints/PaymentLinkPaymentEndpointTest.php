<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class PaymentLinkPaymentEndpointTest extends BaseEndpointTest
{
    /** @test */
    public function testGetPaymentsPageForPaymentLink()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh/payments"
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
                        "redirectUrl": "https://webshop.example.org/order/12345/",
                        "createdAt": "2024-02-12T11:58:35.0Z",
                        "expiresAt": "2024-02-12T12:13:35.0Z",
                        "_links": {
                          "self": {
                            "href": "...",
                            "type": "application/hal+json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/checkout/issuer/select/ideal/7UhSN1zuXS",
                            "type": "text/html"
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
                      "href": "...",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": {
                      "href": "https://api.mollie.com/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh/payments?from=tr_SDkzMggpvx&limit=5",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "...",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $response = $this->apiClient->paymentLinkPayments->pageForId("pl_4Y0eZitmBnQ6IDoMqZQKh");

        $this->assertInstanceOf(PaymentCollection::class, $response);
        $this->assertInstanceOf(Payment::class, $response[0]);
        $this->assertEquals($response[0]->id, "tr_7UhSN1zuXS");
        // Not necessary to test all fields...
    }
}
