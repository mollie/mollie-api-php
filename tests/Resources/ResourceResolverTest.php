<?php

namespace Tests\Resources;

use Mollie\Api\Config;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\ResourceRegistry;
use Mollie\Api\Resources\ResourceResolver;
use Mollie\Api\Resources\WrapperResource;
use PHPUnit\Framework\TestCase;

class ResourceResolverTest extends TestCase
{
    private ResourceResolver $resolver;

    private MollieApiClient $client;

    private ResourceHydrator $hydrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createMock(MollieApiClient::class);
        $this->hydrator = $this->createMock(ResourceHydrator::class);
        $this->resolver = new ResourceResolver($this->hydrator);
    }

    /** @test */
    public function it_resolves_to_a_simple_resource()
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn(AnyResource::class);

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $response->expects($this->once())
            ->method('json')
            ->willReturn((object) ['id' => 'test_123']);

        $mockResource = new AnyResource($this->client);
        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->willReturn($mockResource);

        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(AnyResource::class, $result);
    }

    /** @test */
    public function it_resolves_to_a_collection()
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn(CustomCollection::class);

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $response->expects($this->once())
            ->method('json')
            ->willReturn((object) [
                '_embedded' => (object) ['items' => []],
                '_links' => (object) [],
            ]);

        $mockCollection = new CustomCollection($this->client);
        $this->hydrator->expects($this->once())
            ->method('hydrateCollection')
            ->willReturn($mockCollection);

        $registry = ResourceRegistry::default();
        $registry->register(AnyResource::class, 'items');

        Config::setResourceRegistryResolver(fn () => $registry);
        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomCollection::class, $result);
    }

    /** @test */
    public function it_resolves_to_a_decorated_resource()
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

        $mockResource = new AnyResource($this->client);
        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->willReturn($mockResource);

        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $result);
    }

    /** @test */
    public function it_returns_response_when_no_resource_target()
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $response = $this->createMock(Response::class);

        $request->expects($this->once())
            ->method('getHydratableResource')
            ->willReturn('InvalidClass');

        $result = $this->resolver->resolve($request, $response);

        $this->assertSame($response, $result);
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
