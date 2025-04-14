<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Types\SubscriptionStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Subscription extends BaseResource
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $customerId;

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
     * @var \stdClass
     */
    public $amount;

    /**
     * @var int|null
     */
    public $times;

    /**
     * @var int|null
     */
    public $timesRemaining;

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
     * @var string|null
     */
    public $mandateId;

    /**
     * @var \stdClass|null
     */
    public $metadata;

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
     * @var \stdClass|null
     */
    public $webhookUrl;

    /**
     * Date the next subscription payment will take place. For example: 2018-04-24
     *
     * @var string|null
     */
    public $nextPaymentDate;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): ?Subscription
    {
        $body = [
            'amount' => $this->amount,
            'times' => $this->times,
            'startDate' => $this->startDate,
            'webhookUrl' => $this->webhookUrl,
            'description' => $this->description,
            'mandateId' => $this->mandateId,
            'metadata' => $this->metadata,
            'interval' => $this->interval,
        ];

        return $this->connector->subscriptions->update($this->customerId, $this->id, $body);
    }

    /**
     * Returns whether the Subscription is active or not.
     */
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE;
    }

    /**
     * Returns whether the Subscription is pending or not.
     */
    public function isPending(): bool
    {
        return $this->status === SubscriptionStatus::PENDING;
    }

    /**
     * Returns whether the Subscription is canceled or not.
     */
    public function isCanceled(): bool
    {
        return $this->status === SubscriptionStatus::CANCELED;
    }

    /**
     * Returns whether the Subscription is suspended or not.
     */
    public function isSuspended(): bool
    {
        return $this->status === SubscriptionStatus::SUSPENDED;
    }

    /**
     * Returns whether the Subscription is completed or not.
     */
    public function isCompleted(): bool
    {
        return $this->status === SubscriptionStatus::COMPLETED;
    }

    /**
     * Cancels this subscription
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(): ?Subscription
    {
        if (! isset($this->_links->self->href)) {
            return $this;
        }

        return $this
            ->connector
            ->send((new CancelSubscriptionRequest(
                $this->customerId,
                $this->id
            ))->test($this->mode === 'test'));
    }

    /**
     * Get subscription payments
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function payments(): PaymentCollection
    {
        if (! isset($this->_links->payments->href)) {
            return PaymentCollection::withResponse($this->response, $this->connector);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->payments->href))->setHydratableResource(PaymentCollection::class));
    }
}
