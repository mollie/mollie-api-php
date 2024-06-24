<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Shipment;
use Mollie\Api\Resources\ShipmentCollection;
use Mollie\Api\Types\OrderLineStatus;
use Mollie\Api\Types\OrderStatus;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ShipmentEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testCreateShipment()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders/ord_pbjz8x/shipments",
                [],
                '{
                    "lines": [
                    {
                        "id": "odl_dgtxyl",
                        "quantity": 1
                    },
                    {
                        "id": "odl_jp31jz"
                    }
                   ]
                }'
            ),
            new Response(
                201,
                [],
                $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x")
            )
        );

        $order = $this->getOrder('ord_pbjz8x');
        $shipment = $order->createShipment([
            'lines' => [
                [
                    'id' => 'odl_dgtxyl',
                    'quantity' => 1,
                ],
                [
                    'id' => 'odl_jp31jz',
                ],
            ],
        ]);

        $this->assertShipment($shipment, 'shp_3wmsgCJN4U', 'ord_pbjz8x');
    }

    public function testCreateShipmentUsingShorthand()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders/ord_pbjz8x/shipments",
                [],
                '{
                    "lines": []
                }'
            ),
            new Response(
                201,
                [],
                $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x")
            )
        );

        $order = $this->getOrder('ord_pbjz8x');
        $shipment = $order->shipAll();

        $this->assertShipment($shipment, 'shp_3wmsgCJN4U', 'ord_pbjz8x');
    }

    public function testGetShipment()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x/shipments/shp_3wmsgCJN4U"
            ),
            new Response(
                200,
                [],
                $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x")
            )
        );

        $order = $this->getOrder('ord_pbjz8x');
        $shipment = $this->apiClient->shipments->getFor($order, "shp_3wmsgCJN4U");

        $this->assertShipment($shipment, 'shp_3wmsgCJN4U', 'ord_pbjz8x');
    }

    public function testGetShipmentOnOrderResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x/shipments/shp_3wmsgCJN4U"
            ),
            new Response(
                200,
                [],
                $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x")
            )
        );

        $order = $this->getOrder('ord_pbjz8x');
        $shipment = $order->getShipment('shp_3wmsgCJN4U');

        $this->assertShipment($shipment, 'shp_3wmsgCJN4U', 'ord_pbjz8x');
    }

    public function testListShipmentsViaShipmentEndpoint()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x/shipments"
            ),
            new Response(
                200,
                [],
                '{
                    "count": 2,
                    "_embedded": {
                        "shipments": [
                            ' . $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x") . ',
                            ' . $this->getShipmentResponseFixture("shp_kjh234CASX", "ord_pbjz8x") . '
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/order/ord_pbjz8x/shipments",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/shipments-api/list-shipments",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $order = $this->getOrder('ord_pbjz8x');
        $shipments = $this->apiClient->shipments->listFor($order);

        $this->assertInstanceOf(ShipmentCollection::class, $shipments);
        $this->assertShipment($shipments[0], 'shp_3wmsgCJN4U', 'ord_pbjz8x');
        $this->assertShipment($shipments[1], 'shp_kjh234CASX', 'ord_pbjz8x');
    }

    public function testListShipmentsOnOrderResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/orders/ord_pbjz8x/shipments"
            ),
            new Response(
                200,
                [],
                '{
                    "count": 2,
                    "_embedded": {
                        "shipments": [
                            ' . $this->getShipmentResponseFixture("shp_3wmsgCJN4U", "ord_pbjz8x") . ',
                            ' . $this->getShipmentResponseFixture("shp_kjh234CASX", "ord_pbjz8x") . '
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/order/ord_pbjz8x/shipments",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/shipments-api/list-shipments",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $order = $this->getOrder('ord_pbjz8x');

        $shipments = $order->shipments();

        $this->assertInstanceOf(ShipmentCollection::class, $shipments);
        $this->assertShipment($shipments[0], 'shp_3wmsgCJN4U', 'ord_pbjz8x');
        $this->assertShipment($shipments[1], 'shp_kjh234CASX', 'ord_pbjz8x');
    }

    public function testUpdateShipmentTrackingInfo()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/orders/ord_pbjz8x/shipments/shp_3wmsgCJN4U",
                [],
                '{
                     "tracking": {
                         "carrier": "PostNL",
                         "code": "3SKABA000000000",
                         "url": "http://postnl.nl/tracktrace/?B=3SKABA000000000&P=1016EE&D=NL&T=C"
                     }
                 }'
            ),
            new Response(
                200,
                [],
                $this->getShipmentResponseFixture(
                    "shp_3wmsgCJN4U",
                    "ord_pbjz8x",
                    OrderLineStatus::SHIPPING,
                    '"tracking": {
                         "carrier": "PostNL",
                         "code": "3SKABA000000000",
                         "url": "http://postnl.nl/tracktrace/?B=3SKABA000000000&P=1016EE&D=NL&T=C"
                     },'
                )
            )
        );

        $shipment = $this->getShipment('shp_3wmsgCJN4U', 'ord_pbjz8x', OrderLineStatus::SHIPPING);

        $shipment->tracking = [
            'carrier' => 'PostNL',
            'code' => '3SKABA000000000',
            'url' => 'http://postnl.nl/tracktrace/?B=3SKABA000000000&P=1016EE&D=NL&T=C',
        ];
        $shipment = $shipment->update();

        $this->assertShipment($shipment, 'shp_3wmsgCJN4U', 'ord_pbjz8x');

        $this->assertEquals((object) [
            'carrier' => 'PostNL',
            'code' => '3SKABA000000000',
            'url' => 'http://postnl.nl/tracktrace/?B=3SKABA000000000&P=1016EE&D=NL&T=C',
        ], $shipment->tracking);
    }

    protected function assertShipment($shipment, $shipment_id, $order_id)
    {
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals("shipment", $shipment->resource);
        $this->assertEquals($shipment_id, $shipment->id);
        $this->assertEquals($order_id, $shipment->orderId);
        $this->assertEquals('2018-08-02T09:29:56+00:00', $shipment->createdAt);
        $this->assertLinkObject(
            "https://api.mollie.com/v2/orders/ord_pbjz8x/shipments/{$shipment_id}",
            'application/hal+json',
            $shipment->_links->self
        );
        $this->assertLinkObject(
            'https://api.mollie.com/v2/orders/ord_pbjz8x',
            'application/hal+json',
            $shipment->_links->order
        );
        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/shipments-api/get-shipment',
            'text/html',
            $shipment->_links->documentation
        );

        $line1 = $shipment->lines()[0];
        $this->assertEquals('orderline', $line1->resource);
        $this->assertEquals('odl_dgtxyl', $line1->id);
        $this->assertEquals('ord_pbjz8x', $line1->orderId);
        $this->assertEquals('LEGO 42083 Bugatti Chiron', $line1->name);
        $this->assertEquals('https://shop.lego.com/nl-NL/Bugatti-Chiron-42083', $line1->productUrl);
        $this->assertEquals('https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$', $line1->imageUrl);
        $this->assertEquals('5702016116977', $line1->sku);
        $this->assertEquals('physical', $line1->type);
        $this->assertEquals(OrderLineStatus::SHIPPING, $line1->status);
        $this->assertEquals(2, $line1->quantity);
        $this->assertEquals('2018-08-02T09:29:56+00:00', $line1->createdAt);
        $this->assertEquals('21.00', $line1->vatRate);
        $this->assertAmountObject('121.14', 'EUR', $line1->vatAmount);
        $this->assertAmountObject('399.00', 'EUR', $line1->unitPrice);
        $this->assertAmountObject('100.00', 'EUR', $line1->discountAmount);
        $this->assertAmountObject('698.00', 'EUR', $line1->totalAmount);

        $line2 = $shipment->lines()[1];
        $this->assertEquals('orderline', $line2->resource);
        $this->assertEquals('odl_jp31jz', $line2->id);
        $this->assertEquals('ord_pbjz8x', $line2->orderId);
        $this->assertEquals('LEGO 42056 Porsche 911 GT3 RS', $line2->name);
        $this->assertEquals('https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056', $line2->productUrl);
        $this->assertEquals('https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$', $line2->imageUrl);
        $this->assertEquals('5702015594028', $line2->sku);
        $this->assertEquals('digital', $line2->type);
        $this->assertEquals(OrderLineStatus::SHIPPING, $line2->status);
        $this->assertEquals(1, $line2->quantity);
        $this->assertEquals('2018-08-02T09:29:56+00:00', $line2->createdAt);
        $this->assertEquals('21.00', $line2->vatRate);
        $this->assertAmountObject('57.27', 'EUR', $line2->vatAmount);
        $this->assertAmountObject('329.99', 'EUR', $line2->unitPrice);
        $this->assertAmountObject('329.99', 'EUR', $line2->totalAmount);
    }

    protected function getOrder($id)
    {
        $orderJson = $this->getOrderResponseFixture($id);

        return $this->copy(json_decode($orderJson), new Order($this->apiClient));
    }

    protected function getShipment($shipment_id, $order_id, $orderLineStatus = OrderLineStatus::SHIPPING)
    {
        $shipmentJson = $this->getShipmentResponseFixture($shipment_id, $order_id, $orderLineStatus);

        return $this->copy(json_decode($shipmentJson), new Shipment($this->apiClient));
    }

    protected function getOrderResponseFixture($order_id, $order_status = OrderStatus::CREATED)
    {
        return str_replace(
            "<<order_id>>",
            $order_id,
            '{
             "resource": "order",
             "id": "<<order_id>>",
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
                     "type": "digital",
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

    protected function getShipmentResponseFixture($shipment_id, $order_id, $orderline_status = OrderLineStatus::SHIPPING, $tracking_info = '')
    {
        return str_replace(
            [
                "<<order_id>>",
                "<<shipment_id>>",
                "<<orderline_status>>",
                "<<tracking_info>>",
            ],
            [
                $order_id,
                $shipment_id,
                $orderline_status,
                $tracking_info,
            ],
            '{
             "resource": "shipment",
             "id": "<<shipment_id>>",
             "orderId": "<<order_id>>",
             "createdAt": "2018-08-02T09:29:56+00:00",
             <<tracking_info>>
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
