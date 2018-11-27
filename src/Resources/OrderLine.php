<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\OrderLineStatus;
use Mollie\Api\Types\OrderLineType;

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
     * Can this order line be canceled?
     *
     * @var bool
     */
    public $isCancelable;

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
     * Is this order line created?
     *
     * @return bool
     */
    public function isCreated()
    {
        return $this->status === OrderLineStatus::STATUS_CREATED;
    }

    /**
     * Is this order line paid for?
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === OrderLineStatus::STATUS_PAID;
    }

    /**
     * Is this order line authorized?
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->status === OrderLineStatus::STATUS_AUTHORIZED;
    }

    /**
     * Is this order line canceled?
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === OrderLineStatus::STATUS_CANCELED;
    }

    /**
     * (Deprecated) Is this order line refunded?
     * @deprecated 2018-11-27
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === OrderLineStatus::STATUS_REFUNDED;
    }

    /**
     * Is this order line shipping?
     *
     * @return bool
     */
    public function isShipping()
    {
        return $this->status === OrderLineStatus::STATUS_SHIPPING;
    }

    /**
     * Is this order line completed?
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === OrderLineStatus::STATUS_COMPLETED;
    }

    /**
     * Is this order line for a physical product?
     *
     * @return bool
     */
    public function isPhysical()
    {
        return $this->type === OrderLineType::TYPE_PHYSICAL;
    }

    /**
     * Is this order line for applying a discount?
     *
     * @return bool
     */
    public function isDiscount()
    {
        return $this->type === OrderLineType::TYPE_DISCOUNT;
    }

    /**
     * Is this order line for a digital product?
     *
     * @return bool
     */
    public function isDigital()
    {
        return $this->type === OrderLineType::TYPE_DIGITAL;
    }

    /**
     * Is this order line for applying a shipping fee?
     *
     * @return bool
     */
    public function isShippingFee()
    {
        return $this->type === OrderLineType::TYPE_SHIPPING_FEE;
    }

    /**
     * Is this order line for store credit?
     *
     * @return bool
     */
    public function isStoreCredit()
    {
        return $this->type === OrderLineType::TYPE_STORE_CREDIT;
    }

    /**
     * Is this order line for a gift card?
     *
     * @return bool
     */
    public function isGiftCard()
    {
        return $this->type === OrderLineType::TYPE_GIFT_CARD;
    }

    /**
     * Is this order line for a surcharge?
     *
     * @return bool
     */
    public function isSurcharge()
    {
        return $this->type === OrderLineType::TYPE_SURCHARGE;
    }

}
