<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\ResourceFactory;
use PHPUnit\Framework\TestCase;

class ResourceFactoryTest extends TestCase
{
    /** @test */
    public function create_from_api_result()
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

        $payment = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Payment::class,
            $this->createMock(Response::class)
        );

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertEquals('2018-03-13T14:02:29+00:00', $payment->createdAt);
        $this->assertEquals((object) ['value' => '20.00', 'currency' => 'EUR'], $payment->amount);
    }

    /** @test */
    public function embedded_collections_are_type_casted()
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
            Payment::class,
            $this->createMock(Response::class)
        );

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertInstanceOf(RefundCollection::class, $payment->refunds());
    }

    /** @test */
    /** @test */
    public function embedded_resources_are_type_casted()
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
            Client::class,
            $this->createMock(Response::class)
        );

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(Onboarding::class, $client->_embedded->onboarding);
    }

    /** @test */
    public function it_throws_exception_when_response_is_missing()
    {
        $apiResult = new \stdClass;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Response is required');

        ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Payment::class
        );
    }

    /** @test */
    public function response_param_is_optional_when_data_is_a_response()
    {
        $jsonData = '{
            "resource":"payment",
            "id":"tr_44aKxzEbr8"
        }';

        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn(json_decode($jsonData));

        $payment = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $response,
            Payment::class
        );

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
    }

    /** @test */
    public function it_throws_exception_for_unmapped_embedded_resources()
    {
        $apiResult = json_decode('{
            "resource":"payment",
            "id":"tr_44aKxzEbr8",
            "_embedded": {
                "unknown_resource": {
                    "id": "test"
                }
            }
        }');

        $this->expectException(\Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException::class);

        ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Payment::class,
            $this->createMock(Response::class)
        );
    }

    /** @test */
    public function it_creates_resource_collection()
    {
        $response = $this->createMock(Response::class);
        $data = [
            ['id' => 'payment-1', 'resource' => 'payment'],
            ['id' => 'payment-2', 'resource' => 'payment'],
        ];

        $collection = ResourceFactory::createResourceCollection(
            $this->createMock(MollieApiClient::class),
            PaymentCollection::class,
            $response,
            $data
        );

        $this->assertInstanceOf(PaymentCollection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Payment::class, $collection[0]);
        $this->assertEquals('payment-1', $collection[0]->id);
    }

    /** @test */
    public function it_handles_any_resource_type()
    {
        $apiResult = json_decode('{
            "resource":"custom",
            "id":"custom_123",
            "customField":"test"
        }');

        $resource = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            AnyResource::class,
            $this->createMock(Response::class)
        );

        $this->assertInstanceOf(AnyResource::class, $resource);
        $this->assertEquals('custom_123', $resource->id);
        $this->assertEquals('test', $resource->customField);
    }

    /** @test */
    public function it_creates_decorated_resource()
    {
        $apiResult = json_decode('{
            "resource":"onboarding",
            "status":"pending",
            "canReceivePayments":true,
            "canReceiveSettlements":true,
            "_links": {
                "dashboard": {
                    "href": "https://my.mollie.com/dashboard/org_123"
                }
            }
        }');

        $resource = ResourceFactory::createFromApiResult(
            $this->createMock(MollieApiClient::class),
            $apiResult,
            Onboarding::class,
            $this->createMock(Response::class)
        );

        $decoratedResource = ResourceFactory::createDecoratedResource(
            $resource,
            CustomResourceDecorator::class
        );

        $this->assertInstanceOf(CustomResourceDecorator::class, $decoratedResource);
        $this->assertEquals('pending', $decoratedResource->status);
        $this->assertEquals('https://my.mollie.com/dashboard/org_123', $decoratedResource->dashboardUrl);
    }
}

class CustomPaymentCollection extends BaseCollection
{
    //
}

class CustomResourceDecorator implements IsWrapper
{
    public string $status;

    public bool $canReceivePayments;

    public bool $canReceiveSettlements;

    public string $dashboardUrl;

    public function __construct(
        string $status,
        bool $canReceivePayments,
        bool $canReceiveSettlements,
        string $dashboardUrl
    ) {
        $this->status = $status;
        $this->canReceivePayments = $canReceivePayments;
        $this->canReceiveSettlements = $canReceiveSettlements;
        $this->dashboardUrl = $dashboardUrl;
    }

    public static function fromResource($onboarding): IsWrapper
    {
        /** @var Onboarding $onboarding */
        return new self(
            $onboarding->status,
            $onboarding->canReceivePayments,
            $onboarding->canReceiveSettlements,
            $onboarding->_links->dashboard->href
        );
    }
}
