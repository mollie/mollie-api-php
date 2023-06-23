<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ClientLinkEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @dataProvider clientCreateData
     */
    public function testCreateClientLink(string $client_link_id, string $app_id, string $state, array $scopes, string $approval_prompt, string $expected_url_query)
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
                $this->getClientLinkResponseFixture($client_link_id)
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

        $this->assertEquals($clientLink->id, $client_link_id);
        $this->assertLinkObject("https://my.mollie.com/dashboard/client-link/finalize/{$client_link_id}", "text/html", $clientLink->_links->clientLink);
        $this->assertLinkObject("https://docs.mollie.com/reference/v2/clients-api/create-client-link", "text/html", $clientLink->_links->documentation);

        $redirectLink = $clientLink->getRedirectUrl($app_id, $state, $scopes, $approval_prompt);
        $this->assertEquals("https://my.mollie.com/dashboard/client-link/finalize/{$client_link_id}?{$expected_url_query}", $redirectLink);
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

    public function clientCreateData(): array
    {
        return [
            [
                'cl_vZCnNQsV2UtfXxYifWKWH',
                'app_j9Pakf56Ajta6Y65AkdTtAv',
                'decafbad',
                [
                    'onboarding.read',
                    'onboarding.write',
                ],
                'force',
                'client_id=app_j9Pakf56Ajta6Y65AkdTtAv&state=decafbad&approval_prompt=force&scope=onboarding.read%20onboarding.write',
            ],
            [
                'cl_vZCnNQsV2UtfXxYifWKWG',
                'app_j9Pakf56Ajta6Y65AkdTtAw',
                'decafbad',
                [
                    'onboarding.read',
                ],
                'auto',
                'client_id=app_j9Pakf56Ajta6Y65AkdTtAw&state=decafbad&approval_prompt=auto&scope=onboarding.read',
            ],
        ];
    }
}
