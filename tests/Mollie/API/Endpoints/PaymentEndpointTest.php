<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use stdClass;

class PaymentEndpointTest extends BaseEndpointTest
{
    public function testCreatePayment()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/payments",
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
                   "resource":"payment",
                   "id":"tr_44aKxzEbr8",
                   "mode":"test",
                   "createdAt":"2018-03-13T14:02:29+00:00",
                   "amount":{  
                      "value":"10.00",
                      "currency":"EUR"
                   },
                   "description":"My first API payment",
                   "method":null,
                   "metadata":{  
                      "order_id":1234
                   },
                   "status":"open",
                   "canBeCancelled":false,
                   "expiresAt":"2018-03-13T14:17:29+00:00",
                   "details":null,
                   "profileId":"pfl_2A1gacu42V",
                   "sequenceType":"oneoff",
                   "redirectUrl":"http://example.org/var/www/mollie-api-php/examples/payment/03-return-page.php?order_id=1520949749",
                   "webhookUrl":"http://example.org/var/www/mollie-api-php/examples/payment/02-webhook-verification.php",
                   "_links":{  
                      "self":{  
                         "href":"https://api.mollie.com/v2/payments/tr_bhqkaSS5Mx",
                         "type":"application/json"
                      },
                      "checkout":{  
                         "href":"https://www.mollie.com/payscreen/select-method/bhqkaSS5Mx",
                         "type":"text/html"
                      }
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
        $this->assertEquals('oneoff', $payment->sequenceType);

        $amount = new Stdclass();
        $amount->value = '10.00';
        $amount->currency = "EUR";

        $this->assertEquals($amount, $payment->amount);
        $this->assertEquals("2018-03-13T14:02:29+00:00", $payment->createdAt);
        $this->assertEquals("https://www.mollie.com/payscreen/select-method/bhqkaSS5Mx", $payment->getCheckoutUrl());
    }

}
