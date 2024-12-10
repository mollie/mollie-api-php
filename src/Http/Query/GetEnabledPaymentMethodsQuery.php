<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Types\MethodQuery;

class GetEnabledPaymentMethodsQuery implements Arrayable
{
    private string $sequenceType;

    private string $resource;

    private ?string $locale;

    private ?Money $amount;

    private ?string $billingCountry;

    private ?array $includeWallets;

    private ?array $orderLineCategories;

    private ?string $profileId;

    private ?bool $includeIssuers;

    private ?bool $includePricing;

    public function __construct(
        string $sequenceType = MethodQuery::SEQUENCE_TYPE_ONEOFF,
        string $resource = MethodQuery::RESOURCE_PAYMENTS,
        ?string $locale = null,
        ?Money $amount = null,
        ?string $billingCountry = null,
        ?array $includeWallets = null,
        ?array $orderLineCategories = null,
        ?string $profileId = null,
        ?bool $includeIssuers = null,
        ?bool $includePricing = null
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
        $this->includePricing = $includePricing;
    }

    public function toArray(): array
    {
        return [
            'sequenceType' => $this->sequenceType,
            'locale' => $this->locale,
            'amount' => $this->amount,
            'resource' => $this->resource,
            'billingCountry' => $this->billingCountry,
            'includeWallets' => Arr::join($this->includeWallets ?? []),
            'orderLineCategories' => Arr::join($this->orderLineCategories ?? []),
            'profileId' => $this->profileId,
            'include' => array_filter([
                $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
                $this->includePricing ? MethodQuery::INCLUDE_PRICING : null,
            ]),
        ];
    }
}
