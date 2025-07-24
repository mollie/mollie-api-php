<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Traits\ForwardsCalls;
use ReflectionClass;

/**
 * Builder for creating mock responses for Mollie API resources.
 *
 * @method self embed(string $collectionClass) Embed a collection of resources into the response
 * @method self add(array $data) Add a resource to the embedded response
 * @method self addMany(array $data) Add multiple resources to the embedded response
 */
class ResourceResponseBuilder
{
    use ForwardsCalls;

    private string $resourceClass;

    private array $data = [];

    /** @var array<string, ListResponseBuilder> */
    private array $embeddedBuilders = [];

    private ?string $currentEmbedKey = null;

    public function __construct(string $resourceClass)
    {
        if (! is_subclass_of($resourceClass, BaseResource::class)) {
            throw new LogicException('Resource class must be a subclass of '.BaseResource::class);
        }

        $this->resourceClass = $resourceClass;
    }

    /**
     * Set the resource data.
     */
    public function with(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Create the mock response with the resource data and any embedded collections.
     */
    public function create(): MockResponse
    {
        $reflection = new ReflectionClass($this->resourceClass);
        $baseName = strtolower($reflection->getShortName());

        $sampleContents = json_decode(FakeResponseLoader::load($baseName), true);

        $data = array_merge(
            ['_embedded' => []],
            ['_links' => $sampleContents['_links']],
            $this->data
        );

        foreach ($this->embeddedBuilders as $key => $builder) {
            $embeddedResponse = $builder->create();
            $embeddedData = $embeddedResponse->json();

            $data['_embedded'] = array_merge($data['_embedded'], $embeddedData['_embedded']);
        }

        // reorder the data for dev's convenience
        $data = array_merge(
            $this->data,
            [
                '_embedded' => $data['_embedded'],
                '_links' => $data['_links'],
            ]
        );

        if (empty($data['_embedded'])) {
            unset($data['_embedded']);
        }

        return new MockResponse($data);
    }

    public function __call($method, $parameters)
    {
        if ($method === 'embed') {
            /** @var string $collectionClass */
            $collectionClass = $parameters[0];

            if (! isset($this->embeddedBuilders[$collectionClass])) {
                $this->embeddedBuilders[$collectionClass] = new ListResponseBuilder($collectionClass);
            }

            $this->currentEmbedKey = $collectionClass;

            return $this;
        }

        if ($this->currentEmbedKey && isset($this->embeddedBuilders[$this->currentEmbedKey]) && method_exists($this->embeddedBuilders[$this->currentEmbedKey], $method)) {
            return $this->forwardDecoratedCallTo($this->embeddedBuilders[$this->currentEmbedKey], $method, $parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
}
