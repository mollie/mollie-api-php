<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Utils\Str;
use Mollie\Api\Webhooks\Events\BaseEvent;

class MockEvent
{
    private string $eventClass;

    private string $entityId = '';

    private bool $isSnapshot = false;

    public function __construct(string $eventClass, string $entityId = '')
    {
        if (! is_subclass_of($eventClass, BaseEvent::class)) {
            throw new LogicException('Event class must be a subclass of '.BaseEvent::class);
        }

        $this->eventClass = $eventClass;
        $this->entityId = $entityId;
    }

    public static function for(string $eventClass, string $entityId = ''): self
    {
        return new self($eventClass, $entityId);
    }

    public function entityId(string $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function snapshot(): self
    {
        $this->isSnapshot = true;

        return $this;
    }

    public function simple(): self
    {
        $this->isSnapshot = false;

        return $this;
    }

    public function create(): array
    {
        $eventBlueprintData = $this->loadEventBlueprintData();

        if (! $this->isSnapshot) {
            return $eventBlueprintData;
        }

        $resourceKey = Str::before($this->eventClass::type(), '.');

        $resourceData = MockResponse::ok($resourceKey, $this->entityId)
            ->json();

        $eventBlueprintData['_embedded']['entity'] = $resourceData;

        return $eventBlueprintData;
    }

    private function loadEventBlueprintData(): array
    {
        $eventBlueprint = FakeResponseLoader::loadEventBlueprint();

        $eventBlueprint = str_replace('{{ TYPE }}', $this->eventClass::type(), $eventBlueprint);

        if (! empty($this->entityId)) {
            $eventBlueprint = str_replace('{{ RESOURCE_ID }}', $this->entityId, $eventBlueprint);
        }

        return json_decode($eventBlueprint, true);
    }

}
