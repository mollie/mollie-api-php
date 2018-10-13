<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ChargebackEndpointTest extends BaseEndpointTest
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
                            "paymentId":"tr_nQKWJbDj7j",
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
        $this->assertEquals(2, $chargebacks->count);
        $this->assertCount(2, $chargebacks);

        $documentationLink = (object)[
            "href" => "https://docs.mollie.com/reference/v2/chargebacks-api/list-chargebacks",
            "type" => "text/html"
        ];
        $this->assertEquals($documentationLink, $chargebacks->_links->documentation);

        $selfLink = (object)[
            "href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks",
            "type" => "application/hal+json"
        ];
        $this->assertEquals($selfLink, $chargebacks->_links->self);

        $chargeback = $chargebacks[0];

        $this->assertChargeback($chargeback);
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

        $this->assertChargeback($chargeback);
    }

    protected function assertChargeback($chargeback)
    {
        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals("chargeback", $chargeback->resource);
        $this->assertEquals("chb_n9z0tp", $chargeback->id);

        $this->assertAmountObject("-13.00", "EUR", $chargeback->amount);
        $this->assertAmountObject("-13.00", "EUR", $chargeback->settlementAmount);

        $this->assertEquals("2018-03-28T11:44:32+00:00", $chargeback->createdAt);
        $this->assertEquals("tr_44aKxzEbr8", $chargeback->paymentId);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp",
            "application/hal+json",
            $chargeback->_links->self
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
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
           "redirectUrl":"http://example.org/examples/03-return-page.php?order_id=1234",
           "webhookUrl":"http://example.org/examples/02-webhook-verification.php",
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
