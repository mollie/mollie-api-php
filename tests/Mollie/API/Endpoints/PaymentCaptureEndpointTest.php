<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\Payment;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class PaymentCaptureEndpointTest extends BaseEndpointTest
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;


    public function testCreateCaptureForPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                'POST',
                '/v2/payments/tr_WDqYK6vllg/captures'
            ),
            new Response(
                201,
                [],
                $this->getCaptureFixture('tr_WDqYK6vllg', 'cpt_4qqhO89gsT')
            )
        );

        $capture = $this->apiClient->paymentCaptures->createFor(
            $this->getPayment('tr_WDqYK6vllg'),
            [
                'amount' => [
                    "value" => "1027.99",
                    "currency" => "EUR",
                ],
            ]
        );

        $this->assertCapture($capture);
    }

    public function testGetCaptureForPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/payments/tr_WDqYK6vllg/captures/cpt_4qqhO89gsT'
            ),
            new Response(
                200,
                [],
                $this->getCaptureFixture('tr_WDqYK6vllg', 'cpt_4qqhO89gsT')
            )
        );

        $capture = $this->apiClient->paymentCaptures->getFor(
            $this->getPayment('tr_WDqYK6vllg'),
            'cpt_4qqhO89gsT'
        );

        $this->assertCapture($capture);
    }

    public function testGetCaptureOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/payments/tr_WDqYK6vllg/captures/cpt_4qqhO89gsT'
            ),
            new Response(
                200,
                [],
                $this->getCaptureFixture('tr_WDqYK6vllg', 'cpt_4qqhO89gsT')
            )
        );

        $capture = $this->getPayment('tr_WDqYK6vllg')->getCapture('cpt_4qqhO89gsT');

        $this->assertCapture($capture);
    }

    public function testListCapturesOnPaymentResource()
    {
        $this->mockApiCall(
            new Request(
                'GET',
                '/v2/payments/tr_WDqYK6vllg/captures'
            ),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "captures": [
                            ' . $this->getCaptureFixture('tr_WDqYK6vllg', 'cpt_4qqhO89gsT') . '
                        ]
                    },
                    "count": 1,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/captures-api/list-captures",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.dev/v2/payments/tr_WDqYK6vllg/captures?limit=50",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null
                    }
                }'
            )
        );

        $captures = $this->getPayment('tr_WDqYK6vllg')->captures();

        $this->assertEquals(1, $captures->count());

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/captures-api/list-captures',
            'text/html',
            $captures->_links->documentation
        );

        $this->assertLinkObject(
            'https://api.mollie.dev/v2/payments/tr_WDqYK6vllg/captures?limit=50',
            'application/hal+json',
            $captures->_links->self
        );

        $this->assertNull($captures->_links->previous);
        $this->assertNull($captures->_links->next);

        $this->assertCapture($captures[0]);
    }

    protected function assertCapture($capture)
    {
        $this->assertInstanceOf(Capture::class, $capture);

        $this->assertEquals('capture', $capture->resource);
        $this->assertEquals('cpt_4qqhO89gsT', $capture->id);
        $this->assertEquals('live', $capture->mode);
        $this->assertEquals('tr_WDqYK6vllg', $capture->paymentId);
        $this->assertEquals('shp_3wmsgCJN4U', $capture->shipmentId);
        $this->assertEquals('stl_jDk30akdN', $capture->settlementId);

        $this->assertAmountObject('1027.99', 'EUR', $capture->amount);
        $this->assertAmountObject('399.00', 'EUR', $capture->settlementAmount);

        $this->assertEquals('2018-08-02T09:29:56+00:00', $capture->createdAt);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/payments/tr_WDqYK6vllg/captures/cpt_4qqhO89gsT',
            'application/hal+json',
            $capture->_links->self
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/payments/tr_WDqYK6vllg',
            'application/hal+json',
            $capture->_links->payment
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/orders/ord_8wmqcHMN4U/shipments/shp_3wmsgCJN4U',
            'application/hal+json',
            $capture->_links->shipment
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/settlements/stl_jDk30akdN',
            'application/hal+json',
            $capture->_links->settlement
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/captures-api/get-capture',
            'text/html',
            $capture->_links->documentation
        );
    }

    protected function getCaptureFixture(
        $payment_id = 'tr_WDqYK6vllg',
        $capture_id = 'cpt_4qqhO89gsT'
    ) {
        return str_replace(
            [
                '<<payment_id>>',
                '<<capture_id>>',
            ],
            [
                $payment_id,
                $capture_id,
            ],
            '{
            "resource": "capture",
            "id": "<<capture_id>>",
            "mode": "live",
            "amount": {
                "value": "1027.99",
                "currency": "EUR"
            },
            "settlementAmount": {
                "value": "399.00",
                "currency": "EUR"
            },
            "paymentId": "<<payment_id>>",
            "shipmentId": "shp_3wmsgCJN4U",
            "settlementId": "stl_jDk30akdN",
            "createdAt": "2018-08-02T09:29:56+00:00",
            "_links": {
                "self": {
                    "href": "https://api.mollie.com/v2/payments/<<payment_id>>/captures/<<capture_id>>",
                    "type": "application/hal+json"
                },
                "payment": {
                    "href": "https://api.mollie.com/v2/payments/<<payment_id>>",
                    "type": "application/hal+json"
                },
                "shipment": {
                    "href": "https://api.mollie.com/v2/orders/ord_8wmqcHMN4U/shipments/shp_3wmsgCJN4U",
                    "type": "application/hal+json"
                },
                "settlement": {
                    "href": "https://api.mollie.com/v2/settlements/stl_jDk30akdN",
                    "type": "application/hal+json"
                },
                "documentation": {
                    "href": "https://docs.mollie.com/reference/v2/captures-api/get-capture",
                    "type": "text/html"
                }
            }
          }'
        );
    }

    /**
     * @return Payment
     */
    protected function getPayment($payment_id = 'tr_44aKxzEbr8')
    {
        $paymentJson = '{
           "resource":"payment",
           "id":"<<payment_id>>",
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
                 "href":"https://api.mollie.com/v2/payments/<<payment_id>>",
                 "type":"application/hal+json"
              },
              "documentation":{
                 "href":"https://docs.mollie.com/reference/v2/payments-api/get-payment",
                 "type":"text/html"
              },
              "refunds":{
                 "href":"https://api.mollie.com/v2/payments/<<payment_id>>/refunds",
                 "type":"application/hal+json"
              },
              "captures":{
                "href":"https://api.mollie.com/v2/payments/<<payment_id>>/captures",
                "type":"application/hal+json"
              }
           }
        }';

        $paymentJson = str_replace('<<payment_id>>', $payment_id, $paymentJson);

        return $this->copy(json_decode($paymentJson), new Payment($this->apiClient));
    }
}
