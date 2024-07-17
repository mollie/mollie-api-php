<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Partner;

class OrganizationPartnerEndpoint extends RestEndpoint implements SingleResourceEndpointContract
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "organizations/me/partner";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Partner::class;

    /**
     * Retrieve details about the partner status of the currently authenticated organization.
     *
     * Will throw an ApiException if the resource cannot be found.
     *
     * @return Partner
     * @throws ApiException
     */
    public function get(): Partner
    {
        /** @var Partner */
        return $this->readResource('', []);
    }
}
