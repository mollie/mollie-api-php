<?php

namespace Tests\Mollie\API\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Types\OnboardingStatus;

class OnboardingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestOnboardingStatuses
     */
    public function testOnboardingStatuses($status, $function, $expected_boolean)
    {
        $orderLine = new Onboarding($this->createMock(MollieApiClient::class));
        $orderLine->status = $status;

        $this->assertEquals($expected_boolean, $orderLine->{$function}());
    }

    public function dpTestOnboardingStatuses()
    {
        return [
            [OnboardingStatus::NEEDS_DATA, "needsData", true],
            [OnboardingStatus::NEEDS_DATA, "isInReview", false],
            [OnboardingStatus::NEEDS_DATA, "isCompleted", false],

            [OnboardingStatus::IN_REVIEW, "needsData", false],
            [OnboardingStatus::IN_REVIEW, "isInReview", true],
            [OnboardingStatus::IN_REVIEW, "isCompleted", false],

            [OnboardingStatus::COMPLETED, "needsData", false],
            [OnboardingStatus::COMPLETED, "isInReview", false],
            [OnboardingStatus::COMPLETED, "isCompleted", true],
        ];
    }
}
