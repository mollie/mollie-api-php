<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOnboardingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Onboarding;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetOnboardingRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_onboarding()
    {
        $client = new MockClient([
            GetOnboardingRequest::class => new MockResponse(200, 'onboarding'),
        ]);

        $request = new GetOnboardingRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Onboarding::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetOnboardingRequest;

        $this->assertEquals('onboarding/me', $request->resolveResourcePath());
    }
}
