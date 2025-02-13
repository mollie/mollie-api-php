<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOnboardingStatusRequest;
use Mollie\Api\Resources\Onboarding;
use PHPUnit\Framework\TestCase;

class GetOnboardingStatusRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_onboarding_status()
    {
        $client = new MockMollieClient([
            GetOnboardingStatusRequest::class => MockResponse::ok('onboarding'),
        ]);

        $request = new GetOnboardingStatusRequest;

        /** @var Onboarding */
        $onboarding = $client->send($request);

        $this->assertTrue($onboarding->getResponse()->successful());
        $this->assertInstanceOf(Onboarding::class, $onboarding);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetOnboardingStatusRequest;

        $this->assertEquals('onboarding/me', $request->resolveResourcePath());
    }
}
