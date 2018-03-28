<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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
                                  "href":"http://api.mollie.test/v2/payments/tr_44aKxzEbr8/chargebacks/chb_n9z0tp",
                                  "type":"application/json"
                               },
                               "payment":{  
                                  "href":"http://api.mollie.test/v2/payments/tr_44aKxzEbr8",
                                  "type":"application/json"
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
                                  "href":"http://api.mollie.test/v2/payments/tr_44aKxzEbr8/chargebacks/chb_6cqlwf",
                                  "type":"application/json"
                               },
                               "payment":{  
                                  "href":"http://api.mollie.test/v2/payments/tr_44aKxzEbr8",
                                  "type":"application/json"
                               }
                            }
                         }
                      ]
                   },
                   "_links":{  
                      "documentation":{  
                         "href":"https://www.mollie.test/en/docs/reference/chargebacks/list",
                         "type":"text/html"
                      },
                      "self":{  
                         "href":"http://api.mollie.test/v2/payments/tr_44aKxzEbr8/chargebacks",
                         "type":"application/json"
                      }
                   },
                   "count": 2
                }'
            )
        );

        $chargebacks = $this->getPayment()->chargebacks();

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertEquals(2, $chargebacks->count);
        $this->assertEquals(2, count($chargebacks));

        $documentationLink = (object)["href" => "https://www.mollie.test/en/docs/reference/chargebacks/list", "type" => "text/html"];
        $this->assertEquals($documentationLink, $chargebacks->_links->documentation);

        $selfLink = (object)["href" => "http://api.mollie.test/v2/payments/tr_44aKxzEbr8/chargebacks", "type" => "application/json"];
        $this->assertEquals($selfLink, $chargebacks->_links->self);
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
                         "type":"application/json"
                      },
                      "documentation":{  
                         "href":"https://www.mollie.com/en/docs/reference/payments/get",
                         "type":"text/html"
                      },
                      "chargebacks":{  
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks",
                         "type":"application/json"
                      }
                   }
                }';

        return $this->copy(json_decode($paymentJson), new Payment($this->apiClient));
    }

}