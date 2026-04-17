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

    /**
     * Optional connector bound by {@see \Mollie\Api\Webhooks\WebhookEventMapper}.
     * Kept private because this is plumbing: consumers should rely on
     * {@see asResource()} which resolves this automatically.
     */
    private ?Connector $connector;

    public function __construct(
        string $id,
        string $entityId,
        DateTime $createdAt,
        object $links,
        ?WebhookEntity $entity = null,
        ?string $signature = null,
        ?DateTimeImmutable $receivedAt = null,
        ?Connector $connector = null
    ) {
        $this->id = $id;
        $this->entityId = $entityId;
        $this->createdAt = $createdAt;
        $this->links = $links;
        $this->entity = $entity;
        $this->signature = $signature;
        $this->receivedAt = $receivedAt ?? new DateTimeImmutable;
        $this->connector = $connector;
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
     *
     * The $connector argument is optional when the mapper that produced
     * this event was constructed with one (see
     * {@see \Mollie\Api\Webhooks\WebhookEventMapper::__construct()}).
     * An explicit argument always wins over the bound connector.
     */
    public function asResource(?Connector $connector = null): BaseResource
    {
        $connector = $connector ?? $this->connector;

        if ($connector === null) {
            throw new \LogicException(
                'No connector available to hydrate webhook resource. '
                .'Either pass a connector to asResource(), or construct '
                .'WebhookEventMapper with one via '
                .'new WebhookEventMapper([], $mollie).'
            );
        }

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
