<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateCustomerPaymentRequestFactory;
use Mollie\Api\Factories\GetPaginatedCustomerPaymentsRequestFactory;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Utils\Utility;

class CustomerPaymentsEndpointCollection extends EndpointCollection
{
    /**
     * Create a subscription for a Customer
     *
     * @throws RequestException
     */
    public function createFor(Customer $customer, array $payload = [], array $query = [], bool $testmode = false): Payment
    {
        return $this->createForId($customer->id, $payload, $query, $testmode);
    }

    /**
     * Create a subscription for a Customer ID
     *
     * @throws RequestException
     */
    public function createForId(string $customerId, array $payload = [], array $query = [], bool $testmode = false): Payment
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = CreateCustomerPaymentRequestFactory::new($customerId)
            ->withPayload($payload)
            ->withQuery($query)
            ->create();

        /** @var Payment */
        return $this->send($request->test($testmode));
    }

    /**
     * @param  string  $from  The first resource ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function pageFor(Customer $customer, ?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        return $this->pageForId($customer->id, $from, $limit, $filters);
    }

    /**
     * @param  string  $from  The first resource ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function pageForId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedCustomerPaymentsRequestFactory::new($customerId)
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * Create an iterator for iterating over payments for the given customer, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(
        Customer $customer,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($customer->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over payments for the given customer id, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $request = GetPaginatedCustomerPaymentsRequestFactory::new($customerId)
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
