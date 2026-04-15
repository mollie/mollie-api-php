<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/organizations-api/get-organization
 */
class GetOrganizationRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Organization::class;

    public function __construct(
        private string $id,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return "organizations/{$this->id}";
    }
}
