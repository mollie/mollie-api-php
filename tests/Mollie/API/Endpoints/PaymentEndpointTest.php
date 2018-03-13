<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;

class PaymentEndpointTest extends BaseEndpointTest
{
    public function testCreatePaymentSendsJsonCorrectly()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v1/payments",
                [],
                '{
                    "amount": 10,
                    "description": "My first API payment",
                    "redirectUrl": "https://example.org/redirect",
                    "webhookUrl": "https://example.org/webhook",
                    "metadata": {
                        "order_id": "1234"
                    }
                }'
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "payment",
                    "id": "tr_44aKxzEbr8",
                    "mode": "test",
                    "createdDatetime": "2018-03-13T11:05:07.0Z",
                    "status": "open",
                    "canBeCancelled": false,
                    "expiryPeriod": "PT15M",
                    "amount": "10.00",
                    "description": "My first API payment",
                    "method": null,
                    "metadata": {
                        "order_id": "1234"
                    },
                    "details": null,
                    "profileId": "pfl_8M8kxbu73W",
                    "links": {
                        "paymentUrl": "https://www.mollie.com/payscreen/select-method/44aKxzEbr8",
                        "webhookUrl": "https://example.org/webhook",
                        "redirectUrl": "https://example.org/redirect"
                    }
                }'
            )
        );

        $payment = $this->api_client->payments->create(array(
            "amount" => 10.00,
            "description" => "My first API payment",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "metadata" => array(
                "order_id" => "1234",
            ),
        ));

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('My first API payment', $payment->description);
        $this->assertEquals('10.00', $payment->amount);
    }

}
