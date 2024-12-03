<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\EnableMethodIssuerRequest;
use Mollie\Api\Http\Requests\DisableMethodIssuerRequest;
use Mollie\Api\Resources\Issuer;
use Tests\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class MethodIssuerEndpointCollectionTest extends TestCase
{
    /** @test */
    public function enable()
    {
        $client = new MockClient([
            EnableMethodIssuerRequest::class => new MockResponse(200, 'issuer'),
        ]);

        /** @var Issuer $issuer */
        $issuer = $client->methodIssuers->enable(
            'pfl_v9hTwCvYqw',
            'ideal',
            'ideal_INGBNL2A',
            'ctr_123xyz'
        );

        $this->assertIssuer($issuer);
    }

    /** @test */
    public function disable()
    {
        $client = new MockClient([
            DisableMethodIssuerRequest::class => new MockResponse(204),
        ]);

        $client->methodIssuers->disable(
            'pfl_v9hTwCvYqw',
            'ideal',
            'ideal_INGBNL2A'
        );

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    protected function assertIssuer(Issuer $issuer)
    {
        $this->assertInstanceOf(Issuer::class, $issuer);
        $this->assertEquals('issuer', $issuer->resource);
        $this->assertNotEmpty($issuer->id);
        $this->assertNotEmpty($issuer->description);
        $this->assertNotEmpty($issuer->status);
        $this->assertNotEmpty($issuer->_links);
    }
}
