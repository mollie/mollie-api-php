<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\WebhookCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Utils\Arr;

class GetPaginatedWebhooksRequest extends SortablePaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = WebhookCollection::class;

    /**
     * @param  string|array|null  $eventTypes
     */
    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        $eventTypes = null
    ) {
        parent::__construct($from, $limit, $sort);

        $this
            ->query()
            ->add(
                'eventTypes',
                is_string($eventTypes)
                    ? $eventTypes
                    : Arr::join($eventTypes ?? [])
            );
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'webhooks';
    }
}
