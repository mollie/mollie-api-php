<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Rules\Included;
use Mollie\Api\Types\ClientQuery;

class GetPaginatedClientQuery extends PaginatedQuery
{
    public array $embed = [];

    public function __construct(
        array $embed = [],
        ?string $from = null,
        ?int $limit = null
    ) {
        parent::__construct($from, $limit);

        $this->embed = $embed;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'embed' => Arr::join($this->embed),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'embed' => Included::in(ClientQuery::class),
        ];
    }
}
