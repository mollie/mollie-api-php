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
     * @param array $data
     * @return Method
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor($profile, $methodId, array $data = [])
    {
        $this->parentId = $profile->id;
        $resource = $this->getResourcePath() . '/' . urlencode($methodId);

        $body = null;
        if (count($data) > 0) {
            $body = json_encode($data);
        }

        $result = $this->client->performHttpCall(self::REST_CREATE, $resource, $body);

        return ResourceFactory::createFromApiResult($result, new Method($this->client));
    }

    /**
     * @param $profile
     * @param $methodId
     * @param array $data
     * @return \Mollie\Api\Resources\BaseResource
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function deleteFor($profile, $methodId, array $data = [])
    {
        $this->parentId = $profile->id;

        return $this->rest_delete($methodId, $data);
    }

}
