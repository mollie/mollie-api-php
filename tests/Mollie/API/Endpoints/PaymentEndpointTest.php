<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
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
                   "redirectUrl":"http://example.org/examples/payment/03-return-page.php?order_id=1234",
                   "webhookUrl":"http://example.org/examples/payment/02-webhook-verification.php",
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

        $payment = $this->api_client->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00"
            ],
            "description" => "My first API payment",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "metadata" => [
                "order_id" => "1234",
            ],
        ]);

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
        $this->assertEquals((object)["order_id" => "1234"], $payment->metadata);
        $this->assertEquals(PaymentStatus::STATUS_OPEN, $payment->status);
        $this->assertFalse($payment->canBeCancelled);
        $this->assertEquals("2018-03-13T14:17:29+00:00", $payment->expiresAt);
        $this->assertNull($payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::SEQUENCETYPE_ONEOFF, $payment->sequenceType);
        $this->assertEquals("http://example.org/examples/payment/03-return-page.php?order_id=1234", $payment->redirectUrl);
        $this->assertEquals("http://example.org/examples/payment/02-webhook-verification.php", $payment->webhookUrl);

        $self_link = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/json"];
        $this->assertEquals($self_link, $payment->_links->self);

        $checkout_link = (object)["href" => "https://www.mollie.com/payscreen/select-method/44aKxzEbr8", "type" => "text/html"];
        $this->assertEquals($checkout_link, $payment->_links->checkout);

        $documentation_link = (object)["href" => "https://www.mollie.com/en/docs/reference/payments/create", "type" => "text/html"];
        $this->assertEquals($documentation_link, $payment->_links->documentation);
    }

    public function testGetPayment()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8",
                [],
                ''
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
                   "method":"ideal",
                   "metadata":{  
                      "order_id":1234
                   },
                   "status":"paid",
                   "paidAt":"2018-03-19T12:18:35+00:00",
                   "amountRefunded":{  
                      "value":"0.00",
                      "currency":"EUR"
                   },
                   "amountRemaining":{  
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "details":{  
                      "consumerName":"T. TEST",
                      "consumerAccount":"NL17RABO0213698412",
                      "consumerBic":"TESTNL99"
                   },
                   "locale":"nl_NL",
                   "countryCode":"NL",
                   "profileId":"pfl_2A1gacu42V",
                   "sequenceType":"oneoff",
                   "redirectUrl":"http://example.org/examples/03-return-page.php?order_id=1234",
                   "webhookUrl":"http://example.org/examples/02-webhook-verification.php",
                   "settlementAmount":{  
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "_links":{  
                      "self":{  
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/json"
                      },
                      "documentation":{  
                         "href":"https://www.mollie.com/en/docs/reference/payments/get",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $payment = $this->api_client->payments->get("tr_44aKxzEbr8");

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertEquals("2018-03-13T14:02:29+00:00", $payment->createdAt);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $payment->amount);

        $this->assertEquals('My first API payment', $payment->description);
        $this->assertEquals("ideal", $payment->method);
        $this->assertEquals((object)["order_id" => "1234"], $payment->metadata);
        $this->assertEquals(PaymentStatus::STATUS_PAID, $payment->status);


        $amount_refuncded = new Stdclass();
        $amount_refuncded->value = '0.00';
        $amount_refuncded->currency = "EUR";
        $this->assertEquals($amount_refuncded, $payment->amountRefunded);

        $amount_remaining = new Stdclass();
        $amount_remaining->value = '20.00';
        $amount_remaining->currency = "EUR";
        $this->assertEquals($amount_remaining, $payment->amountRemaining);

        $details = (object)[
            'consumerName' => 'T. TEST',
            'consumerAccount' => 'NL17RABO0213698412',
            'consumerBic' => 'TESTNL99'
        ];

        $this->assertEquals($details, $payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::SEQUENCETYPE_ONEOFF, $payment->sequenceType);
        $this->assertEquals("http://example.org/examples/03-return-page.php?order_id=1234", $payment->redirectUrl);
        $this->assertEquals("http://example.org/examples/02-webhook-verification.php", $payment->webhookUrl);

        $self_link = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/json"];
        $this->assertEquals($self_link, $payment->_links->self);

        $documentation_link = (object)["href" => "https://www.mollie.com/en/docs/reference/payments/get", "type" => "text/html"];
        $this->assertEquals($documentation_link, $payment->_links->documentation);
    }

    public function testListPayment()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments?limit=3",
                [],
                ''
            ),
            new Response(
                201,
                [],
                '{
                  "_embedded": {
                    "payments": [
                      {
                        "resource": "payment",
                        "id": "tr_admNa2tFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 1",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "canBeCancelled": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_admNa2tFfa",
                            "type": "application/json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/admNa2tFfa",
                            "type": "text/html"
                          }
                        }
                      },
                      {
                        "resource": "payment",
                        "id": "tr_bcaLc7hFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 2",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "canBeCancelled": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_bcaLc7hFfa",
                            "type": "application/json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/bcaLc7hFfa",
                            "type": "text/html"
                          }
                        }
                      },
                      {
                        "resource": "payment",
                        "id": "tr_pslHy1tFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 3",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "canBeCancelled": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_pslHy1tFfa",
                            "type": "application/json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/pslHy1tFfa",
                            "type": "text/html"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://www.mollie.com/en/docs/reference/payments/list",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.nl/v2/payments?limit=3",
                      "type": "application/json"
                    },
                    "previous": null,
                    "next": {
                      "href": "http://api.mollie.nl/v2/payments?from=tr_eW8f5kzUkF&limit=3",
                      "type": "application/json"
                    }
                  },
                  "count": 3
                }'
            )
        );

        $payments = $this->api_client->payments->all(0, 3);


        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertEquals(3, $payments->count);
        $this->assertEquals(3, count($payments));

        $documentation_link = (object)["href" => "https://www.mollie.com/en/docs/reference/payments/list", "type" => "text/html"];
        $this->assertEquals($documentation_link, $payments->_links->documentation);

        $self_link = (object)["href" => "http://api.mollie.nl/v2/payments?limit=3", "type" => "application/json"];
        $this->assertEquals($self_link, $payments->_links->self);

        $this->assertNull($payments->_links->previous);

        $next_link = (object)["href" => "http://api.mollie.nl/v2/payments?from=tr_eW8f5kzUkF&limit=3", "type" => "application/json"];
        $this->assertEquals($next_link, $payments->_links->next);
    }

}
