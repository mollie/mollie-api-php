<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Types\SubscriptionStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Subscription extends BaseResource
{
    public string $id;

    public string $customerId;

    /**
     * Either "live" or "test" depending on the customer's mode.
     */
    public string $mode;

    /**
     * UTC datetime the subscription was created in ISO-8601 format.
     */
    public string $createdAt;

    public SubscriptionStatus|string|null $status = null;

    public Money $amount;

    public ?int $times = null;

    public ?int $timesRemaining = null;

    public string $interval;

    public string $description;

    public ?string $method = null;

    public ?string $mandateId = null;

    /**
     * @var \stdClass|null
     */
    public $metadata;

    /**
     * UTC datetime the subscription was canceled in ISO-8601 format.
     */
    public ?string $canceledAt = null;

    /**
     * Date the subscription started. For example: 2018-04-24.
     */
    public ?string $startDate = null;

    /**
     * Contains an optional 'webhookUrl'.
     */
    public ?string $webhookUrl = null;

    /**
     * Date the next subscription payment will take place. For example: 2018-04-24.
     */
    public ?string $nextPaymentDate = null;

    /**
     * @var \stdClass|null
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

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active;
    }

    public function isPending(): bool
    {
        return $this->status === SubscriptionStatus::Pending;
    }

    public function isCanceled(): bool
    {
        return $this->status === SubscriptionStatus::Canceled;
    }

    public function isSuspended(): bool
    {
        return $this->status === SubscriptionStatus::Suspended;
    }

    public function isCompleted(): bool
    {
        return $this->status === SubscriptionStatus::Completed;
    }

    /**
     * Cancels this subscription.
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
     * Get subscription payments.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function payments(): PaymentCollection
    {
        if (! isset($this->_links->payments->href)) {
            return $this->connector->subscriptionPayments->pageFor($this, null, null, ['testmode' => $this->mode === 'test']);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->payments->href))->setHydratableResource(PaymentCollection::class));
    }
}
