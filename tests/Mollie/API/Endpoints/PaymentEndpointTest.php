<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class PaymentEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

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
                      "order_id":"1234"
                   },
                   "status":"open",
                   "isCancelable":false,
                   "expiresAt":"2018-03-13T14:17:29+00:00",
                   "details":null,
                   "profileId":"pfl_2A1gacu42V",
                   "sequenceType":"oneoff",
                   "redirectUrl":"https://example.org/redirect",
                   "webhookUrl":"https://example.org/webhook",
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "checkout":{
                         "href":"https://www.mollie.com/payscreen/select-method/44aKxzEbr8",
                         "type":"text/html"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/payments-api/create-payment",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $payment = $this->apiClient->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00",
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
        $this->assertEquals(PaymentStatus::OPEN, $payment->status);
        $this->assertFalse($payment->isCancelable);
        $this->assertEquals("2018-03-13T14:17:29+00:00", $payment->expiresAt);
        $this->assertNull($payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::ONEOFF, $payment->sequenceType);
        $this->assertEquals("https://example.org/redirect", $payment->redirectUrl);
        $this->assertEquals("https://example.org/webhook", $payment->webhookUrl);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $payment->_links->self);

        $checkoutLink = (object)["href" => "https://www.mollie.com/payscreen/select-method/44aKxzEbr8", "type" => "text/html"];
        $this->assertEquals($checkoutLink, $payment->_links->checkout);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/payments-api/create-payment", "type" => "text/html"];
        $this->assertEquals($documentationLink, $payment->_links->documentation);
    }

    public function testUpdatePayment()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/payments/tr_7UhSN1zuXS",
                [],
                '{
                    "description":"Order #98765",
                    "redirectUrl":"https://example.org/webshop/order/98765/",
                    "cancelUrl":"https://example.org/webshop/order/98765/canceled/",
                    "webhookUrl":"https://example.org/webshop/payments/webhook/",
                    "restrictPaymentMethodsToCountry": null,
                    "metadata":{
                        "order_id":"98765"
                    },
                    "dueDate":null,
                    "locale":null
                }'
            ),
            new Response(
                200,
                [],
                '{
                   "resource": "payment",
                   "id": "tr_7UhSN1zuXS",
                   "mode": "test",
                   "createdAt": "2018-03-20T09:13:37+00:00",
                   "amount": {
                       "value": "10.00",
                       "currency": "EUR"
                   },
                   "description": "Order #98765",
                   "method": null,
                   "metadata": {
                       "order_id": "98765"
                   },
                   "status": "open",
                   "isCancelable": false,
                   "expiresAt": "2018-03-20T09:28:37+00:00",
                   "details": null,
                   "profileId": "pfl_QkEhN94Ba",
                   "sequenceType": "oneoff",
                   "redirectUrl": "https://example.org/webshop/order/98765/",
                   "webhookUrl": "https://example.org/webshop/payments/webhook/",
                   "_links": {
                       "self": {
                           "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS",
                           "type": "application/json"
                       },
                       "checkout": {
                           "href": "https://www.mollie.com/payscreen/select-method/7UhSN1zuXS",
                           "type": "text/html"
                       },
                       "documentation": {
                           "href": "https://docs.mollie.com/reference/v2/payments-api/update-payment",
                           "type": "text/html"
                       }
                   }
               }'
            )
        );

        // Get Payment stub
        $payment = new Payment($this->apiClient);
        $payment->id = "tr_7UhSN1zuXS";
        $payment->_links = $this->createNamedLinkObject(
            "self",
            "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS",
            "application/json"
        );

        // Modify fields
        $payment->description = "Order #98765";
        $payment->redirectUrl = "https://example.org/webshop/order/98765/";
        $payment->webhookUrl = "https://example.org/webshop/payments/webhook/";
        $payment->metadata = ["order_id" => "98765"];
        $payment->cancelUrl = "https://example.org/webshop/order/98765/canceled/";

        $payment = $payment->update();

        $this->assertEquals("payment", $payment->resource);
        $this->assertEquals("tr_7UhSN1zuXS", $payment->id);

        $this->assertEquals("Order #98765", $payment->description);
        $this->assertEquals("https://example.org/webshop/order/98765/", $payment->redirectUrl);
        $this->assertEquals("https://example.org/webshop/payments/webhook/", $payment->webhookUrl);
        $this->assertEquals((object) ["order_id" => "98765"], $payment->metadata);
        $this->assertEquals("oneoff", $payment->sequenceType);
        $this->assertEquals("pfl_QkEhN94Ba", $payment->profileId);
        $this->assertNull($payment->details);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS",
            "application/json",
            $payment->_links->self
        );

        $this->assertLinkObject(
            "https://www.mollie.com/payscreen/select-method/7UhSN1zuXS",
            "text/html",
            $payment->_links->checkout
        );

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/payments-api/update-payment",
            "text/html",
            $payment->_links->documentation
        );
    }

    public function testGetPayment()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8?testmode=true",
                [],
                ''
            ),
            new Response(
                200,
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
                      "order_id":"1234"
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
                   "redirectUrl":"https://example.org/redirect",
                   "webhookUrl":"https://example.org/webhook",
                   "settlementAmount":{
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/payments-api/get-payment",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $payment = $this->apiClient->payments->get("tr_44aKxzEbr8", ["testmode" => true]);

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
        $this->assertEquals(PaymentStatus::PAID, $payment->status);

        $amountRefunded = new Stdclass();
        $amountRefunded->value = '0.00';
        $amountRefunded->currency = "EUR";
        $this->assertEquals($amountRefunded, $payment->amountRefunded);

        $amountRemaining = new Stdclass();
        $amountRemaining->value = '20.00';
        $amountRemaining->currency = "EUR";
        $this->assertEquals($amountRemaining, $payment->amountRemaining);

        $details = (object)[
            'consumerName' => 'T. TEST',
            'consumerAccount' => 'NL17RABO0213698412',
            'consumerBic' => 'TESTNL99',
        ];

        $this->assertEquals($details, $payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::ONEOFF, $payment->sequenceType);
        $this->assertEquals("https://example.org/redirect", $payment->redirectUrl);
        $this->assertEquals("https://example.org/webhook", $payment->webhookUrl);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $payment->_links->self);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/payments-api/get-payment", "type" => "text/html"];
        $this->assertEquals($documentationLink, $payment->_links->documentation);
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
                200,
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_admNa2tFfa",
                            "type": "application/hal+json"
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_bcaLc7hFfa",
                            "type": "application/hal+json"
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_pslHy1tFfa",
                            "type": "application/hal+json"
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
                      "href": "https://docs.mollie.com/reference/v2/payments-api/list-payments",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.com/v2/payments?limit=3",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": {
                      "href": "http://api.mollie.com/v2/payments?from=tr_eW8f5kzUkF&limit=3",
                      "type": "application/hal+json"
                    }
                  },
                  "count": 3
                }'
            )
        );

        $payments = $this->apiClient->payments->page(null, 3);

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertEquals(3, $payments->count());
        $this->assertEquals(3, count($payments));

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/payments-api/list-payments", "type" => "text/html"];
        $this->assertEquals($documentationLink, $payments->_links->documentation);

        $selfLink = (object)["href" => "http://api.mollie.com/v2/payments?limit=3", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $payments->_links->self);

        $this->assertNull($payments->_links->previous);

        $nextLink = (object)["href" => "http://api.mollie.com/v2/payments?from=tr_eW8f5kzUkF&limit=3", "type" => "application/hal+json"];
        $this->assertEquals($nextLink, $payments->_links->next);
    }

    public function testIteratePayment()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments",
                [],
                ''
            ),
            new Response(
                200,
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_admNa2tFfa",
                            "type": "application/hal+json"
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_bcaLc7hFfa",
                            "type": "application/hal+json"
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
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_pslHy1tFfa",
                            "type": "application/hal+json"
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
                      "href": "https://docs.mollie.com/reference/v2/payments-api/list-payments",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.com/v2/payments?limit=3",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 3
                }'
            )
        );

        foreach ($this->apiClient->payments->iterator() as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
            $this->assertEquals("payment", $payment->resource);
        }
    }
}
