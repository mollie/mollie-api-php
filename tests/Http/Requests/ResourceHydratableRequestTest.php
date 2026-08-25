<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\ResourceWrapper;
use Mollie\Api\Resources\WrapperResource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResourceHydratableRequestTest extends TestCase
{
    #[Test]
    public function it_can_get_target_resource_class()
    {
        $request = new ConcreteResourceHydratableRequest;

        $this->assertEquals(BaseResource::class, $request->getHydratableResource());
    }

    #[Test]
    public function it_throws_exception_when_target_resource_class_is_not_set()
    {
        $request = new InvalidResourceHydratableRequest;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resource class is not set.');

        $request->getHydratableResource();
    }

    #[Test]
    public function it_can_hydrate_response_into_resource_wrapper()
    {
        $request = new class extends ResourceHydratableRequest {
            protected ?string $hydratableResource = DummyResource::class;

            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };

        // Set the wrapper as the hydratable resource
        $request->setHydratableResource(new WrapperResource(DummyResourceWrapper::class));

        // Assert the wrapper is set as the hydratable resource
        $this->assertInstanceOf(WrapperResource::class, $request->getHydratableResource());
        $this->assertSame(DummyResource::class, $request->getHydratableResourceTarget());
        $this->assertInstanceOf(WrapperResource::class, $request->getHydratableResourceWrapper());
        $this->assertTrue($request->isHydratable());
    }

    #[Test]
    public function string_targets_replace_the_target_without_clearing_the_wrapper()
    {
        $request = new ConcreteResourceHydratableRequest;
        $wrapper = new WrapperResource(DummyResourceWrapper::class);

        $request->setHydratableResource($wrapper);
        $request->setHydratableResource(DummyResource::class);

        $this->assertSame(DummyResource::class, $request->getHydratableResourceTarget());
        $this->assertSame($wrapper, $request->getHydratableResourceWrapper());
        $this->assertSame($wrapper, $request->getHydratableResource());
    }

    #[Test]
    public function reset_is_a_wrapper_clear_compatibility_alias()
    {
        $request = new ConcreteResourceHydratableRequest;
        $request->setHydratableResource(new WrapperResource(DummyResourceWrapper::class));

        $request->resetHydratableResource();

        $this->assertNull($request->getHydratableResourceWrapper());
        $this->assertSame(BaseResource::class, $request->getHydratableResourceTarget());
        $this->assertSame(BaseResource::class, $request->getHydratableResource());
    }

    #[Test]
    public function it_rejects_an_invalid_string_target()
    {
        $request = new InvalidResourceHydratableRequest;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Hydratable resource class 'InvalidClass' must extend ".BaseResource::class.' or '.ResourceCollection::class.'.'
        );

        $request->setHydratableResource('InvalidClass');
    }

    #[Test]
    public function it_rejects_a_direct_base_collection_subclass()
    {
        $request = new InvalidResourceHydratableRequest;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Hydratable resource class \'Tests\\Http\\Requests\\DirectBaseCollection\' must extend '.BaseResource::class.' or '.ResourceCollection::class.'.'
        );

        $request->setHydratableResource(DirectBaseCollection::class);
    }
}

class DirectBaseCollection extends BaseCollection
{
}

class ConcreteResourceHydratableRequest extends ResourceHydratableRequest
{
    protected ?string $hydratableResource = BaseResource::class;

    public function resolveResourcePath(): string
    {
        return 'test';
    }
}

class InvalidResourceHydratableRequest extends ResourceHydratableRequest
{
    public function resolveResourcePath(): string
    {
        return 'test';
    }
}

class DummyResource extends BaseResource
{
    public $id;

    public $name;
}

class DummyResourceWrapper extends ResourceWrapper
{
    public static function fromResource($resource): self
    {
        return (new self)->wrap($resource);
    }
}
