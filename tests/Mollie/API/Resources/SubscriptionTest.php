<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\SubscriptionStatus;

class SubscriptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestSubscriptionStatuses
     */
    public function testSubscriptionStatuses($status, $function, $expected_boolean)
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = $status;

        $this->assertEquals($expected_boolean, $subscription->{$function}());
    }

    public function dpTestSubscriptionStatuses()
    {
        return [
            [SubscriptionStatus::STATUS_PENDING, "isPending", true],
            [SubscriptionStatus::STATUS_PENDING, "isCanceled", false],
            [SubscriptionStatus::STATUS_PENDING, "isCompleted", false],
            [SubscriptionStatus::STATUS_PENDING, "isSuspended", false],
            [SubscriptionStatus::STATUS_PENDING, "isActive", false],

            [SubscriptionStatus::STATUS_CANCELED, "isPending", false],
            [SubscriptionStatus::STATUS_CANCELED, "isCanceled", true],
            [SubscriptionStatus::STATUS_CANCELED, "isCompleted", false],
            [SubscriptionStatus::STATUS_CANCELED, "isSuspended", false],
            [SubscriptionStatus::STATUS_CANCELED, "isActive", false],

            [SubscriptionStatus::STATUS_COMPLETED, "isPending", false],
            [SubscriptionStatus::STATUS_COMPLETED, "isCanceled", false],
            [SubscriptionStatus::STATUS_COMPLETED, "isCompleted", true],
            [SubscriptionStatus::STATUS_COMPLETED, "isSuspended", false],
            [SubscriptionStatus::STATUS_COMPLETED, "isActive", false],

            [SubscriptionStatus::STATUS_SUSPENDED, "isPending", false],
            [SubscriptionStatus::STATUS_SUSPENDED, "isCanceled", false],
            [SubscriptionStatus::STATUS_SUSPENDED, "isCompleted", false],
            [SubscriptionStatus::STATUS_SUSPENDED, "isSuspended", true],
            [SubscriptionStatus::STATUS_SUSPENDED, "isActive", false],

            [SubscriptionStatus::STATUS_ACTIVE, "isPending", false],
            [SubscriptionStatus::STATUS_ACTIVE, "isCanceled", false],
            [SubscriptionStatus::STATUS_ACTIVE, "isCompleted", false],
            [SubscriptionStatus::STATUS_ACTIVE, "isSuspended", false],
            [SubscriptionStatus::STATUS_ACTIVE, "isActive", true],
        ];
    }
}
