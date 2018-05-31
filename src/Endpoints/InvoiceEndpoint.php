<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;

class InvoiceEndpoint extends EndpointAbstract
{
    protected $resourcePath = "invoices";

    /**
     * Get the object that is used by this API. Every API uses one type of object.
     *
     * @return \Mollie\Api\Resources\BaseResource
     */
    protected function getResourceObject()
    {
        return new Invoice($this->api);
    }

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return \Mollie\Api\Resources\BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new InvoiceCollection($this->api, $count, $_links);
    }

    /**
     * @param null $from
     * @param null $limit
     * @param array|null $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection
     */
    public function all($from = null, $limit = null, array $parameters = [])
    {
        return $this->page($from, $limit, $parameters);
    }
}