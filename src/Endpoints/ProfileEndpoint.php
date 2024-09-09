<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

class ProfileEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "profiles";

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     *
     * @var string
     */
    protected static string $resourceIdPrefix = 'pfl_';

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Profile::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = ProfileCollection::class;

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
        /** @var Profile */
        return $this->createResource($data, $filters);
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

        /** @var Profile */
        return $this->readResource($profileId, $parameters);
    }

    /**
     * Update a specific Profile resource.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param string $profileId
     * @param array $data
     * @return null|Profile
     * @throws ApiException
     */
    public function update(string $profileId, array $data = []): ?Profile
    {
        $this->guardAgainstInvalidId($profileId);

        /** @var null|Profile */
        return $this->updateResource($profileId, $data);
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
        static::$resource = CurrentProfile::class;

        /** @var CurrentProfile */
        return $this->readResource('me', $parameters);
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
     * @return null|Profile
     * @throws ApiException
     */
    public function delete($profileId, array $data = []): ?Profile
    {
        /** @var null|Profile */
        return $this->deleteResource($profileId, $data);
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
        /** @var ProfileCollection */
        return $this->fetchCollection($from, $limit, $parameters);
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
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
