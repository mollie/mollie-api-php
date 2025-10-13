<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class ConnectBalanceTransfer extends BaseResource
{
    /**
     * Indicates the response contains a connect balance transfer object.
     * Will always contain the string "connect-balance-transfer" for this endpoint.
     *
     * @var string
     */
    public $resource;

    /**
     * The identifier uniquely referring to this connect balance transfer.
     *
     * @example cbt_4KgGJJSZpH
     *
     * @var string
     */
    public $id;

    /**
     * The amount that is transferred from the source balance to the destination balance.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * The source balance from which the amount will be transferred.
     *
     * @var \stdClass
     */
    public $source;

    /**
     * The destination balance to which the amount will be transferred.
     *
     * @var \stdClass
     */
    public $destination;

    /**
     * A short description of the balance transfer.
     *
     * @var string
     */
    public $description;

    /**
     * UTC datetime the connect balance transfer was created in ISO-8601 format.
     *
     * @example "2023-12-25T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * Links to help navigate through the Mollie API and related resources.
     *
     * @var \stdClass
     */
    public $_links;
}
