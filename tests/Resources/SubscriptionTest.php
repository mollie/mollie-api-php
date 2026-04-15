<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\SubscriptionStatus;

class SubscriptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestSubscriptionStatuses
     */
    public function test_subscription_statuses($status, $function, $expected_boolean)
    {
        $subscription = new Subscription(
            $this->createMock(MollieApiClient::class),
        );
        $subscription->status = $status;

        $this->assertEquals($expected_boolean, $subscription->{$function}());
    }

    public function dpTestSubscriptionStatuses()
    {
        return [
            [SubscriptionStatus::Pending->value, 'isPending', true],
            [SubscriptionStatus::Pending->value, 'isCanceled', false],
            [SubscriptionStatus::Pending->value, 'isCompleted', false],
            [SubscriptionStatus::Pending->value, 'isSuspended', false],
            [SubscriptionStatus::Pending->value, 'isActive', false],

            [SubscriptionStatus::Canceled->value, 'isPending', false],
            [SubscriptionStatus::Canceled->value, 'isCanceled', true],
            [SubscriptionStatus::Canceled->value, 'isCompleted', false],
            [SubscriptionStatus::Canceled->value, 'isSuspended', false],
            [SubscriptionStatus::Canceled->value, 'isActive', false],

            [SubscriptionStatus::Completed->value, 'isPending', false],
            [SubscriptionStatus::Completed->value, 'isCanceled', false],
            [SubscriptionStatus::Completed->value, 'isCompleted', true],
            [SubscriptionStatus::Completed->value, 'isSuspended', false],
            [SubscriptionStatus::Completed->value, 'isActive', false],

            [SubscriptionStatus::Suspended->value, 'isPending', false],
            [SubscriptionStatus::Suspended->value, 'isCanceled', false],
            [SubscriptionStatus::Suspended->value, 'isCompleted', false],
            [SubscriptionStatus::Suspended->value, 'isSuspended', true],
            [SubscriptionStatus::Suspended->value, 'isActive', false],

            [SubscriptionStatus::Active->value, 'isPending', false],
            [SubscriptionStatus::Active->value, 'isCanceled', false],
            [SubscriptionStatus::Active->value, 'isCompleted', false],
            [SubscriptionStatus::Active->value, 'isSuspended', false],
            [SubscriptionStatus::Active->value, 'isActive', true],
        ];
    }
}
