<?php

namespace Mollie\Api\Webhooks;

use DateTimeImmutable;
use Mollie\Api\Config;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceFactory;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

class WebhookEntity
{
    private string $resourceType;

    private string $id;

    private array $data;

    public function __construct(string $resourceType, string $id, array $data)
    {
        $this->resourceType = $resourceType;
        $this->id = $id;
        $this->data = $data;
    }

    public function isInTestmode(): bool
    {
        return $this->getData('mode') === 'test';
    }

    /**
     * Create an entity representation from webhook embedded entity data.
     *
     * @param  array|object  $entityData
     */
    public static function create($entityData): WebhookEntity
    {
        $data = Utility::transform($entityData, fn ($data) => is_object($data) ? (array) $data : $data, null, []);

        $resourceType = Arr::get($data, 'resource');
        $id = Arr::get($data, 'id');

        return new WebhookEntity($resourceType, $id, $data);
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getData(?string $key = null)
    {
        if ($key) {
            return Arr::get($this->data, $key);
        }

        return $this->data;
    }

    /**
     * Hydrate this entity into a fully-typed SDK resource using the embedded
     * snapshot. No HTTP call is made — the signed webhook payload already
     * contains the full resource state at event time.
     *
     * The caller may supply a pre-built {@see WebhookSnapshotOrigin} to
     * record real event metadata (id, signature, received-at). When omitted
     * a null-signature fallback origin is constructed so ad-hoc or test
     * usages outside the {@see WebhookEventMapper} flow keep working.
     */
    public function asResource(Connector $connector, ?WebhookSnapshotOrigin $origin = null): BaseResource
    {
        $targetClass = $this->resolveTargetResourceClass();
        $resource = ResourceFactory::create($connector, $targetClass);

        $origin = $origin ?? new WebhookSnapshotOrigin(
            $connector,
            'unknown',
            null,
            new DateTimeImmutable
        );

        return (new SnapshotHydrator)->hydrate($resource, $this->data, $origin);
    }

    private function resolveTargetResourceClass(): string
    {
        return Config::resourceRegistry()->for($this->resourceType) ?? AnyResource::class;
    }
}
