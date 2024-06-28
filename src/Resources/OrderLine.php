<?php

namespace Mollie\Api\Resources;

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
     * The number of items that are shipped for this order line.
     *
     * @var int
     */
    public $quantityShipped;

    /**
     * The total amount that is shipped for this order line.
     *
     * @var \stdClass
     */
    public $amountShipped;

    /**
     * The number of items that are refunded for this order line.
     *
     * @var int
     */
    public $quantityRefunded;

    /**
     * The total amount that is refunded for this order line.
     *
     * @var \stdClass
     */
    public $amountRefunded;

    /**
     * The number of items that are canceled in this order line.
     *
     * @var int
     */
    public $quantityCanceled;

    /**
     * The total amount that is canceled in this order line.
     *
     * @var \stdClass
     */
    public $amountCanceled;

    /**
     * The number of items that can still be shipped for this order line.
     *
     * @var int
     */
    public $shippableQuantity;

    /**
     * The number of items that can still be refunded for this order line.
     *
     * @var int
     */
    public $refundableQuantity;

    /**
     * The number of items that can still be canceled for this order line.
     *
     * @var int
     */
    public $cancelableQuantity;

    /**
     * The price of a single item in the order line.
     *
     * @var \stdClass
     */
    public $unitPrice;

    /**
     * Any discounts applied to the order line.
     *
     * @var \stdClass|null
     */
    public $discountAmount;

    /**
     * The total amount of the line, including VAT and discounts.
     *
     * @var \stdClass
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
     * @var \stdClass
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
     * During creation of the order you can set custom metadata on order lines that is stored with
     * the order, and given back whenever you retrieve that order line.
     *
     * @var \stdClass|mixed|null
     */
    public $metadata;

    /**
     * The order line's date and time of creation, in ISO 8601 format.
     *
     * @example 2018-08-02T09:29:56+00:00
     * @var string
     */
    public $createdAt;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * Get the url pointing to the product page in your web shop of the product sold.
     *
     * @return string|null
     */
    public function getProductUrl()
    {
        if (empty($this->_links->productUrl)) {
            return null;
        }

        return $this->_links->productUrl;
    }

    /**
     * Get the image URL of the product sold.
     *
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        if (empty($this->_links->imageUrl)) {
            return null;
        }

        return $this->_links->imageUrl;
    }

    /**
     * Is this order line created?
     *
     * @return bool
     */
    public function isCreated(): bool
    {
        return $this->status === OrderLineStatus::CREATED;
    }

    /**
     * Is this order line paid for?
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === OrderLineStatus::PAID;
    }

    /**
     * Is this order line authorized?
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->status === OrderLineStatus::AUTHORIZED;
    }

    /**
     * Is this order line canceled?
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->status === OrderLineStatus::CANCELED;
    }

    /**
     * Is this order line shipping?
     *
     * @return bool
     */
    public function isShipping(): bool
    {
        return $this->status === OrderLineStatus::SHIPPING;
    }

    /**
     * Is this order line completed?
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderLineStatus::COMPLETED;
    }

    /**
     * Is this order line for a physical product?
     *
     * @return bool
     */
    public function isPhysical(): bool
    {
        return $this->type === OrderLineType::PHYSICAL;
    }

    /**
     * Is this order line for applying a discount?
     *
     * @return bool
     */
    public function isDiscount(): bool
    {
        return $this->type === OrderLineType::DISCOUNT;
    }

    /**
     * Is this order line for a digital product?
     *
     * @return bool
     */
    public function isDigital(): bool
    {
        return $this->type === OrderLineType::DIGITAL;
    }

    /**
     * Is this order line for applying a shipping fee?
     *
     * @return bool
     */
    public function isShippingFee(): bool
    {
        return $this->type === OrderLineType::SHIPPING_FEE;
    }

    /**
     * Is this order line for store credit?
     *
     * @return bool
     */
    public function isStoreCredit(): bool
    {
        return $this->type === OrderLineType::STORE_CREDIT;
    }

    /**
     * Is this order line for a gift card?
     *
     * @return bool
     */
    public function isGiftCard(): bool
    {
        return $this->type === OrderLineType::GIFT_CARD;
    }

    /**
     * Is this order line for a surcharge?
     *
     * @return bool
     */
    public function isSurcharge(): bool
    {
        return $this->type === OrderLineType::SURCHARGE;
    }

    /**
     * Update an orderline by supplying one or more parameters in the data array
     *
     * @return null|Order
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): ?Order
    {
        /** @var null|Order */
        $result = $this->client->orderLines->update($this->orderId, $this->id, $this->getUpdateData());

        /** @var Order */
        return ResourceFactory::createFromApiResult($this->client, $result, Order::class);
    }

    /**
     * Get sanitized array of order line data
     *
     * @return array
     */
    public function getUpdateData(): array
    {
        $data = [
            "name" => $this->name,
            'imageUrl' => $this->imageUrl,
            'productUrl' => $this->productUrl,
            'metadata' => $this->metadata,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'discountAmount' => $this->discountAmount,
            'totalAmount' => $this->totalAmount,
            'vatAmount' => $this->vatAmount,
            'vatRate' => $this->vatRate,
        ];

        // Explicitly filter only NULL values to keep "vatRate => 0" intact
        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }
}
