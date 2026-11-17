<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks;

use DateTimeImmutable;
use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;
use Mollie\Api\Webhooks\Events\BaseEvent;
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

class WebhookEventMapper
{

    /** @var array<string, class-string<BaseEvent>> */
    private array $map;

    public function __construct(array $map = [])
    {
        $this->setup($map);
    }

    /**
     * Process incoming webhook payload and create the appropriate event handler.
     *
     * @param array|string $payload The raw webhook payload from POST request
     * @param string|null $signature Optional X-Mollie-Signature header value,
     *        threaded through to the resulting event so downstream code can
     *        inspect webhook provenance without re-validating.
     * @return BaseEvent The specific event handler
     * @throws \InvalidArgumentException If payload is invalid
     */
    public function processPayload($payload, ?string $signature = null): BaseEvent
    {
        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        $this->validatePayload($payload);

        return $this->resolveEvent(
            Arr::get($payload, 'id'),
            Arr::get($payload, 'type'),
            Arr::get($payload, 'entityId'),
            Utility::transform(Arr::get($payload, 'createdAt'), fn (string $timestamp) => new DateTime($timestamp), DateTime::class),
            (object) (Arr::get($payload, '_links') ?? []),
            $this->createWebhookEntityFromPayload($payload),
            $signature,
            new DateTimeImmutable
        );
    }

    /**
     * Register or override a mapping for a given type string.
     */
    public function register(string $type, string $handlerClass): void
    {
        $this->map[$type] = $handlerClass;
    }

    /**
     * Resolve the concrete event handler for a webhook event.
     *
     * @return BaseEvent
     */
    public function resolveEvent(
        string $id,
        string $type,
        string $entityId,
        DateTime $createdAt,
        object $links,
        ?WebhookEntity $entity = null,
        ?string $signature = null,
        ?DateTimeImmutable $receivedAt = null
    ): BaseEvent {
        if (! Arr::exists($this->map, $type)) {
            throw new \InvalidArgumentException("Unsupported event type: {$type}");
        }

        /** @var class-string<BaseEvent> $class */
        $class = Arr::get($this->map, $type);

        return new $class(
            $id,
            $entityId,
            $createdAt,
            $links,
            $entity,
            $signature,
            $receivedAt ?? new DateTimeImmutable
        );
    }

