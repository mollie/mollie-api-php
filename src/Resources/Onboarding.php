<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\OnboardingStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Onboarding extends BaseResource
{
    public string $name;

    public string $signedUpAt;

    public OnboardingStatus|string $status;

    public bool $canReceivePayments;

    public bool $canReceiveSettlements;

    /**
     * @var \stdClass
     */
    public $_links;

    public function needsData(): bool
    {
        return $this->status === OnboardingStatus::NeedsData;
    }

    public function inReview(): bool
    {
        return $this->status === OnboardingStatus::InReview;
    }

    public function isCompleted(): bool
    {
        return $this->status === OnboardingStatus::Completed;
    }
}
