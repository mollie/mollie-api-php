<?php

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\WebhookEventMapper;
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
}
