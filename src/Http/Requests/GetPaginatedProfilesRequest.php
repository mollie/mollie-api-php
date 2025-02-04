<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedProfilesRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ProfileCollection::class;

    private ?string $from;

    private ?int $limit;

    public function __construct(
        ?string $from = null,
        ?int $limit = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'profiles';
    }
}
