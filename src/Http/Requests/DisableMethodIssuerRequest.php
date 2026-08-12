<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method as HttpMethod;

/**
 * @see https://docs.mollie.com/reference/disable-method-issuer
 */
class DisableMethodIssuerRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::DELETE;

    public function __construct(
        private string $profileId,
        private string $methodId,
        private string $issuerId,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->profileId}/methods/{$this->methodId}/issuers/{$this->issuerId}";
    }
}
