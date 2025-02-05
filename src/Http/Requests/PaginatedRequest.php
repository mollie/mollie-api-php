<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;

abstract class PaginatedRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

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
}
