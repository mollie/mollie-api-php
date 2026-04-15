<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\OnboardingStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Onboarding extends BaseResource
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $signedUpAt;

    /**
     * Either "needs-data", "in-review" or "completed".
     * Indicates this current status of the organization’s onboarding process.
     *
     * @var string
     */
    public $status;

    /**
     * @var bool
     */
    public $canReceivePayments;

    /**
     * @var bool
     */
    public $canReceiveSettlements;

    /**
     * @var \stdClass
     */
    public $_links;

    public function needsData(): bool
    {
        return $this->status === OnboardingStatus::NeedsData->value;
    }

    public function inReview(): bool
    {
        return $this->status === OnboardingStatus::InReview->value;
    }

    public function isCompleted(): bool
    {
        return $this->status === OnboardingStatus::Completed->value;
    }
}
