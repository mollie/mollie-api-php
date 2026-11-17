<?php

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;
use Mollie\Api\Webhooks\Events\BusinessAccountCreated;
use Mollie\Api\Webhooks\Events\BusinessAccountDraftTransferApproved;
use Mollie\Api\Webhooks\Events\BusinessAccountDraftTransferCreated;
use Mollie\Api\Webhooks\Events\BusinessAccountDraftTransferDeclined;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferBlocked;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferFailed;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferInitiated;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferPendingReview;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferProcessed;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferRequested;
use Mollie\Api\Webhooks\Events\BusinessAccountTransferReturned;
use Mollie\Api\Webhooks\Events\CaptureFailed;
use Mollie\Api\Webhooks\Events\CaptureSucceeded;
use Mollie\Api\Webhooks\Events\ChargebackReceived;
use Mollie\Api\Webhooks\Events\ChargebackReversed;
use Mollie\Api\Webhooks\Events\ConnectBalanceTransferFailed;
use Mollie\Api\Webhooks\Events\ConnectBalanceTransferSucceeded;
use Mollie\Api\Webhooks\Events\DisputeCreated;
use Mollie\Api\Webhooks\Events\DisputeResolved;
use Mollie\Api\Webhooks\Events\DisputeUpdated;
use Mollie\Api\Webhooks\Events\FileAccepted;
use Mollie\Api\Webhooks\Events\FileFailed;
use Mollie\Api\Webhooks\Events\FileRejected;
use Mollie\Api\Webhooks\Events\PaymentAuthorized;
use Mollie\Api\Webhooks\Events\PaymentCanceled;
use Mollie\Api\Webhooks\Events\PaymentExpired;
use Mollie\Api\Webhooks\Events\PaymentFailed;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\Events\PaymentPaid;
use Mollie\Api\Webhooks\Events\PaymentPending;
use Mollie\Api\Webhooks\Events\PayoutCanceled;
use Mollie\Api\Webhooks\Events\PayoutCompleted;
use Mollie\Api\Webhooks\Events\PayoutFailed;
use Mollie\Api\Webhooks\Events\PayoutInitiated;
use Mollie\Api\Webhooks\Events\PayoutProcessingAtBank;
use Mollie\Api\Webhooks\Events\ProfileBlocked;
use Mollie\Api\Webhooks\Events\ProfileCreated;
use Mollie\Api\Webhooks\Events\ProfileDeleted;
use Mollie\Api\Webhooks\Events\ProfileVerified;
use Mollie\Api\Webhooks\Events\RefundCanceled;
use Mollie\Api\Webhooks\Events\RefundFailed;
use Mollie\Api\Webhooks\Events\RefundPending;
use Mollie\Api\Webhooks\Events\RefundProcessing;
use Mollie\Api\Webhooks\Events\RefundQueued;
use Mollie\Api\Webhooks\Events\RefundRefunded;
use Mollie\Api\Webhooks\Events\ReportCompleted;
use Mollie\Api\Webhooks\Events\ReportFailed;
use Mollie\Api\Webhooks\Events\SalesInvoiceCanceled;
use Mollie\Api\Webhooks\Events\SalesInvoiceCreated;
use Mollie\Api\Webhooks\Events\SalesInvoiceEInvoiceFailed;
use Mollie\Api\Webhooks\Events\SalesInvoiceEInvoiceIssued;
use Mollie\Api\Webhooks\Events\SalesInvoiceIssued;
use Mollie\Api\Webhooks\Events\SalesInvoicePaid;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferExpired;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferMatchCompleted;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferMatched;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferMatchFailed;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferReceived;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferReturnCompleted;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferReturned;
use Mollie\Api\Webhooks\Events\UnmatchedCreditTransferReturnFailed;
use Mollie\Api\Webhooks\WebhookEventMapper;
use Mollie\Api\Webhooks\WebhookEventType;
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

    /** @test */
    public function every_documented_event_type_resolves_to_its_event_class()
    {
        foreach (WebhookEventType::getAll() as $type) {
            if ($type === WebhookEventType::ALL) {
                continue;
            }

            $event = $this->mapper->resolveEvent('event_test', $type, 'entity_test', new DateTime('2026-01-01T00:00:00+00:00'), (object) []);

            $this->assertSame($type, $event::type());
        }
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
            'payout.initiated' => [
                PayoutInitiated::class,
            ],
            'payout.processing-at-bank' => [
                PayoutProcessingAtBank::class,
            ],
            'payout.completed' => [
                PayoutCompleted::class,
            ],
            'payout.canceled' => [
                PayoutCanceled::class,
            ],
            'payout.failed' => [
                PayoutFailed::class,
            ],
            'business-account-transfer.requested' => [
                BusinessAccountTransferRequested::class,
            ],
            'business-account-transfer.initiated' => [
                BusinessAccountTransferInitiated::class,
            ],
            'business-account-transfer.pending-review' => [
                BusinessAccountTransferPendingReview::class,
            ],
            'business-account-transfer.processed' => [
                BusinessAccountTransferProcessed::class,
            ],
            'business-account-transfer.failed' => [
                BusinessAccountTransferFailed::class,
            ],
            'business-account-transfer.blocked' => [
                BusinessAccountTransferBlocked::class,
            ],
            'business-account-transfer.returned' => [
                BusinessAccountTransferReturned::class,
            ],
            'connect-balance-transfer.succeeded' => [
                ConnectBalanceTransferSucceeded::class,
            ],
            'connect-balance-transfer.failed' => [
                ConnectBalanceTransferFailed::class,
            ],
            'sales-invoice.created' => [
                SalesInvoiceCreated::class,
            ],
            'sales-invoice.issued' => [
                SalesInvoiceIssued::class,
            ],
            'sales-invoice.canceled' => [
                SalesInvoiceCanceled::class,
            ],
            'sales-invoice.paid' => [
                SalesInvoicePaid::class,
            ],
            'dispute.created' => [
                DisputeCreated::class,
            ],
            'dispute.resolved' => [
                DisputeResolved::class,
            ],
            'dispute.updated' => [
                DisputeUpdated::class,
            ],
            'file.accepted' => [
                FileAccepted::class,
            ],
            'file.rejected' => [
                FileRejected::class,
            ],
            'file.failed' => [
                FileFailed::class,
            ],
            'unmatched-credit-transfer.received' => [
                UnmatchedCreditTransferReceived::class,
            ],
            'unmatched-credit-transfer.matched' => [
                UnmatchedCreditTransferMatched::class,
            ],
            'unmatched-credit-transfer.returned' => [
                UnmatchedCreditTransferReturned::class,
            ],
            'unmatched-credit-transfer.expired' => [
                UnmatchedCreditTransferExpired::class,
            ],
            'payment.paid' => [
                PaymentPaid::class,
            ],
            'payment.authorized' => [
                PaymentAuthorized::class,
            ],
            'payment.failed' => [
                PaymentFailed::class,
            ],
            'payment.canceled' => [
                PaymentCanceled::class,
            ],
            'payment.expired' => [
                PaymentExpired::class,
            ],
            'payment.pending' => [
                PaymentPending::class,
            ],
            'refund.queued' => [
                RefundQueued::class,
            ],
            'refund.pending' => [
                RefundPending::class,
            ],
            'refund.processing' => [
                RefundProcessing::class,
            ],
            'refund.refunded' => [
                RefundRefunded::class,
            ],
            'refund.failed' => [
                RefundFailed::class,
            ],
            'refund.canceled' => [
                RefundCanceled::class,
            ],
            'chargeback.received' => [
                ChargebackReceived::class,
            ],
            'chargeback.reversed' => [
                ChargebackReversed::class,
            ],
            'capture.succeeded' => [
                CaptureSucceeded::class,
            ],
            'capture.failed' => [
                CaptureFailed::class,
            ],
            'sales-invoice.e-invoice-issued' => [
                SalesInvoiceEInvoiceIssued::class,
            ],
            'sales-invoice.e-invoice-failed' => [
                SalesInvoiceEInvoiceFailed::class,
            ],
            'business-account-draft-transfer.created' => [
                BusinessAccountDraftTransferCreated::class,
            ],
            'business-account-draft-transfer.approved' => [
                BusinessAccountDraftTransferApproved::class,
            ],
            'business-account-draft-transfer.declined' => [
                BusinessAccountDraftTransferDeclined::class,
            ],
            'business-account.created' => [
                BusinessAccountCreated::class,
            ],
            'unmatched-credit-transfer.match.completed' => [
                UnmatchedCreditTransferMatchCompleted::class,
            ],
            'unmatched-credit-transfer.match.failed' => [
                UnmatchedCreditTransferMatchFailed::class,
            ],
            'unmatched-credit-transfer.return.completed' => [
                UnmatchedCreditTransferReturnCompleted::class,
            ],
            'unmatched-credit-transfer.return.failed' => [
                UnmatchedCreditTransferReturnFailed::class,
            ],
            'report.completed' => [
                ReportCompleted::class,
            ],
            'report.failed' => [
                ReportFailed::class,
            ],
            'profile.created' => [
                ProfileCreated::class,
            ],
            'profile.verified' => [
                ProfileVerified::class,
            ],
            'profile.blocked' => [
                ProfileBlocked::class,
            ],
            'profile.deleted' => [
                ProfileDeleted::class,
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
