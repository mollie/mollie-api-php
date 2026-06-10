<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\ResourceRegistry;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

class ResourceRegistryTest extends TestCase
{
    /** @test */
    public function resolves_default_resources_by_type(): void
    {
        $registry = new ResourceRegistry();

        $this->assertSame(Payment::class, $registry->for('payment'));
        $this->assertSame(Payment::class, $registry->for('payments'));

        // underscore input gets normalized to kebab internally
        $this->assertSame(PaymentLink::class, $registry->for('payment_link'));
        $this->assertSame(PaymentLink::class, $registry->for('payment-link'));
    }

    /** @test */
    public function returns_names_for_resource(): void
    {
        $registry = new ResourceRegistry();

        $this->assertSame('payment', $registry->singularOf(Payment::class));
        $this->assertSame('payments', $registry->pluralOf(Payment::class));
    }

    /** @test */
    public function can_register_custom_resource_with_overrides(): void
    {
        $registry = new ResourceRegistry([]);

        $class = \Mollie\Api\Resources\Customer::class;

        $registry->register($class, 'clients', 'client');

        $this->assertSame($class, $registry->for('client'));
        $this->assertSame($class, $registry->for('clients'));

        $this->assertSame('client', $registry->singularOf($class));
        $this->assertSame('clients', $registry->pluralOf($class));
    }

    /** @test */
    public function unknown_type_returns_null(): void
    {
        $registry = new ResourceRegistry();
        $this->assertNull($registry->for('non-existent-type'));
    }

    /** @test */
    public function default_registry_covers_concrete_api_resources(): void
    {
        $registry = new ResourceRegistry();

        foreach ($this->concreteApiResources() as $resourceClass) {
            $this->assertTrue(
                $registry->isRegistered($resourceClass),
                "{$resourceClass} is missing from ResourceRegistry defaults"
            );
        }
    }

    /**
     * @return list<class-string<BaseResource>>
     */
    private function concreteApiResources(): array
    {
        $resources = [];

        foreach (glob(__DIR__.'/../../src/Resources/*.php') ?: [] as $path) {
            $class = 'Mollie\\Api\\Resources\\'.basename($path, '.php');

            if (! is_subclass_of($class, BaseResource::class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }

            if (in_array($class, [AnyResource::class, CurrentProfile::class], true)) {
                continue;
            }

            $resources[] = $class;
        }

        sort($resources);

        return $resources;
    }
}
