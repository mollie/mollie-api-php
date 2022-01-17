<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Partner;

final class OrganizationPartnerEndpointTest extends BaseEndpointTest
{
    public function testGetWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/organizations/me/partner'),
            new Response(
                200,
                [],
                '{
                    "resource": "partner",
                    "partnerType": "signuplink",
                    "partnerContractSignedAt": "2018-03-20T13:13:37+00:00",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/organizations/me/partner",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/organizations-api/get-partner",
                            "type": "text/html"
                        },
                        "signuplink": {
                            "href": "https://www.mollie.com/dashboard/signup/myCode?lang=en",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $partner = $this->apiClient->organizationPartners->get();

        $this->assertInstanceOf(Partner::class, $partner);
        $this->assertEquals("partner", $partner->resource);
        $this->assertEquals("signuplink", $partner->partnerType);
        $this->assertEquals("2018-03-20T13:13:37+00:00", $partner->partnerContractSignedAt);

        $selfLink = (object)['href' => 'https://api.mollie.com/v2/organizations/me/partner', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $partner->_links->self);

        $signUpLink = (object)['href' => 'https://www.mollie.com/dashboard/signup/myCode?lang=en', 'type' => 'text/html'];
        $this->assertEquals($signUpLink, $partner->_links->signuplink);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/organizations-api/get-partner', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $partner->_links->documentation);
    }
}
