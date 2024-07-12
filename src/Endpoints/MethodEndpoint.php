<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Resources\ResourceFactory;
use Mollie\Api\Types\PaymentMethodStatus;

class MethodEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "methods";

    /**
     * @return Method
     */
    protected function getResourceObject()
    {
        return new Method($this->client);
    }

    /**
     * Retrieve all active methods. In test mode, this includes pending methods. The results are not paginated.
     *
     * @deprecated Use allActive() instead
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\MethodCollection
     * @throws ApiException
     */
    public function all(array $parameters = [])
    {
        return $this->allActive($parameters);
    }

    /**
     * Retrieve all active methods for the organization. In test mode, this includes pending methods.
     * The results are not paginated.
     *
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\MethodCollection
     * @throws ApiException
     */
    public function allActive(array $parameters = [])
    {
        $url = "methods/all" . $this->buildQueryString($parameters);
        $result = $this->client->performHttpCall("GET", $url);

        $allowedStatuses = $this->isUsingTestmode($parameters)
            ? [PaymentMethodStatus::ACTIVATED, PaymentMethodStatus::PENDING_REVIEW]
            : [PaymentMethodStatus::ACTIVATED];

        $data = array_filter($result->_embedded->methods, function ($method) use ($allowedStatuses) {
            return in_array($method->status, $allowedStatuses);
        });

        unset($result->_links->self); // Remove incorrect self link

        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            Method::class,
            $data,
            $result->_links
        );
    }

    /**
     * Retrieve all available methods for the organization, including activated and not yet activated methods. The
     * results are not paginated. Make sure to include the profileId parameter if using an OAuth Access Token.
     *
     * @param array $parameters Query string parameters.
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\MethodCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function allAvailable(array $parameters = [])
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
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return MethodCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new MethodCollection($count, $_links);
    }

    /**
     * Retrieve a payment method from Mollie.
     *
     * Will throw a ApiException if the method id is invalid or the resource cannot be found.
     *
     * @param string $methodId
     * @param array $parameters
     * @return \Mollie\Api\Resources\Method
     * @throws ApiException
     */
    public function get($methodId, array $parameters = [])
    {
        if (empty($methodId)) {
            throw new ApiException("Method ID is empty.");
        }

        return parent::rest_read($methodId, $parameters);
    }

    /**
     * Retrieve a complete, non-paginated collection of active Mollie payment methods.
     * Note that only Euro supporting methods are included.
     * For a full list of activated methods regardless the currency use the allActive() method.
     *
     * @param array $parameters
     * @return mixed|\Mollie\Api\Resources\BaseCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function list(array $parameters = [])
    {
        return $this->rest_list(null, null, $parameters);
    }

    /**
     * @param array $parameters
     * @return bool
     */
    protected function isUsingTestmode(array $parameters): bool
    {
        if ($this->client->usesOAuth()) {
            if (
                array_key_exists("testmode", $parameters)
                && is_bool($parameters["testmode"])
            ) {
                return $parameters["testmode"];
            }

            return false;
        }

        return $this->client->usesTestmodeApiKey();
    }
}
