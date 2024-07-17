<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;

class PaymentChargebackEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "payments_chargebacks";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Chargeback::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = ChargebackCollection::class;

    /**
     * @param Payment $payment
     * @param string $chargebackId
     * @param array $parameters
     *
     * @return Chargeback
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Payment $payment, string $chargebackId, array $parameters = []): Chargeback
    {
        return $this->getForId($payment->id, $chargebackId, $parameters);
    }

    /**
     * @param string $paymentId
     * @param string $chargebackId
     * @param array $parameters
     *
     * @return Chargeback
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $paymentId, string $chargebackId, array $parameters = []): Chargeback
    {
        $this->parentId = $paymentId;

        /** @var Chargeback */
        return $this->readResource($chargebackId, $parameters);
    }

    /**
     * @param Payment $payment
     * @param array $parameters
     *
     * @return ChargebackCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Payment $payment, array $parameters = []): ChargebackCollection
    {
        /** @var ChargebackCollection */
        return $this->listForId($payment->id, $parameters);
    }

    /**
     * Create an iterator for iterating over chargebacks for the given payment, retrieved from Mollie.
     *
     * @param Payment $payment
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorFor(
        Payment $payment,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($payment->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * @param string $paymentId
     * @param array $parameters
     *
     * @return ChargebackCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $paymentId, array $parameters = []): ChargebackCollection
    {
        $this->parentId = $paymentId;

        /** @var ChargebackCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over chargebacks for the given payment id, retrieved from Mollie.
     *
     * @param string $paymentId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $paymentId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $paymentId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
