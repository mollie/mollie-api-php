<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetOnboardingRequest;
use Mollie\Api\Resources\Onboarding;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class OnboardingEndpointCollectionTest extends TestCase
{
    /** @test */
    public function status()
    {
        $client = new MockClient([
            GetOnboardingRequest::class => new MockResponse(200, 'onboarding'),
        ]);

        /** @var Onboarding $onboarding */
        $onboarding = $client->onboarding->status();

        $this->assertInstanceOf(Onboarding::class, $onboarding);
        $this->assertEquals('onboarding', $onboarding->resource);
        $this->assertNotEmpty($onboarding->name);
        $this->assertNotEmpty($onboarding->status);
        $this->assertNotEmpty($onboarding->canReceivePayments);
        $this->assertNotEmpty($onboarding->canReceiveSettlements);
    }
}
