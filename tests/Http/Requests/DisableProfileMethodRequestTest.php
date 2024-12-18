<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DisableProfileMethodRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DisableProfileMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_disable_profile_method()
    {
        $client = new MockMollieClient([
            DisableProfileMethodRequest::class => new MockResponse(204, ''),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $request = new DisableProfileMethodRequest($profileId, $methodId);

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
        $request = new DisableProfileMethodRequest($profileId, $methodId);

        $this->assertEquals(
            "profiles/{$profileId}/methods/{$methodId}",
            $request->resolveResourcePath()
        );
    }
}
