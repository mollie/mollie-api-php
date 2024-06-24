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
            [SubscriptionStatus::PENDING, "isPending", true],
            [SubscriptionStatus::PENDING, "isCanceled", false],
            [SubscriptionStatus::PENDING, "isCompleted", false],
            [SubscriptionStatus::PENDING, "isSuspended", false],
            [SubscriptionStatus::PENDING, "isActive", false],

            [SubscriptionStatus::CANCELED, "isPending", false],
            [SubscriptionStatus::CANCELED, "isCanceled", true],
            [SubscriptionStatus::CANCELED, "isCompleted", false],
            [SubscriptionStatus::CANCELED, "isSuspended", false],
            [SubscriptionStatus::CANCELED, "isActive", false],

            [SubscriptionStatus::COMPLETED, "isPending", false],
            [SubscriptionStatus::COMPLETED, "isCanceled", false],
            [SubscriptionStatus::COMPLETED, "isCompleted", true],
            [SubscriptionStatus::COMPLETED, "isSuspended", false],
            [SubscriptionStatus::COMPLETED, "isActive", false],

            [SubscriptionStatus::SUSPENDED, "isPending", false],
            [SubscriptionStatus::SUSPENDED, "isCanceled", false],
            [SubscriptionStatus::SUSPENDED, "isCompleted", false],
            [SubscriptionStatus::SUSPENDED, "isSuspended", true],
            [SubscriptionStatus::SUSPENDED, "isActive", false],

            [SubscriptionStatus::ACTIVE, "isPending", false],
            [SubscriptionStatus::ACTIVE, "isCanceled", false],
            [SubscriptionStatus::ACTIVE, "isCompleted", false],
            [SubscriptionStatus::ACTIVE, "isSuspended", false],
            [SubscriptionStatus::ACTIVE, "isActive", true],
        ];
    }
}
