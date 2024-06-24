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
     * @param string $vatRate
     * @param bool $expected_boolean
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
            [OrderLineType::PHYSICAL, "isPhysical", true],
            [OrderLineType::PHYSICAL, "isDiscount", false],
            [OrderLineType::PHYSICAL, "isDigital", false],
            [OrderLineType::PHYSICAL, "isShippingFee", false],
            [OrderLineType::PHYSICAL, "isStoreCredit", false],
            [OrderLineType::PHYSICAL, "isGiftCard", false],
            [OrderLineType::PHYSICAL, "isSurcharge", false],

            [OrderLineType::DISCOUNT, "isPhysical", false],
            [OrderLineType::DISCOUNT, "isDiscount", true],
            [OrderLineType::DISCOUNT, "isDigital", false],
            [OrderLineType::DISCOUNT, "isShippingFee", false],
            [OrderLineType::DISCOUNT, "isStoreCredit", false],
            [OrderLineType::DISCOUNT, "isGiftCard", false],
            [OrderLineType::DISCOUNT, "isSurcharge", false],

            [OrderLineType::DIGITAL, "isPhysical", false],
            [OrderLineType::DIGITAL, "isDiscount", false],
            [OrderLineType::DIGITAL, "isDigital", true],
            [OrderLineType::DIGITAL, "isShippingFee", false],
            [OrderLineType::DIGITAL, "isStoreCredit", false],
            [OrderLineType::DIGITAL, "isGiftCard", false],
            [OrderLineType::DIGITAL, "isSurcharge", false],

            [OrderLineType::SHIPPING_FEE, "isPhysical", false],
            [OrderLineType::SHIPPING_FEE, "isDiscount", false],
            [OrderLineType::SHIPPING_FEE, "isDigital", false],
            [OrderLineType::SHIPPING_FEE, "isShippingFee", true],
            [OrderLineType::SHIPPING_FEE, "isStoreCredit", false],
            [OrderLineType::SHIPPING_FEE, "isGiftCard", false],
            [OrderLineType::SHIPPING_FEE, "isSurcharge", false],

            [OrderLineType::STORE_CREDIT, "isPhysical", false],
            [OrderLineType::STORE_CREDIT, "isDiscount", false],
            [OrderLineType::STORE_CREDIT, "isDigital", false],
            [OrderLineType::STORE_CREDIT, "isShippingFee", false],
            [OrderLineType::STORE_CREDIT, "isStoreCredit", true],
            [OrderLineType::STORE_CREDIT, "isGiftCard", false],
            [OrderLineType::STORE_CREDIT, "isSurcharge", false],

            [OrderLineType::GIFT_CARD, "isPhysical", false],
            [OrderLineType::GIFT_CARD, "isDiscount", false],
            [OrderLineType::GIFT_CARD, "isDigital", false],
            [OrderLineType::GIFT_CARD, "isShippingFee", false],
            [OrderLineType::GIFT_CARD, "isStoreCredit", false],
            [OrderLineType::GIFT_CARD, "isGiftCard", true],
            [OrderLineType::GIFT_CARD, "isSurcharge", false],

            [OrderLineType::SURCHARGE, "isPhysical", false],
            [OrderLineType::SURCHARGE, "isDiscount", false],
            [OrderLineType::SURCHARGE, "isDigital", false],
            [OrderLineType::SURCHARGE, "isShippingFee", false],
            [OrderLineType::SURCHARGE, "isStoreCredit", false],
            [OrderLineType::SURCHARGE, "isGiftCard", false],
            [OrderLineType::SURCHARGE, "isSurcharge", true],
        ];
    }

    public function dpTestOrderLineStatuses()
    {
        return [
            [OrderLineStatus::CREATED, "isCreated", true],
            [OrderLineStatus::CREATED, "isPaid", false],
            [OrderLineStatus::CREATED, "isAuthorized", false],
            [OrderLineStatus::CREATED, "isCanceled", false],
            [OrderLineStatus::CREATED, "isShipping", false],
            [OrderLineStatus::CREATED, "isCompleted", false],

            [OrderLineStatus::PAID, "isCreated", false],
            [OrderLineStatus::PAID, "isPaid", true],
            [OrderLineStatus::PAID, "isAuthorized", false],
            [OrderLineStatus::PAID, "isCanceled", false],
            [OrderLineStatus::PAID, "isShipping", false],
            [OrderLineStatus::PAID, "isCompleted", false],

            [OrderLineStatus::AUTHORIZED, "isCreated", false],
            [OrderLineStatus::AUTHORIZED, "isPaid", false],
            [OrderLineStatus::AUTHORIZED, "isAuthorized", true],
            [OrderLineStatus::AUTHORIZED, "isCanceled", false],
            [OrderLineStatus::AUTHORIZED, "isShipping", false],
            [OrderLineStatus::AUTHORIZED, "isCompleted", false],

            [OrderLineStatus::CANCELED, "isCreated", false],
            [OrderLineStatus::CANCELED, "isPaid", false],
            [OrderLineStatus::CANCELED, "isAuthorized", false],
            [OrderLineStatus::CANCELED, "isCanceled", true],
            [OrderLineStatus::CANCELED, "isShipping", false],
            [OrderLineStatus::CANCELED, "isCompleted", false],

            [OrderLineStatus::SHIPPING, "isCreated", false],
            [OrderLineStatus::SHIPPING, "isPaid", false],
            [OrderLineStatus::SHIPPING, "isAuthorized", false],
            [OrderLineStatus::SHIPPING, "isCanceled", false],
            [OrderLineStatus::SHIPPING, "isShipping", true],
            [OrderLineStatus::SHIPPING, "isCompleted", false],

            [OrderLineStatus::COMPLETED, "isCreated", false],
            [OrderLineStatus::COMPLETED, "isPaid", false],
            [OrderLineStatus::COMPLETED, "isAuthorized", false],
            [OrderLineStatus::COMPLETED, "isCanceled", false],
            [OrderLineStatus::COMPLETED, "isShipping", false],
            [OrderLineStatus::COMPLETED, "isCompleted", true],
        ];
    }
}
