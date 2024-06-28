<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderCollection;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Shipment;
use Mollie\Api\Types\OrderLineStatus;
use Mollie\Api\Types\OrderLineType;
use Mollie\Api\Types\OrderStatus;
use stdClass;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class OrderEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testCreateOrder()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders",
                [],
                '{
                 "amount": {
                     "value": "1027.99",
                     "currency": "EUR"
                 },
                 "billingAddress": {
                     "organizationName": "Organization Name LTD.",
                     "streetAndNumber": "Keizersgracht 313",
                     "postalCode": "1016 EE",
                     "city": "Amsterdam",
                     "country": "nl",
                     "givenName": "Luke",
                     "familyName": "Skywalker",
                     "email": "luke@skywalker.com"
                 },
                 "shippingAddress": {
                     "organizationName": "Organization Name LTD.",
                     "streetAndNumber": "Keizersgracht 313",
                     "postalCode": "1016 EE",
                     "city": "Amsterdam",
                     "country": "nl",
                     "givenName": "Luke",
                     "familyName": "Skywalker",
                     "email": "luke@skywalker.com"
                 },
                 "metadata": {
                     "order_id": "1337",
                     "description": "Lego cars"
                 },
                 "consumerDateOfBirth": "1958-01-31",
                 "orderNumber": "1337",
                 "locale": "nl_NL",
                 "method" : "klarnapaylater",
                 "redirectUrl": "https://example.org/redirect",
                 "webhookUrl": "https://example.org/webhook",
                 "lines": [
                     {
                         "sku": "5702016116977",
                         "name": "LEGO 42083 Bugatti Chiron",
                         "productUrl": "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                         "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$",
                         "quantity": 2,
                         "unitPrice": {
                             "currency": "EUR",
                             "value": "399.00"
                         },
                         "vatRate": "21.00",
                         "vatAmount": {
                             "currency": "EUR",
                             "value": "121.14"
                         },
                         "discountAmount": {
                             "currency": "EUR",
                             "value": "100.00"
                         },
                         "totalAmount": {
                             "currency": "EUR",
                             "value": "698.00"
                         }
                     },
                     {
                         "type": "digital",
                         "sku": "5702015594028",
                         "name": "LEGO 42056 Porsche 911 GT3 RS",
                         "productUrl": "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                         "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$",
                         "quantity": 1,
                         "unitPrice": {
                             "currency": "EUR",
                             "value": "329.99"
                         },
                         "vatRate": "21.00",
                         "vatAmount": {
                             "currency": "EUR",
                             "value": "57.27"
                         },
                         "totalAmount": {
                             "currency": "EUR",
                             "value": "329.99"
                         }
                     }
                 ]
             }'
            ),
            new Response(
                201,
                [],
                $this->getOrderResponseFixture("ord_pbjz8x")
            )
        );

        $order = $this->apiClient->orders->create([
            "amount" => [
                "value" => "1027.99",
                "currency" => "EUR",
            ],
            "billingAddress" => [
                "organizationName" => "Organization Name LTD.",
                "streetAndNumber" => "Keizersgracht 313",
                "postalCode" => "1016 EE",
                "city" => "Amsterdam",
                "country" => "nl",
                "givenName" => "Luke",
                "familyName" => "Skywalker",
                "email" => "luke@skywalker.com",
            ],
            "shippingAddress" => [
                "organizationName" => "Organization Name LTD.",
                "streetAndNumber" => "Keizersgracht 313",
                "postalCode" => "1016 EE",
                "city" => "Amsterdam",
                "country" => "nl",
                "givenName" => "Luke",
                "familyName" => "Skywalker",
                "email" => "luke@skywalker.com",
            ],
            "metadata" => [
                "order_id" => "1337",
                "description" => "Lego cars",
            ],
            "consumerDateOfBirth" => "1958-01-31",
            "locale" => "nl_NL",
            "orderNumber" => "1337",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "method" => "klarnapaylater",
            "lines" => [
                [
                    "sku" => "5702016116977",
                    "name" => "LEGO 42083 Bugatti Chiron",
                    "productUrl" => "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                    "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$',
                    "quantity" => 2,
                    "vatRate" => "21.00",
                    "unitPrice" => [
                        "currency" => "EUR",
                        "value" => "399.00",
                    ],
                    "totalAmount" => [
                        "currency" => "EUR",
                        "value" => "698.00",
                    ],
                    "discountAmount" => [
                        "currency" => "EUR",
                        "value" => "100.00",
                    ],
                    "vatAmount" => [
                        "currency" => "EUR",
                        "value" => "121.14",
                    ],
                ],
                [
                    "type" => "digital",
                    "sku" => "5702015594028",
                    "name" => "LEGO 42056 Porsche 911 GT3 RS",
                    "productUrl" => "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                    "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$',
                    "quantity" => 1,
                    "vatRate" => "21.00",
                    "unitPrice" => [
                        "currency" => "EUR",
                        "value" => "329.99",
                    ],
                    "totalAmount" => [
                        "currency" => "EUR",
                        "value" => "329.99",
                    ],
                    "vatAmount" => [
                        "currency" => "EUR",
                        "value" => "57.27",
                    ],
                ],
            ],
        ]);

        $this->assertOrder($order, 'ord_pbjz8x');
    }

    public function testGetOrderDirectly()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x"
            ),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture("ord_pbjz8x")
            )
        );

        $order = $this->apiClient->orders->get('ord_pbjz8x');

        $this->assertOrder($order, 'ord_pbjz8x');
    }

    public function testGetOrderDirectlyIncludingPayments()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_kEn1PlbGa?embed=payments"
            ),
            new Response(
                200,
                [],
                '{
                     "resource": "order",
                     "id": "ord_kEn1PlbGa",
                     "profileId": "pfl_URR55HPMGx",
                     "method": "klarnapaylater",
                     "amount": {
                         "value": "1027.99",
                         "currency": "EUR"
                     },
                     "status": "created",
                     "isCancelable": true,
                     "metadata": null,
                     "createdAt": "2018-08-02T09:29:56+00:00",
                     "expiresAt": "2018-08-30T09:29:56+00:00",
                     "mode": "live",
                     "locale": "nl_NL",
                     "billingAddress": {
                         "organizationName": "Mollie B.V.",
                         "streetAndNumber": "Keizersgracht 313",
                         "postalCode": "1016 EE",
                         "city": "Amsterdam",
                         "country": "nl",
                         "givenName": "Luke",
                         "familyName": "Skywalker",
                         "email": "luke@skywalker.com"
                     },
                     "orderNumber": "18475",
                     "shippingAddress": {
                         "organizationName": "Mollie B.V.",
                         "streetAndNumber": "Keizersgracht 313",
                         "postalCode": "1016 EE",
                         "city": "Amsterdam",
                         "country": "nl",
                         "givenName": "Luke",
                         "familyName": "Skywalker",
                         "email": "luke@skywalker.com"
                     },
                     "redirectUrl": "https://example.org/redirect",
                     "lines": [
                         {
                             "resource": "orderline",
                             "id": "odl_dgtxyl",
                             "orderId": "ord_pbjz8x",
                             "name": "LEGO 42083 Bugatti Chiron",
                             "sku": "5702016116977",
                             "type": "physical",
                             "status": "created",
                             "metadata": null,
                             "isCancelable": false,
                             "quantity": 2,
                             "quantityShipped": 0,
                             "amountShipped": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "quantityRefunded": 0,
                             "amountRefunded": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "quantityCanceled": 0,
                             "amountCanceled": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "shippableQuantity": 0,
                             "refundableQuantity": 0,
                             "cancelableQuantity": 0,
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
                             "createdAt": "2018-08-02T09:29:56+00:00",
                             "_links": {
                                 "productUrl": {
                                     "href": "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                                     "type": "text/html"
                                 },
                                 "imageUrl": {
                                     "href": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$",
                                     "type": "text/html"
                                 }
                             }
                         },
                         {
                             "resource": "orderline",
                             "id": "odl_jp31jz",
                             "orderId": "ord_pbjz8x",
                             "name": "LEGO 42056 Porsche 911 GT3 RS",
                             "sku": "5702015594028",
                             "type": "physical",
                             "status": "created",
                             "metadata": null,
                             "isCancelable": false,
                             "quantity": 1,
                             "quantityShipped": 0,
                             "amountShipped": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "quantityRefunded": 0,
                             "amountRefunded": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "quantityCanceled": 0,
                             "amountCanceled": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "shippableQuantity": 0,
                             "refundableQuantity": 0,
                             "cancelableQuantity": 0,
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
                             "createdAt": "2018-08-02T09:29:56+00:00",
                             "_links": {
                                 "productUrl": {
                                     "href": "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                                     "type": "text/html"
                                 },
                                 "imageUrl": {
                                     "href": "https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$",
                                     "type": "text/html"
                                 }
                             }
                         }
                     ],
                     "_embedded": {
                         "payments": [
                             {
                                 "resource": "payment",
                                 "id": "tr_ncaPcAhuUV",
                                 "mode": "live",
                                 "createdAt": "2018-09-07T12:00:05+00:00",
                                 "amount": {
                                     "value": "1027.99",
                                     "currency": "EUR"
                                 },
                                 "description": "Order #1337 (Lego cars)",
                                 "method": null,
                                 "metadata": null,
                                 "status": "open",
                                 "isCancelable": false,
                                 "locale": "nl_NL",
                                 "profileId": "pfl_URR55HPMGx",
                                 "orderId": "ord_kEn1PlbGa",
                                 "sequenceType": "oneoff",
                                 "redirectUrl": "https://example.org/redirect",
                                 "_links": {
                                     "self": {
                                         "href": "https://api.mollie.com/v2/payments/tr_ncaPcAhuUV",
                                         "type": "application/hal+json"
                                     },
                                     "checkout": {
                                         "href": "https://www.mollie.com/payscreen/select-method/ncaPcAhuUV",
                                         "type": "text/html"
                                     },
                                     "order": {
                                         "href": "https://api.mollie.com/v2/orders/ord_kEn1PlbGa",
                                         "type": "application/hal+json"
                                     }
                                 }
                             }
                         ]
                     },
                     "_links": {
                         "self": {
                             "href": "https://api.mollie.com/v2/orders/ord_pbjz8x",
                             "type": "application/hal+json"
                         },
                         "checkout": {
                             "href": "https://www.mollie.com/payscreen/order/checkout/pbjz8x",
                             "type": "text/html"
                         },
                         "documentation": {
                             "href": "https://docs.mollie.com/reference/v2/orders-api/get-order",
                             "type": "text/html"
                         }
                     }
                 }'
            )
        );

        $order = $this->apiClient->orders->get('ord_kEn1PlbGa', ['embed' => 'payments']);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('ord_kEn1PlbGa', $order->id);

        $payments = $order->payments();
        $this->assertInstanceOf(PaymentCollection::class, $payments);

        $payment = $payments[0];
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_ncaPcAhuUV', $payment->id);
        $this->assertEquals('2018-09-07T12:00:05+00:00', $payment->createdAt);
        $this->assertAmountObject('1027.99', 'EUR', $payment->amount);
        $this->assertEquals('Order #1337 (Lego cars)', $payment->description);
        $this->assertNull($payment->method);
        $this->assertNull($payment->metadata);
        $this->assertEquals('open', $payment->status);
        $this->assertFalse($payment->isCancelable);
        $this->assertEquals('nl_NL', $payment->locale);
        $this->assertEquals('pfl_URR55HPMGx', $payment->profileId);
        $this->assertEquals('ord_kEn1PlbGa', $payment->orderId);
        $this->assertEquals('oneoff', $payment->sequenceType);
        $this->assertEquals('https://example.org/redirect', $payment->redirectUrl);
        $this->assertLinkObject(
            'https://api.mollie.com/v2/payments/tr_ncaPcAhuUV',
            'application/hal+json',
            $payment->_links->self
        );
        $this->assertLinkObject(
            'https://www.mollie.com/payscreen/select-method/ncaPcAhuUV',
            'text/html',
            $payment->_links->checkout
        );
        $this->assertLinkObject(
            'https://api.mollie.com/v2/orders/ord_kEn1PlbGa',
            'application/hal+json',
            $payment->_links->order
        );
    }

    public function testGetOrderOnShipmentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x"
            ),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture("ord_pbjz8x")
            )
        );

        $shipment = $this->getShipment("shp_3wmsgCJN4U", "ord_pbjz8x");
        $order = $shipment->order();

        $this->assertOrder($order, 'ord_pbjz8x');
    }

    public function testListOrders()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/orders"),
            new Response(
                200,
                [],
                '{
                   "count": 3,
                   "_embedded": {
                       "orders": [
                           ' . $this->getOrderResponseFixture("ord_pbjz1x") . ',
                           ' . $this->getOrderResponseFixture("ord_pbjz2y") . ',
                           ' . $this->getOrderResponseFixture("ord_pbjz3z") . '
                       ]
                   },
                   "_links": {
                       "self": {
                           "href": "https://api.mollie.com/v2/orders",
                           "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": {
                           "href": "https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS",
                           "type": "application/hal+json"
                       },
                       "documentation": {
                           "href": "https://docs.mollie.com/reference/v2/orders-api/list-orders",
                           "type": "text/html"
                       }
                   }
               }'
            )
        );

        $orders = $this->apiClient->orders->page();

        $this->assertInstanceOf(OrderCollection::class, $orders);
        $this->assertEquals(3, $orders->count());
        $this->assertEquals(3, count($orders));

        $this->assertNull($orders->_links->previous);
        $selfLink = $this->createLinkObject(
            "https://api.mollie.com/v2/orders",
            "application/hal+json"
        );
        $this->assertEquals($selfLink, $orders->_links->self);

        $nextLink = $this->createLinkObject(
            "https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS",
            "application/hal+json"
        );
        $this->assertEquals($nextLink, $orders->_links->next);

        $documentationLink = $this->createLinkObject(
            "https://docs.mollie.com/reference/v2/orders-api/list-orders",
            "text/html"
        );
        $this->assertEquals($documentationLink, $orders->_links->documentation);

        $this->assertOrder($orders[0], 'ord_pbjz1x');
        $this->assertOrder($orders[1], 'ord_pbjz2y');
        $this->assertOrder($orders[2], 'ord_pbjz3z');
    }

    public function testIterateOrders()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/orders"),
            new Response(
                200,
                [],
                '{
                   "count": 3,
                   "_embedded": {
                       "orders": [
                           ' . $this->getOrderResponseFixture("ord_pbjz1x") . ',
                           ' . $this->getOrderResponseFixture("ord_pbjz2y") . ',
                           ' . $this->getOrderResponseFixture("ord_pbjz3z") . '
                       ]
                   },
                   "_links": {
                       "self": {
                           "href": "https://api.mollie.com/v2/orders",
                           "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": null,
                       "documentation": {
                           "href": "https://docs.mollie.com/reference/v2/orders-api/list-orders",
                           "type": "text/html"
                       }
                   }
               }'
            )
        );

        foreach ($this->apiClient->orders->iterator() as $order) {
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function testCancelOrderDirectly()
    {
        $this->mockApiCall(
            new Request("DELETE", "/v2/orders/ord_pbjz1x"),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture(
                    'ord_pbjz1x',
                    OrderStatus::CANCELED
                )
            )
        );
        $order = $this->apiClient->orders->cancel('ord_pbjz1x');
        $this->assertOrder($order, 'ord_pbjz1x', OrderStatus::CANCELED);
    }

    public function testCancelOrderOnResource()
    {
        $this->mockApiCall(
            new Request("DELETE", "/v2/orders/ord_pbjz1x"),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture(
                    'ord_pbjz1x',
                    OrderStatus::CANCELED
                )
            )
        );
        $order = $this->getOrder('ord_pbjz1x');
        $canceledOrder = $order->cancel();
        $this->assertOrder($canceledOrder, 'ord_pbjz1x', OrderStatus::CANCELED);
    }

    public function testCancelOrderLines()
    {
        $this->mockApiCall(
            new Request(
                "DELETE",
                "/v2/orders/ord_8wmqcHMN4U/lines",
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
            new Response(204)
        );

        $order = $this->getOrder('ord_8wmqcHMN4U');

        $result = $order->cancelLines([
            'lines' => [
                [
                    'id' => 'odl_dgtxyl',
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->assertNull($result);
    }

    public function testCancelAllOrderLines()
    {
        $this->mockApiCall(
            new Request(
                "DELETE",
                "/v2/orders/ord_8wmqcHMN4U/lines",
                [],
                '{
                   "lines": [],
                   "foo": "bar"
                 }'
            ),
            new Response(204)
        );

        $order = $this->getOrder('ord_8wmqcHMN4U');

        $result = $order->cancelAllLines([
            'foo' => 'bar',
        ]);

        $this->assertNull($result);
    }

    /** @test */
    public function testUpdateOrder()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/orders/ord_pbjz8x",
                [],
                '{
                     "billingAddress": {
                         "organizationName": "Organization Name LTD.",
                         "streetAndNumber": "Keizersgracht 313",
                         "postalCode": "1234AB",
                         "city": "Amsterdam",
                         "country": "NL",
                         "givenName": "Piet",
                         "familyName": "Mondriaan",
                         "email": "piet@mondriaan.com",
                         "region": "Noord-Holland",
                         "title": "Dhr",
                         "phone": "+31208202070"
                     },
                     "shippingAddress": {
                         "organizationName": "Organization Name LTD.",
                         "streetAndNumber": "Keizersgracht 313",
                         "postalCode": "1016 EE",
                         "city": "Amsterdam",
                         "country": "nl",
                         "givenName": "Luke",
                         "familyName": "Skywalker",
                         "email": "luke@skywalker.com"
                     },
                     "orderNumber": "16738",
                     "redirectUrl": "https://example.org/updated-redirect",
                     "cancelUrl": "https://example.org/updated-cancel-url",
                     "webhookUrl": "https://example.org/updated-webhook"
                 }'
            ),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture(
                    "ord_pbjz8x",
                    OrderStatus::CREATED,
                    "16738"
                )
            )
        );

        /** @var Order $order */
        $order = $this->getOrder("ord_pbjz8x");

        $order->billingAddress->organizationName = "Organization Name LTD.";
        $order->billingAddress->streetAndNumber = "Keizersgracht 313";
        $order->billingAddress->city = "Amsterdam";
        $order->billingAddress->region = "Noord-Holland";
        $order->billingAddress->postalCode = "1234AB";
        $order->billingAddress->country = "NL";
        $order->billingAddress->title = "Dhr";
        $order->billingAddress->givenName = "Piet";
        $order->billingAddress->familyName = "Mondriaan";
        $order->billingAddress->email = "piet@mondriaan.com";
        $order->billingAddress->phone = "+31208202070";
        $order->orderNumber = "16738";
        $order->redirectUrl = "https://example.org/updated-redirect";
        $order->cancelUrl = "https://example.org/updated-cancel-url";
        $order->webhookUrl = "https://example.org/updated-webhook";
        $order = $order->update();

        $this->assertOrder($order, "ord_pbjz8x", OrderStatus::CREATED, "16738");
    }

    public function testUpdateOrderLine()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/orders/ord_pbjz8x/lines/odl_dgtxyl",
                [],
                '{
                     "name": "LEGO 71043 Hogwarts™ Castle",
                     "productUrl": "https://shop.lego.com/en-GB/product/Hogwarts-Castle-71043",
                     "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image//LEGO/71043_alt1?$main$",
                     "quantity": 2,
                     "vatRate": "21.00",
                     "sku": "5702016116977",
                     "unitPrice": {
                        "currency": "EUR",
                        "value": "349.00"
                     },
                     "totalAmount": {
                        "currency": "EUR",
                        "value": "598.00"
                     },
                     "discountAmount": {
                        "currency": "EUR",
                        "value": "100.00"
                     },
                     "vatAmount": {
                        "currency": "EUR",
                        "value": "103.79"
                     },
                     "metadata": {
                        "foo": "bar"
                     }
               }'
            ),
            new Response(200, [], $this->getOrderResponseFixture('ord_pbjz8x'))
        );

        $orderLine = new OrderLine($this->apiClient);
        $orderLine->id = 'odl_dgtxyl';
        $orderLine->orderId = 'ord_pbjz8x';
        $orderLine->name = 'LEGO 71043 Hogwarts™ Castle';
        $orderLine->productUrl = 'https://shop.lego.com/en-GB/product/Hogwarts-Castle-71043';
        $orderLine->imageUrl = 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/71043_alt1?$main$';
        $orderLine->sku = '5702016116977';
        $orderLine->quantity = 2;
        $orderLine->vatRate = '21.00';
        $orderLine->unitPrice = (object) ['currency' => 'EUR', 'value' => '349.00'];
        $orderLine->totalAmount = (object) ['currency' => 'EUR', 'value' => '598.00'];
        $orderLine->discountAmount = (object) ['currency' => 'EUR', 'value' => '100.00'];
        $orderLine->vatAmount = (object) ['currency' => 'EUR', 'value' => '103.79'];
        $orderLine->metadata = (object) ['foo' => 'bar'];

        $result = $orderLine->update();

        $this->assertOrder($result, 'ord_pbjz8x');
    }

    protected function assertOrder($order, $order_id, $order_status = OrderStatus::CREATED, $orderNumber = "1337")
    {
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('order', $order->resource);
        $this->assertEquals($order_id, $order->id);
        $this->assertEquals('pfl_URR55HPMGx', $order->profileId);
        $this->assertEquals('live', $order->mode);
        $this->assertEquals('klarnapaylater', $order->method);
        $this->assertEquals('2018-08-02T09:29:56+00:00', $order->createdAt);
        $this->assertEquals('2018-09-02T09:29:56+00:00', $order->expiresAt);

        $this->assertAmountObject('1027.99', 'EUR', $order->amount);
        $this->assertAmountObject('0.00', 'EUR', $order->amountCaptured);
        $this->assertAmountObject('0.00', 'EUR', $order->amountRefunded);

        $this->assertEquals((object) [
            'order_id' => '1337',
            'description' => 'Lego cars',
        ], $order->metadata);

        $this->assertEquals($order_status, $order->status);

        $billingAddress = new stdClass();
        $billingAddress->organizationName = "Organization Name LTD.";
        $billingAddress->streetAndNumber = "Keizersgracht 313";
        $billingAddress->postalCode = "1016 EE";
        $billingAddress->city = "Amsterdam";
        $billingAddress->country = "nl";
        $billingAddress->givenName = "Luke";
        $billingAddress->familyName = "Skywalker";
        $billingAddress->email = "luke@skywalker.com";
        $this->assertEquals($billingAddress, $order->billingAddress);

        $shippingAddress = new stdClass();
        $shippingAddress->organizationName = "Organization Name LTD.";
        $shippingAddress->streetAndNumber = "Keizersgracht 313";
        $shippingAddress->postalCode = "1016 EE";
        $shippingAddress->city = "Amsterdam";
        $shippingAddress->country = "nl";
        $shippingAddress->givenName = "Luke";
        $shippingAddress->familyName = "Skywalker";
        $shippingAddress->email = "luke@skywalker.com";
        $this->assertEquals($shippingAddress, $order->shippingAddress);

        $this->assertEquals($orderNumber, $order->orderNumber);
        $this->assertEquals('nl_NL', $order->locale);

        $this->assertEquals("https://example.org/redirect", $order->redirectUrl);
        $this->assertEquals("https://example.org/webhook", $order->webhookUrl);

        $links = (object)[
            'self' => $this->createLinkObject(
                'https://api.mollie.com/v2/orders/' . $order_id,
                'application/hal+json'
            ),
            'checkout' => $this->createLinkObject(
                'https://www.mollie.com/payscreen/select-method/7UhSN1zuXS',
                'text/html'
            ),
            'documentation' => $this->createLinkObject(
                'https://docs.mollie.com/reference/v2/orders-api/get-order',
                'text/html'
            ),
        ];
        $this->assertEquals($links, $order->_links);

        $line1 = new stdClass();
        $line1->resource = "orderline";
        $line1->id = "odl_dgtxyl";
        $line1->orderId = $order_id;
        $line1->name = "LEGO 42083 Bugatti Chiron";
        $line1->productUrl = "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083";
        $line1->imageUrl = 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$';
        $line1->sku = "5702016116977";
        $line1->type = OrderLineType::PHYSICAL;
        $line1->status = OrderLineStatus::CREATED;
        $line1->isCancelable = true;
        $line1->quantity = 2;
        $line1->unitPrice = $this->createAmountObject("399.00", "EUR");
        $line1->vatRate = "21.00";
        $line1->vatAmount = $this->createAmountObject("121.14", "EUR");
        $line1->discountAmount = $this->createAmountObject("100.00", "EUR");
        $line1->totalAmount = $this->createAmountObject("698.00", "EUR");
        $line1->createdAt = "2018-08-02T09:29:56+00:00";
        $this->assertEquals($line1, $order->lines[0]);

        $line2 = new stdClass();
        $line2->resource = "orderline";
        $line2->id = "odl_jp31jz";
        $line2->orderId = $order_id;
        $line2->name = "LEGO 42056 Porsche 911 GT3 RS";
        $line2->productUrl = "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056";
        $line2->imageUrl = 'https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$';
        $line2->sku = "5702015594028";
        $line2->type = OrderLineType::DIGITAL;
        $line2->status = OrderLineStatus::CREATED;
        $line2->isCancelable = true;
        $line2->quantity = 1;
        $line2->unitPrice = $this->createAmountObject("329.99", "EUR");
        $line2->vatRate = "21.00";
        $line2->vatAmount = $this->createAmountObject("57.27", "EUR");
        $line2->totalAmount = $this->createAmountObject("329.99", "EUR");
        $line2->createdAt = "2018-08-02T09:29:56+00:00";
        $this->assertEquals($line2, $order->lines[1]);

        $this->assertNull($order->payments());
    }

    protected function getOrder($id)
    {
        $orderJson = $this->getOrderResponseFixture($id);

        return $this->copy(json_decode($orderJson), new Order($this->apiClient));
    }

    protected function getOrderResponseFixture($order_id, $order_status = OrderStatus::CREATED, $orderNumber = '1337')
    {
        return str_replace(
            [
                "<<order_id>>",
                "<<order_number>>",
            ],
            [
                $order_id,
                $orderNumber,
            ],
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
             "expiresAt": "2018-09-02T09:29:56+00:00",
             "mode": "live",
             "billingAddress": {
                 "organizationName": "Organization Name LTD.",
                 "streetAndNumber": "Keizersgracht 313",
                 "postalCode": "1016 EE",
                 "city": "Amsterdam",
                 "country": "nl",
                 "givenName": "Luke",
                 "familyName": "Skywalker",
                 "email": "luke@skywalker.com"
             },
             "shippingAddress": {
                 "organizationName": "Organization Name LTD.",
                 "streetAndNumber": "Keizersgracht 313",
                 "postalCode": "1016 EE",
                 "city": "Amsterdam",
                 "country": "nl",
                 "givenName": "Luke",
                 "familyName": "Skywalker",
                 "email": "luke@skywalker.com"
             },
             "orderNumber": <<order_number>>,
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

    protected function getShipment($shipmentId, $orderId, $orderlineStatus = OrderLineStatus::SHIPPING)
    {
        $shipmentJson = $this->getShipmentResponseFixture(
            $shipmentId,
            $orderId,
            $orderlineStatus
        );

        return $this->copy(json_decode($shipmentJson), new Shipment($this->apiClient));
    }

    protected function getShipmentResponseFixture($shipmentId, $orderId, $orderlineStatus = OrderLineStatus::SHIPPING)
    {
        return str_replace(
            [
                "<<order_id>>",
                "<<shipment_id>>",
                "<<orderline_status>>",
            ],
            [
                $orderId,
                $shipmentId,
                $orderlineStatus,
            ],
            '{
             "resource": "shipment",
             "id": "<<shipment_id>>",
             "orderId": "<<order_id>>",
             "createdAt": "2018-08-02T09:29:56+00:00",
             "profileId": "pfl_URR55HPMGx",
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
                     "status": "<<orderline_status>>",
                     "quantity": 1,
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
                     "status": "<<orderline_status>>",
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
                 "self": {
                     "href": "https://api.mollie.com/v2/orders/<<order_id>>/shipments/<<shipment_id>>",
                     "type": "application/hal+json"
                 },
                 "order": {
                     "href": "https://api.mollie.com/v2/orders/<<order_id>>",
                     "type": "application/hal+json"
                 },
                 "documentation": {
                     "href": "https://docs.mollie.com/reference/v2/shipments-api/get-shipment",
                     "type": "text/html"
                 }
             }
         }'
        );
    }
}
