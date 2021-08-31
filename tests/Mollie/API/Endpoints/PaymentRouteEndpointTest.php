<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Payment;

class PaymentRouteEndpointTest extends BaseEndpointTest
{
    public function testCreatePaymentRoute()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/payments",
                [],
                '{
                    "profileId": "pfl_2A1gacu42V",
                    "amount": {
                        "value": "20.00",
                        "currency": "EUR"
                    },
                    "description": "My first API route payment",
                    "redirectUrl": "https://example.org/redirect",
                    "webhookUrl": "https://example.org/webhook",
                    "routing": [{
                        "amount": {
                            "currency": "EUR",
                            "value": "15.00"
                        },
                        "destination": {
                            "type": "organization",
                            "organizationId": "org_2345"
                        },
                        "releaseDate": "2021-09-05"
                    }]
                }'
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "payment",
                    "id": "tr_44aKxzEbr8",
                    "mode": "test",
                    "createdAt": "2021-08-28T14:02:29+00:00",
                    "amount": {
                        "value": "20.00",
                        "currency": "EUR"
                    },
                    "description": "My first API route payment",
                    "method": null,
                    "status": "open",
                    "isCancelable": false,
                    "expiresAt": "2021-08-29T14:17:29+00:00",
                    "details": null,
                    "profileId": "pfl_2A1gacu42V",
                    "sequenceType": "oneoff",
                    "redirectUrl": "https://example.org/redirect",
                    "webhookUrl": "https://example.org/webhook",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8?testmode=true",
                            "type": "application/hal+json"
                        },
                        "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/44aKxzEbr8",
                            "type": "text/html"
                        },
                        "dashboard": {
                            "href": "https://www.mollie.com/dashboard/org_1234/payments/tr_44aKxzEbr8",
                            "type": "text/html"
                        },
                        "routes": {
                            "href": "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/routes?testmode=true",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/payments-api/create-payment",
                            "type": "text/html"
                        }
                    },
                    "_embedded": null,
                    "amountCaptured": null,
                    "applicationFee": null,
                    "routing": [{
                        "id": "rt_1234",
                        "amount": {
                            "value": "15.00",
                            "currency": "EUR"
                        },
                        "destination": {
                            "type": "organization",
                            "organizationId": "org_2345"
                        },
                        "releaseDate": "2021-09-05"
                    }],
                    "authorizedAt": null,
                    "expiredAt": null,
                    "customerId": null
                }'
            )
        );

        $payment = $this->apiClient->payments->create([
            "profileId" => "pfl_2A1gacu42V",
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00",
            ],
            "description" => "My first API route payment",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "routing" => [
                [
                    "amount" => [
                        "currency" => "EUR",
                        "value" => "15.00",
                    ],
                    "destination" => [
                        "type" => "organization",
                        "organizationId" => "org_2345",
                    ],
                    "releaseDate" => "2021-09-05",
                ],
            ],

        ]);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertEquals("rt_1234", $payment->routing[0]->id);
        $this->assertEquals("15.00", $payment->routing[0]->amount->value);
        $this->assertEquals("EUR", $payment->routing[0]->amount->currency);
        $this->assertEquals("organization", $payment->routing[0]->destination->type);
        $this->assertEquals("org_2345", $payment->routing[0]->destination->organizationId);
        $this->assertEquals("2021-09-05", $payment->routing[0]->releaseDate);
    }
}
