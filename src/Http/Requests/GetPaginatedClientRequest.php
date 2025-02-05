<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Utils\Arr;

class GetPaginatedClientRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ClientCollection::class;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?bool $embedOrganization = null,
        ?bool $embedOnboarding = null
    ) {
        parent::__construct($from, $limit);

        $this->query()
            ->add('embed', Arr::join([
                $embedOrganization ? ClientQuery::EMBED_ORGANIZATION : null,
                $embedOnboarding ? ClientQuery::EMBED_ONBOARDING : null,
            ]));
    }

    public function resolveResourcePath(): string
    {
        return 'clients';
    }
}
