<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Partner;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Partner>
 */
class GetOrganizationPartnerStatusRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Partner::class;

    public function resolveResourcePath(): string
    {
        return 'organizations/me/partner';
    }
}
