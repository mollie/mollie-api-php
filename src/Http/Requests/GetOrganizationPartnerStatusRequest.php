<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Partner;
use Mollie\Api\Types\Method;

class GetOrganizationPartnerStatusRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Partner::class;

    public function resolveResourcePath(): string
    {
        return 'organizations/me/partner';
    }
}
