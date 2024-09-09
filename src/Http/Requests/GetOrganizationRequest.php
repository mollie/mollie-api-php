<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Organization;
use Mollie\Api\Types\Method;

class GetOrganizationRequest extends SimpleRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Organization::class;

    public function resolveResourcePath(): string
    {
        return 'organizations';
    }
}
