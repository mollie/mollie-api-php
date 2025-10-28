<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateConnectBalanceTransferRequestFactory;
use Mollie\Api\Http\Requests\GetConnectBalanceTransferRequest;
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Resources\ConnectBalanceTransferCollection;
use Mollie\Api\Resources\LazyCollection;

class ConnectBalanceTransferEndpointCollection extends EndpointCollection
{
    /**
     * Creates a Connect balance transfer in Mollie.
     *
     * @param  array  $payload  An array containing details on the balance transfer.
     *
     * @throws RequestException
     */
    public function create(array $payload = [], bool $test = false): ConnectBalanceTransfer
    {
        $request = CreateConnectBalanceTransferRequestFactory::new()
            ->withPayload($payload)
            ->create();

        /** @var ConnectBalanceTransfer */
        return $this->send($request->test($test));
    }

    /**
     * Retrieve a Connect balance transfer from Mollie.
     *
     * Will throw an ApiException if the balance transfer id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $id, bool $test = false): ConnectBalanceTransfer
    {
        /** @var ConnectBalanceTransfer */
        return $this->send((new GetConnectBalanceTransferRequest($id))->test($test));
    }

    /**
     * Retrieves a collection of Connect balance transfers from Mollie.
     *
     * @throws RequestException
     */
    public function page(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null
    ): ConnectBalanceTransferCollection {
        $request = new ListConnectBalanceTransfersRequest(
            $from,
            $limit,
            $sort
        );

        /** @var ConnectBalanceTransferCollection */
        return $this->send($request);
    }

    /**
     * Create an iterator for iterating over Connect balance transfers retrieved from Mollie.
     *
     * @param  string|null  $from  The first balance transfer ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        bool $iterateBackwards = false,
        bool $test = false
    ): LazyCollection {
        $request = new ListConnectBalanceTransfersRequest(
            $from,
            $limit,
            $sort
        );

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($test)
        );
    }
}
