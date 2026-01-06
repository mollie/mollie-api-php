<?php

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

class GetEnabledMethodsRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    protected $hydratableResource = MethodCollection::class;

    private string $sequenceType;

    private string $resource;

    private ?string $locale;

    private ?Money $amount;

    private ?string $billingCountry;

    private ?array $includeWallets;

    private ?array $orderLineCategories;

    private ?string $profileId;

    private ?bool $includeIssuers;

    /**
     * Whether to filter out methods with a status of null.
     */
    private bool $filtersNullStatus = true;

    public function __construct(
        string $sequenceType = SequenceType::ONEOFF,
        string $resource = MethodQuery::RESOURCE_PAYMENTS,
        ?string $locale = null,
        ?Money $amount = null,
        ?string $billingCountry = null,
        ?array $includeWallets = null,
        ?array $orderLineCategories = null,
        ?string $profileId = null,
        ?bool $includeIssuers = null
    ) {
        $this->sequenceType = $sequenceType;
        $this->resource = $resource;
        $this->locale = $locale;
        $this->amount = $amount;
        $this->billingCountry = $billingCountry;
        $this->includeWallets = $includeWallets;
        $this->orderLineCategories = $orderLineCategories;
        $this->profileId = $profileId;
        $this->includeIssuers = $includeIssuers;

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
