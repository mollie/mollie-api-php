<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Partners;
use Mollie\Api\Resources\PartnersCollection;

class PartnersEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "clients";

    /**
     * @return Partners
     */
    protected function getResourceObject()
    {
        return new Partners($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return PartnersCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PartnersCollection($this->client, $count, $_links);
    }

    /**
     * Retrieve an organization from Mollie.
     *
     * Will throw a ApiException if the organization id is invalid or the resource cannot be found.
     *
     * @param string $clientId
     * @param array $parameters
     * @return Partners
     * @throws ApiException
     */
    public function get($clientId, array $parameters = [])
    {
        if (empty($clientId)) {
            throw new ApiException("Organization ID is empty.");
        }

        return parent::rest_read($clientId, $parameters);
    }

    /**
     * Retrieves a collection of Partners from Mollie.
     *
     * @param string $from The first client ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PartnersCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
