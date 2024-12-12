<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method as HttpMethod;

class DisableMethodIssuerRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::DELETE;

    private string $profileId;

    private string $methodId;

    private string $issuerId;

    public function __construct(string $profileId, string $methodId, string $issuerId)
    {
        $this->profileId = $profileId;
        $this->methodId = $methodId;
        $this->issuerId = $issuerId;
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->profileId}/methods/{$this->methodId}/issuers/{$this->issuerId}";
    }
}
