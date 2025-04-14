<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateProfileRequestFactory;
use Mollie\Api\Factories\UpdateProfileRequestFactory;
use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Requests\GetCurrentProfileRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;
use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;
use Mollie\Api\Utils\Utility;

class ProfileEndpointCollection extends EndpointCollection
{
    /**
     * Creates a Profile in Mollie.
     *
     * @throws RequestException
     */
    public function create(array $payload = []): Profile
    {
        $request = CreateProfileRequestFactory::new()
            ->withPayload($payload)
            ->create();

        /** @var Profile */
        return $this->send($request);
    }

    /**
     * Retrieve a Profile from Mollie.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     * @return Profile|CurrentProfile
     *
     * @throws RequestException
     */
    public function get(string $profileId, $testmode = false): Profile
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Profile */
        return $this->send((new GetProfileRequest($profileId))->test($testmode));
    }

    /**
     * Retrieve the current Profile from Mollie.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function getCurrent($testmode = false): CurrentProfile
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var CurrentProfile */
        return $this->send((new GetCurrentProfileRequest)->test($testmode));
    }

    /**
     * Update a specific Profile resource.
     *
     * @throws RequestException
     */
    public function update(string $profileId, array $payload = []): ?Profile
    {
        $request = UpdateProfileRequestFactory::new($profileId)
            ->withPayload($payload)
            ->create();

        /** @var Profile|null */
        return $this->send($request);
    }

    /**
     * Delete a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @throws RequestException
     */
    public function delete(string $profileId): void
    {
        $this->send(new DeleteProfileRequest($profileId));
    }

    /**
     * Retrieves a collection of Profiles from Mollie.
     *
     * @param  string|null  $from  The first profile ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null): ProfileCollection
    {
        /** @var ProfileCollection */
        return $this->send(new GetPaginatedProfilesRequest($from, $limit));
    }

    /**
     * Create an iterator for iterating over profiles retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->send(
            (new GetPaginatedProfilesRequest($from, $limit))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
