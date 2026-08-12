<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Capture extends BaseResource
{
    use HasMode;

    public string $id;

    /**
     * Mode of the capture, either "live" or "test" depending on the API Key that was used.
     */
    public string $mode;

    /**
     * Description of the capture.
     */
    public ?string $description = null;

    /**
     * Status of the capture.
     */
    public ?string $status = null;

    /**
     * Amount object containing the value and currency.
     */
    public Money $amount;

    /**
     * Amount object containing the settlement value and currency.
     */
    public ?Money $settlementAmount = null;

    /**
     * Id of the capture's payment (on the Mollie platform).
     */
    public string $paymentId;

    /**
     * Id of the capture's shipment (on the Mollie platform).
     */
    public ?string $shipmentId = null;

    /**
     * Id of the capture's settlement (on the Mollie platform).
     */
    public ?string $settlementId = null;

    /**
     * Provide any data you like, for example a string or a JSON object. The data will be saved alongside the capture.
     *
     * @var \stdClass|mixed|null
     */
    public $metadata;

    public ?string $createdAt = null;

    /**
     * @var \stdClass|null
     */
    public $_links;
}
