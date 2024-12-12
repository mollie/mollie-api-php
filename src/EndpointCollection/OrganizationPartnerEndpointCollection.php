<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;

/**
 * @deprecated Use OrganizationEndpointCollection::partnerStatus() instead
 */
class OrganizationPartnerEndpointCollection extends EndpointCollection
{
    public function status(): Partner
    {
        return $this->send(new GetOrganizationPartnerStatusRequest);
    }
}
