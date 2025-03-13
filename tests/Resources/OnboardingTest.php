<?php

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Types\OnboardingStatus;

class OnboardingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestOnboardingStatuses
     */
    public function test_onboarding_statuses($status, $function, $expected_boolean)
    {
        $onboarding = new Onboarding(
            $this->createMock(MollieApiClient::class),
        );
        $onboarding->status = $status;

        $this->assertEquals($expected_boolean, $onboarding->{$function}());
    }

    public function dpTestOnboardingStatuses()
    {
        return [
            [OnboardingStatus::NEEDS_DATA, 'needsData', true],
            [OnboardingStatus::NEEDS_DATA, 'inReview', false],
            [OnboardingStatus::NEEDS_DATA, 'isCompleted', false],

            [OnboardingStatus::IN_REVIEW, 'needsData', false],
            [OnboardingStatus::IN_REVIEW, 'inReview', true],
            [OnboardingStatus::IN_REVIEW, 'isCompleted', false],

            [OnboardingStatus::COMPLETED, 'needsData', false],
            [OnboardingStatus::COMPLETED, 'inReview', false],
            [OnboardingStatus::COMPLETED, 'isCompleted', true],
        ];
    }
}
