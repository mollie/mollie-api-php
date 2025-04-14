<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Issuer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method as HttpMethod;

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
    protected $hydratableResource = Issuer::class;

    private string $profileId;

    private string $methodId;

    private string $issuerId;

    private ?string $contractId;

    public function __construct(
        string $profileId,
        string $methodId,
        string $issuerId,
        ?string $contractId = null
    ) {
        $this->profileId = $profileId;
        $this->methodId = $methodId;
        $this->issuerId = $issuerId;
        $this->contractId = $contractId;
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
