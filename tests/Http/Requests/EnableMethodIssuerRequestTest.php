<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\EnableMethodIssuerRequest;
use Mollie\Api\Resources\Issuer;
use PHPUnit\Framework\TestCase;

class EnableMethodIssuerRequestTest extends TestCase
{
    /** @test */
    public function it_can_enable_method_issuer()
    {
        $client = new MockMollieClient([
            EnableMethodIssuerRequest::class => MockResponse::ok('issuer'),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $issuerId = 'INGBNL2A';
        $request = new EnableMethodIssuerRequest($profileId, $methodId, $issuerId);

        /** @var Issuer */
        $issuer = $client->send($request);

        $this->assertTrue($issuer->getResponse()->successful());
        $this->assertInstanceOf(Issuer::class, $issuer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $issuerId = 'INGBNL2A';
        $request = new EnableMethodIssuerRequest($profileId, $methodId, $issuerId);

        $this->assertEquals(
            "profiles/{$profileId}/methods/{$methodId}/issuers/{$issuerId}",
            $request->resolveResourcePath()
        );
    }
}
