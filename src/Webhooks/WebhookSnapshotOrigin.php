<?php

namespace Mollie\Api\Webhooks;

use DateTimeImmutable;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\ResourceOrigin;

/**
 * Provenance record for a resource hydrated from a signed webhook
 * envelope. Carries the event id, signature (when the caller retained
 * it from validation), and the timestamp at which the SDK received the
 * webhook. No HTTP response is involved.
 */
final class WebhookSnapshotOrigin implements ResourceOrigin
{
    private Connector $connector;

    private string $eventId;

    private ?string $signature;

    private DateTimeImmutable $receivedAt;

    public function __construct(
        Connector $connector,
        string $eventId,
        ?string $signature,
        DateTimeImmutable $receivedAt
    ) {
        $this->connector = $connector;
        $this->eventId = $eventId;
        $this->signature = $signature;
        $this->receivedAt = $receivedAt;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function getReceivedAt(): DateTimeImmutable
    {
        return $this->receivedAt;
    }
}
