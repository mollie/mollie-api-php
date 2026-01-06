<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Webhooks\WebhookEntity;

abstract class BaseEvent
{
    public string $id;

    public string $entityId;

    public DateTime $createdAt;

    public object $links;

    public ?WebhookEntity $entity;

    public function __construct(
        string $id,
        string $entityId,
        DateTime $createdAt,
        object $links,
        ?WebhookEntity $entity = null
    ) {
        $this->id = $id;
        $this->entityId = $entityId;
        $this->createdAt = $createdAt;
        $this->links = $links;
        $this->entity = $entity;
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
