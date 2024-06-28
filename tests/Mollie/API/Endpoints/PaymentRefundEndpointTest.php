<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use stdClass;

class PaymentRefundEndpointTest extends BaseEndpointTest
{
    public function testGetRefundForPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/refunds/re_PsAvxvLsnm"
            ),
            new Response(
                200,
                [],
                '{
                   "resource":"refund",
                   "id":"re_PsAvxvLsnm",
                   "amount":{
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "status":"pending",
                   "createdAt":"2018-03-19T12:33:37+00:00",
                   "description":"My first API payment",
                   "paymentId":"tr_44aKxzEbr8",
                   "settlementAmount":{
                      "value":"-20.00",
                      "currency":"EUR"
                   },
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm",
                         "type":"application/hal+json"
                      },
                      "payment":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/refunds-api/get-refund",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $refund = $this->apiClient->paymentRefunds->getFor($this->getPayment(), "re_PsAvxvLsnm");

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_PsAvxvLsnm", $refund->id);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->amount);

        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-19T12:33:37+00:00", $refund->createdAt);
        $this->assertEquals("My first API payment", $refund->description);
        $this->assertEquals("tr_44aKxzEbr8", $refund->paymentId);

        $amount = new Stdclass();
        $amount->value = '-20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->settlementAmount);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/refunds-api/get-refund", "type" => "text/html"];
        $this->assertEquals($documentationLink, $refund->_links->documentation);
    }

    public function testCreateRefundForPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/payments/tr_44aKxzEbr8/refunds"
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "refund",
                    "id": "re_4qqhO89gsT",
                    "amount": {
                        "currency": "EUR",
                        "value": "20.00"
                    },
                    "status": "pending",
                    "createdAt": "2018-03-14T17:09:02.0Z",
                    "description": "Order #33",
                    "metadata": {
                         "bookkeeping_id": 12345
                    },
                    "paymentId": "tr_WDqYK6vllg",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/refunds/re_4qqhO89gsT",
                            "type": "application/hal+json"
                        },
                        "payment": {
                            "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/refunds-api/create-payment-refund",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );
        $payment = $this->getPayment();
        $refundData = [
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00",
            ],
        ];
        $refund = $this->apiClient->paymentRefunds->createFor($payment, $refundData);

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_4qqhO89gsT", $refund->id);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->amount);

        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-14T17:09:02.0Z", $refund->createdAt);
        $this->assertEquals("Order #33", $refund->description);
        $this->assertEquals("tr_WDqYK6vllg", $refund->paymentId);


        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/refunds/re_4qqhO89gsT", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_WDqYK6vllg", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/refunds-api/create-payment-refund", "type" => "text/html"];
        $this->assertEquals($documentationLink, $refund->_links->documentation);
    }

    public function testGetRefundOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/refunds/re_PsAvxvLsnm"
            ),
            new Response(
                201,
                [],
                '{
                   "resource":"refund",
                   "id":"re_PsAvxvLsnm",
                   "amount":{
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "status":"pending",
                   "createdAt":"2018-03-19T12:33:37+00:00",
                   "description":"My first API payment",
                   "paymentId":"tr_44aKxzEbr8",
                   "settlementAmount":{
                      "value":"-20.00",
                      "currency":"EUR"
                   },
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm",
                         "type":"application/hal+json"
                      },
                      "payment":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/refunds-api/create-refund",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $refund = $this->getPayment()->getRefund("re_PsAvxvLsnm");

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_PsAvxvLsnm", $refund->id);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->amount);

        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-19T12:33:37+00:00", $refund->createdAt);
        $this->assertEquals("My first API payment", $refund->description);
        $this->assertEquals("tr_44aKxzEbr8", $refund->paymentId);

        $amount = new Stdclass();
        $amount->value = '-20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->settlementAmount);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/refunds-api/create-refund", "type" => "text/html"];
        $this->assertEquals($documentationLink, $refund->_links->documentation);
    }

    public function testCreateRefund()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/payments/tr_44aKxzEbr8/refunds",
                [],
                '{"amount":{"currency":"EUR","value":"20.00"}}'
            ),
            new Response(
                201,
                [],
                '{
                   "resource":"refund",
                   "id":"re_PsAvxvLsnm",
                   "amount":{
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "status":"pending",
                   "createdAt":"2018-03-19T12:33:37+00:00",
                   "description":"My first API payment",
                   "paymentId":"tr_44aKxzEbr8",
                   "settlementAmount":{
                      "value":"-20.00",
                      "currency":"EUR"
                   },
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm",
                         "type":"application/hal+json"
                      },
                      "payment":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/refunds-api/create-refund",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $refund = $this->getPayment()->refund([
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00",
            ],
        ]);

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_PsAvxvLsnm", $refund->id);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->amount);

        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-19T12:33:37+00:00", $refund->createdAt);
        $this->assertEquals("My first API payment", $refund->description);
        $this->assertEquals("tr_44aKxzEbr8", $refund->paymentId);

        $amount = new Stdclass();
        $amount->value = '-20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $refund->settlementAmount);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_Tgxm3amJBT/refunds/re_PmEtpvSsnm", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/refunds-api/create-refund", "type" => "text/html"];
        $this->assertEquals($documentationLink, $refund->_links->documentation);
    }

    public function testGetRefundsOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/refunds",
                [],
                ''
            ),
            new Response(
                201,
                [],
                '{
                  "_embedded": {
                    "refunds": [
                      {
                        "resource": "refund",
                        "id": "re_haCsig5aru",
                        "amount": {
                          "value": "2.00",
                          "currency": "EUR"
                        },
                        "status": "pending",
                        "createdAt": "2018-03-28T10:56:10+00:00",
                        "description": "My first API payment",
                        "paymentId": "tr_44aKxzEbr8",
                        "settlementAmount": {
                          "value": "-2.00",
                          "currency": "EUR"
                        },
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds/re_haCsig5aru",
                            "type": "application/hal+json"
                          },
                          "payment": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/refunds-api/list-refunds",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "http://api.mollie.nl/v2/payments/tr_44aKxzEbr8/refunds?limit=10",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 1
                }'
            )
        );

        $refunds = $this->getPayment()->refunds();

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertEquals(1, $refunds->count());
        $this->assertCount(1, $refunds);

        $refund = $refunds[0];

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_haCsig5aru", $refund->id);
        $this->assertEquals("2.00", $refund->amount->value);
        $this->assertEquals("EUR", $refund->amount->currency);
        $this->assertEquals("pending", $refund->status);
        $this->assertEquals("2018-03-28T10:56:10+00:00", $refund->createdAt);
        $this->assertEquals("My first API payment", $refund->description);
        $this->assertEquals("tr_44aKxzEbr8", $refund->paymentId);
        $this->assertEquals("-2.00", $refund->settlementAmount->value);
        $this->assertEquals("EUR", $refund->settlementAmount->currency);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds/re_haCsig5aru", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);
    }

    public function testListRefundsOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/refunds",
                [],
                ''
            ),
            new Response(
                201,
                [],
                '{
                  "_embedded": {
                    "refunds": [
                          {
                            "resource": "refund",
                            "id": "re_b63hJyxbap",
                            "amount": {
                                "value": "1.00",
                                "currency": "EUR"
                            },
                            "createdAt": "2021-01-17T20:57:40+00:00",
                            "description": "Order #1610620820",
                            "paymentId": "tr_4NydwvhQDd",
                            "orderId": null,
                            "lines": null,
                            "settlementAmount": {
                                "value": "-1.00",
                                "currency": "EUR"
                            },
                            "status": "refunded",
                            "_links": {
                                "self": {
                                    "href": "https://api.mollie.com/v2/payments/tr_4NydwvhQDd/refunds/re_b63hJyxbap",
                                    "type": "application/hal+json"
                                },
                                "payment": {
                                    "href": "https://api.mollie.com/v2/payments/tr_4NydwvhQDd",
                                    "type": "application/hal+json"
                                }
                            },
                            "metadata": null
                        },
                        {
                            "resource": "refund",
                            "id": "re_SpBqRM9rcn",
                            "amount": {
                                "value": "2.00",
                                "currency": "EUR"
                            },
                            "createdAt": "2021-01-17T20:26:10+00:00",
                            "description": "test",
                            "paymentId": "tr_4NydwvhQDd",
                            "orderId": null,
                            "lines": null,
                            "settlementAmount": {
                                "value": "-2.00",
                                "currency": "EUR"
                            },
                            "status": "refunded",
                            "_links": {
                                "self": {
                                    "href": "https://api.mollie.com/v2/payments/tr_4NydwvhQDd/refunds/re_SpBqRM9rcn",
                                    "type": "application/hal+json"
                                },
                                "payment": {
                                    "href": "https://api.mollie.com/v2/payments/tr_4NydwvhQDd",
                                    "type": "application/hal+json"
                                }
                            },
                            "metadata": null
                        }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/refunds-api/list-refunds",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/payments/tr_4NydwvhQDd/refunds?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 2
                }'
            )
        );

        $refunds = $this->getPayment()->listRefunds();

        $this->assertInstanceOf(RefundCollection::class, $refunds);

        /** @var RefundCollection $refunds */
        $this->assertEquals(2, $refunds->count());
        $this->assertCount(2, $refunds);

        $refund = $refunds[0];
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals("re_b63hJyxbap", $refund->id);
        $this->assertEquals("1.00", $refund->amount->value);
        $this->assertEquals("EUR", $refund->amount->currency);
        $this->assertEquals("refunded", $refund->status);
        $this->assertEquals("2021-01-17T20:57:40+00:00", $refund->createdAt);
        $this->assertEquals("Order #1610620820", $refund->description);
        $this->assertEquals("tr_4NydwvhQDd", $refund->paymentId);
        $this->assertEquals("-1.00", $refund->settlementAmount->value);
        $this->assertEquals("EUR", $refund->settlementAmount->currency);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_4NydwvhQDd/refunds/re_b63hJyxbap", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $refund->_links->self);

        $paymentLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_4NydwvhQDd", "type" => "application/hal+json"];
        $this->assertEquals($paymentLink, $refund->_links->payment);
    }

    /**
     * @return Payment
     */
    private function getPayment()
    {
        $paymentJson = '{
                   "resource":"payment",
                   "id":"tr_44aKxzEbr8",
                   "mode":"test",
                   "createdAt":"2018-03-19T12:17:57+00:00",
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
                      },
                      "refunds":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds",
                         "type":"application/hal+json"
                      }
                   }
                }';

        return $this->copy(json_decode($paymentJson), new Payment($this->apiClient));
    }
}
