<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Resources\Partner;
use Mollie\Api\Utils\Utility;

class OrganizationEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve an organization from Mollie.
     *
     * Will throw a ApiException if the organization id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function get(string $id, $testmode = false): Organization
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Organization */
        return $this->send((new GetOrganizationRequest($id))->test($testmode));
    }

    /**
     * Retrieve the current organization from Mollie.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function current($testmode = false): Organization
    {
        /** @var Organization */
        return $this->get('me', $testmode);
    }

    /**
     * Retrieve the partner status of the current organization.
     *
     * @throws RequestException
     */
    public function partnerStatus(): Partner
    {
        return $this->send(new GetOrganizationPartnerStatusRequest);
    }
}
