<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Resources\Organization;

class OrganizationEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve an organization from Mollie.
     *
     * Will throw a ApiException if the organization id is invalid or the resource cannot be found.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function get(string $id, $testmode = []): Organization
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Organization */
        return $this->send(new GetOrganizationRequest($id, $testmode));
    }

    /**
     * Retrieve the current organization from Mollie.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function current($testmode = []): Organization
    {
        /** @var Organization */
        return $this->get('me', $testmode);
    }
}
