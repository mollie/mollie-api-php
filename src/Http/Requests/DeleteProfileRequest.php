<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/profiles-api/delete-profile
 */
class DeleteProfileRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    public function __construct(
        private string $id,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
