<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Resources\ResourceFactory;

class MethodEndpoint extends CollectionEndpointAbstract
{
    protected string $resourcePath = "methods";

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
     * Retrieve all active methods. In test mode, this includes pending methods. The results are not paginated.
     *
     * @deprecated Use allActive() instead
     * @param array $parameters
     *
     * @return MethodCollection
     * @throws ApiException
     */
    public function all(array $parameters = []): MethodCollection
    {
        return $this->allActive($parameters);
    }

    /**
     * Retrieve all active methods for the organization. In test mode, this includes pending methods.
     * The results are not paginated.
     *
     * @param array $parameters
     *
     * @return MethodCollection
     * @throws ApiException
     */
    public function allActive(array $parameters = []): MethodCollection
    {
        return parent::rest_list(null, null, $parameters);
    }

    /**
     * Retrieve all available methods for the organization, including activated and not yet activated methods. The
     * results are not paginated. Make sure to include the profileId parameter if using an OAuth Access Token.
     *
     * @param array $parameters Query string parameters.
     * @return MethodCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function allAvailable(array $parameters = []): MethodCollection
    {
        $url = 'methods/all' . $this->buildQueryString($parameters);

        $result = $this->client->performHttpCall('GET', $url);

        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            Method::class,
            $result->_embedded->methods,
            $result->_links
        );
    }

    /**
     * Retrieve a payment method from Mollie.
     *
     * Will throw a ApiException if the method id is invalid or the resource cannot be found.
     *
     * @param string $methodId
     * @param array $parameters
     * @return Method
     * @throws ApiException
     */
    public function get(string $methodId, array $parameters = []): Method
    {
        if (empty($methodId)) {
            throw new ApiException("Method ID is empty.");
        }

        return parent::rest_read($methodId, $parameters);
    }
}
