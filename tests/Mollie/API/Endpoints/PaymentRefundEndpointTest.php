<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
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

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/refunds-api/create-refund", "type" => "text/html"];
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

        $refund = $this->getPayment("tr_44aKxzEbr8")->getRefund("re_PsAvxvLsnm");

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
                      "refunds":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds",
                         "type":"application/hal+json"
                      }
                   }
                }';

        return $this->copy(json_decode($paymentJson), new Payment($this->apiClient));
    }
}
