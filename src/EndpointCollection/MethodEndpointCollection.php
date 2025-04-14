<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetAllPaymentMethodsRequestFactory;
use Mollie\Api\Factories\GetEnabledMethodsRequestFactory;
use Mollie\Api\Factories\GetMethodRequestFactory;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Utils\Utility;

class MethodEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve all methods from Mollie.
     * Will return all available methods, both enabled and disabled.
     *
     * @param  array  $query  Query string parameters
     *
     * @throws RequestException
     */
    public function all(array $query = []): MethodCollection
    {
        $request = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery($query)
            ->create();

        /** @var MethodCollection */
        return $this->send($request);
    }

    /**
     * Retrieve all enabled methods for the organization.
     * In test mode, this includes pending methods.
     * The results are not paginated.
     *
     * @throws RequestException
     */
    public function allEnabled(array $query = [], bool $testmode = false): MethodCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetEnabledMethodsRequestFactory::new()
            ->withQuery($query)
            ->create();

        /** @var MethodCollection */
        return $this->send($request->test($testmode));
    }

    /**
     * @deprecated Use allEnabled() instead
     *
     * @throws RequestException
     */
    public function allActive($query = [], bool $testmode = false): MethodCollection
    {
        return $this->allEnabled($query, $testmode);
    }

    /**
     * Retrieve a payment method from Mollie.
     *
     * Will throw an ApiException if the method id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $methodId, $query = [], bool $testmode = false): Method
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetMethodRequestFactory::new($methodId)
            ->withQuery($query)
            ->create();

        /** @var Method */
        return $this->send($request->test($testmode));
    }
}
