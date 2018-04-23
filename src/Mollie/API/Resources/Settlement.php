<?php

namespace Mollie\Api\Resources;

class Settlement extends BaseResource
{

    /**
     * Id of the settlement.
     *
     * @var string
     */
    public $id;
    /**
     * The settlement reference. This corresponds to an invoice that's in your Dashboard.
     *
     * @var string
     */
    public $reference;
    /**
     * Total settlement amount in euros.
     *
     * @var double
     */
    public $amount;
    /**
     * @var string
     */
    public $settledDatetime;
    /**
     * Revenues and costs nested per year, per month, and per payment method.
     *
     * @see https://www.mollie.com/en/docs/reference/settlements/get#period-object
     * @var object
     */
    public $periods;
    /**
     * Payment IDs that were settled (either paid out or reversed).
     *
     * @var string[]
     */
    public $paymentIds;

}