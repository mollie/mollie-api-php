<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;

class GetPaginatedClientRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ClientCollection::class;

    private ?string $from;

    private ?int $limit;

    private ?bool $embedOrganization;

    private ?bool $embedOnboarding;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?bool $embedOrganization = null,
        ?bool $embedOnboarding = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->embedOrganization = $embedOrganization;
        $this->embedOnboarding = $embedOnboarding;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'embed' => Arr::join([
                $this->embedOrganization ? ClientQuery::EMBED_ORGANIZATION : null,
                $this->embedOnboarding ? ClientQuery::EMBED_ONBOARDING : null,
            ]),
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'clients';
    }
}
