<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\OrderStatus;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class OrderPaymentEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testCreateOrderPayment()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/orders/ord_stTC2WHAuS/payments",
                [],
                '{
                    "method": "banktransfer",
                    "dueDate": "2018-12-21"
                }'
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "payment",
                    "id": "tr_WDqYK6vllg",
                    "mode": "test",
                    "amount": {
                        "currency": "EUR",
                        "value": "698.00"
                    },
                    "status": "open",
                    "description": "Order #1337 (Lego cars)",
                    "createdAt": "2018-12-01T17:09:02+00:00",
                    "method": "banktransfer",
                    "metadata": null,
                    "orderId": "ord_stTC2WHAuS",
                    "isCancelable": true,
                    "locale": "nl_NL",
                    "profileId": "pfl_URR55HPMGx",
                    "sequenceType": "oneoff",
                    "settlementAmount": {
                        "value": "698.00",
                        "currency": "EUR"
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                            "type": "application/hal+json"
                        },
                        "order": {
                            "href": "https://api.mollie.com/v2/orders/ord_stTC2WHAuS",
                            "type": "application/hal+json"
                        },
                        "checkout": {
                            "href": "https://www.mollie.com/paymentscreen/testmode/?method=banktransfer&token=fgnwdh",
                            "type": "text/html"
                        },
                        "status": {
                            "href": "https://www.mollie.com/paymentscreen/banktransfer/status/fgnwdh",
                            "type": "text/html"
                        },
                        "payOnline": {
                            "href": "https://www.mollie.com/paymentscreen/banktransfer/pay-online/fgnwdh",
                            "type": "text/html"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/orders-api/create-order-payment",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $order = $this->getOrder('ord_stTC2WHAuS');

        $payment = $order->createPayment([
            'method' => 'banktransfer',
            'dueDate' => '2018-12-21',
        ]);

        $this->assertNotNull($payment);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertEquals('tr_WDqYK6vllg', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertAmountObject(698, 'EUR', $payment->amount);
        $this->assertEquals('open', $payment->status);
        $this->assertEquals(PaymentStatus::OPEN, $payment->status);
        $this->assertEquals('Order #1337 (Lego cars)', $payment->description);
        $this->assertEquals('2018-12-01T17:09:02+00:00', $payment->createdAt);
        $this->assertEquals(PaymentMethod::BANKTRANSFER, $payment->method);
        $this->assertNull($payment->metadata);
        $this->assertEquals('ord_stTC2WHAuS', $payment->orderId);
        $this->assertTrue($payment->isCancelable);
        $this->assertEquals('nl_NL', $payment->locale);
        $this->assertEquals('pfl_URR55HPMGx', $payment->profileId);
        $this->assertEquals(SequenceType::ONEOFF, $payment->sequenceType);
        $this->assertAmountObject(698, 'EUR', $payment->settlementAmount);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/payments/tr_WDqYK6vllg',
            'application/hal+json',
            $payment->_links->self
        );
        $this->assertLinkObject(
            'https://api.mollie.com/v2/orders/ord_stTC2WHAuS',
            'application/hal+json',
            $payment->_links->order
        );
        $this->assertLinkObject(
            'https://www.mollie.com/paymentscreen/testmode/?method=banktransfer&token=fgnwdh',
            'text/html',
            $payment->_links->checkout
        );
        $this->assertLinkObject(
            'https://www.mollie.com/paymentscreen/banktransfer/status/fgnwdh',
            'text/html',
            $payment->_links->status
        );
        $this->assertLinkObject(
            'https://www.mollie.com/paymentscreen/banktransfer/pay-online/fgnwdh',
            'text/html',
            $payment->_links->payOnline
        );
        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/orders-api/create-order-payment',
            'text/html',
            $payment->_links->documentation
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
