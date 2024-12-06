<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\EnableMethodIssuerRequest;
use Mollie\Api\Http\Response;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class EnableMethodIssuerRequestTest extends TestCase
{
    /** @test */
    public function it_can_enable_method_issuer()
    {
        $client = new MockClient([
            EnableMethodIssuerRequest::class => new MockResponse(204, ''),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $issuerId = 'INGBNL2A';
        $request = new EnableMethodIssuerRequest($profileId, $methodId, $issuerId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
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