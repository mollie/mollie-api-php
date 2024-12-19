<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\EnableProfileMethodRequest;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;

class EnableProfileMethodRequestTest extends TestCase
{
    /** @test */
    public function it_can_enable_profile_method()
    {
        $client = new MockMollieClient([
            EnableProfileMethodRequest::class => MockResponse::noContent(''),
        ]);

        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $request = new EnableProfileMethodRequest($profileId, $methodId);

        /** @var Method */
        $method = $client->send($request);

        $this->assertTrue($method->getResponse()->successful());
        $this->assertEquals(204, $method->getResponse()->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $profileId = 'pfl_v9hTwCvYqw';
        $methodId = 'ideal';
        $request = new EnableProfileMethodRequest($profileId, $methodId);

        $this->assertEquals(
            "profiles/{$profileId}/methods/{$methodId}",
            $request->resolveResourcePath()
        );
    }
}
