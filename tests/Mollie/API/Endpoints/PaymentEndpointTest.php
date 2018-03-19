<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
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
                    "amount":{  
                      "value":"20.00",
                      "currency":"EUR"
                    },
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
                      "value":"20.00",
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
                   "redirectUrl":"http://example.org/var/www/mollie-api-php/examples/payment/03-return-page.php?order_id=1234",
                   "webhookUrl":"http://example.org/var/www/mollie-api-php/examples/payment/02-webhook-verification.php",
                   "_links":{  
                      "self":{  
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/json"
                      },
                      "checkout":{  
                         "href":"https://www.mollie.com/payscreen/select-method/44aKxzEbr8",
                         "type":"text/html"
                      },
                      "documentation":{  
                         "href":"https://www.mollie.com/en/docs/reference/payments/create",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $payment = $this->api_client->payments->create(array(
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00"
            ],
            "description" => "My first API payment",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "metadata" => array(
                "order_id" => "1234",
            ),
        ));

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertEquals("2018-03-13T14:02:29+00:00", $payment->createdAt);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $payment->amount);

        $this->assertEquals('My first API payment', $payment->description);
        $this->assertNull($payment->method);
        $this->assertEquals((object) ["order_id" => "1234"], $payment->metadata);
        $this->assertEquals(PaymentStatus::STATUS_OPEN, $payment->status);
        $this->assertFalse($payment->canBeCancelled);
        $this->assertEquals("2018-03-13T14:17:29+00:00", $payment->expiresAt);
        $this->assertNull($payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::SEQUENCETYPE_ONEOFF, $payment->sequenceType);
        $this->assertEquals("http://example.org/var/www/mollie-api-php/examples/payment/03-return-page.php?order_id=1234", $payment->redirectUrl);
        $this->assertEquals("http://example.org/var/www/mollie-api-php/examples/payment/02-webhook-verification.php", $payment->webhookUrl);

        $self_link = (object) ["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/json"];
        $this->assertEquals($self_link, $payment->_links->self);

        $checkout_link = (object) ["href" => "https://www.mollie.com/payscreen/select-method/44aKxzEbr8", "type" => "text/html"];
        $this->assertEquals($checkout_link, $payment->_links->checkout);

        $documentation_link = (object) ["href" => "https://www.mollie.com/en/docs/reference/payments/create", "type" => "text/html"];
        $this->assertEquals($documentation_link, $payment->_links->documentation);
    }

}
