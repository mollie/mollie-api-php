<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\EnableMethodRequest;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;

class EnableMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_enable_profile_method()
    {
        $client = new MockMollieClient([
            EnableMethodRequest::class => MockResponse::ok('method'),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $request = new EnableMethodRequest($profileId, $methodId);

        /** @var Method */
        $method = $client->send($request);

        $this->assertTrue($method->getResponse()->successful());
        $this->assertInstanceOf(Method::class, $method);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $request = new EnableMethodRequest($profileId, $methodId);

        $this->assertEquals(
            "profiles/{$profileId}/methods/{$methodId}",
            $request->resolveResourcePath()
        );
    }
}
