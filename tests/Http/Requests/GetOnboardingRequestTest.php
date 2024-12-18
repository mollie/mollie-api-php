<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOnboardingRequest;
use Mollie\Api\Resources\Onboarding;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetOnboardingRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_onboarding()
    {
        $client = new MockClient([
            GetOnboardingRequest::class => new MockResponse(200, 'onboarding'),
        ]);

        $request = new GetOnboardingRequest;

        /** @var Onboarding */
        $onboarding = $client->send($request);

        $this->assertTrue($onboarding->getResponse()->successful());
        $this->assertInstanceOf(Onboarding::class, $onboarding);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetOnboardingRequest;

        $this->assertEquals('onboarding/me', $request->resolveResourcePath());
    }
}
