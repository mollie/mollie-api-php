<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\SubscriptionStatus;

class SubscriptionTest extends \PHPUnit\Framework\TestCase
{
    public function testIsActiveIsTrueWhenStatusIsActive ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_ACTIVE;

        $this->assertTrue($subscription->isActive());
    }

    public function testIsActiveIsFalseWhenStatusIsNotActive ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_PENDING;

        $this->assertFalse($subscription->isActive());
    }

    public function testIsPendingIsTrueWhenStatusIsPending()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_PENDING;

        $this->assertTrue($subscription->isPending());
    }

    public function testIsPendingIsTrueWhenStatusIsNotPending ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_ACTIVE;

        $this->assertFalse($subscription->isPending());
    }

    public function testIsCanceledIsTrueWhenStatusIsCanceled()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_CANCELED;

        $this->assertTrue($subscription->isCanceled());
    }

    public function testIsCanceledIsTrueWhenStatusIsNotCanceled ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_PENDING;

        $this->assertFalse($subscription->isCanceled());
    }

    public function testIsSuspendedIsTrueWhenStatusIsSuspended()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_SUSPENDED;

        $this->assertTrue($subscription->isSuspended());
    }

    public function testIsSuspendedIsTrueWhenStatusIsNotSuspended ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_PENDING;

        $this->assertFalse($subscription->isSuspended());
    }

    public function testIsCompletedIsTrueWhenStatusIsCompleted()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_COMPLETED;

        $this->assertTrue($subscription->isCompleted());
    }

    public function testIsCompletedIsTrueWhenStatusIsNotCompleted ()
    {
        $subscription = new Subscription($this->createMock(MollieApiClient::class));
        $subscription->status = SubscriptionStatus::STATUS_PENDING;

        $this->assertFalse($subscription->isCompleted());
    }

}
