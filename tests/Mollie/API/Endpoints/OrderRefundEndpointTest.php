<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Types\OrderStatus;
use Mollie\Api\Types\RefundStatus;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class OrderRefundEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testCreatePartialOrderRefund()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders/ord_stTC2WHAuS/refunds",
                [],
                '{
                     "lines": [
                        {
                            "id": "odl_dgtxyl",
                            "quantity": 1
                        }
                     ]
                 }'
            ),
            new Response(
                201,
                [],
                $this->getOrderRefundResponseFixture('re_4qqhO89gsT', 'ord_stTC2WHAuS')
            )
        );

        $order = $this->getOrder('ord_stTC2WHAuS');

        $refund = $order->refund([
            'lines' => [
                [
                    'id' => 'odl_dgtxyl',
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->assertOrderRefund($refund, 're_4qqhO89gsT');
    }

    public function testCreateCompleteOrderRefund()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders/ord_stTC2WHAuS/refunds",
                [],
                '{
                     "lines": []
                 }'
            ),
            new Response(
                201,
                [],
                $this->getOrderRefundResponseFixture('re_4qqhO89gsT', 'ord_stTC2WHAuS')
            )
        );

        $order = $this->getOrder('ord_stTC2WHAuS');

        $refund = $order->refundAll();

        $this->assertOrderRefund($refund, 're_4qqhO89gsT');
    }

    public function testListOrderRefunds()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_stTC2WHAuS/refunds"
            ),
            new Response(
                200,
                [],
                '{
                    "count": 1,
                    "_embedded": {
                        "refunds": [
                            {
                                "resource": "refund",
                                "id": "re_4qqhO89gsT",
                                "amount": {
                                    "currency": "EUR",
                                    "value": "698.00"
                                },
                                "status": "pending",
                                "createdAt": "2018-03-19T12:33:37+00:00",
                                "description": "Item not in stock, refunding",
                                "paymentId": "tr_WDqYK6vllg",
                                "orderId": "ord_pbjz8x",
                                "lines": [
                                    {
                                        "resource": "orderline",
                                        "id": "odl_dgtxyl",
                                        "orderId": "ord_pbjz8x",
                                        "name": "LEGO 42083 Bugatti Chiron",
                                        "productUrl": "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                                        "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$",
                                        "sku": "5702016116977",
                                        "type": "physical",
                                        "status": "refunded",
                                        "quantity": 2,
                                        "unitPrice": {
                                            "value": "399.00",
                                            "currency": "EUR"
                                        },
                                        "vatRate": "21.00",
                                        "vatAmount": {
                                            "value": "121.14",
                                            "currency": "EUR"
                                        },
                                        "discountAmount": {
                                            "value": "100.00",
                                            "currency": "EUR"
                                        },
                                        "totalAmount": {
                                            "value": "698.00",
                                            "currency": "EUR"
                                        },
                                        "createdAt": "2018-08-02T09:29:56+00:00"
                                    }
                                ],
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/refunds/re_4qqhO89gsT",
                                        "type": "application/hal+json"
                                    },
                                    "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                                        "type": "application/hal+json"
                                    },
                                    "order": {
                                        "href": "https://api.mollie.com/v2/orders/ord_pbjz8x",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/refunds-api/get-refund",
                                        "type": "text/html"
                                    }
                                }
                            }
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS/refunds?limit=5",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": {
                            "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS/refunds?from=re_APBiGPH2vV&limit=5",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/orders-api/list-order-refunds",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $order = $this->getOrder('ord_stTC2WHAuS');

        $refunds = $order->refunds();

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertEquals(1, $refunds->count());
        $this->assertCount(1, $refunds);

        $this->assertOrderRefund($refunds[0], 're_4qqhO89gsT');
    }

    protected function assertOrderRefund($refund, $refund_id, $refund_status = RefundStatus::PENDING)
    {
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals($refund_id, $refund->id);
        $this->assertAmountObject('698.00', 'EUR', $refund->amount);

        $this->assertEquals($refund_status, $refund->status);
        $this->assertEquals("2018-03-19T12:33:37+00:00", $refund->createdAt);
        $this->assertEquals("Item not in stock, refunding", $refund->description);
        $this->assertEquals("tr_WDqYK6vllg", $refund->paymentId);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/refunds/{$refund_id}",
            'application/hal+json',
            $refund->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/refunds-api/get-refund',
            'text/html',
            $refund->_links->documentation
        );
    }

    protected function getOrderRefundResponseFixture($refund_id, $order_id)
    {
        return str_replace(
            ["<<refund_id>>", "<<order_id>>"],
            [$refund_id, $order_id],
            '{
                    "resource": "refund",
                    "id": "<<refund_id>>",
                    "amount": {
                        "currency": "EUR",
                        "value": "698.00"
                    },
                    "status": "pending",
                    "createdAt": "2018-03-19T12:33:37+00:00",
                    "description": "Item not in stock, refunding",
                    "paymentId": "tr_WDqYK6vllg",
                    "orderId": "<<order_id>>",
                    "lines": [
                        {
                            "resource": "orderline",
                            "id": "odl_dgtxyl",
                            "orderId": "<<order_id>>",
                            "name": "LEGO 42083 Bugatti Chiron",
                            "productUrl": "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                            "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$",
                            "sku": "5702016116977",
                            "type": "physical",
                            "status": "refunded",
                            "quantity": 2,
                            "unitPrice": {
                                "value": "399.00",
                                "currency": "EUR"
                            },
                            "vatRate": "21.00",
                            "vatAmount": {
                                "value": "121.14",
                                "currency": "EUR"
                            },
                            "discountAmount": {
                                "value": "100.00",
                                "currency": "EUR"
                            },
                            "totalAmount": {
                                "value": "698.00",
                                "currency": "EUR"
                            },
                            "createdAt": "2018-08-02T09:29:56+00:00"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/refunds/<<refund_id>>",
                            "type": "application/hal+json"
                        },
                        "payment": {
                            "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                            "type": "application/hal+json"
                        },
                        "order": {
                            "href": "https://api.mollie.com/v2/orders/<<order_id>>",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/refunds-api/get-refund",
                            "type": "text/html"
                        }
                    }
                }'
        );
    }

    protected function getOrder($id)
    {
        $orderJson = $this->getOrderResponseFixture($id);

        return $this->copy(json_decode($orderJson), new Order($this->apiClient));
    }

    protected function getOrderResponseFixture($order_id, $order_status = OrderStatus::CREATED)
    {
        return str_replace(
            "<<order_id>>",
            $order_id,
            '{
             "resource": "order",
             "id": "<<order_id>>",
             "profileId": "pfl_URR55HPMGx",
             "amount": {
                 "value": "1027.99",
                 "currency": "EUR"
             },
             "amountCaptured": {
                 "value": "0.00",
                 "currency": "EUR"
             },
             "amountRefunded": {
                 "value": "0.00",
                 "currency": "EUR"
             },
             "status": "' . $order_status . '",
             "metadata": {
                 "order_id": "1337",
                 "description": "Lego cars"
             },
             "consumerDateOfBirth": "1958-01-31",
             "createdAt": "2018-08-02T09:29:56+00:00",
             "mode": "live",
             "billingAddress": {
                 "streetAndNumber": "Keizersgracht 313",
                 "postalCode": "1016 EE",
                 "city": "Amsterdam",
                 "country": "nl",
                 "givenName": "Luke",
                 "familyName": "Skywalker",
                 "email": "luke@skywalker.com"
             },
             "shippingAddress": {
                 "streetAndNumber": "Keizersgracht 313",
                 "postalCode": "1016 EE",
                 "city": "Amsterdam",
                 "country": "nl",
                 "givenName": "Luke",
                 "familyName": "Skywalker",
                 "email": "luke@skywalker.com"
             },
             "orderNumber": "1337",
             "locale": "nl_NL",
             "method" : "klarnapaylater",
             "isCancelable": true,
             "redirectUrl": "https://example.org/redirect",
             "webhookUrl": "https://example.org/webhook",
             "lines": [
                 {
                     "resource": "orderline",
                     "id": "odl_dgtxyl",
                     "orderId": "<<order_id>>",
                     "name": "LEGO 42083 Bugatti Chiron",
                     "productUrl": "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                     "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$",
                     "sku": "5702016116977",
                     "type": "physical",
                     "status": "created",
                     "isCancelable": true,
                     "quantity": 2,
                     "unitPrice": {
                         "value": "399.00",
                         "currency": "EUR"
                     },
                     "vatRate": "21.00",
                     "vatAmount": {
                         "value": "121.14",
                         "currency": "EUR"
                     },
                     "discountAmount": {
                         "value": "100.00",
                         "currency": "EUR"
                     },
                     "totalAmount": {
                         "value": "698.00",
                         "currency": "EUR"
                     },
                     "createdAt": "2018-08-02T09:29:56+00:00"
                 },
                 {
                     "resource": "orderline",
                     "id": "odl_jp31jz",
                     "orderId": "<<order_id>>",
                     "name": "LEGO 42056 Porsche 911 GT3 RS",
                     "productUrl": "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                     "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$",
                     "sku": "5702015594028",
                     "type": "digital",
                     "status": "created",
                     "isCancelable": true,
                     "quantity": 1,
                     "unitPrice": {
                         "value": "329.99",
                         "currency": "EUR"
                     },
                     "vatRate": "21.00",
                     "vatAmount": {
                         "value": "57.27",
                         "currency": "EUR"
                     },
                     "totalAmount": {
                         "value": "329.99",
                         "currency": "EUR"
                     },
                     "createdAt": "2018-08-02T09:29:56+00:00"
                 }
             ],
             "_links": {
                 "refunds": {
                     "href": "https://api.mollie.com/v2/orders/<<order_id>>/refunds",
                     "type": "application/hal+json"
                      },
                 "self": {
                     "href": "https://api.mollie.com/v2/orders/<<order_id>>",
                     "type": "application/hal+json"
                 },
                 "checkout": {
                     "href": "https://www.mollie.com/payscreen/select-method/7UhSN1zuXS",
                     "type": "text/html"
                 },
                 "documentation": {
                     "href": "https://docs.mollie.com/reference/v2/orders-api/get-order",
                     "type": "text/html"
                 }
             }
         }'
        );
    }
}
