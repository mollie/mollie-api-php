<?php

namespace Mollie\Api\Webhooks\Events;

use DateTimeImmutable;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Webhooks\WebhookEntity;
use Mollie\Api\Webhooks\WebhookSnapshotOrigin;

abstract class BaseEvent
{
    public string $id;

    public string $entityId;

    public DateTime $createdAt;

    public object $links;

    public ?WebhookEntity $entity;

    /**
     * The X-Mollie-Signature header captured when the webhook was
     * validated. Null for legacy/unsigned deliveries or when the
     * caller did not thread the signature through
     * {@see \Mollie\Api\Webhooks\WebhookEventMapper::processPayload()}.
     */
    public ?string $signature;

    /**
     * The timestamp at which the SDK first saw this webhook payload.
     * Defaults to "now" when not supplied.
     */
    public DateTimeImmutable $receivedAt;

    public function __construct(
        string $id,
        string $entityId,
        DateTime $createdAt,
        object $links,
        ?WebhookEntity $entity = null,
        ?string $signature = null,
        ?DateTimeImmutable $receivedAt = null
    ) {
        $this->id = $id;
        $this->entityId = $entityId;
        $this->createdAt = $createdAt;
        $this->links = $links;
        $this->entity = $entity;
        $this->signature = $signature;
        $this->receivedAt = $receivedAt ?? new DateTimeImmutable;
    }

    abstract public static function type(): string;

    public function entity(): WebhookEntity
    {
        return $this->getEntitySafely();
    }

    public function entityData(?string $key = null): mixed
    {
        return $this->getEntitySafely()->getData($key);
    }

    /**
     * Hydrate the embedded entity into a fully-typed SDK resource,
     * propagating the rich webhook origin (event id, signature,
     * received-at). Throws if the webhook delivery was "simple" and
     * carried no embedded entity.
     */
    public function asEntity(Connector $connector): BaseResource
    {
        return $this->entity()->asResource(
            $connector,
            new WebhookSnapshotOrigin(
                $connector,
                $this->id,
                $this->signature,
                $this->receivedAt
            )
        );
    }

    private function getEntitySafely(): WebhookEntity
    {
        $this->guardAgainstMissingEntity();

        /** @var WebhookEntity $entity */
        $entity = $this->entity;

        return $entity;
    }

    private function guardAgainstMissingEntity()
    {
        if (! $this->entity) {
            throw new \Exception('Event entity not found. Make sure to subscribe to full event payloads.');
        }
    }
}
