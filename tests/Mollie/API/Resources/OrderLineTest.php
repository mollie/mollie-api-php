<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Types\OrderLineStatus;

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

    public function dpTestOrderLineStatuses()
    {
        return [
            [OrderLineStatus::STATUS_CREATED, "isCreated", true],
            [OrderLineStatus::STATUS_CREATED, "isPaid", false],
            [OrderLineStatus::STATUS_CREATED, "isAuthorized", false],
            [OrderLineStatus::STATUS_CREATED, "isCanceled", false],
            [OrderLineStatus::STATUS_CREATED, "isRefunded", false],
            [OrderLineStatus::STATUS_CREATED, "isShipping", false],
            [OrderLineStatus::STATUS_CREATED, "isVoid", false],

            [OrderLineStatus::STATUS_PAID, "isCreated", false],
            [OrderLineStatus::STATUS_PAID, "isPaid", true],
            [OrderLineStatus::STATUS_PAID, "isAuthorized", false],
            [OrderLineStatus::STATUS_PAID, "isCanceled", false],
            [OrderLineStatus::STATUS_PAID, "isRefunded", false],
            [OrderLineStatus::STATUS_PAID, "isShipping", false],
            [OrderLineStatus::STATUS_PAID, "isVoid", false],

            [OrderLineStatus::STATUS_AUTHORIZED, "isCreated", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isPaid", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isAuthorized", true],
            [OrderLineStatus::STATUS_AUTHORIZED, "isCanceled", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isRefunded", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isShipping", false],
            [OrderLineStatus::STATUS_AUTHORIZED, "isVoid", false],

            [OrderLineStatus::STATUS_CANCELED, "isCreated", false],
            [OrderLineStatus::STATUS_CANCELED, "isPaid", false],
            [OrderLineStatus::STATUS_CANCELED, "isAuthorized", false],
            [OrderLineStatus::STATUS_CANCELED, "isCanceled", true],
            [OrderLineStatus::STATUS_CANCELED, "isRefunded", false],
            [OrderLineStatus::STATUS_CANCELED, "isShipping", false],
            [OrderLineStatus::STATUS_CANCELED, "isVoid", false],

            [OrderLineStatus::STATUS_REFUNDED, "isCreated", false],
            [OrderLineStatus::STATUS_REFUNDED, "isPaid", false],
            [OrderLineStatus::STATUS_REFUNDED, "isAuthorized", false],
            [OrderLineStatus::STATUS_REFUNDED, "isCanceled", false],
            [OrderLineStatus::STATUS_REFUNDED, "isRefunded", true],
            [OrderLineStatus::STATUS_REFUNDED, "isShipping", false],
            [OrderLineStatus::STATUS_REFUNDED, "isVoid", false],

            [OrderLineStatus::STATUS_SHIPPING, "isCreated", false],
            [OrderLineStatus::STATUS_SHIPPING, "isPaid", false],
            [OrderLineStatus::STATUS_SHIPPING, "isAuthorized", false],
            [OrderLineStatus::STATUS_SHIPPING, "isCanceled", false],
            [OrderLineStatus::STATUS_SHIPPING, "isRefunded", false],
            [OrderLineStatus::STATUS_SHIPPING, "isShipping", true],
            [OrderLineStatus::STATUS_SHIPPING, "isVoid", false],

            [OrderLineStatus::STATUS_VOID, "isCreated", false],
            [OrderLineStatus::STATUS_VOID, "isPaid", false],
            [OrderLineStatus::STATUS_VOID, "isAuthorized", false],
            [OrderLineStatus::STATUS_VOID, "isCanceled", false],
            [OrderLineStatus::STATUS_VOID, "isRefunded", false],
            [OrderLineStatus::STATUS_VOID, "isShipping", false],
            [OrderLineStatus::STATUS_VOID, "isVoid", true],
        ];
    }
}
