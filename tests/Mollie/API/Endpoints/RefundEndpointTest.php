<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class RefundEndpointTest extends BaseEndpointTest
{
    public function testListRefunds()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/refunds",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "refunds": [
                      {
                        "resource": "refund",
                        "id": "re_haCsig5aru",
                        "amount": {
                          "value": "2.00",
                          "currency": "EUR"
                        },
                        "status": "pending",
                        "createdAt": "2018-03-28T10:56:10+00:00",
                        "description": "My first API payment",
                        "paymentId": "tr_44aKxzEbr8",
                        "settlementAmount": {
                          "value": "-2.00",
                          "currency": "EUR"
                        },
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds/re_haCsig5aru",
                            "type": "application/hal+json"
                          },
                          "payment": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/refunds-api/list-refunds",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.nl/v2/refunds?limit=10",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 1
                }'
            )
        );

        $refunds = $this->apiClient->refunds->page();

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertEquals(1, $refunds->count());
        $this->assertCount(1, $refunds);

        $refund = $refunds[0];

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_haCsig5aru", $refund->id);
        $this->assertEquals("2.00", $refund->amount->value);
        $this->assertEquals("EUR", $refund->amount->currency);
        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-28T10:56:10+00:00", $refund->createdAt);
        $this->assertEquals("My first API payment", $refund->description);
        $this->assertEquals("tr_44aKxzEbr8", $refund->paymentId);
        $this->assertEquals("-2.00", $refund->settlementAmount->value);
        $this->assertEquals("EUR", $refund->settlementAmount->currency);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds/re_haCsig5aru", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);
    }

    public function testIterateRefunds()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/refunds",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "refunds": [
                      {
                        "resource": "refund",
                        "id": "re_haCsig5aru",
                        "amount": {
                          "value": "2.00",
                          "currency": "EUR"
                        },
                        "status": "pending",
                        "createdAt": "2018-03-28T10:56:10+00:00",
                        "description": "My first API payment",
                        "paymentId": "tr_44aKxzEbr8",
                        "settlementAmount": {
                          "value": "-2.00",
                          "currency": "EUR"
                        },
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds/re_haCsig5aru",
                            "type": "application/hal+json"
                          },
                          "payment": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/refunds-api/list-refunds",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.nl/v2/refunds?limit=10",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 1
                }'
            )
        );

        foreach ($this->apiClient->refunds->iterator() as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
            $this->assertEquals("refund", $refund->resource);
        }
    }
}
