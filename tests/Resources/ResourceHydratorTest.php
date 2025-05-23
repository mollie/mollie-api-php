<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\WrapperResource;
use Mollie\Api\Traits\IsIteratableRequest;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\RefundCollection;

class ResourceHydratorTest extends TestCase
{
    private ResourceHydrator $hydrator;

    private MollieApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hydrator = new ResourceHydrator;
        $this->client = $this->createMock(MollieApiClient::class);
    }

    /** @test */
    public function hydrate_simple_resource(): void
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $mockResource = new class($this->client) extends BaseResource {};

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn(get_class($mockResource));

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $result = $this->hydrator->hydrate($request, $response);

        $this->assertInstanceOf(BaseResource::class, $result);
    }

    /** @test */
    public function hydrate_collection(): void
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $mockCollection = new class($this->client) extends ResourceCollection {
            public static string $resource = AnyResource::class;

            public static string $collectionName = 'items';
        };

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn(get_class($mockCollection));

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $response->expects($this->once())
            ->method('json')
            ->willReturn((object) [
                '_embedded' => (object) ['items' => []],
                '_links' => (object) [],
            ]);

        $result = $this->hydrator->hydrate($request, $response);

        $this->assertInstanceOf(ResourceCollection::class, $result);
    }

    /** @test */
    public function hydrate_iteratable_collection(): void
    {
        $request = $this->createMock(IteratableRequest::class);
        $request->method('getHydratableResource')
            ->willReturn(CustomCollection::class);

        $request->expects($this->once())
            ->method('iteratorEnabled')
            ->willReturn(true);

        $request->expects($this->once())
            ->method('iteratesBackwards')
            ->willReturn(false);

        $this->assertInstanceOf(IsIteratable::class, $request);

        $response = $this->createMock(Response::class);
        $response->method('getConnector')
            ->willReturn($this->client);

        $response->expects($this->once())
            ->method('json')
            ->willReturn((object) [
                '_embedded' => (object) ['items' => [
                    (object) [
                        'id' => 'id',
                        'foo' => 'bar',
                    ],
                ]],
                '_links' => (object) [],
            ]);

        $result = $this->hydrator->hydrate($request, $response);

        $this->assertInstanceOf(LazyCollection::class, $result);
        $this->assertInstanceOf(AnyResource::class, $result->first());
    }

    /** @test */
    public function hydrate_decorated_resource(): void
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $decoratedResource = new WrapperResource(CustomDecorator::class);

        $request->expects($this->exactly(2))
            ->method('getHydratableResource')
            ->willReturnOnConsecutiveCalls($decoratedResource, AnyResource::class);

        $request->expects($this->once())
            ->method('resetHydratableResource')
            ->willReturnSelf();

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $result = $this->hydrator->hydrate($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $result);
    }

    /** @test */
    public function hydrate_returns_response_when_no_resource_target(): void
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn('InvalidClass');

        $result = $this->hydrator->hydrate($request, $response);

        $this->assertSame($response, $result);
    }

    /** @test */
    public function it_hydrates_from_api_result()
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

        $resource = new Payment($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertEquals('payment', $resource->resource);
        $this->assertEquals('tr_44aKxzEbr8', $resource->id);
        $this->assertEquals('test', $resource->mode);
        $this->assertEquals('2018-03-13T14:02:29+00:00', $resource->createdAt);
        $this->assertEquals((object) ['value' => '20.00', 'currency' => 'EUR'], $resource->amount);
    }

    /** @test */
    public function it_hydrates_embedded_collections()
    {
        $apiResult = json_decode('{
            "resource":"payment",
            "id":"tr_44aKxzEbr8",
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

        $resource = new Payment($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertInstanceOf(RefundCollection::class, $resource->refunds());
    }

    /** @test */
    public function it_hydrates_embedded_resources()
    {
        $apiResult = [
            "resource" => "client",
            "id" => "org_1337",
            "_embedded" => [
                "onboarding" => (object) [
                    "resource" => "onboarding",
                    "name" => "Mollie B.V.",
                    "status" => "completed"
                ]
            ]
        ];

        $resource = new Client($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertInstanceOf(Onboarding::class, $resource->_embedded->onboarding);
    }

    /** @test */
    public function it_hydrates_a_collection()
    {
        $collection = new PaymentCollection($this->client);
        $items = [
            ['id' => 'payment-1', 'resource' => 'payment'],
            ['id' => 'payment-2', 'resource' => 'payment']
        ];
        $response = $this->createMock(Response::class);

        $result = $this->hydrator->hydrateCollection($collection, $items, $response);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Payment::class, $result[0]);
        $this->assertEquals('payment-1', $result[0]->id);
    }

    /** @test */
    public function it_hydrates_a_simple_resource()
    {
        $resource = new class($this->client) extends BaseResource {};
        $data = ['id' => 'test_123', 'name' => 'Test Resource'];
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $data, $response);

        $this->assertEquals('test_123', $resource->id);
        $this->assertEquals('Test Resource', $resource->name);
    }

    /** @test */
    public function it_throws_exception_for_unmapped_embedded_resources()
    {
        $resource = new class($this->client) extends BaseResource implements EmbeddedResourcesContract {
            public function getEmbeddedResourcesMap(): array
            {
                return [];
            }
        };

        $data = [
            '_embedded' => (object) [
                'unknown' => ['id' => 'test']
            ]
        ];

        $response = $this->createMock(Response::class);

        $this->expectException(EmbeddedResourcesNotParseableException::class);
        $this->hydrator->hydrate($resource, $data, $response);
    }
}

class IteratableRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected $hydratableResource = CustomCollection::class;

    public function resolveResourcePath(): string
    {
        return 'items';
    }
}

class CustomCollection extends CursorCollection
{
    public static string $resource = AnyResource::class;

    public static string $collectionName = 'items';
}

class CustomDecorator implements IsWrapper
{
    public static function fromResource($resource): self
    {
        return new self;
    }
}
