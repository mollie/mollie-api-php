<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Customer extends BaseResource
{
    use HasMode;

    /**
     * Id of the customer.
     *
     * @var string
     */
    public $id;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) customer.
     *
     * @var string
     */
    public $mode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string|null
     */
    public $locale;

    /**
     * @var \stdClass|mixed|null
     */
    public $metadata;

    /**
     * @var string[]|array
     */
    public $recentlyUsedMethods;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * @throws ApiException
     */
    public function update(): ?Customer
    {
        $body = [
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'metadata' => $this->metadata,
        ];

        /** @var null|Customer */
        return $this->connector->customers->update($this->id, $body);
    }

    /**
     * @return Payment
     *
     * @throws ApiException
     */
    public function createPayment(array $options = [], array $filters = [])
    {
        return $this->connector->customerPayments->createFor(
            $this,
            $options,
            $filters,
            $this->isInTestmode()
        );
    }

    /**
     * Get all payments for this customer
     *
     * @return PaymentCollection
     *
     * @throws ApiException
     */
    public function payments()
    {
        return $this->connector->customerPayments->pageFor($this, null, null, $this->withMode());
    }

    /**
     * @return Subscription
     *
     * @throws ApiException
     */
    public function createSubscription(array $options = [], array $filters = [])
    {
        return $this->connector->subscriptions->createFor($this, $options, $this->isInTestmode());
    }

    /**
     * @param  string  $subscriptionId
     * @return Subscription
     *
     * @throws ApiException
     */
    public function getSubscription($subscriptionId)
    {
        return $this->connector->subscriptions->getFor($this, $subscriptionId, $this->isInTestmode());
    }

    /**
     * @param  string  $subscriptionId
     * @return \Mollie\Api\Resources\Subscription
     *
     * @throws ApiException
     */
    public function cancelSubscription($subscriptionId)
    {
        return $this->connector->subscriptions->cancelFor($this, $subscriptionId, $this->isInTestmode());
    }

    /**
     * Get all subscriptions for this customer
     *
     * @return SubscriptionCollection
     *
     * @throws ApiException
     */
    public function subscriptions()
    {
        return $this->connector->subscriptions->pageFor($this, null, null, $this->withMode());
    }

    /**
     * @return Mandate
     *
     * @throws ApiException
     */
    public function createMandate(array $options = [])
    {
        return $this->connector->mandates->createFor($this, $options, $this->isInTestmode());
    }

    /**
     * @param  string  $mandateId
     * @return Mandate
     *
     * @throws ApiException
     */
    public function getMandate($mandateId, array $parameters = [])
    {
        return $this->connector->mandates->getFor($this, $mandateId, $this->withMode($parameters));
    }

    /**
     * @param  string  $mandateId
     *
     * @throws ApiException
     */
    public function revokeMandate($mandateId): void
    {
        $this->connector->mandates->revokeFor($this, $mandateId, $this->withMode());
    }

    /**
     * Get all mandates for this customer
     *
     * @return MandateCollection
     *
     * @throws ApiException
     */
    public function mandates()
    {
        return $this->connector->mandates->pageFor($this, null, null, $this->withMode());
    }

    /**
     * Helper function to check for mandate with status valid
     *
     * @return bool
     */
    public function hasValidMandate()
    {
        return $this->mandates()
            ->contains(fn (Mandate $mandate) => $mandate->isValid());
    }

    /**
     * Helper function to check for specific payment method mandate with status valid
     */
    public function hasValidMandateForMethod($method): bool
    {
        return $this->mandates()
            ->contains(fn (Mandate $mandate) => $mandate->isValid() && $mandate->method === $method);
    }
}
