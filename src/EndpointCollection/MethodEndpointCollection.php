<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetAllMethodsQueryFactory as GetAllPaymentMethodsQueryFactory;
use Mollie\Api\Factories\GetEnabledMethodsQueryFactory as GetEnabledPaymentMethodsQueryFactory;
use Mollie\Api\Factories\GetPaymentMethodQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Query\GetAllMethodsQuery as GetAllPaymentMethodsQuery;
use Mollie\Api\Http\Query\GetEnabledMethodsQuery as GetEnabledPaymentMethodsQuery;
use Mollie\Api\Http\Query\GetPaymentMethodQuery;
use Mollie\Api\Http\Requests\GetAllMethodsRequest as GetAllPaymentMethodsRequest;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest as GetEnabledPaymentMethodsRequest;
use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;

class MethodEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve all methods from Mollie.
     * Will return all available methods, both enabled and disabled.
     *
     * @param  array|GetAllPaymentMethodsQuery  $query  Query string parameters
     *
     * @throws ApiException
     */
    public function all($query = []): MethodCollection
    {
        if (! $query instanceof GetAllPaymentMethodsQuery) {
            $query = GetAllPaymentMethodsQueryFactory::new($query)
                ->create();
        }

        /** @var MethodCollection */
        return $this->send(new GetAllPaymentMethodsRequest($query));
    }

    /**
     * Retrieve all enabled methods for the organization.
     * In test mode, this includes pending methods.
     * The results are not paginated.
     *
     * @throws ApiException
     */
    public function allEnabled($query = [], bool $testmode = false): MethodCollection
    {
        if (! $query instanceof GetEnabledPaymentMethodsQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetEnabledPaymentMethodsQueryFactory::new($query)
                ->create();
        }

        /** @var MethodCollection */
        return $this->send((new GetEnabledPaymentMethodsRequest($query))->test($testmode));
    }

    /**
     * @deprecated Use allEnabled() instead
     *
     * @throws ApiException
     */
    public function allActive($query = [], ?bool $testmode = null): MethodCollection
    {
        return $this->allEnabled($query, $testmode);
    }

    /**
     * Retrieve a payment method from Mollie.
     *
     * Will throw an ApiException if the method id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function get(string $methodId, $query = [], bool $testmode = false): Method
    {
        if (! $query instanceof GetPaymentMethodQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaymentMethodQueryFactory::new($query)
                ->create();
        }

        /** @var Method */
        return $this->send((new GetPaymentMethodRequest($methodId, $query))->test($testmode));
    }
}
