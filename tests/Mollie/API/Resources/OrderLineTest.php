<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Types\OrderLineStatus;
use Mollie\Api\Types\OrderLineType;

class OrderLineTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param boolean $expected_boolean
     *
     * @dataProvider dpTestOrderLineStatuses
     */
    public function testOrderLineStatuses($status, $function, $expected_boolean)
    {
        $orderLine = new OrderLine($this->createMock(MollieApiClient::class));
        $orderLine->status = $status;

        $this->assertEquals($expected_boolean, $orderLine->{$function}());
    }

    /**
     * @param string $type
     * @param string $function
     * @param boolean $expected_boolean
     *
     * @dataProvider dpTestOrderLineTypes
     */
    public function testOrderLineTypes($type, $function, $expected_boolean)
    {
        $orderLine = new OrderLine($this->createMock(MollieApiClient::class));
        $orderLine->type = $type;

        $this->assertEquals($expected_boolean, $orderLine->{$function}());
    }

    public function dpTestOrderLineTypes()
    {
        return [
            [OrderLineType::TYPE_PHYSICAL, "isPhysical", true],
            [OrderLineType::TYPE_PHYSICAL, "isDiscount", false],
            [OrderLineType::TYPE_PHYSICAL, "isDigital", false],
            [OrderLineType::TYPE_PHYSICAL, "isShippingFee", false],
            [OrderLineType::TYPE_PHYSICAL, "isStoreCredit", false],
            [OrderLineType::TYPE_PHYSICAL, "isGiftCard", false],
            [OrderLineType::TYPE_PHYSICAL, "isSurcharge", false],

            [OrderLineType::TYPE_DISCOUNT, "isPhysical", false],
            [OrderLineType::TYPE_DISCOUNT, "isDiscount", true],
            [OrderLineType::TYPE_DISCOUNT, "isDigital", false],
            [OrderLineType::TYPE_DISCOUNT, "isShippingFee", false],
            [OrderLineType::TYPE_DISCOUNT, "isStoreCredit", false],
            [OrderLineType::TYPE_DISCOUNT, "isGiftCard", false],
            [OrderLineType::TYPE_DISCOUNT, "isSurcharge", false],

            [OrderLineType::TYPE_DIGITAL, "isPhysical", false],
            [OrderLineType::TYPE_DIGITAL, "isDiscount", false],
            [OrderLineType::TYPE_DIGITAL, "isDigital", true],
            [OrderLineType::TYPE_DIGITAL, "isShippingFee", false],
            [OrderLineType::TYPE_DIGITAL, "isStoreCredit", false],
            [OrderLineType::TYPE_DIGITAL, "isGiftCard", false],
            [OrderLineType::TYPE_DIGITAL, "isSurcharge", false],

            [OrderLineType::TYPE_SHIPPING_FEE, "isPhysical", false],
            [OrderLineType::TYPE_SHIPPING_FEE, "isDiscount", false],
            [OrderLineType::TYPE_SHIPPING_FEE, "isDigital", false],
            [OrderLineType::TYPE_SHIPPING_FEE, "isShippingFee", true],
            [OrderLineType::TYPE_SHIPPING_FEE, "isStoreCredit", false],
            [OrderLineType::TYPE_SHIPPING_FEE, "isGiftCard", false],
            [OrderLineType::TYPE_SHIPPING_FEE, "isSurcharge", false],

            [OrderLineType::TYPE_STORE_CREDIT, "isPhysical", false],
            [OrderLineType::TYPE_STORE_CREDIT, "isDiscount", false],
            [OrderLineType::TYPE_STORE_CREDIT, "isDigital", false],
            [OrderLineType::TYPE_STORE_CREDIT, "isShippingFee", false],
            [OrderLineType::TYPE_STORE_CREDIT, "isStoreCredit", true],
            [OrderLineType::TYPE_STORE_CREDIT, "isGiftCard", false],
            [OrderLineType::TYPE_STORE_CREDIT, "isSurcharge", false],

            [OrderLineType::TYPE_GIFT_CARD, "isPhysical", false],
            [OrderLineType::TYPE_GIFT_CARD, "isDiscount", false],
            [OrderLineType::TYPE_GIFT_CARD, "isDigital", false],
            [OrderLineType::TYPE_GIFT_CARD, "isShippingFee", false],
            [OrderLineType::TYPE_GIFT_CARD, "isStoreCredit", false],
            [OrderLineType::TYPE_GIFT_CARD, "isGiftCard", true],
            [OrderLineType::TYPE_GIFT_CARD, "isSurcharge", false],

            [OrderLineType::TYPE_SURCHARGE, "isPhysical", false],
            [OrderLineType::TYPE_SURCHARGE, "isDiscount", false],
            [OrderLineType::TYPE_SURCHARGE, "isDigital", false],
            [OrderLineType::TYPE_SURCHARGE, "isShippingFee", false],
            [OrderLineType::TYPE_SURCHARGE, "isStoreCredit", false],
            [OrderLineType::TYPE_SURCHARGE, "isGiftCard", false],
            [OrderLineType::TYPE_SURCHARGE, "isSurcharge", true],
        ];
    }

    public function dpTestOrderLineStatuses()
    {
        return [
            [OrderLineStatus::STATUS_CREATED, "isCreated", true],
            [OrderLineStatus::STATUS_CREATED, "isPaid", false],
            [OrderLineStatus::STATUS_CREATED, "isAuthorized", false],
            [OrderLineStatus::STATUS_CREATED, "isCanceled", false],
            [OrderLineStatus::STATUS_CREATED, "isRefunded", false],
            [OrderLineStatus::STATUS_CREATED, "isShipped", false],
            [OrderLineStatus::STATUS_CREATED, "isVoid", false],
            [OrderLineStatus::STATUS_CREATED, "isCancelable", true],

            [OrderLineStatus::STATUS_PAID, "isCreated", false],
            [OrderLineStatus::STATUS_PAID, "isPaid", true],
            [OrderLineStatus::STATUS_PAID, "isAuthorized", false],
            [OrderLineStatus::STATUS_PAID, "isCanceled", false],
            [OrderLineStatus::STATUS_PAID, "isRefunded", false],
            [OrderLineStatus::STATUS_PAID, "isShipped", false],
            [OrderLineStatus::STATUS_PAID, "isVoid", false],
            [OrderLineStatus::STATUS_PAID, "isCancelable", false],

            [OrderLineStatus::STATUS_AUTHORIZED, "isCreated", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isPaid", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isAuthorized", true],
            [OrderLineStatus::STATUS_AUTHORIZED, "isCanceled", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isRefunded", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isShipped", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isVoid", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isCancelable", true],

            [OrderLineStatus::STATUS_CANCELED, "isCreated", false],
            [OrderLineStatus::STATUS_CANCELED, "isPaid", false],
            [OrderLineStatus::STATUS_CANCELED, "isAuthorized", false],
            [OrderLineStatus::STATUS_CANCELED, "isCanceled", true],
            [OrderLineStatus::STATUS_CANCELED, "isRefunded", false],
            [OrderLineStatus::STATUS_CANCELED, "isShipped", false],
            [OrderLineStatus::STATUS_CANCELED, "isVoid", false],
            [OrderLineStatus::STATUS_CANCELED, "isCancelable", false],


            [OrderLineStatus::STATUS_REFUNDED, "isCreated", false],
            [OrderLineStatus::STATUS_REFUNDED, "isPaid", false],
            [OrderLineStatus::STATUS_REFUNDED, "isAuthorized", false],
            [OrderLineStatus::STATUS_REFUNDED, "isCanceled", false],
            [OrderLineStatus::STATUS_REFUNDED, "isRefunded", true],
            [OrderLineStatus::STATUS_REFUNDED, "isShipped", false],
            [OrderLineStatus::STATUS_REFUNDED, "isVoid", false],
            [OrderLineStatus::STATUS_REFUNDED, "isCancelable", false],


            [OrderLineStatus::STATUS_SHIPPED, "isCreated", false],
            [OrderLineStatus::STATUS_SHIPPED, "isPaid", false],
            [OrderLineStatus::STATUS_SHIPPED, "isAuthorized", false],
            [OrderLineStatus::STATUS_SHIPPED, "isCanceled", false],
            [OrderLineStatus::STATUS_SHIPPED, "isRefunded", false],
            [OrderLineStatus::STATUS_SHIPPED, "isShipped", true],
            [OrderLineStatus::STATUS_SHIPPED, "isVoid", false],
            [OrderLineStatus::STATUS_SHIPPED, "isCancelable", false],


            [OrderLineStatus::STATUS_VOID, "isCreated", false],
            [OrderLineStatus::STATUS_VOID, "isPaid", false],
            [OrderLineStatus::STATUS_VOID, "isAuthorized", false],
            [OrderLineStatus::STATUS_VOID, "isCanceled", false],
            [OrderLineStatus::STATUS_VOID, "isRefunded", false],
            [OrderLineStatus::STATUS_VOID, "isShipped", false],
            [OrderLineStatus::STATUS_VOID, "isVoid", true],
            [OrderLineStatus::STATUS_VOID, "isCancelable", false],

        ];
    }
}
