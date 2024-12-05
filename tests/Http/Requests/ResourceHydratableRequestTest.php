<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Resources\BaseResource;
use Tests\TestCase;

class ResourceHydratableRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_target_resource_class()
    {
        $request = new ConcreteResourceHydratableRequest;

        $this->assertEquals(BaseResource::class, $request->getTargetResourceClass());
    }

    /** @test */
    public function it_can_toggle_auto_hydration()
    {
        $request = new ConcreteResourceHydratableRequest;

        $this->assertFalse($request->shouldAutoHydrate());

        ConcreteResourceHydratableRequest::hydrate(true);
        $this->assertTrue($request->shouldAutoHydrate());

        ConcreteResourceHydratableRequest::hydrate(false);
        $this->assertFalse($request->shouldAutoHydrate());
    }

    /** @test */
    public function it_throws_exception_when_target_resource_class_is_not_set()
    {
        $request = new InvalidResourceHydratableRequest;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resource class is not set.');

        $request->getTargetResourceClass();
    }
}

class ConcreteResourceHydratableRequest extends ResourceHydratableRequest
{
    public static string $targetResourceClass = BaseResource::class;

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
