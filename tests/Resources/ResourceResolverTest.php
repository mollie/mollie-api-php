<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Config;
use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\ResourceRegistry;
use Mollie\Api\Resources\ResourceResolver;
use Mollie\Api\Resources\WrapperResource;
use Mollie\Api\Traits\IsIteratableRequest;
use PHPUnit\Framework\Attributes\Test;
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

    protected function tearDown(): void
    {
        Config::setResourceRegistryResolver(null);

        parent::tearDown();
    }

    #[Test]
    public function it_resolves_to_a_simple_resource()
    {
        $request = (new DynamicGetRequest(''))->setHydratableResource(AnyResource::class);
        $response = $this->createMock(Response::class);

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

    #[Test]
    public function it_resolves_to_a_collection()
    {
        $request = (new DynamicGetRequest(''))->setHydratableResource(CustomCollection::class);
        $response = $this->createMock(Response::class);

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

        $registry = new ResourceRegistry;
        $registry->register(AnyResource::class, 'items');

        Config::setResourceRegistryResolver(fn () => $registry);
        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomCollection::class, $result);
    }

    #[Test]
    public function it_resolves_to_a_decorated_resource()
    {
        $request = new class extends ResourceHydratableRequest {
            protected ?string $hydratableResource = AnyResource::class;

            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };
        $response = $this->createMock(Response::class);
        $wrapper = new WrapperResource(CustomDecorator::class);
        $request->setHydratableResource($wrapper);

        $response->expects($this->once())
            ->method('getConnector')
            ->willReturn($this->client);

        $mockResource = new AnyResource($this->client);
        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->willReturn($mockResource);

        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $result);
        $this->assertSame($mockResource, $result->wrapped);
        $this->assertSame(AnyResource::class, $request->getHydratableResourceTarget());
        $this->assertSame($wrapper, $request->getHydratableResourceWrapper());
    }

    #[Test]
    public function it_decorates_the_raw_response_when_no_resource_target_is_declared()
    {
        $request = new class extends ResourceHydratableRequest {
            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };
        $response = $this->createMock(Response::class);
        $request->setHydratableResource(new WrapperResource(CustomDecorator::class));

        $result = $this->resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $result);
        $this->assertSame($response, $result->wrapped);
    }

    #[Test]
    public function it_returns_the_raw_response_when_target_and_wrapper_are_null()
    {
        $request = new class extends ResourceHydratableRequest {
            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };
        $response = $this->createMock(Response::class);

        $result = $this->resolver->resolve($request, $response);

        $this->assertSame($response, $result);
    }

    #[Test]
    public function it_rejects_an_invalid_declared_target_at_the_resolver_boundary()
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $request->method('getHydratableResourceTarget')->willReturn('InvalidClass');
        $request->method('getHydratableResourceWrapper')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Hydratable resource class 'InvalidClass' must extend Mollie\\Api\\Resources\\BaseResource or ".ResourceCollection::class.'.'
        );

        $this->resolver->resolve($request, $this->createMock(Response::class));
    }

    #[Test]
    public function it_rejects_a_direct_base_collection_subclass_at_the_resolver_boundary()
    {
        $request = $this->createMock(ResourceHydratableRequest::class);
        $request->method('getHydratableResourceTarget')->willReturn(DirectBaseCollection::class);
        $request->method('getHydratableResourceWrapper')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Hydratable resource class \'Tests\\Resources\\DirectBaseCollection\' must extend Mollie\\Api\\Resources\\BaseResource or '.ResourceCollection::class.'.'
        );

        $this->resolver->resolve($request, $this->createMock(Response::class));
    }

    #[Test]
    public function repeated_resolution_is_decorated_and_non_mutating()
    {
        $hydrator = $this->createMock(ResourceHydrator::class);
        $resolver = new ResourceResolver($hydrator);
        $request = new class extends ResourceHydratableRequest {
            protected ?string $hydratableResource = AnyResource::class;

            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };
        $wrapper = new WrapperResource(CustomDecorator::class);
        $request->setHydratableResource($wrapper);
        $response = $this->createMock(Response::class);
        $response->expects($this->exactly(2))->method('getConnector')->willReturn($this->client);
        $response->expects($this->exactly(2))->method('json')->willReturn((object) ['id' => 'test_123']);
        $hydrator->expects($this->exactly(2))
            ->method('hydrate')
            ->willReturnCallback(fn ($resource) => $resource);

        $first = $resolver->resolve($request, $response);
        $second = $resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $first);
        $this->assertInstanceOf(CustomDecorator::class, $second);
        $this->assertSame(AnyResource::class, $request->getHydratableResourceTarget());
        $this->assertSame($wrapper, $request->getHydratableResourceWrapper());
    }

    #[Test]
    public function it_decorates_the_resolved_collection()
    {
        $hydrator = $this->createMock(ResourceHydrator::class);
        $resolver = new ResourceResolver($hydrator);
        $request = (new DynamicGetRequest(''))
            ->setHydratableResource(CustomCollection::class)
            ->setHydratableResource(new WrapperResource(CustomDecorator::class));
        $response = $this->collectionResponse();
        $collection = new CustomCollection($this->client);
        $hydrator->expects($this->once())->method('hydrateCollection')->willReturn($collection);
        $this->registerCustomCollection();

        $result = $resolver->resolve($request, $response);

        $this->assertInstanceOf(CustomDecorator::class, $result);
        $this->assertSame($collection, $result->wrapped);
    }

    #[Test]
    public function it_decorates_forward_and_reverse_lazy_iterator_results()
    {
        $this->registerCustomCollection();

        foreach ([false, true] as $iterateBackwards) {
            $hydrator = $this->createMock(ResourceHydrator::class);
            $resolver = new ResourceResolver($hydrator);
            $lazyCollection = new LazyCollection(static function (): \Generator {
                yield from [];
            });
            $collection = new IteratorRecordingCollection($this->client);
            $collection->iteratorResult = $lazyCollection;
            $hydrator->expects($this->once())
                ->method('hydrateCollection')
                ->willReturn($collection);
            $request = new class extends ResourceHydratableRequest implements IsIteratable {
                use IsIteratableRequest;

                protected ?string $hydratableResource = IteratorRecordingCollection::class;

                public function resolveResourcePath(): string
                {
                    return 'test';
                }
            };
            $request->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->setHydratableResource(new WrapperResource(CustomDecorator::class));

            $result = $resolver->resolve($request, $this->collectionResponse());

            $this->assertInstanceOf(CustomDecorator::class, $result);
            $this->assertSame($lazyCollection, $result->wrapped);
            $this->assertSame($iterateBackwards, $collection->requestedDirection);
        }
    }

    private function collectionResponse(): Response
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getConnector')->willReturn($this->client);
        $response->expects($this->once())->method('json')->willReturn((object) [
            '_embedded' => (object) ['items' => []],
            '_links' => (object) [],
        ]);

        return $response;
    }

    private function registerCustomCollection(): void
    {
        $registry = new ResourceRegistry;
        $registry->register(AnyResource::class, 'items');
        Config::setResourceRegistryResolver(fn () => $registry);
    }
}

class DirectBaseCollection extends BaseCollection
{
}

class CustomCollection extends CursorCollection
{
    public static string $resource = AnyResource::class;

    public static string $collectionName = 'items';
}

class IteratorRecordingCollection extends CursorCollection
{
    public static string $resource = AnyResource::class;

    public ?bool $requestedDirection = null;

    public LazyCollection $iteratorResult;

    public function getAutoIterator(bool $iterateBackwards = false): LazyCollection
    {
        $this->requestedDirection = $iterateBackwards;

        return $this->iteratorResult;
    }
}

class CustomDecorator implements IsWrapper
{
    public $wrapped;

    public static function fromResource($resource): self
    {
        $decorator = new self;
        $decorator->wrapped = $resource;

        return $decorator;
    }
}
