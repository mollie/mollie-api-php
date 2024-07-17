<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Issuer;

class MethodIssuerEndpointTest extends BaseEndpointTest
{
    /** @test */
    public function testEnableIssuer()
    {
        $this->mockApiCall(
            new Request(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_QkEhN94Ba/methods/ideal/issuers/festivalcadeau'
            ),
            new Response(
                201,
                [],
                '{
                    "resource": "issuer",
                    "id": "festivalcadeau",
                    "name": "Festival Cadeau",
                    "method": "ideal",
                    "image": {
                        "size1x": "https://www.mollie.com/images/payscreen/methods/ideal.png",
                        "size2x": "https://www.mollie.com/images/payscreen/methods/ideal%402x.png"
                    }
                }'
            )
        );

        $response = $this->apiClient->methodIssuers->enable('pfl_QkEhN94Ba', 'ideal', 'festivalcadeau');
        $this->assertInstanceOf(Issuer::class, $response);
        $this->assertEquals('festivalcadeau', $response->id);
    }

    /** @test */
    public function testDisableIssuer()
    {
        $this->mockApiCall(
            new Request(
                'DELETE',
                'https://api.mollie.com/v2/profiles/pfl_QkEhN94Ba/methods/ideal/issuers/festivalcadeau'
            ),
            new Response(204)
        );

        $response = $this->apiClient->methodIssuers->disable('pfl_QkEhN94Ba', 'ideal', 'festivalcadeau');

        $this->assertNull($response);
    }
}
