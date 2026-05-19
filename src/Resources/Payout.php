<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Payout extends BaseResource
{
    /**
     * The identifier uniquely referring to this payout.
     *
     * @example po_4KgGJJSZpH
     *
     * @var string
     */
    public $id;

    /**
     * The identifier of the balance that will be paid out.
     *
     * @example bal_gVMhHKqSSRYJyPsuoPNFH
     *
     * @var string
     */
    public $balanceId;

    /**
     * The amount paid out, excluding any applicable fees.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * The description that will appear on the bank statement for this payout.
     *
     * @var string|null
     */
    public $description;

    /**
     * The status of the payout.
     * Possible values: "requested", "initiated", "processing-at-bank", "completed", "canceled", "failed"
     *
     * @var string
     */
    public $status;

    /**
     * UTC datetime the payout was created in ISO-8601 format.
     *
     * @example "2026-05-19T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;
}
