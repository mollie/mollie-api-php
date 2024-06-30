<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\RefundStatus;

class RefundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestRefundStatuses
     */
    public function testRefundStatuses($status, $function, $expected_boolean)
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->{$function}());
    }

    /**
     * @param string $status
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestRefundCanBeCanceled
     */
    public function testRefundCanBeCanceled($status, $expected_boolean)
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->canBeCanceled());
    }

    public function dpTestRefundStatuses()
    {
        return [
            [RefundStatus::PENDING, "isPending", true],
            [RefundStatus::PENDING, "isProcessing", false],
            [RefundStatus::PENDING, "isQueued", false],
            [RefundStatus::PENDING, "isTransferred", false],
            [RefundStatus::PENDING, "isFailed", false],

            [RefundStatus::PROCESSING, "isPending", false],
            [RefundStatus::PROCESSING, "isProcessing", true],
            [RefundStatus::PROCESSING, "isQueued", false],
            [RefundStatus::PROCESSING, "isTransferred", false],
            [RefundStatus::PROCESSING, "isFailed", false],

            [RefundStatus::QUEUED, "isPending", false],
            [RefundStatus::QUEUED, "isProcessing", false],
            [RefundStatus::QUEUED, "isQueued", true],
            [RefundStatus::QUEUED, "isTransferred", false],
            [RefundStatus::QUEUED, "isFailed", false],

            [RefundStatus::REFUNDED, "isPending", false],
            [RefundStatus::REFUNDED, "isProcessing", false],
            [RefundStatus::REFUNDED, "isQueued", false],
            [RefundStatus::REFUNDED, "isTransferred", true],
            [RefundStatus::REFUNDED, "isFailed", false],

            [RefundStatus::FAILED, "isPending", false],
            [RefundStatus::FAILED, "isProcessing", false],
            [RefundStatus::FAILED, "isQueued", false],
            [RefundStatus::FAILED, "isTransferred", false],
            [RefundStatus::FAILED, "isFailed", true],
        ];
    }

    public function dpTestRefundCanBeCanceled()
    {
        return [
            [RefundStatus::PENDING, true],
            [RefundStatus::PROCESSING, false],
            [RefundStatus::QUEUED, true],
            [RefundStatus::REFUNDED, false],
            [RefundStatus::FAILED, false],
            [RefundStatus::CANCELED, false],
        ];
    }
}
