<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Types\Method as HttpMethod;
use Mollie\Api\Types\MethodQuery;
use Mollie\Api\Types\SequenceType;
use Mollie\Api\Utils\Arr;

/**
 * @see https://docs.mollie.com/reference/v2/methods-api/list-methods
 */
class GetEnabledMethodsRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    protected ?string $hydratableResource = MethodCollection::class;

    /**
     * Whether to filter out methods with a status of null.
     */
    private bool $filtersNullStatus = true;

    public function __construct(
        private string $sequenceType = SequenceType::Oneoff->value,
        private string $resource = MethodQuery::RESOURCE_PAYMENTS,
        private ?string $locale = null,
        private ?Money $amount = null,
        private ?string $billingCountry = null,
        private ?array $includeWallets = null,
        private ?array $orderLineCategories = null,
        private ?string $profileId = null,
        private ?bool $includeIssuers = null,
    ) {
        $this->middleware()->onResponse(function ($result) {
            if ($this->filtersNullStatus && $result instanceof MethodCollection) {
                return $result
                    ->filter(fn (Method $method) => $method->status !== null);
            }

            return $result;
        }, 'filter_null_status', MiddlewarePriority::LOW);
    }

    public function withoutNullStatus(): self
    {
        $this->filtersNullStatus = true;

        return $this;
    }

    public function withNullStatus(): self
    {
        $this->filtersNullStatus = false;

        return $this;
    }

    protected function defaultQuery(): array
    {
        return [
            'sequenceType' => $this->sequenceType,
            'resource' => $this->resource,
            'locale' => $this->locale,
            'amount' => $this->amount,
            'billingCountry' => $this->billingCountry,
            'includeWallets' => Arr::join($this->includeWallets ?? []),
            'orderLineCategories' => Arr::join($this->orderLineCategories ?? []),
            'profileId' => $this->profileId,
            'include' => $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'methods';
    }
}
