<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\MollieApiClient;

abstract class PaginatedRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected string $method = MollieApiClient::HTTP_GET;

    public ?string $from = null;

    public ?int $limit = null;

    public array $filters = [];

    public function __construct(
        array $filters = [],
        ?string $from = null,
        ?int $limit = null,
    ) {
        $this->filters = $filters;
        $this->from = $from;
        $this->limit = $limit;
    }

    public function getQuery(): array
    {
        return array_merge([
            'from' => $this->from,
            'limit' => $this->limit,
        ], $this->filters);
    }
}