    private function setup(array $map): void
    {
        $this->map = array_merge([
            PaymentLinkPaid::type() => PaymentLinkPaid::class,
            BalanceTransactionCreated::type() => BalanceTransactionCreated::class,
            PayoutInitiated::type() => PayoutInitiated::class,
            PayoutProcessingAtBank::type() => PayoutProcessingAtBank::class,
            PayoutCompleted::type() => PayoutCompleted::class,
            PayoutCanceled::type() => PayoutCanceled::class,
            PayoutFailed::type() => PayoutFailed::class,
            ConnectBalanceTransferFailed::type() => ConnectBalanceTransferFailed::class,
            ConnectBalanceTransferSucceeded::type() => ConnectBalanceTransferSucceeded::class,
            DisputeCreated::type() => DisputeCreated::class,
            DisputeResolved::type() => DisputeResolved::class,
            DisputeUpdated::type() => DisputeUpdated::class,
            FileAccepted::type() => FileAccepted::class,
            FileRejected::type() => FileRejected::class,
            FileFailed::type() => FileFailed::class,
            UnmatchedCreditTransferReceived::type() => UnmatchedCreditTransferReceived::class,
            UnmatchedCreditTransferMatched::type() => UnmatchedCreditTransferMatched::class,
            UnmatchedCreditTransferReturned::type() => UnmatchedCreditTransferReturned::class,
            UnmatchedCreditTransferExpired::type() => UnmatchedCreditTransferExpired::class,
            BusinessAccountTransferRequested::type() => BusinessAccountTransferRequested::class,
            BusinessAccountTransferInitiated::type() => BusinessAccountTransferInitiated::class,
            BusinessAccountTransferPendingReview::type() => BusinessAccountTransferPendingReview::class,
            BusinessAccountTransferProcessed::type() => BusinessAccountTransferProcessed::class,
            BusinessAccountTransferFailed::type() => BusinessAccountTransferFailed::class,
            BusinessAccountTransferBlocked::type() => BusinessAccountTransferBlocked::class,
            BusinessAccountTransferReturned::type() => BusinessAccountTransferReturned::class,
            SalesInvoiceCreated::type() => SalesInvoiceCreated::class,
            SalesInvoiceIssued::type() => SalesInvoiceIssued::class,
            SalesInvoiceCanceled::type() => SalesInvoiceCanceled::class,
            SalesInvoicePaid::type() => SalesInvoicePaid::class,
            ProfileCreated::type() => ProfileCreated::class,
            ProfileVerified::type() => ProfileVerified::class,
            ProfileBlocked::type() => ProfileBlocked::class,
            ProfileDeleted::type() => ProfileDeleted::class,
            PaymentPaid::type() => PaymentPaid::class,
            PaymentAuthorized::type() => PaymentAuthorized::class,
            PaymentFailed::type() => PaymentFailed::class,
            PaymentCanceled::type() => PaymentCanceled::class,
            PaymentExpired::type() => PaymentExpired::class,
            PaymentPending::type() => PaymentPending::class,
            RefundQueued::type() => RefundQueued::class,
            RefundPending::type() => RefundPending::class,
            RefundProcessing::type() => RefundProcessing::class,
            RefundRefunded::type() => RefundRefunded::class,
            RefundFailed::type() => RefundFailed::class,
            RefundCanceled::type() => RefundCanceled::class,
            ChargebackReceived::type() => ChargebackReceived::class,
            ChargebackReversed::type() => ChargebackReversed::class,
            CaptureSucceeded::type() => CaptureSucceeded::class,
            CaptureFailed::type() => CaptureFailed::class,
            SalesInvoiceEInvoiceIssued::type() => SalesInvoiceEInvoiceIssued::class,
            SalesInvoiceEInvoiceFailed::type() => SalesInvoiceEInvoiceFailed::class,
            BusinessAccountDraftTransferCreated::type() => BusinessAccountDraftTransferCreated::class,
            BusinessAccountDraftTransferApproved::type() => BusinessAccountDraftTransferApproved::class,
            BusinessAccountDraftTransferDeclined::type() => BusinessAccountDraftTransferDeclined::class,
            BusinessAccountCreated::type() => BusinessAccountCreated::class,
            UnmatchedCreditTransferMatchCompleted::type() => UnmatchedCreditTransferMatchCompleted::class,
            UnmatchedCreditTransferMatchFailed::type() => UnmatchedCreditTransferMatchFailed::class,
            UnmatchedCreditTransferReturnCompleted::type() => UnmatchedCreditTransferReturnCompleted::class,
            UnmatchedCreditTransferReturnFailed::type() => UnmatchedCreditTransferReturnFailed::class,
            ReportCompleted::type() => ReportCompleted::class,
            ReportFailed::type() => ReportFailed::class,
        ], $map);
    }

    /**
     * Validate that the payload contains required fields.
     *
     * @param array $payload
     * @throws \InvalidArgumentException
     */
    private function validatePayload(array $payload): void
    {
        $requiredFields = ['id', 'type', 'entityId', 'createdAt'];

        foreach ($requiredFields as $field) {
            if (! Arr::exists($payload, $field) || empty(Arr::get($payload, $field))) {
                throw new \InvalidArgumentException("Missing or empty required field: {$field}");
            }
        }
    }

    /**
     * Create a webhook entity from the embedded data in the payload.
     *
     * Mollie keys the embedded resource under `_embedded.entity`.
     * Iteration is key-agnostic so any future schema tweak (additional
     * `_embedded` sub-blocks, renamed key) cannot silently break
     * webhook handling; only candidates carrying `id` and `resource`
     * are eligible.
     *
     * @param array $payload
     * @return WebhookEntity|null
     */
    private function createWebhookEntityFromPayload(array $payload): ?WebhookEntity
    {
        $embedded = Arr::get($payload, '_embedded', []);

        if (! is_array($embedded)) {
            return null;
        }

        foreach ($embedded as $candidate) {
            if (is_array($candidate) && isset($candidate['id'], $candidate['resource'])) {
                return WebhookEntity::create($candidate);
            }
        }

        return null;
    }
}
