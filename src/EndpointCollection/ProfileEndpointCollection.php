<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateProfilePayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdateProfilePayloadFactory;
use Mollie\Api\Utils\Utility;
use Mollie\Api\Http\Data\CreateProfilePayload;
use Mollie\Api\Http\Data\UpdateProfilePayload;
use Mollie\Api\Http\Requests\CreateProfileRequest;
use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;
use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;

class ProfileEndpointCollection extends EndpointCollection
{
    /**
     * Creates a Profile in Mollie.
     *
     * @param  array|CreateProfilePayload  $payload  An array containing details on the profile.
     *
     * @throws ApiException
     */
    public function create($payload = []): Profile
    {
        if (! $payload instanceof CreateProfilePayload) {
            $payload = CreateProfilePayloadFactory::new($payload)
                ->create();
        }

        /** @var Profile */
        return $this->send(new CreateProfileRequest($payload));
    }

    /**
     * Retrieve a Profile from Mollie.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param  array|bool  $testmode
     * @return Profile|CurrentProfile
     *
     * @throws ApiException
     */
    public function get(string $profileId, $testmode = []): Profile
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Profile */
        return $this->send((new GetProfileRequest($profileId))->test($testmode));
    }

    /**
     * Retrieve the current Profile from Mollie.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function getCurrent($testmode = []): CurrentProfile
    {
        GetProfileRequest::$targetResourceClass = CurrentProfile::class;

        /** @var CurrentProfile */
        return $this->get('me', $testmode);
    }

    /**
     * Update a specific Profile resource.
     *
     * Will throw an ApiException if the profile id is invalid or the resource cannot be found.
     *
     * @param  array|UpdateProfilePayload  $payload
     *
     * @throws ApiException
     */
    public function update(string $profileId, $payload = []): ?Profile
    {
        if (! $payload instanceof UpdateProfilePayload) {
            $payload = UpdateProfilePayloadFactory::new($payload)
                ->create();
        }

        /** @var Profile|null */
        return $this->send(new UpdateProfileRequest($profileId, $payload));
    }

    /**
     * Delete a Profile from Mollie.
     *
     * Will throw a ApiException if the profile id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @throws ApiException
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
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null): ProfileCollection
    {
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        /** @var ProfileCollection */
        return $this->send(new GetPaginatedProfilesRequest($query));
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
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(
            (new GetPaginatedProfilesRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
