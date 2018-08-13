<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderCollection;
use Mollie\Api\Types\OrderStatus;
use stdClass;

class OrderEndpointTest extends BaseEndpointTest
{
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
                         "type": "physical",
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
                         "type": "physical",
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
              "currency" => "EUR"
            ],
            "billingAddress" => [
              "streetAndNumber" => "Keizersgracht 313",
              "postalCode" => "1016 EE",
              "city" => "Amsterdam",
              "country" => "nl",
              "givenName" => "Luke",
              "familyName" => "Skywalker",
              "email" => "luke@skywalker.com",
            ],
            "shippingAddress" => [
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
              "description" => "Lego cars"
            ],
            "consumerDateOfBirth" => "1958-01-31",
            "locale" => "nl_NL",
            "orderNumber" => "1337",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "method" => "klarnapaylater",
            "lines" => [
                [
                    "type" => "physical",
                    "sku" => "5702016116977",
                    "name" => "LEGO 42083 Bugatti Chiron",
                    "productUrl" => "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                    "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$',
                    "quantity" => 2,
                    "vatRate" => "21.00",
                    "unitPrice" => [
                        "currency" => "EUR",
                        "value" => "399.00"
                    ],
                    "totalAmount" => [
                        "currency" => "EUR",
                        "value" => "698.00"
                    ],
                    "discountAmount" => [
                        "currency" => "EUR",
                        "value" => "100.00"
                    ],
                    "vatAmount" => [
                        "currency" => "EUR",
                        "value" => "121.14"
                    ]
                ],
                [
                    "type" => "physical",
                    "sku" => "5702015594028",
                    "name" => "LEGO 42056 Porsche 911 GT3 RS",
                    "productUrl" => "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                    "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$',
                    "quantity" => 1,
                    "vatRate" => "21.00",
                    "unitPrice" => [
                        "currency" => "EUR",
                        "value" => "329.99"
                    ],
                    "totalAmount" => [
                        "currency" => "EUR",
                        "value" => "329.99"
                    ],
                    "vatAmount" => [
                        "currency" => "EUR",
                        "value" => "57.27"
                    ]
                ]
            ]
        ]);

        $this->assertOrder($order, 'ord_pbjz8x');
    }

    /** @test */
    public function testGetOrder()
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

    /** @test */
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
        $this->assertEquals(3, $orders->count);
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

    /** @test */
    public function testDeleteOrder()
    {
        $this->mockApiCall(
            new Request("DELETE", "/v2/orders/ord_pbjz1x"),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture(
                    'ord_pbjz1x',
                    OrderStatus::STATUS_CANCELED
                )
            )
        );
        $order = $this->apiClient->orders->delete('ord_pbjz1x');
        $this->assertOrder($order, 'ord_pbjz1x', OrderStatus::STATUS_CANCELED);
    }

    /** @test */
    public function testCancelOrder()
    {
        $this->mockApiCall(
            new Request("DELETE", "/v2/orders/ord_pbjz1x"),
            new Response(
                200,
                [],
                $this->getOrderResponseFixture(
                    'ord_pbjz1x',
                    OrderStatus::STATUS_CANCELED
                )
            )
        );
        $order = $this->apiClient->orders->cancel('ord_pbjz1x');
        $this->assertOrder($order, 'ord_pbjz1x', OrderStatus::STATUS_CANCELED);
    }

    protected function assertOrder($order, $order_id, $order_status = OrderStatus::STATUS_CREATED)
    {
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('order', $order->resource);
        $this->assertEquals($order_id, $order->id);
        $this->assertEquals('pfl_URR55HPMGx', $order->profileId);
        $this->assertEquals('live', $order->mode);
        $this->assertEquals('2018-08-02T09:29:56+00:00', $order->createdAt);

        $amount = $this->createAmountObject('1027.99', 'EUR');
        $this->assertEquals($amount, $order->amount);

        $zeroAmount = $this->createAmountObject('0.00', 'EUR');
        $this->assertEquals($zeroAmount, $order->amountCaptured);
        $this->assertEquals($zeroAmount, $order->amountRefunded);

        $this->assertEquals((object) [
          'order_id' => '1337',
          'description' => 'Lego cars',
        ], $order->metadata);

        $this->assertEquals($order_status, $order->status);

        $billingAddress = new stdClass();
        $billingAddress->streetAndNumber = "Keizersgracht 313";
        $billingAddress->postalCode = "1016 EE";
        $billingAddress->city = "Amsterdam";
        $billingAddress->country = "nl";
        $billingAddress->givenName = "Luke";
        $billingAddress->familyName = "Skywalker";
        $billingAddress->email = "luke@skywalker.com";
        $this->assertEquals($billingAddress, $order->billingAddress);

        $shippingAddress = new stdClass();
        $shippingAddress->streetAndNumber = "Keizersgracht 313";
        $shippingAddress->postalCode = "1016 EE";
        $shippingAddress->city = "Amsterdam";
        $shippingAddress->country = "nl";
        $shippingAddress->givenName = "Luke";
        $shippingAddress->familyName = "Skywalker";
        $shippingAddress->email = "luke@skywalker.com";
        $this->assertEquals($shippingAddress, $order->shippingAddress);

        $this->assertEquals('1337', $order->orderNumber);
        $this->assertEquals('nl_NL', $order->locale);

        $this->assertEquals("https://example.org/redirect", $order->redirectUrl);
        $this->assertEquals("https://example.org/webhook", $order->webhookUrl);

        $links = (object )[
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
        $line1->type = "physical";
        $line1->status = OrderStatus::STATUS_CREATED;
        $line1->quantity = 2;
        $line1->unitPrice = $this->createAmountObject("399.00", "EUR");
        $line1->vatRate = "21.00";
        $line1->vatAmount = $this->createAmountObject("121.14", "EUR");
        $line1->discountAmount = $this->createAmountObject("100.00", "EUR");
        $line1->totalAmount = $this->createAmountObject("698.00", "EUR");
        $line1->createdAt = "2018-08-02T09:29:56+00:00";
        $line1->_links = $this->createNamedLinkObject(
            "self",
            "https://api.mollie.com/v2/orders/$order_id/orderlines/odl_dgtxyl",
            "application/hal+json"
        );
        $this->assertEquals($line1, $order->lines[0]);

        $line2 = new stdClass();
        $line2->resource = "orderline";
        $line2->id = "odl_jp31jz";
        $line2->orderId = $order_id;
        $line2->name = "LEGO 42056 Porsche 911 GT3 RS";
        $line2->productUrl = "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056";
        $line2->imageUrl = 'https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$';
        $line2->sku = "5702015594028";
        $line2->type = "physical";
        $line2->status = OrderStatus::STATUS_CREATED;
        $line2->quantity = 1;
        $line2->unitPrice = $this->createAmountObject("329.99", "EUR");
        $line2->vatRate = "21.00";
        $line2->vatAmount = $this->createAmountObject("57.27", "EUR");
        $line2->totalAmount = $this->createAmountObject("329.99", "EUR");
        $line2->createdAt = "2018-08-02T09:29:56+00:00";
        $line2->_links = $this->createNamedLinkObject(
            "self",
            "https://api.mollie.com/v2/orders/$order_id/orderlines/odl_jp31jz",
            "application/hal+json"
        );
        $this->assertEquals($line2, $order->lines[1]);
    }

    protected function createAmountObject($value, $currency)
    {
        $amount = new stdClass();
        $amount->value = $value;
        $amount->currency = $currency;
        return $amount;
    }

    protected function createLinkObject($href, $type)
    {
        $link = new stdClass();
        $link->href = $href;
        $link->type = $type;
        return $link;
    }

    protected function createNamedLinkObject($name, $href, $type)
    {
        $linkContainer = new stdClass();
        $linkContainer->{$name} = $this->createLinkObject($href, $type);
        return $linkContainer;
    }

    protected function getOrderResponseFixture($order_id, $order_status = OrderStatus::STATUS_CREATED)
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
                     "createdAt": "2018-08-02T09:29:56+00:00",
                     "_links": {
                         "self": {
                             "href": "https://api.mollie.com/v2/orders/<<order_id>>/orderlines/odl_dgtxyl",
                             "type": "application/hal+json"
                         }
                     }
                 },
                 {
                     "resource": "orderline",
                     "id": "odl_jp31jz",
                     "orderId": "<<order_id>>",
                     "name": "LEGO 42056 Porsche 911 GT3 RS",
                     "productUrl": "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                     "imageUrl": "https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$",
                     "sku": "5702015594028",
                     "type": "physical",
                     "status": "created",
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
                     "createdAt": "2018-08-02T09:29:56+00:00",
                     "_links": {
                         "self": {
                             "href": "https://api.mollie.com/v2/orders/<<order_id>>/orderlines/odl_jp31jz",
                             "type": "application/hal+json"
                         }
                     }
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
}
