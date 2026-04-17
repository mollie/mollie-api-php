<?php

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\WebhookEventMapper;
use Mollie\Api\Webhooks\WebhookSnapshotOrigin;
use PHPUnit\Framework\TestCase;

class WebhookEventMapperTest extends TestCase
{
    private WebhookEventMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new WebhookEventMapper();
    }

    /**
     * @test
     * @dataProvider valid_event_provider
     */
    public function process_valid_payloads(string $expectedClass): void
    {
        $simpleEventPayload = MockEvent::for($expectedClass, 'entity_test')
            ->simple()
            ->create();

        $fullEventPayload = MockEvent::for($expectedClass, 'entity_test')
            ->snapshot()
            ->create();

        $event = $this->mapper->processPayload($simpleEventPayload);

        $this->assertInstanceOf($expectedClass, $event);
        $this->assertEquals($simpleEventPayload['id'], $event->id);

        $event = $this->mapper->processPayload($fullEventPayload);

        $this->assertInstanceOf($expectedClass, $event);
        $this->assertEquals($fullEventPayload['id'], $event->id);
    }

    public function valid_event_provider(): array
    {
        return [
            'payment-link.paid' => [
                PaymentLinkPaid::class,
            ],
            'balance-transaction.created' => [
                BalanceTransactionCreated::class,
            ],
        ];
    }

    /** @test */
    public function process_unsupported_event_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->mapper->processPayload([
            'id' => 'whev_unsupported123',
            'type' => 'unsupported.event.type',
            'entityId' => 'test123',
            'createdAt' => '2023-12-25T10:30:54+00:00',
            '_links' => [],
        ]);
    }

    /** @test */
    public function process_payload_with_missing_required_fields(): void
    {
        $payload = [
            'id' => 'whev_test123',
            'type' => 'payment-link.paid',
            // Missing entityId and createdAt
            '_links' => [],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or empty required field: entityId');

        $this->mapper->processPayload($payload);
    }

    /** @test */
    public function process_payload_with_empty_required_fields(): void
    {
        $payload = [
            'id' => '',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_test123',
            'createdAt' => '2023-12-25T10:30:54+00:00',
            '_links' => [],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or empty required field: id');

        $this->mapper->processPayload($payload);
    }

    /** @test */
    public function process_payload_threads_signature_through_to_event(): void
    {
        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $before = new \DateTimeImmutable;
        $event = $this->mapper->processPayload($payload, 'sha256=abc123');
        $after = new \DateTimeImmutable;

        $this->assertSame('sha256=abc123', $event->signature);
        $this->assertGreaterThanOrEqual($before, $event->receivedAt);
        $this->assertLessThanOrEqual($after, $event->receivedAt);
    }

    /** @test */
    public function process_payload_defaults_signature_to_null(): void
    {
        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $event = $this->mapper->processPayload($payload);

        $this->assertNull($event->signature);
    }

    /** @test */
    public function as_entity_on_event_produces_rich_webhook_origin(): void
    {
        $client = new MockMollieClient;

        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $event = $this->mapper->processPayload($payload, 'sha256=sig');

        /** @var PaymentLink $resource */
        $resource = $event->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        $this->assertSame($event->id, $resource->getOrigin()->getEventId());
        $this->assertSame('sha256=sig', $resource->getOrigin()->getSignature());
        $this->assertSame($event->receivedAt, $resource->getOrigin()->getReceivedAt());
        $this->assertNull($resource->getResponse());
        $client->assertSentCount(0);
    }

    /** @test */
    public function mapper_bound_connector_enables_zero_arg_as_resource(): void
    {
        $client = new MockMollieClient;
        $mapper = new WebhookEventMapper([], $client);

        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $event = $mapper->processPayload($payload, 'sha256=sig');

        /** @var PaymentLink $resource */
        $resource = $event->asResource();

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        $this->assertSame($event->id, $resource->getOrigin()->getEventId());
        $this->assertSame('sha256=sig', $resource->getOrigin()->getSignature());
        $client->assertSentCount(0);
    }

    /** @test */
    public function explicit_connector_overrides_bound_connector(): void
    {
        $boundClient = new MockMollieClient;
        $otherClient = new MockMollieClient;
        $mapper = new WebhookEventMapper([], $boundClient);

        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $event = $mapper->processPayload($payload);

        /** @var PaymentLink $resource */
        $resource = $event->asResource($otherClient);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        // The origin carries the explicitly-passed connector, not the bound one.
        $this->assertSame($otherClient, $resource->getOrigin()->getConnector());
        $boundClient->assertSentCount(0);
        $otherClient->assertSentCount(0);
    }

    /** @test */
    public function zero_arg_as_resource_without_bound_connector_throws(): void
    {
        $payload = MockEvent::for(PaymentLinkPaid::class, 'pl_test123')
            ->snapshot()
            ->create();

        $event = $this->mapper->processPayload($payload);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No connector available to hydrate webhook resource.');

        $event->asResource();
    }

    /** @test */
    public function create_webhook_entity_from_payload_resolves_entity_key(): void
    {
        $payload = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => [
                'entity' => [
                    'id' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
                    'resource' => 'payment-link',
                    'mode' => 'live',
                ],
            ],
            '_links' => [],
        ];

        $event = $this->mapper->processPayload($payload);

        $this->assertNotNull($event->entity);
        $this->assertSame('pl_qng5gbbv8NAZ5gpM5ZYgx', $event->entity->getId());
        $this->assertSame('payment-link', $event->entity->getResourceType());
    }

    /** @test */
    public function create_webhook_entity_skips_non_entity_embedded_keys(): void
    {
        $payload = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => [
                'merchant_context' => ['locale' => 'en_US'],
                'entity' => [
                    'id' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
                    'resource' => 'payment-link',
                    'mode' => 'live',
                ],
            ],
            '_links' => [],
        ];

        $event = $this->mapper->processPayload($payload);

        $this->assertNotNull($event->entity);
        $this->assertSame('payment-link', $event->entity->getResourceType());
    }

    /**
     * @test
     * @dataProvider payloadsWithoutEmbeddedEntityProvider
     */
    public function create_webhook_entity_returns_null_when_no_entity_candidate(array $payload): void
    {
        $event = $this->mapper->processPayload($payload);

        $this->assertNull($event->entity);
    }

    public function payloadsWithoutEmbeddedEntityProvider(): array
    {
        $base = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_links' => [],
        ];

        return [
            'missing _embedded' => [$base],
            'non-array _embedded scalar' => [$base + ['_embedded' => 'not-an-array']],
            '_embedded entries without id/resource' => [
                $base + ['_embedded' => ['merchant_context' => ['locale' => 'en_US']]],
            ],
        ];
    }

    /** @test */
    public function as_resource_throws_when_event_has_no_embedded_entity(): void
    {
        $payload = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_links' => [],
        ];

        $event = $this->mapper->processPayload($payload);

        $this->assertNull($event->entity);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Event entity not found');

        $event->asResource(new MockMollieClient);
    }
}
