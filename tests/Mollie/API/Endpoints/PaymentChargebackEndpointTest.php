<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class PaymentChargebackEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testListChargebacksOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/chargebacks"
            ),
            new Response(
                200,
                [],
                '{
                   "_embedded":{
                      "chargebacks":[
                         {
                            "resource":"chargeback",
                            "id":"chb_n9z0tp",
                            "amount":{
                               "value":"-13.00",
                               "currency":"EUR"
                            },
                            "createdAt":"2018-03-28T11:44:32+00:00",
                            "paymentId":"tr_44aKxzEbr8",
                            "settlementAmount":{
                               "value":"-13.00",
                               "currency":"EUR"
                            },
                            "_links":{
                               "self":{
                                  "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp",
                                  "type":"application/hal+json"
                               },
                               "payment":{
                                  "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                                  "type":"application/hal+json"
                               },
                               "documentation": {
                                  "href": "https://docs.mollie.com/reference/v2/chargebacks-api/get-chargeback",
                                  "type": "text/html"
                               }
                            }
                         },
                         {
                            "resource":"chargeback",
                            "id":"chb_6cqlwf",
                            "amount":{
                               "value":"-0.37",
                               "currency":"EUR"
                            },
                            "createdAt":"2018-03-28T11:44:32+00:00",
                            "paymentId":"tr_44aKxzEbr8",
                            "settlementAmount":{
                               "value":"-0.37",
                               "currency":"EUR"
                            },
                            "_links":{
                               "self":{
                                  "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks/chb_6cqlwf",
                                  "type":"application/hal+json"
                               },
                               "payment":{
                                  "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                                  "type":"application/hal+json"
                               },
                              "documentation": {
                                  "href": "https://docs.mollie.com/reference/v2/chargebacks-api/get-chargeback",
                                  "type": "text/html"
                               }
                            }
                         }
                      ]
                   },
                   "_links":{
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/chargebacks-api/list-chargebacks",
                         "type":"text/html"
                      },
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks",
                         "type":"application/hal+json"
                      }
                   },
                   "count": 2
                }'
            )
        );

        $chargebacks = $this->getPayment()->chargebacks();

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertEquals(2, $chargebacks->count());
        $this->assertCount(2, $chargebacks);

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/chargebacks-api/list-chargebacks",
            "text/html",
            $chargebacks->_links->documentation
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks",
            "application/hal+json",
            $chargebacks->_links->self
        );

        $this->assertChargeback($chargebacks[0], 'tr_44aKxzEbr8', 'chb_n9z0tp', "-13.00");
        $this->assertChargeback($chargebacks[1], 'tr_44aKxzEbr8', 'chb_6cqlwf', "-0.37");
    }

    public function testGetChargebackOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp"
            ),
            new Response(
                200,
                [],
                '{
                  "resource":"chargeback",
                  "id":"chb_n9z0tp",
                  "amount":{
                     "value":"-13.00",
                     "currency":"EUR"
                  },
                  "createdAt":"2018-03-28T11:44:32+00:00",
                  "paymentId":"tr_44aKxzEbr8",
                  "settlementAmount":{
                     "value":"-13.00",
                     "currency":"EUR"
                  },
                  "_links":{
                     "self":{
                        "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp",
                        "type":"application/hal+json"
                     },
                     "payment":{
                        "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                        "type":"application/hal+json"
                     },
                     "documentation": {
                          "href": "https://docs.mollie.com/reference/v2/chargebacks-api/get-chargeback",
                          "type": "text/html"
                      }
                  }
               }'
            )
        );

        $chargeback = $this->getPayment()->getChargeback("chb_n9z0tp");

        $this->assertChargeback($chargeback, 'tr_44aKxzEbr8', 'chb_n9z0tp', "-13.00");
    }

    public function testPaymentChargebacksListForIdPaymentChargebackEndpoint()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/chargebacks"
            ),
            new Response(
                200,
                [],
                '{
                    "count": 3,
                    "_embedded": {
                        "chargebacks": [
                            {
                                "resource": "chargeback",
                                "id": "chb_n9z0tp",
                                "amount": {
                                    "currency": "EUR",
                                    "value": "43.38"
                                },
                                "settlementAmount": {
                                    "currency": "EUR",
                                    "value": "-35.07"
                                },
                                "createdAt": "2018-03-14T17:00:52.0Z",
                                "reversedAt": null,
                                "paymentId": "tr_44aKxzEbr8",
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/chargebacks/chb_n9z0tp",
                                        "type": "application/hal+json"
                                    },
                                    "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/chargebacks-api/get-payment-chargeback",
                                        "type": "text/html"
                                    }
                                }
                            },
                            { },
                            { }
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS/chargebacks",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/chargebacks-api/list-payment-chargebacks",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $chargebacks = $this->apiClient->paymentChargebacks->listForId('tr_44aKxzEbr8');

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertEquals($chargebacks[0]->id, 'chb_n9z0tp');
        $this->assertAmountObject('43.38', 'EUR', $chargebacks[0]->amount);
        $this->assertEquals($chargebacks[0]->paymentId, 'tr_44aKxzEbr8');
    }

    public function testPaymentChargebacksListForPaymentChargebackEndpoint()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/chargebacks"
            ),
            new Response(
                200,
                [],
                '{
                    "count": 3,
                    "_embedded": {
                        "chargebacks": [
                            {
                                "resource": "chargeback",
                                "id": "chb_n9z0tp",
                                "amount": {
                                    "currency": "USD",
                                    "value": "43.38"
                                },
                                "settlementAmount": {
                                    "currency": "EUR",
                                    "value": "-35.07"
                                },
                                "createdAt": "2018-03-14T17:00:52.0Z",
                                "reversedAt": null,
                                "paymentId": "tr_44aKxzEbr8",
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg/chargebacks/chb_n9z0tp",
                                        "type": "application/hal+json"
                                    },
                                    "payment": {
                                        "href": "https://api.mollie.com/v2/payments/tr_WDqYK6vllg",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/chargebacks-api/get-payment-chargeback",
                                        "type": "text/html"
                                    }
                                }
                            },
                            { },
                            { }
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_7UhSN1zuXS/chargebacks",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/chargebacks-api/list-payment-chargebacks",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );
        $payment = $this->getPayment();

        $chargebacks = $this->apiClient->paymentChargebacks->listFor($payment);

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertEquals($chargebacks[0]->id, 'chb_n9z0tp');
        $this->assertEquals($chargebacks[0]->paymentId, 'tr_44aKxzEbr8');
    }

    protected function assertChargeback($chargeback, $paymentId, $chargebackId, $amount)
    {
        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals("chargeback", $chargeback->resource);
        $this->assertEquals($chargebackId, $chargeback->id);

        $this->assertAmountObject($amount, "EUR", $chargeback->amount);
        $this->assertAmountObject($amount, "EUR", $chargeback->settlementAmount);

        $this->assertEquals("2018-03-28T11:44:32+00:00", $chargeback->createdAt);
        $this->assertEquals($paymentId, $chargeback->paymentId);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/{$paymentId}/chargebacks/{$chargebackId}",
            "application/hal+json",
            $chargeback->_links->self
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/{$paymentId}",
            "application/hal+json",
            $chargeback->_links->payment
        );

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/chargebacks-api/get-chargeback",
            "text/html",
            $chargeback->_links->documentation
        );
    }

    /**
     * @return Payment
     */
    protected function getPayment()
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
              "chargebacks":{
                 "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks",
                 "type":"application/hal+json"
              }
           }
        }';

        return $this->copy(json_decode($paymentJson), new Payment($this->apiClient));
    }
}
