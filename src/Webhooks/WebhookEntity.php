<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Config;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceFactory;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;
use Nyholm\Psr7\Request as PsrRequest;
use Nyholm\Psr7\Response as PsrResponse;

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
     */
    public function asResource(Connector $connector): BaseResource
    {
        $targetClass = $this->resolveTargetResourceClass();

        $resource = ResourceFactory::create($connector, $targetClass);
        $response = $this->buildSyntheticResponse($connector);

        // Pass the decoded stdClass tree (not the source array) so nested values
        // such as `amount` and `_links` arrive as objects, matching the shape
        // produced by the live HTTP path.
        return (new ResourceHydrator)->hydrate($resource, $response->json(), $response);
    }

    private function resolveTargetResourceClass(): string
    {
        return Config::resourceRegistry()->for($this->resourceType) ?? AnyResource::class;
    }

    /**
     * Build a Response object backed by the webhook snapshot so hydrated
     * resources satisfy the IsResponseAware contract without a live HTTP call.
     */
    private function buildSyntheticResponse(Connector $connector): Response
    {
        $url = Arr::get($this->data, '_links.self.href') ?? '';
        $body = (string) json_encode($this->data);

        $psrRequest = new PsrRequest('GET', $url);
        $psrResponse = new PsrResponse(200, ['Content-Type' => 'application/hal+json'], $body);

        $pendingRequest = new PendingRequest(
            $connector,
            (new DynamicGetRequest($url))->setHydratableResource($this->resolveTargetResourceClass())
        );

        return new Response($psrResponse, $psrRequest, $pendingRequest);
    }
}
