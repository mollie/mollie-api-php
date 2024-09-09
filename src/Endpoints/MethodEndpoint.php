<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;

class MethodEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     */
    protected string $resourcePath = 'methods';

    /**
     * Resource class name.
     */
    public static string $resource = Method::class;

    /**
     * The resource collection class name.
     */
    public static string $resourceCollection = MethodCollection::class;

    /**
     * Retrieve all active methods. In test mode, this includes pending methods. The results are not paginated.
     *
     * @deprecated Use allActive() instead
     *
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
     *
     * @throws ApiException
     */
    public function allActive(array $parameters = []): MethodCollection
    {
        /** @var MethodCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * Retrieve all available methods for the organization, including activated and not yet activated methods. The
     * results are not paginated. Make sure to include the profileId parameter if using an OAuth Access Token.
     *
     * @param  array  $parameters  Query string parameters.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function allAvailable(array $parameters = []): MethodCollection
    {
        $url = 'methods/all'.$this->buildQueryString($parameters);

        return $this
            ->client
            ->send(new DynamicGetRequest(
                $url,
                MethodCollection::class,
            ));
    }

    /**
     * Retrieve a payment method from Mollie.
     *
     * Will throw a ApiException if the method id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function get(string $methodId, array $parameters = []): Method
    {
        /** @var Method */
        return $this->readResource($methodId, $parameters);
    }
}
