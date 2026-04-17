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
        $resource = $event->asEntity($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        $this->assertSame($event->id, $resource->getOrigin()->getEventId());
        $this->assertSame('sha256=sig', $resource->getOrigin()->getSignature());
        $this->assertSame($event->receivedAt, $resource->getOrigin()->getReceivedAt());
        $this->assertNull($resource->getResponse());
        $client->assertSentCount(0);
    }

    /** @test */
    public function create_webhook_entity_from_payload_resolves_entity_with_resource_type_key(): void
    {
        // Real Mollie webhook POST shape uses _embedded["payment-link"],
        // not _embedded.entity. The mapper must iterate _embedded keys.
        $payload = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => [
                'payment-link' => [
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
        // Future-proof: extra blocks under _embedded that lack id+resource
        // fields must not be picked up as the entity.
        $payload = [
            'id' => 'event_abc',
            'type' => 'payment-link.paid',
            'entityId' => 'pl_qng5gbbv8NAZ5gpM5ZYgx',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => [
                'merchant_context' => ['locale' => 'en_US'],
                'payment-link' => [
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
}
