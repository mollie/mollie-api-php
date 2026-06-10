<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\OnboardingStatus;
use Mollie\Api\Utils\Utility;

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
        return Utility::equals($this->status, OnboardingStatus::NeedsData);
    }

    public function inReview(): bool
    {
        return Utility::equals($this->status, OnboardingStatus::InReview);
    }

    public function isCompleted(): bool
    {
        return Utility::equals($this->status, OnboardingStatus::Completed);
    }
}
