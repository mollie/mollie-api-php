<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Method;
use Mollie\Api\Types\Method as HttpMethod;
use Mollie\Api\Types\MethodQuery;
use Mollie\Api\Utils\Arr;

class GetMethodRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    protected $hydratableResource = Method::class;

    private string $methodId;

    private ?string $locale;

    private ?string $currency;

    private ?string $profileId;

    private ?bool $includeIssuers;

    private ?bool $includePricing;

    public function __construct(
        string $methodId,
        ?string $locale = null,
        ?string $currency = null,
        ?string $profileId = null,
        ?bool $includeIssuers = null,
        ?bool $includePricing = null
    ) {
        $this->methodId = $methodId;
        $this->locale = $locale;
        $this->currency = $currency;
        $this->profileId = $profileId;
        $this->includeIssuers = $includeIssuers;
        $this->includePricing = $includePricing;
    }

    protected function defaultQuery(): array
    {
        return [
            'locale' => $this->locale,
            'currency' => $this->currency,
            'profileId' => $this->profileId,
            'include' => Arr::join([
                $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
                $this->includePricing ? MethodQuery::INCLUDE_PRICING : null,
            ]),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "methods/{$this->methodId}";
    }
}
