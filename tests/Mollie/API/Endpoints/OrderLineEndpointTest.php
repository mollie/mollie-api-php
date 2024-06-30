<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Order;

class OrderLineEndpointTest extends BaseEndpointTest
{
    public function testCancelLinesRequiresLinesArray()
    {
        $this->expectException(ApiException::class);

        $this->guzzleClient = $this->createMock(Client::class);
        $this->apiClient = new MollieApiClient($this->guzzleClient);

        $order = new Order($this->apiClient);
        $order->id = 'ord_pbjz8x';

        $this->apiClient->orderLines->cancelFor($order, []);
    }

    public function testUpdateMultipleOrderLines()
    {
        $this->mockApiCall(
            new Request(
                'PATCH',
                '/v2/orders/ord_pbjz8x/lines',
                [],
                '{
                 "operations": [
                     {
                         "operation": "update",
                         "data": {
                             "id": "odl_1.1l9vx0",
                             "name": "New order line name"
                         }
                     },
                     {
                         "operation": "cancel",
                         "data": {
                             "id": "odl_1.4hqjw6"
                         }
                     },
                     {
                         "operation": "add",
                         "data": {
                             "name": "Adding new orderline",
                             "quantity": 2,
                             "sku": "12345679",
                             "totalAmount": {
                                 "currency": "EUR",
                                 "value": "30.00"
                             },
                             "type": "digital",
                             "unitPrice": {
                                 "currency": "EUR",
                                 "value": "15.00"
                             },
                             "vatAmount": {
                                 "currency": "EUR",
                                 "value": "0.00"
                             },
                             "vatRate": "0.00"
                         }
                     }
                 ]
             }'
            ),
            new Response(
                200,
                [],
                '{
                     "resource": "order",
                     "id": "ord_pbjz8x",
                     "profileId": "pfl_h7UgNeDGTA",
                     "method": "klarnapaylater",
                     "amount": {
                         "value": "50.00",
                         "currency": "EUR"
                     },
                     "status": "created",
                     "isCancelable": true,
                     "metadata": null,
                     "createdAt": "2022-06-09T13:49:10+00:00",
                     "expiresAt": "2022-07-07T13:49:10+00:00",
                     "mode": "live",
                     "locale": "en_US",
                     "billingAddress": {
                         "streetAndNumber": "Herengracht 1",
                         "postalCode": "1052CB",
                         "city": "Amsterdam",
                         "country": "NL",
                         "givenName": "mollie",
                         "familyName": "test",
                         "email": "test@test.com"
                     },
                     "shopperCountryMustMatchBillingCountry": false,
                     "orderNumber": "1",
                     "redirectUrl": "https://api.platform.mollielabs.net",
                     "webhookUrl": "https://api.platform.mollielabs.net",
                     "lines": [
                         {
                             "resource": "orderline",
                             "id": "odl_1.1l9vx0",
                             "orderId": "ord_pbjz8x",
                             "name": "New orderline name",
                             "sku": "123456",
                             "type": "digital",
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
                                 "value": "10.00",
                                 "currency": "EUR"
                             },
                             "vatRate": "0.00",
                             "vatAmount": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "totalAmount": {
                                 "value": "20.00",
                                 "currency": "EUR"
                             },
                             "createdAt": "2022-06-09T13:49:10+00:00"
                         },
                         {
                             "resource": "orderline",
                             "id": "odl_1.4hqjw6",
                             "orderId": "ord_pbjz8x",
                             "name": "A cancelled orderline",
                             "sku": "1234444",
                             "type": "digital",
                             "status": "canceled",
                             "metadata": null,
                             "isCancelable": true,
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
                             "quantityCanceled": 1,
                             "amountCanceled": {
                                 "value": "5.00",
                                 "currency": "EUR"
                             },
                             "shippableQuantity": 0,
                             "refundableQuantity": 0,
                             "cancelableQuantity": 0,
                             "unitPrice": {
                                 "value": "5.00",
                                 "currency": "EUR"
                             },
                             "vatRate": "0.00",
                             "vatAmount": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "totalAmount": {
                                 "value": "5.00",
                                 "currency": "EUR"
                             },
                             "createdAt": "2022-06-10T11:05:21+00:00"
                         },
                         {
                             "resource": "orderline",
                             "id": "odl_1.3ccpk8",
                             "orderId": "ord_pbjz8x",
                             "name": "Adding new orderline",
                             "sku": "12345679",
                             "type": "digital",
                             "status": "created",
                             "metadata": null,
                             "isCancelable": true,
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
                                 "value": "15.00",
                                 "currency": "EUR"
                             },
                             "vatRate": "0.00",
                             "vatAmount": {
                                 "value": "0.00",
                                 "currency": "EUR"
                             },
                             "totalAmount": {
                                 "value": "30.00",
                                 "currency": "EUR"
                             },
                             "createdAt": "2022-06-10T11:16:49+00:00"
                         }
                     ],
                     "_links": {
                         "self": {
                             "href": "https://api.mollie.com/v2/orders/ord_pbjz8x",
                             "type": "application/hal+json"
                         },
                         "dashboard": {
                             "href": "https://www.mollie.com/dashboard/org_2816091/orders/ord_pbjz8x",
                             "type": "text/html"
                         },
                         "checkout": {
                             "href": "https://www.mollie.com/checkout/order/xvb27g",
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

        $order = $this->apiClient->orderLines->updateMultiple(
            'ord_pbjz8x',
            [
                    [
                        "operation" => "update",
                        "data" => [
                            "id" => "odl_1.1l9vx0",
                            "name" => "New order line name",
                        ],
                    ],
                    [
                        "operation" => "cancel",
                        "data" => [
                            "id" => "odl_1.4hqjw6",
                        ],
                    ],
                    [
                        "operation" => "add",
                        "data" => [
                            "name" => "Adding new orderline",
                            "quantity" => 2,
                            "sku" => "12345679",
                            "totalAmount" => [
                                "currency" => "EUR",
                                "value" => "30.00",
                            ],
                            "type" => "digital",
                            "unitPrice" => [
                                "currency" => "EUR",
                                "value" => "15.00",
                            ],
                            "vatAmount" => [
                                "currency" => "EUR",
                                "value" => "0.00",
                            ],
                            "vatRate" => "0.00",
                        ],
                    ],
                ]
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('ord_pbjz8x', $order->id);
    }
}
