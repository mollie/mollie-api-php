<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Method;
use Mollie\Api\Types\Method as HttpMethod;
use Mollie\Api\Types\MethodQuery;

/**
 * @see https://docs.mollie.com/reference/v2/methods-api/get-method
 */
class GetMethodRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    protected ?string $hydratableResource = Method::class;

    public function __construct(
        private string $methodId,
        private ?string $locale = null,
        private ?string $currency = null,
        private ?string $profileId = null,
        private ?bool $includeIssuers = null,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'locale' => $this->locale,
            'currency' => $this->currency,
            'profileId' => $this->profileId,
            'include' => $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "methods/{$this->methodId}";
    }
}
