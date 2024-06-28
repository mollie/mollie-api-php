<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\SubscriptionStatus;

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
     * @return null|Subscription
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): ?Subscription
    {
        $body = [
            "amount" => $this->amount,
            "times" => $this->times,
            "startDate" => $this->startDate,
            "webhookUrl" => $this->webhookUrl,
            "description" => $this->description,
            "mandateId" => $this->mandateId,
            "metadata" => $this->metadata,
            "interval" => $this->interval,
        ];

        return $this->client->subscriptions->update($this->customerId, $this->id, $body);
    }

    /**
     * Returns whether the Subscription is active or not.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE;
    }

    /**
     * Returns whether the Subscription is pending or not.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === SubscriptionStatus::PENDING;
    }

    /**
     * Returns whether the Subscription is canceled or not.
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->status === SubscriptionStatus::CANCELED;
    }

    /**
     * Returns whether the Subscription is suspended or not.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === SubscriptionStatus::SUSPENDED;
    }

    /**
     * Returns whether the Subscription is completed or not.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === SubscriptionStatus::COMPLETED;
    }

    /**
     * Cancels this subscription
     *
     * @return null|Subscription
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(): ?Subscription
    {
        if (! isset($this->_links->self->href)) {
            return $this;
        }

        $body = null;
        if ($this->client->usesOAuth()) {
            $body = json_encode([
                "testmode" => $this->mode === "test" ? true : false,
            ]);
        }

        $result = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_DELETE,
            $this->_links->self->href,
            $body
        );

        if ($result->isEmpty()) {
            return null;
        }

        /** @var Subscription */
        return ResourceFactory::createFromApiResult($this->client, $result->decode(), Subscription::class);
    }

    /**
     * Get subscription payments
     *
     * @return PaymentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function payments(): PaymentCollection
    {
        if (! isset($this->_links->payments->href)) {
            return new PaymentCollection($this->client);
        }

        $result = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_GET,
            $this->_links->payments->href
        )->decode();

        /** @var PaymentCollection */
        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->payments,
            Payment::class,
            $result->_links
        );
    }
}
