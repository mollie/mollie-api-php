<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;

class MandateEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "customers_mandates";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Mandate
     */
    protected function getResourceObject()
    {
        return new Mandate($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return MandateCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new MandateCollection($this->client, $count, $_links);
    }

    /**
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return \Mollie\Api\Resources\Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Customer $customer, array $options = [], array $filters = [])
    {
        return $this->createForId($customer->id, $options, $filters);
    }

    /**
     * @param string $customerId
     * @param array $options
     * @param array $filters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId($customerId, array $options = [], array $filters = [])
    {
        $this->parentId = $customerId;

        return parent::rest_create($options, $filters);
    }

    /**
     * @param Customer $customer
     * @param string $mandateId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Customer $customer, $mandateId, array $parameters = [])
    {
        return $this->getForId($customer->id, $mandateId, $parameters);
    }

    /**
     * @param string $customerId
     * @param string $mandateId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId($customerId, $mandateId, array $parameters = [])
    {
        $this->parentId = $customerId;

        return parent::rest_read($mandateId, $parameters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\MandateCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Customer $customer, $from = null, $limit = null, array $parameters = [])
    {
        return $this->listForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * @param string $customerId
     * @param null $from
     * @param null $limit
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\MandateCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId($customerId, $from = null, $limit = null, array $parameters = [])
    {
        $this->parentId = $customerId;

        return parent::rest_list($from, $limit, $parameters);
    }

    /**
     * @param Customer $customer
     * @param string $mandateId
     * @param array $data
     *
     * @return null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function revokeFor(Customer $customer, $mandateId, $data = [])
    {
        return $this->revokeForId($customer->id, $mandateId, $data);
    }

    /**
     * @param string $customerId
     * @param string $mandateId
     * @param array $data
     *
     * @return null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function revokeForId($customerId, $mandateId, $data = [])
    {
        $this->parentId = $customerId;

        return parent::rest_delete($mandateId, $data);
    }
}
