<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\OrderStatus;

class OrderLine extends BaseResource
{
    /**
     * Always 'orderline'
     *
     * @var string
     */
    public $resource;

    /**
     * Id of the order line.
     *
     * @var string
     */
    public $id;

    /**
     * The ID of the order this line belongs to.
     *
     * @example ord_kEn1PlbGa
     * @var string
     */
    public $orderId;

    /**
     * The type of product bought.
     *
     * @example physical
     * @var string
     */
    public $type;

    /**
     * A description of the order line.
     *
     * @example LEGO 4440 Forest Police Station
     * @var string
     */
    public $name;

    /**
     * The status of the order line.
     *
     * @var string
     */
    public $status;

    /**
     * The number of items in the order line.
     *
     * @var int
     */
    public $quantity;

    /**
     * The price of a single item in the order line.
     *
     * @var object
     */
    public $unitPrice;

    /**
     * Any discounts applied to the order line.
     *
     * @var object|null
     */
    public $discountAmount;

    /**
     * The total amount of the line, including VAT and discounts.
     *
     * @var object
     */
    public $totalAmount;

    /**
     * The VAT rate applied to the order line. It is defined as a string
     * and not as a float to ensure the correct number of decimals are
     * passed.
     *
     * @example "21.00"
     * @var string
     */
    public $vatRate;

    /**
     * The amount of value-added tax on the line.
     *
     * @var object
     */
    public $vatAmount;

    /**
     * The SKU, EAN, ISBN or UPC of the product sold.
     *
     * @var string|null
     */
    public $sku;

    /**
     * A link pointing to an image of the product sold.
     *
     * @var string|null
     */
    public $imageUrl;

    /**
     * A link pointing to the product page in your web shop of the product sold.
     *
     * @var string|null
     */
    public $productUrl;

    /**
     * The order line's date and time of creation, in ISO 8601 format.
     *
     * @example 2018-08-02T09:29:56+00:00
     * @var string
     */
    public $createdAt;

    /**
     * Is this order created?
     *
     * @return bool
     */
    public function isCreated()
    {
        return $this->status === OrderStatus::STATUS_CREATED;
    }

    /**
     * Is this order paid for?
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === OrderStatus::STATUS_PAID;
    }

    /**
     * Is this order authorized?
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->status === OrderStatus::STATUS_AUTHORIZED;
    }

    /**
     * Is this order canceled?
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === OrderStatus::STATUS_CANCELED;
    }

    /**
     * Is this order refunded?
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === OrderStatus::STATUS_REFUNDED;
    }

    /**
     * Is this order shipping?
     *
     * @return bool
     */
    public function isShipping()
    {
        return $this->status === OrderStatus::STATUS_SHIPPING;
    }

    /**
     * Is this order void?
     *
     * @return bool
     */
    public function isVoid()
    {
        return $this->status === OrderStatus::STATUS_VOID;
    }
}
