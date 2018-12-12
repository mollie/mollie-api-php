<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ResourceFactory;

class ProfileMethodEndpoint extends EndpointAbstract
{
    protected $resourcePath = "profiles_methods";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Method
     */
    protected function getResourceObject()
    {
        return new Method($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return MethodCollection()
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new MethodCollection($count, $_links);
    }

    /**
     * @param Profile $profile
     * @param string $methodId
     * @param array $options
     * @return Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor($profile, $methodId, array $options = [])
    {
        $this->parentId = $profile->id;
        $resource = $this->getResourcePath() . '/' . urlencode($methodId);

        $body = null;
        if (count($options) > 0) {
            $body = json_encode($options);
        }

        $result = $this->client->performHttpCall(self::REST_CREATE, $resource, $body);

        return ResourceFactory::createFromApiResult($result, new Method($this->client));
    }

}
