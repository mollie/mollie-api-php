<?php

namespace Mollie\Api\Tests\Resources;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\ResourceDecorator;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\DecorateResource;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Traits\IsIteratableRequest;
use PHPUnit\Framework\TestCase;

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

        $mockResource = new class($this->client, $response) extends BaseResource {};

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

        $mockCollection = new class($this->client, $response) extends ResourceCollection
        {
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

        $decoratedResource = (new DecorateResource(AnyResource::class))
            ->with(CustomDecorator::class);

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

class CustomDecorator implements ResourceDecorator
{
    public static function fromResource($resource): self
    {
        return new self;
    }
}
