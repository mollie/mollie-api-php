<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method as HttpMethod;

class DisableMethodRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::DELETE;

    private string $profileId;

    private string $methodId;

    public function __construct(string $profileId, string $methodId)
    {
        $this->profileId = $profileId;
        $this->methodId = $methodId;
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->profileId}/methods/{$this->methodId}";
    }
}
