<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;

class ChargebackEndpointTest extends BaseEndpointTest
{
    public function testGetChargebacksOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payments/tr_44aKxzEbr8/chargebacks",
                [],
                ''
            ),
            new Response(
                201,
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

        /** @var Chargeback $chargeback */
        $chargeback = $chargebacks[0];

        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals("chb_n9z0tp", $chargeback->id);
        $this->assertEquals("-13.00", $chargeback->amount->value);
        $this->assertEquals("EUR", $chargeback->amount->currency);
        $this->assertEquals("2018-03-28T11:44:32+00:00", $chargeback->createdAt);
        $this->assertEquals("tr_44aKxzEbr8", $chargeback->paymentId);
        $this->assertEquals("-13.00", $chargeback->settlementAmount->value);
        $this->assertEquals("EUR", $chargeback->settlementAmount->currency);

        $selfLink = (object)[
            "href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp",
            "type" => "application/hal+json"
        ];
        $this->assertEquals($selfLink, $chargeback->_links->self);

        $paymentLink = (object)[
            "href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
            "type" => "application/hal+json"
        ];
        $this->assertEquals($paymentLink, $chargeback->_links->payment);
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