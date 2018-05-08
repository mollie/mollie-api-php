<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class CustomerPaymentsEndpoint extends EndpointAbstract
{
    protected $resourcePath = "customers_payments";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return \Mollie\Api\Resources\BaseResource
     */
    protected function getResourceObject()
    {
        return new Payment($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return \Mollie\Api\Resources\BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PaymentCollection($this->api, $count, $_links);
    }

    /**
     * Create a subscription for a Customer
     *
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return object
     */
    public function createFor(Customer $customer, array $options = [], array $filters = [])
    {
        $this->parentId = $customer->id;

        return parent::create($options, $filters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return BaseCollection
     */
    public function listFor(Customer $customer, $from = null, $limit = null, array $parameters = [])
    {
        $this->parentId = $customer->id;

        return parent::page($from, $limit, $parameters);
    }
}