<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\ResourceFactory;

class PaymentEndpoint extends CollectionEndpointAbstract
{
    protected string $resourcePath = "payments";

    public const RESOURCE_ID_PREFIX = 'tr_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Payment
    {
        return new Payment($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): PaymentCollection
    {
        return new PaymentCollection($this->client, $count, $_links);
    }

    /**
     * Creates a payment in Mollie.
     *
     * @param array $data An array containing details on the payment.
     * @param array $filters
     *
     * @return Payment
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = []): Payment
    {
        return $this->rest_create($data, $filters);
    }

    /**
     * Update the given Payment.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @param string $paymentId
     * @param array $data
     *
     * @return Payment
     * @throws ApiException
     */
    public function update($paymentId, array $data = []): Payment
    {
        if (empty($paymentId) || strpos($paymentId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid payment ID: '{$paymentId}'. A payment ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::rest_update($paymentId, $data);
    }

    /**
     * Retrieve a single payment from Mollie.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @param string $paymentId
     * @param array $parameters
     *
     * @return Payment
     * @throws ApiException
     */
    public function get($paymentId, array $parameters = []): Payment
    {
        if (empty($paymentId) || strpos($paymentId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid payment ID: '{$paymentId}'. A payment ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::rest_read($paymentId, $parameters);
    }

    /**
     * Deletes the given Payment.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $paymentId
     * @param array $data
     *
     * @return Payment
     * @throws ApiException
     */
    public function delete(string $paymentId, array $data = []): ?Payment
    {
        return $this->cancel($paymentId, $data);
    }

    /**
     * Cancel the given Payment. This is just an alias of the 'delete' method.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $paymentId
     * @param array $data
     *
     * @return Payment
     * @throws ApiException
     */
    public function cancel(string $paymentId, array $data = []): ?Payment
    {
        return $this->rest_delete($paymentId, $data);
    }

    /**
     * Retrieves a collection of Payments from Mollie.
     *
     * @param string $from The first payment ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function page(string $from = null, int $limit = null, array $parameters = []): PaymentCollection
    {
        return $this->rest_list($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over payments retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->rest_iterator($from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Issue a refund for the given payment.
     *
     * The $data parameter may either be an array of endpoint parameters, a float value to
     * initiate a partial refund, or empty to do a full refund.
     *
     * @param Payment $payment
     * @param array|float|null $data
     *
     * @return Refund
     * @throws ApiException
     * @todo: Refactor
     */
    public function refund(Payment $payment, $data = []): Refund
    {
        $resource = "{$this->getResourcePath()}/" . urlencode($payment->id) . "/refunds";

        $body = null;
        if (($data === null ? 0 : count($data)) > 0) {
            $body = json_encode($data);
        }

        $result = $this->client->performHttpCall(self::REST_CREATE, $resource, $body);

        return ResourceFactory::createFromApiResult($result, new Refund($this->client));
    }
}
