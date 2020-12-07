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
     * @param bool $expected_boolean
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
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestOrderLineTypes
     */
    public function testOrderLineTypes($type, $function, $expected_boolean)
    {
        $orderLine = new OrderLine($this->createMock(MollieApiClient::class));
        $orderLine->type = $type;

        $this->assertEquals($expected_boolean, $orderLine->{$function}());
    }

    /**
     * @param $vatRate
     * @param $expected_boolean
     *
     * @dataProvider dpTestUpdateVatRate
     */
    public function testUpdateVatRate($vatRate, $expected_boolean)
    {
        $orderLine = new OrderLine($this->createMock(MollieApiClient::class));
        $orderLine->vatRate = $vatRate;

        $this->assertEquals(isset($orderLine->getUpdateData()['vatRate']), $expected_boolean);
    }

    public function dpTestUpdateVatRate()
    {
        return [
            [0, true],
            ['0', true],
            [null, false],
        ];
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
            [OrderLineStatus::STATUS_CREATED, "isShipping", false],
            [OrderLineStatus::STATUS_CREATED, "isCompleted", false],

            [OrderLineStatus::STATUS_PAID, "isCreated", false],
            [OrderLineStatus::STATUS_PAID, "isPaid", true],
            [OrderLineStatus::STATUS_PAID, "isAuthorized", false],
            [OrderLineStatus::STATUS_PAID, "isCanceled", false],
            [OrderLineStatus::STATUS_PAID, "isShipping", false],
            [OrderLineStatus::STATUS_PAID, "isCompleted", false],

            [OrderLineStatus::STATUS_AUTHORIZED, "isCreated", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isPaid", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isAuthorized", true],
            [OrderLineStatus::STATUS_AUTHORIZED, "isCanceled", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isShipping", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isCompleted", false],

            [OrderLineStatus::STATUS_CANCELED, "isCreated", false],
            [OrderLineStatus::STATUS_CANCELED, "isPaid", false],
            [OrderLineStatus::STATUS_CANCELED, "isAuthorized", false],
            [OrderLineStatus::STATUS_CANCELED, "isCanceled", true],
            [OrderLineStatus::STATUS_CANCELED, "isShipping", false],
            [OrderLineStatus::STATUS_CANCELED, "isCompleted", false],

            [OrderLineStatus::STATUS_SHIPPING, "isCreated", false],
            [OrderLineStatus::STATUS_SHIPPING, "isPaid", false],
            [OrderLineStatus::STATUS_SHIPPING, "isAuthorized", false],
            [OrderLineStatus::STATUS_SHIPPING, "isCanceled", false],
            [OrderLineStatus::STATUS_SHIPPING, "isShipping", true],
            [OrderLineStatus::STATUS_SHIPPING, "isCompleted", false],

            [OrderLineStatus::STATUS_COMPLETED, "isCreated", false],
            [OrderLineStatus::STATUS_COMPLETED, "isPaid", false],
            [OrderLineStatus::STATUS_COMPLETED, "isAuthorized", false],
            [OrderLineStatus::STATUS_COMPLETED, "isCanceled", false],
            [OrderLineStatus::STATUS_COMPLETED, "isShipping", false],
            [OrderLineStatus::STATUS_COMPLETED, "isCompleted", true],
        ];
    }
}
