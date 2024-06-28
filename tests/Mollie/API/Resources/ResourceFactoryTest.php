<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
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

    public function testEmbeddedResourcesAreTypeCasted()
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
        $this->assertInstanceOf(RefundCollection::class, $payment->_embedded->refunds);
    }
}
