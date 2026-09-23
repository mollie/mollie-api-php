<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceCollection;
use Mollie\Api\Resources\ResourceWrapper;
use Mollie\Api\Resources\WrapperResource;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[Test]
    public function wrap_into_decorates_without_touching_the_canonical_target(): void
    {
        $request = new ConcreteResourceHydratableRequest;

        $returned = $request->wrapInto(DummyResourceWrapper::class);

        $this->assertSame($request, $returned);
        $this->assertSame(BaseResource::class, $request->getHydratableResourceTarget());
        $this->assertInstanceOf(WrapperResource::class, $request->getHydratableResourceWrapper());
        $this->assertSame(DummyResourceWrapper::class, $request->getHydratableResourceWrapper()->getWrapper());
    }

    #[Test]
    public function hydrate_into_replaces_the_canonical_target_and_keeps_the_wrapper(): void
    {
        $request = (new ConcreteResourceHydratableRequest)->wrapInto(DummyResourceWrapper::class);

        $returned = $request->hydrateInto(DummyResource::class);

        $this->assertSame($request, $returned);
        $this->assertSame(DummyResource::class, $request->getHydratableResourceTarget());
        $this->assertInstanceOf(WrapperResource::class, $request->getHydratableResourceWrapper());
    }

    /**
     * The target arrives as a plain string parameter on purpose: a literal such as
     * 'InvalidClass' passed straight to a class-string<T> parameter fails PHPStan level 5.
     */
    #[DataProvider('dpInvalidTargets')]
    #[Test]
    public function hydrate_into_validates_the_target_like_set_hydratable_resource(string $target): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Hydratable resource class '{$target}' must extend ".BaseResource::class.' or '.ResourceCollection::class.'.');

        (new InvalidResourceHydratableRequest)->hydrateInto($target);
    }

    public static function dpInvalidTargets(): array
    {
        return [
            'not a class' => ['InvalidClass'],
            'BaseCollection is not a ResourceCollection' => [DirectBaseCollection::class],
        ];
    }

    #[DataProvider('dpNonWrappers')]
    #[Test]
    public function wrap_into_rejects_classes_that_are_not_wrappers(string $wrapper): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The wrapper class '{$wrapper}' does not implement the IsWrapper interface.");

        (new ConcreteResourceHydratableRequest)->wrapInto($wrapper);
    }

    public static function dpNonWrappers(): array
    {
        return [
            'a resource, not a wrapper' => [DummyResource::class],
        ];
    }

    #[Test]
    public function the_old_overload_and_the_new_methods_write_the_same_state(): void
    {
        $old = (new ConcreteResourceHydratableRequest)
            ->setHydratableResource(DummyResource::class)
            ->setHydratableResource(new WrapperResource(DummyResourceWrapper::class));
        $new = (new ConcreteResourceHydratableRequest)
            ->hydrateInto(DummyResource::class)
            ->wrapInto(DummyResourceWrapper::class);

        $this->assertSame($old->getHydratableResourceTarget(), $new->getHydratableResourceTarget());
        $this->assertSame($old->getHydratableResourceWrapper()->getWrapper(), $new->getHydratableResourceWrapper()->getWrapper());
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
