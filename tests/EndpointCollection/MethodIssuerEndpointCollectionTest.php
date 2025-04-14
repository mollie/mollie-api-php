<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DisableMethodIssuerRequest;
use Mollie\Api\Http\Requests\EnableMethodIssuerRequest;
use Mollie\Api\Resources\Issuer;
use PHPUnit\Framework\TestCase;

class MethodIssuerEndpointCollectionTest extends TestCase
{
    /** @test */
    public function enable()
    {
        $client = new MockMollieClient([
            EnableMethodIssuerRequest::class => MockResponse::ok('issuer'),
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
        $client = new MockMollieClient([
            DisableMethodIssuerRequest::class => MockResponse::noContent(),
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
