<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceWrapper;
use Mollie\Api\Resources\WrapperResource;
use PHPUnit\Framework\TestCase;

class ResourceHydratableRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_target_resource_class()
    {
        $request = new ConcreteResourceHydratableRequest;

        $this->assertEquals(BaseResource::class, $request->getHydratableResource());
    }

    /** @test */
    public function it_throws_exception_when_target_resource_class_is_not_set()
    {
        $request = new InvalidResourceHydratableRequest;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resource class is not set.');

        $request->getHydratableResource();
    }

    /** @test */
    public function it_can_hydrate_response_into_resource_wrapper()
    {
        $request = new class extends ResourceHydratableRequest {
            protected $hydratableResource = DummyResource::class;

            public function resolveResourcePath(): string
            {
                return 'test';
            }
        };

        // Set the wrapper as the hydratable resource
        $request->setHydratableResource(new WrapperResource(DummyResourceWrapper::class));

        // Assert the wrapper is set as the hydratable resource
        $this->assertInstanceOf(WrapperResource::class, $request->getHydratableResource());
        $this->assertTrue($request->isHydratable());
    }
}

class ConcreteResourceHydratableRequest extends ResourceHydratableRequest
{
    protected $hydratableResource = BaseResource::class;

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
