<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreatePayoutRequestFactory;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Http\Requests\CancelPayoutRequest;
use Mollie\Api\Http\Requests\GetPayoutRequest;
use Mollie\Api\Http\Requests\GetPaginatedPayoutsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payout;
use Mollie\Api\Resources\PayoutCollection;
use Mollie\Api\Utils\Utility;

class PayoutEndpointCollection extends EndpointCollection
{
    /**
     * Creates a payout in Mollie.
     *
     * @param  array  $payload  An array containing details on the payout.
     * @param  bool  $testmode  Set to true to create the payout in test mode.
     *
     * @throws RequestException
     */
    public function create(array $payload = [], bool $testmode = false): Payout
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = CreatePayoutRequestFactory::new()
            ->withPayload($payload)
            ->create();

        /** @var Payout */
        return $this->send($request->test($testmode));
    }

    /**
     * Retrieve a payout from Mollie.
     *
     * Will throw an ApiException if the payout id is invalid or the resource cannot be found.
     *
     * @param  string  $id  The payout ID.
     * @param  bool  $testmode  Set to true to retrieve the payout in test mode.
     *
     * @throws RequestException
     */
    public function get(string $id, bool $testmode = false): Payout
    {
        /** @var Payout */
        return $this->send((new GetPayoutRequest($id))->test($testmode));
    }

    /**
     * Cancel the given payout.
     *
     * Will throw an ApiException if the payout id is invalid, the resource cannot be found, or the payout can no longer be canceled.
     *
     * @param  string  $id  The payout ID.
     * @param  bool  $testmode  Set to true to cancel the payout in test mode.
     *
     * @throws RequestException
     */
    public function cancel(string $id, bool $testmode = false): Payout
    {
        /** @var Payout */
        return $this->send((new CancelPayoutRequest($id))->test($testmode));
    }

    /**
     * Retrieves a collection of payouts from Mollie.
     *
     * @param  string|null  $from  The first payout ID you want to include in your list.
     * @param  int|null  $limit  The maximum number of payouts to return.
     * @param  array  $filters  An array of filters, such as "balanceId", "sort", and "testmode".
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): PayoutCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $query = SortablePaginatedQueryFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        $request = new GetPaginatedPayoutsRequest(
            $query->from,
            $query->limit,
            $query->sort,
            $filters['balanceId'] ?? null
        );

        /** @var PayoutCollection */
        return $this->send($request->test($testmode));
    }

    /**
     * Create an iterator for iterating over payouts retrieved from Mollie.
     *
     * @param  string|null  $from  The first payout ID you want to include in your list.
     * @param  int|null  $limit  The maximum number of payouts to return per page.
     * @param  array  $filters  An array of filters, such as "balanceId", "sort", and "testmode".
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     *
     * @throws RequestException
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $query = SortablePaginatedQueryFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        $request = new GetPaginatedPayoutsRequest(
            $query->from,
            $query->limit,
            $query->sort,
            $filters['balanceId'] ?? null
        );

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
