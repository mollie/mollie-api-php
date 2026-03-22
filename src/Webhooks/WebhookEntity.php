<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Config;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\SupportsTestmode;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceRegistry;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

class WebhookEntity
{
    use HasMode;

    private string $resourceType;

    private string $id;

    private array $data;

    private ?string $mode = null;

    public function __construct(string $resourceType, string $id, array $data)
    {
        $this->resourceType = $resourceType;
        $this->id = $id;
        $this->data = $data;
        $this->mode = $this->getData('mode');
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
     * Upgrade this entity to a fully-typed SDK resource using the connector.
     */
    public function asResource(Connector $connector): BaseResource
    {
        $targetClass = $this->resolveTargetResourceClass();

        $request = $this->tryCreateGetRequest($targetClass);

        if ($request instanceof SupportsTestmode) {
            $request = $request->test($this->isInTestmode());
        }

        if ($request instanceof ResourceHydratableRequest) {
            return $connector->send($request);
        }

        $href = $this->extractSelfHref($this->data);

        if ($href) {
            return $this->sendDynamic($connector, $href, $targetClass);
        }

        $fallbackHref = $this->buildFallbackHref($targetClass);

        return $this->sendDynamic($connector, $fallbackHref, $targetClass);
    }

    /**
     * @param  array  $data
     */
    private function extractSelfHref(array $data): ?string
    {
        return Arr::get($data, '_links.self.href');
    }

    private function resolvePlural(string $targetClass): ?string
    {
        $names = Config::resourceRegistry()->namesOf($targetClass);

        return $names[ResourceRegistry::PLURAL_KEY] ?? null;
    }

    private function resolveTargetResourceClass(): string
    {
        return Config::resourceRegistry()->for($this->resourceType) ?? AnyResource::class;
    }

    private function tryCreateGetRequest(string $targetClass): ?ResourceHydratableRequest
    {
        $resourceBasename = Utility::classBasename($targetClass);

        /** @var class-string<ResourceHydratableRequest> */
        $requestClass = 'Mollie\\Api\\Http\\Requests\\Get' . $resourceBasename . 'Request';

        if (! class_exists($requestClass)) {
            return null;
        }

        try {
            return new $requestClass($this->id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildFallbackHref(string $targetClass): string
    {
        $plural = $this->resolvePlural($targetClass) ?? $this->resourceType;

        return $plural . '/' . $this->id;
    }

    private function sendDynamic(Connector $connector, string $href, string $targetClass): BaseResource
    {
        return $connector->send(
            (new DynamicGetRequest($href))->setHydratableResource($targetClass)
        );
    }
}
