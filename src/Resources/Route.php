<?php

namespace Mollie\Api\Resources;

class Route extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * The $amount that was routed.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * The $destination where the routed payment was send.
     *
     * @var \stdClass
     */
    public $destination;

    /**
     * UTC datetime The settlement of a routed payment can be delayed on payment level, by specifying a $releaseDate
     *
     * @example "2013-12-25"
     * @var string
     */
    public $releaseDate;



    /**
     * //TODO don't know if we need it
     * The payment id that was refunded.
     *
     * @var string
     */
    public $paymentId;
}
