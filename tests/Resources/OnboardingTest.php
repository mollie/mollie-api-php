<?php

declare(strict_types=1);

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
            [OnboardingStatus::NeedsData->value, 'needsData', true],
            [OnboardingStatus::NeedsData->value, 'inReview', false],
            [OnboardingStatus::NeedsData->value, 'isCompleted', false],

            [OnboardingStatus::InReview->value, 'needsData', false],
            [OnboardingStatus::InReview->value, 'inReview', true],
            [OnboardingStatus::InReview->value, 'isCompleted', false],

            [OnboardingStatus::Completed->value, 'needsData', false],
            [OnboardingStatus::Completed->value, 'inReview', false],
            [OnboardingStatus::Completed->value, 'isCompleted', true],
        ];
    }
}
