<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Types\OnboardingStatus;

final class OnboardingEndpointTest extends BaseEndpointTest
{
    public function testGetWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/onboarding/me'),
            new Response(
                200,
                [],
                '{
                  "resource": "onboarding",
                  "name": "Mollie B.V.",
                  "signedUpAt": "2018-12-20T10:49:08+00:00",
                  "status": "completed",
                  "canReceivePayments": true,
                  "canReceiveSettlements": true,
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/onboarding/me",
                      "type": "application/hal+json"
                    },
                    "dashboard": {
                      "href": "https://www.mollie.com/dashboard/onboarding",
                      "type": "text/html"
                    },
                    "organization": {
                      "href": "https://api.mollie.com/v2/organization/org_12345",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/onboarding-api/get-onboarding-status",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $onboarding = $this->apiClient->onboarding->get();

        $this->assertInstanceOf(Onboarding::class, $onboarding);
        $this->assertEquals("onboarding", $onboarding->resource);
        $this->assertEquals("Mollie B.V.", $onboarding->name);
        $this->assertEquals(OnboardingStatus::COMPLETED, $onboarding->status);
        $this->assertEquals("2018-12-20T10:49:08+00:00", $onboarding->signedUpAt);
        $this->assertEquals(true, $onboarding->canReceivePayments);
        $this->assertEquals(true, $onboarding->canReceiveSettlements);

        $selfLink = (object)['href' => 'https://api.mollie.com/v2/onboarding/me', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $onboarding->_links->self);

        $dashboardLink = (object)['href' => 'https://www.mollie.com/dashboard/onboarding', 'type' => 'text/html'];
        $this->assertEquals($dashboardLink, $onboarding->_links->dashboard);

        $organizationLink = (object)['href' => 'https://api.mollie.com/v2/organization/org_12345', 'type' => 'application/hal+json'];
        $this->assertEquals($organizationLink, $onboarding->_links->organization);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/onboarding-api/get-onboarding-status', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $onboarding->_links->documentation);
    }

    public function testSubmitWorks()
    {
        $this->mockApiCall(
            new Request('POST', '/v2/onboarding/me'),
            new Response(204)
        );

        $this->apiClient->onboarding->submit();
    }
}
