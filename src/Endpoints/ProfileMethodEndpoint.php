<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ResourceFactory;

class ProfileMethodEndpoint extends EndpointCollection
{
    protected string $resourcePath = "profiles_methods";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Method
    {
        return new Method($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): MethodCollection
    {
        return new MethodCollection($count, $_links);
    }

    /**
     * Enable a method for the provided Profile ID.
     *
     * @param string $profileId
     * @param string $methodId
     * @param array $data
     *
     * @return Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $profileId, string $methodId, array $data = []): Method
    {
        $this->parentId = $profileId;

        $result = $this->client->performHttpCall(
            self::REST_CREATE,
            $this->getResourcePath() . '/' . urlencode($methodId),
            $this->parseRequestBody($data)
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Enable a method for the provided Profile object.
     *
     * @param Profile $profile
     * @param string $methodId
     * @param array $data
     *
     * @return Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Profile $profile, string $methodId, array $data = []): Method
    {
        return $this->createForId($profile->id, $methodId, $data);
    }

    /**
     * Enable a method for the current profile.
     *
     * @param string $methodId
     * @param array $data
     *
     * @return Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForCurrentProfile(string $methodId, array $data = []): Method
    {
        return $this->createForId('me', $methodId, $data);
    }

    /**
     * Disable a method for the provided Profile ID.
     *
     * @param string $profileId
     * @param string $methodId
     * @param array $data
     *
     * @return null|Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function deleteForId($profileId, $methodId, array $data = []): ?Method
    {
        $this->parentId = $profileId;

        return $this->deleteResource($methodId, $data);
    }

    /**
     * Disable a method for the provided Profile object.
     *
     * @param Profile $profile
     * @param string $methodId
     * @param array $data
     *
     * @return null|Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function deleteFor($profile, $methodId, array $data = []): ?Method
    {
        return $this->deleteForId($profile->id, $methodId, $data);
    }

    /**
     * Disable a method for the current profile.
     *
     * @param string $methodId
     * @param array $data
     *
     * @return null|Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function deleteForCurrentProfile($methodId, array $data): ?Method
    {
        return $this->deleteForId('me', $methodId, $data);
    }
}
