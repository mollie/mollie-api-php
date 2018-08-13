<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Order;
use Mollie\Api\Types\OrderStatus;

class OrderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param boolean $expected_boolean
     *
     * @dataProvider dpTestOrderStatuses
     */
    public function testOrderStatuses($status, $function, $expected_boolean)
    {
        $order = new Order($this->createMock(MollieApiClient::class));
        $order->status = $status;

        $this->assertEquals($expected_boolean, $order->{$function}());
    }

    public function dpTestOrderStatuses()
    {
        return [
            [OrderStatus::STATUS_CREATED, "isCreated", true],
            [OrderStatus::STATUS_CREATED, "isPaid", false],
            [OrderStatus::STATUS_CREATED, "isAuthorized", false],
            [OrderStatus::STATUS_CREATED, "isCanceled", false],
            [OrderStatus::STATUS_CREATED, "isRefunded", false],
            [OrderStatus::STATUS_CREATED, "isShipping", false],
            [OrderStatus::STATUS_CREATED, "isCompleted", false],
            [OrderStatus::STATUS_CREATED, "isVoid", false],

            [OrderStatus::STATUS_PAID, "isCreated", false],
            [OrderStatus::STATUS_PAID, "isPaid", true],
            [OrderStatus::STATUS_PAID, "isAuthorized", false],
            [OrderStatus::STATUS_PAID, "isCanceled", false],
            [OrderStatus::STATUS_PAID, "isRefunded", false],
            [OrderStatus::STATUS_PAID, "isShipping", false],
            [OrderStatus::STATUS_PAID, "isCompleted", false],
            [OrderStatus::STATUS_PAID, "isVoid", false],

            [OrderStatus::STATUS_AUTHORIZED, "isCreated", false],
            [OrderStatus::STATUS_AUTHORIZED, "isPaid", false],
            [OrderStatus::STATUS_AUTHORIZED, "isAuthorized", true],
            [OrderStatus::STATUS_AUTHORIZED, "isCanceled", false],
            [OrderStatus::STATUS_AUTHORIZED, "isRefunded", false],
            [OrderStatus::STATUS_AUTHORIZED, "isShipping", false],
            [OrderStatus::STATUS_AUTHORIZED, "isCompleted", false],
            [OrderStatus::STATUS_AUTHORIZED, "isVoid", false],

            [OrderStatus::STATUS_CANCELED, "isCreated", false],
            [OrderStatus::STATUS_CANCELED, "isPaid", false],
            [OrderStatus::STATUS_CANCELED, "isAuthorized", false],
            [OrderStatus::STATUS_CANCELED, "isCanceled", true],
            [OrderStatus::STATUS_CANCELED, "isRefunded", false],
            [OrderStatus::STATUS_CANCELED, "isShipping", false],
            [OrderStatus::STATUS_CANCELED, "isCompleted", false],
            [OrderStatus::STATUS_CANCELED, "isVoid", false],

            [OrderStatus::STATUS_REFUNDED, "isCreated", false],
            [OrderStatus::STATUS_REFUNDED, "isPaid", false],
            [OrderStatus::STATUS_REFUNDED, "isAuthorized", false],
            [OrderStatus::STATUS_REFUNDED, "isCanceled", false],
            [OrderStatus::STATUS_REFUNDED, "isRefunded", true],
            [OrderStatus::STATUS_REFUNDED, "isShipping", false],
            [OrderStatus::STATUS_REFUNDED, "isCompleted", false],
            [OrderStatus::STATUS_REFUNDED, "isVoid", false],

            [OrderStatus::STATUS_SHIPPING, "isCreated", false],
            [OrderStatus::STATUS_SHIPPING, "isPaid", false],
            [OrderStatus::STATUS_SHIPPING, "isAuthorized", false],
            [OrderStatus::STATUS_SHIPPING, "isCanceled", false],
            [OrderStatus::STATUS_SHIPPING, "isRefunded", false],
            [OrderStatus::STATUS_SHIPPING, "isShipping", true],
            [OrderStatus::STATUS_SHIPPING, "isCompleted", false],
            [OrderStatus::STATUS_SHIPPING, "isVoid", false],

            [OrderStatus::STATUS_COMPLETED, "isCreated", false],
            [OrderStatus::STATUS_COMPLETED, "isPaid", false],
            [OrderStatus::STATUS_COMPLETED, "isAuthorized", false],
            [OrderStatus::STATUS_COMPLETED, "isCanceled", false],
            [OrderStatus::STATUS_COMPLETED, "isRefunded", false],
            [OrderStatus::STATUS_COMPLETED, "isShipping", false],
            [OrderStatus::STATUS_COMPLETED, "isCompleted", true],
            [OrderStatus::STATUS_COMPLETED, "isVoid", false],

            [OrderStatus::STATUS_VOID, "isCreated", false],
            [OrderStatus::STATUS_VOID, "isPaid", false],
            [OrderStatus::STATUS_VOID, "isAuthorized", false],
            [OrderStatus::STATUS_VOID, "isCanceled", false],
            [OrderStatus::STATUS_VOID, "isRefunded", false],
            [OrderStatus::STATUS_VOID, "isShipping", false],
            [OrderStatus::STATUS_VOID, "isCompleted", false],
            [OrderStatus::STATUS_VOID, "isVoid", true],
        ];
    }
}
