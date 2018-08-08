<?php

namespace Mollie\Api\Resources;

class Order extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the order.
     *
     * @var string
     */
    public $id;

    /**
     * The profile ID this payment belongs to.
     *
     * @example pfl_xH2kP6Nc6X
     * @var string
     */
    public $profileId;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) order.
     *
     * @var string
     */
    public $mode;

    /**
     * Amount object containing the value and currency
     *
     * @var object
     */
    public $amount;

    /**
     * The total amount captured, thus far.
     *
     * @var object
     */
    public $amountCaptured;

    /**
     * The total amount refunded, thus far.
     *
     * @var object
     */
    public $amountRefunded;

    /**
     * The status of the order.
     *
     * @var string
     */
    public $status;

    /**
     * The person and the address the order is billed to.
     *
     * @var object
     */
    public $billingAddress;

    /**
     * The date of birth of your customer, if available.
     * @example 1976-08-21
     * @var string|null
     */
    public $consumerDateOfBirth;

    /**
     * The order number that was used when creating the order.
     *
     * @var string
     */
    public $orderNumber;

    /**
     * The person and the address the order is billed to.
     *
     * @var object
     */
    public $shippingAddress;

    /**
     * The locale used for this order.
     *
     * @var string
     */
    public $locale;

    /**
     * During creation of the order you can set custom metadata that is stored with
     * the order, and given back whenever you retrieve that order.
     *
     * @var object|mixed|null
     */
    public $metadata;

    /**
     * UTC datetime the order was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $createdAt;

    /**
     * An object with several URL objects relevant to the customer. Every URL object will contain an href and a type field.
     * @var object[]
     */
    public $_links;

    /**
     * The order lines contain the actual things the customer bought.
     * @var array|object[]
     */
    public $lines;
}
