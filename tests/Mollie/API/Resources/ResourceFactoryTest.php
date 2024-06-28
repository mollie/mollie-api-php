<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\ResourceFactory;

class ResourceFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateFromApiResponseWorks()
    {
        $apiResult = json_decode('{
            "resource":"payment",
            "id":"tr_44aKxzEbr8",
            "mode":"test",
            "createdAt":"2018-03-13T14:02:29+00:00",
            "amount":{
                "value":"20.00",
                "currency":"EUR"
            }
        }');

        $payment = ResourceFactory::createFromApiResult($this->createMock(MollieApiClient::class), $apiResult, Payment::class);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals("payment", $payment->resource);
        $this->assertEquals("tr_44aKxzEbr8", $payment->id);
        $this->assertEquals("test", $payment->mode);
        $this->assertEquals("2018-03-13T14:02:29+00:00", $payment->createdAt);
        $this->assertEquals((object) ["value" => "20.00", "currency" => "EUR"], $payment->amount);
    }

    public function testEmbeddedCollectionsAreTypeCasted()
    {
        $apiResult = json_decode('{
            "resource":"payment",
            "id":"tr_44aKxzEbr8",
            "mode":"test",
            "createdAt":"2018-03-13T14:02:29+00:00",
            "amount":{
                "value":"20.00",
                "currency":"EUR"
            },
            "_embedded": {
                "refunds": [
                    {
                        "resource": "refund",
                        "id": "re_4qqhO89gsT",
                        "amount": {
                            "value": "20.00",
                            "currency": "EUR"
                        }
                    }
                ]
            }
        }');

        /** @var Payment $payment */
        $payment = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Payment::class
        );

        $this->assertInstanceOf(Payment::class, $payment);
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(RefundCollection::class, $payment->_embedded->refunds);
    }

    /** @test */
    public function testEmbeddedResourcesAreTypeCasted()
    {
        $apiResult = json_decode('{
            "resource": "client",
            "id": "org_1337",
            "organizationCreatedAt": "2018-03-21T13:13:37+00:00",
            "commission": {
                "count": 200,
                "totalAmount": {
                    "currency": "EUR",
                    "value": "10.00"
                }
            },
            "_embedded": {
                "onboarding": {
                  "resource": "onboarding",
                  "name": "Mollie B.V.",
                  "signedUpAt": "2018-12-20T10:49:08+00:00",
                  "status": "completed",
                  "canReceivePayments": true,
                  "canReceiveSettlements": true
                }
            }
        }');

        /** @var Client $client */
        $client = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Client::class
        );

        $this->assertInstanceOf(Client::class, $client);
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Onboarding::class, $client->_embedded->onboarding);
    }
}
