<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
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
     * @var \stdClass
     */
    public $_links;

    /**
     * @return BaseResource|Subscription
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $body = json_encode([
            "amount" => $this->amount,
            "times" => $this->times,
            "startDate" => $this->startDate,
            "webhookUrl" => $this->webhookUrl,
            "description" => $this->description,
            "mandateId" => $this->mandateId,
        ]);

        $result = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_PATCH,
            $this->_links->self->href,
            $body
        );

        return ResourceFactory::createFromApiResult($result, new Subscription($this->client));
    }


    /**
     * Returns whether the Subscription is active or not.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === SubscriptionStatus::STATUS_ACTIVE;
    }

    /**
     * Returns whether the Subscription is pending or not.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === SubscriptionStatus::STATUS_PENDING;
    }

    /**
     * Returns whether the Subscription is canceled or not.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === SubscriptionStatus::STATUS_CANCELED;
    }

    /**
     * Returns whether the Subscription is suspended or not.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status === SubscriptionStatus::STATUS_SUSPENDED;
    }

    /**
     * Returns whether the Subscription is completed or not.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === SubscriptionStatus::STATUS_COMPLETED;
    }

    /**
     * Cancels this subscription
     *
     * @return Subscription
     */
    public function cancel()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $body = null;
        if($this->client->usesOAuth()) {
            $body = json_encode([
                "testmode" => $this->mode === "test" ? true : false
            ]);
        }

        $result = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_DELETE,
            $this->_links->self->href,
            $body
        );

        return ResourceFactory::createFromApiResult($result, new Subscription($this->client));
    }
}
