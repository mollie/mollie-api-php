<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;
use Mollie\Api\Webhooks\Events\BaseEvent;
use Mollie\Api\Webhooks\Events\ConnectBalanceTransferFailed;
use Mollie\Api\Webhooks\Events\ConnectBalanceTransferSucceeded;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\Events\SalesInvoiceCanceled;
use Mollie\Api\Webhooks\Events\SalesInvoiceCreated;
use Mollie\Api\Webhooks\Events\SalesInvoiceIssued;
use Mollie\Api\Webhooks\Events\SalesInvoicePaid;

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
     * @return BaseEvent The specific event handler
     * @throws \InvalidArgumentException If payload is invalid
     */
    public function processPayload($payload): BaseEvent
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
            $this->createWebhookEntityFromPayload($payload)
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
        ?WebhookEntity $entity = null
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
            $entity
        );
    }

    private function setup(array $map): void
    {
        $this->map = array_merge([
            PaymentLinkPaid::type() => PaymentLinkPaid::class,
            BalanceTransactionCreated::type() => BalanceTransactionCreated::class,
            ConnectBalanceTransferFailed::type() => ConnectBalanceTransferFailed::class,
            ConnectBalanceTransferSucceeded::type() => ConnectBalanceTransferSucceeded::class,
            SalesInvoiceCreated::type() => SalesInvoiceCreated::class,
            SalesInvoiceIssued::type() => SalesInvoiceIssued::class,
            SalesInvoiceCanceled::type() => SalesInvoiceCanceled::class,
            SalesInvoicePaid::type() => SalesInvoicePaid::class,
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
     * @param array $payload
     * @return WebhookEntity|null
     */
    private function createWebhookEntityFromPayload(array $payload): ?WebhookEntity
    {
        $embedded = Arr::get($payload, '_embedded', []);
        $entityData = array_pop($embedded);

        if (! $entityData) {
            return null;
        }

        $entity = WebhookEntity::create($entityData);

        return $entity;
    }
}
