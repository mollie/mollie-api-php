<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Types\SubscriptionStatus;

class Subscription extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $id;

    /**
     * Either "live" or "test" depending on the customer's mode.
     *
     * @var string
     */
    public $mode;

    /**
     * UTC datetime the subscription created in ISO-8601 format.
     *
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $status;

    /**
     * @var object
     */
    public $amount;

    /**
     * @var int|null
     */
    public $times;

    /**
     * @var string
     */
    public $interval;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string|null
     */
    public $method;

    /**
     * UTC datetime the subscription canceled in ISO-8601 format.
     *
     * @var string|null
     */
    public $canceledAt;

    /**
     * Date the subscription started. For example: 2018-04-24
     *
     * @var string|null
     */
    public $startDate;

    /**
     * Contains an optional 'webhookUrl'.
     *
     * @var object|null
     */
    public $webhookUrl;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * Returns whether the Subscription is active or not.
     *
     * @return bool
     */
    public function isActive ()
    {
        return $this->status === SubscriptionStatus::STATUS_ACTIVE;
    }

    /**
     * Returns whether the Subscription is pending or not.
     *
     * @return bool
     */
    public function isPending ()
    {
        return $this->status === SubscriptionStatus::STATUS_PENDING;
    }

    /**
     * Returns whether the Subscription is canceled or not.
     *
     * @return bool
     */
    public function isCanceled ()
    {
        return $this->status === SubscriptionStatus::STATUS_CANCELED;
    }

    /**
     * Returns whether the Subscription is suspended or not.
     *
     * @return bool
     */
    public function isSuspended ()
    {
        return $this->status === SubscriptionStatus::STATUS_SUSPENDED;
    }

    /**
     * Returns whether the Subscription is completed or not.
     *
     * @return bool
     */
    public function isCompleted ()
    {
        return $this->status === SubscriptionStatus::STATUS_COMPLETED;
    }
}