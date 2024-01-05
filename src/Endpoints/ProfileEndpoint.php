<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

class ProfileEndpoint extends CollectionRestEndpoint
{
    protected string $resourcePath = "profiles";

    protected $resourceClass = Profile::class;

    public const RESOURCE_ID_PREFIX = 'pfl_';
    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Profile
    {
        return new $this->resourceClass($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): ProfileCollection
    {
        return new ProfileCollection($this->client, $count, $_links);
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
    public function create(array $data = [], array $filters = []): Profile
    {
        return $this->rest_create($data, $filters);
    }

    /**
     * Retrieve a Profile from Mollie.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     * @param array $parameters
     *
     * @return Profile
     * @throws ApiException
     */
    public function get($profileId, array $parameters = []): Profile
    {
        if ($profileId === 'me') {
            return $this->getCurrent($parameters);
        }

        return $this->rest_read($profileId, $parameters);
    }

    /**
     * Update a specific Profile resource.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     * @param array $data
     * @return Profile
     * @throws ApiException
     */
    public function update(string $profileId, array $data = []): Profile
    {
        if (empty($profileId) || strpos($profileId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid profile id: '{$profileId}'. An profile id should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::rest_update($profileId, $data);
    }

    /**
     * Retrieve the current Profile from Mollie.
     *
     * @param array $parameters
     *
     * @return CurrentProfile
     * @throws ApiException
     */
    public function getCurrent(array $parameters = []): CurrentProfile
    {
        $this->resourceClass = CurrentProfile::class;

        return $this->rest_read('me', $parameters);
    }

    /**
     * Delete a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $profileId
     *
     * @param array $data
     * @return Profile
     * @throws ApiException
     */
    public function delete($profileId, array $data = []): ?Profile
    {
        return $this->rest_delete($profileId, $data);
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
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): ProfileCollection
    {
        return $this->rest_list($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over profiles retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->rest_iterator($from, $limit, $parameters, $iterateBackwards);
    }
}
