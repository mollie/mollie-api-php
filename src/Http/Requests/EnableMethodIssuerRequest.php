<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Issuer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method as HttpMethod;

/**
 * @see https://docs.mollie.com/reference/enable-method-issuer
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Issuer>
 */
class EnableMethodIssuerRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Issuer::class;

    public function __construct(
        private string $profileId,
        private string $methodId,
        private string $issuerId,
        private ?string $contractId = null,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'contractId' => $this->contractId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->profileId}/methods/{$this->methodId}/issuers/{$this->issuerId}";
    }
}
