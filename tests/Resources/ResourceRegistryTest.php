<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Config;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\ResourceRegistry;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Resources\WebhookCollection;
use Mollie\Api\Webhooks\WebhookEntity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ResourceRegistryTest extends TestCase
{
    protected function tearDown(): void
    {
        Config::setResourceRegistryResolver(null);

        parent::tearDown();
    }

    #[Test]
    public function resolves_default_resources_by_type(): void
    {
        $registry = new ResourceRegistry;

        $this->assertSame(Payment::class, $registry->for('payment'));
        $this->assertSame(Payment::class, $registry->for('payments'));

        // underscore input gets normalized to kebab internally
        $this->assertSame(PaymentLink::class, $registry->for('payment_link'));
        $this->assertSame(PaymentLink::class, $registry->for('payment-link'));
    }

    #[Test]
    public function returns_names_for_resource(): void
    {
        $registry = new ResourceRegistry;

        $this->assertSame('payment', $registry->singularOf(Payment::class));
        $this->assertSame('payments', $registry->pluralOf(Payment::class));
    }

    #[Test]
    public function can_register_custom_resource_with_overrides(): void
    {
        $registry = new ResourceRegistry([]);

        $class = Customer::class;

        $registry->register($class, 'buyers', 'buyer');

        $this->assertSame($class, $registry->for('buyer'));
        $this->assertSame($class, $registry->for('buyers'));

        $this->assertSame('buyer', $registry->singularOf($class));
        $this->assertSame('buyers', $registry->pluralOf($class));
    }

    #[Test]
    public function re_registering_a_resource_replaces_its_names(): void
    {
        $registry = new ResourceRegistry;

        $registry->register(Payment::class, 'transactions', 'transaction');

        $this->assertNull($registry->for('payment'));
        $this->assertNull($registry->for('payments'));
        $this->assertSame(Payment::class, $registry->for('transaction'));
        $this->assertSame(Payment::class, $registry->for('transactions'));
        $this->assertSame('transaction', $registry->singularOf(Payment::class));
        $this->assertSame('transactions', $registry->pluralOf(Payment::class));
    }

    #[Test]
    public function singular_collision_is_rejected_atomically(): void
    {
        $registry = new ResourceRegistry;

        try {
            $registry->register(Customer::class, 'buyers', 'payment');
            $this->fail('Expected a singular type collision.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(
                "Resource type 'payment' is already registered to ".Payment::class.'.',
                $exception->getMessage()
            );
        }

        $this->assertDefaultPaymentAndCustomerMappings($registry);
        $this->assertNull($registry->for('buyer'));
        $this->assertNull($registry->for('buyers'));
    }

    #[Test]
    public function plural_collision_is_rejected_atomically(): void
    {
        $registry = new ResourceRegistry;

        try {
            $registry->register(Customer::class, 'payments', 'buyer');
            $this->fail('Expected a plural type collision.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(
                "Resource type 'payments' is already registered to ".Payment::class.'.',
                $exception->getMessage()
            );
        }

        $this->assertDefaultPaymentAndCustomerMappings($registry);
        $this->assertNull($registry->for('buyer'));
    }

    #[Test]
    public function normalized_collision_is_rejected_atomically(): void
    {
        $registry = new ResourceRegistry;

        try {
            $registry->register(Customer::class, 'buyers', 'payment_link');
            $this->fail('Expected a normalized type collision.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(
                "Resource type 'payment-link' is already registered to ".PaymentLink::class.'.',
                $exception->getMessage()
            );
        }

        $this->assertDefaultPaymentAndCustomerMappings($registry);
        $this->assertSame(PaymentLink::class, $registry->for('payment_link'));
        $this->assertSame(PaymentLink::class, $registry->for('payment-links'));
        $this->assertNull($registry->for('buyers'));
    }

    #[Test]
    public function singular_and_plural_may_be_identical_for_one_resource(): void
    {
        $registry = new ResourceRegistry;

        $registry->register(Customer::class, 'customer-record', 'customer-record');

        $this->assertSame(Customer::class, $registry->for('customer_record'));
        $this->assertSame('customer-record', $registry->singularOf(Customer::class));
        $this->assertSame('customer-record', $registry->pluralOf(Customer::class));
        $this->assertNull($registry->for('customer'));
        $this->assertNull($registry->for('customers'));
    }

    #[Test]
    public function custom_webhook_and_collection_lookups_share_the_same_mapping(): void
    {
        $registry = new ResourceRegistry;
        $registry->register(Webhook::class, 'webhook-forwards', 'webhook-forward');
        Config::setResourceRegistryResolver(fn () => $registry);

        $entity = WebhookEntity::create([
            'id' => 'hook_123',
            'resource' => 'webhook_forward',
        ]);

        $this->assertInstanceOf(Webhook::class, $entity->asResource(new MockMollieClient));
        $this->assertSame(Webhook::class, $registry->for('webhook-forwards'));
        $this->assertSame('webhook-forwards', $registry->pluralOf(WebhookCollection::getResourceClass()));
    }

    #[Test]
    public function unknown_type_returns_null(): void
    {
        $registry = new ResourceRegistry;
        $this->assertNull($registry->for('non-existent-type'));
    }

    #[Test]
    public function default_registry_covers_concrete_api_resources(): void
    {
        $registry = new ResourceRegistry;

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

    private function assertDefaultPaymentAndCustomerMappings(ResourceRegistry $registry): void
    {
        $this->assertSame(Payment::class, $registry->for('payment'));
        $this->assertSame(Payment::class, $registry->for('payments'));
        $this->assertSame('payment', $registry->singularOf(Payment::class));
        $this->assertSame('payments', $registry->pluralOf(Payment::class));
        $this->assertSame(Customer::class, $registry->for('customer'));
        $this->assertSame(Customer::class, $registry->for('customers'));
        $this->assertSame('customer', $registry->singularOf(Customer::class));
        $this->assertSame('customers', $registry->pluralOf(Customer::class));
    }
}
