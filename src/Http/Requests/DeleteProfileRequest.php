<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

class DeleteProfileRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
