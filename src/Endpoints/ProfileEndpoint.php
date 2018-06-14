<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

class ProfileEndpoint extends EndpointAbstract
{
    protected $resourcePath = "profiles";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Profile
     */
    protected function getResourceObject()
    {
        return new Profile($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return ProfileCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new ProfileCollection($this->api, $count, $_links);
    }

    /**
     * Creates a Profile in Mollie.
     *
     * @param array $data An array containing details on the profile.
     * @param array $filters
     *
     * @return Profile
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
       return $this->rest_create($data, $filters);
    }

    /**
     * Retrieve a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     * @param array $parameters
     *
     * @return Profile
     * @throws ApiException
     */
    public function get($profileId, array $parameters = [])
    {
        return $this->rest_read($profileId, $parameters);
    }

    /**
     * Delete a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $profileId
     *
     * @return Profile
     * @throws ApiException
     */
    public function delete($profileId)
    {
        return $this->rest_delete($profileId);
    }

    /**
     * Retrieves a collection of Profiles from Mollie.
     *
     * @param string $from The first profile ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return ProfileCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }


}