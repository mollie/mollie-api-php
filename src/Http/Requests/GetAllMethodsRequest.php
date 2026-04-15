<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Types\Method as HttpMethod;
use Mollie\Api\Types\MethodQuery;
use Mollie\Api\Utils\Arr;

/**
 * @see https://docs.mollie.com/reference/list-all-methods
 */
class GetAllMethodsRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = MethodCollection::class;

    public function __construct(
        private bool $includeIssuers = false,
        private bool $includePricing = false,
        private ?string $locale = null,
        private ?Money $amount = null,
        private ?string $profileId = null,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => Arr::join([
                $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
                $this->includePricing ? MethodQuery::INCLUDE_PRICING : null,
            ]),
            'locale' => $this->locale,
            'amount' => $this->amount,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'methods/all';
    }
}
