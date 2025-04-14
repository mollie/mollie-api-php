<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOnboardingStatusRequest;
use Mollie\Api\Resources\Onboarding;
use PHPUnit\Framework\TestCase;

class OnboardingEndpointCollectionTest extends TestCase
{
    /** @test */
    public function status()
    {
        $client = new MockMollieClient([
            GetOnboardingStatusRequest::class => MockResponse::ok('onboarding'),
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
