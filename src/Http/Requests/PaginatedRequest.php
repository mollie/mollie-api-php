<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;

/**
 * @template TResource of object
 *
 * @extends ResourceHydratableRequest<TResource>
 */
abstract class PaginatedRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    public function __construct(
        private ?string $from = null,
        private ?int $limit = null,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }
}
