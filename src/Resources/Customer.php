<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

class Customer extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

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
     * @var object|mixed|null
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
     * @var object[]
     */
    public $_links;

    /**
     * @return BaseResource
     */
    public function update()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $body = json_encode(array(
            "name" => $this->name,
            "email" => $this->email,
            "locale" => $this->locale,
            "metadata" => $this->metadata,
        ));

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_POST, $this->_links->self->href, $body);

        return ResourceFactory::createFromApiResult($result, new Customer($this->client));
    }

    /**
     * @param array $options
     * @param array $filters
     *
     * @return object
     */
    public function createPayment(array $options = [], array $filters = [])
    {
        return $this->client->customerPayments->createFor($this, $options, $filters);
    }

    /**
     * Get all payments for this customer
     */
    public function payments()
    {
        return $this->client->customerPayments->listFor($this);
    }

    /**
     * @param array $options
     * @param array $filters
     *
     * @return object
     */
    public function createSubscription(array $options = [], array $filters = [])
    {
        return $this->client->subscriptions->createFor($this, $options, $filters);
    }

    /**
     * @param string $subscriptionId
     * @param array $parameters
     *
     * @return object
     */
    public function getSubscription($subscriptionId, array $parameters = [])
    {
        return $this->client->subscriptions->getFor($this, $subscriptionId, $parameters);
    }

    /**
     * @param string $subscriptionId
     *
     * @return object
     */
    public function cancelSubscription($subscriptionId)
    {
        return $this->client->subscriptions->cancelFor($this, $subscriptionId);
    }

    /**
     * Get all subscriptions for this customer
     */
    public function subscriptions()
    {
        return $this->client->subscriptions->listFor($this);
    }

    /**
     * @param array $options
     * @param array $filters
     *
     * @return object
     */
    public function createMandate(array $options = [], array $filters = [])
    {
        return $this->client->mandates->createFor($this, $options, $filters);
    }

    /**
     * @param string $mandateId
     * @param array $parameters
     *
     * @return object
     */
    public function getMandate($mandateId, array $parameters = [])
    {
        return $this->client->mandates->getFor($this, $mandateId, $parameters);
    }

    /**
     * @param string $mandateId
     *
     * @return object
     */
    public function revokeMandate($mandateId)
    {
        return $this->client->mandates->revokeFor($this, $mandateId);
    }

    /**
     * Get all mandates for this customer
     */
    public function mandates()
    {
        return $this->client->mandates->listFor($this);
    }
}