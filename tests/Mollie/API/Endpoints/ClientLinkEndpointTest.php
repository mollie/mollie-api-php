<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ClientLinkEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testCreateClientLink()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/client-links",
                [],
                '{
                    "owner": {
                        "email": "foo@test.com",
                        "givenName": "foo",
                        "familyName": "bar",
                        "locale": "nl_NL"
                    },
                    "name": "Foo Company",
                    "address": {
                        "streetAndNumber": "Keizersgracht 313",
                        "postalCode": "1016 EE",
                        "city": "Amsterdam",
                        "country": "nl"
                    },
                    "registrationNumber": "30204462",
                    "vatNumber": "NL123456789B01"
                }'
            ),
            new Response(
                201,
                [],
                $this->getClientLinkResponseFixture("csr_vZCnNQsV2UtfXxYifWKWH")
            )
        );

        $clientLink = $this->apiClient->clientLinks->create([
            "owner" => [
                "email" => "foo@test.com",
                "givenName" => "foo",
                "familyName" => "bar",
                "locale" => "nl_NL",
            ],
            "name" => "Foo Company",
            "address" => [
                "streetAndNumber" => "Keizersgracht 313",
                "postalCode" => "1016 EE",
                "city" => "Amsterdam",
                "country" => "nl",
            ],
            "registrationNumber" => "30204462",
            "vatNumber" => "NL123456789B01",
        ]);

        $this->assertEquals($clientLink->id, "csr_vZCnNQsV2UtfXxYifWKWH");
        $this->assertLinkObject("https://my.mollie.com/dashboard/client-link/finalize/csr_vZCnNQsV2UtfXxYifWKWH", "text/html", $clientLink->_links->clientLink);
        $this->assertLinkObject("https://docs.mollie.com/reference/v2/clients-api/create-client-link", "text/html", $clientLink->_links->documentation);

        $redirectLink = $clientLink->getRedirectUrl("app_j9Pakf56Ajta6Y65AkdTtAv", "decafbad", "force", [
            'onboarding.read',
            'onboarding.write',
        ]);
        $this->assertEquals("https://my.mollie.com/dashboard/client-link/finalize/csr_vZCnNQsV2UtfXxYifWKWH?client_id=app_j9Pakf56Ajta6Y65AkdTtAv&state=decafbad&approval_prompt=force&scopes=onboarding.read%2Bonboarding.write", $redirectLink);
    }

    protected function getClientLinkResponseFixture(string $client_link_id)
    {
        return str_replace(
            [
                "<<client_link_id>>",
            ],
            [
                $client_link_id,
            ],
            '{
                "id": "<<client_link_id>>",
                "resource": "client-link",
                "_links": {
                    "clientLink": {
                        "href": "https://my.mollie.com/dashboard/client-link/finalize/<<client_link_id>>",
                        "type": "text/html"
                    },
                    "documentation": {
                        "href": "https://docs.mollie.com/reference/v2/clients-api/create-client-link",
                        "type": "text/html"
                    }
                }
            }'
        );
    }
}
